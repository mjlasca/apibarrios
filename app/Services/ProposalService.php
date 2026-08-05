<?php

namespace App\Services;

use App\Models\Actividade;
use App\Models\barrio;
use App\Models\Clasificacione;
use App\Models\cliente;
use App\Models\Cobertura;
use App\Models\Cola;
use App\Models\gruposbarrio;
use App\Models\LineasPropuesta;
use App\Models\Propuesta;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

class ProposalService
{
    public const PREFIX = 'O';

    /**
     * Business dayzone: timestamps are stored in UTC but day boundaries
     * must match the local calendar the operators work with.
     */
    public const LOCAL_TIMEZONE = 'America/Argentina/Buenos_Aires';

    public function __construct(
        private readonly ClientService $clients,
    ) {
    }

    /**
     * Persist a full proposal: policyholder, linked clients, coverage,
     * neighborhoods (single or from groups) and proposal lines.
     * Wrapped in a transaction to guarantee data integrity.
     *
     * @throws Throwable
     */
    public function create(array $payload): Propuesta
    {
        return DB::transaction(function () use ($payload) {
            $proposal = $this->persist(new Propuesta(), $payload);

            Cola::query()->create([
                'entity' => 'propuestas',
                'entity_id' => $proposal->id,
                'codempresa' => $proposal->codempresa,
                'ptoventa' => self::PREFIX,
            ]);

            return $proposal;
        });
    }

    /**
     * Replace the content of an existing proposal with a new payload.
     * Existing lines are deleted and recreated, so the stored insured list
     * always matches the submitted rows. No Cola row is enqueued here:
     * the original proposal was already queued on creation.
     *
     * @throws Throwable
     */
    public function update(Propuesta $proposal, array $payload): Propuesta
    {
        return DB::transaction(function () use ($proposal, $payload) {
            return $this->persist($proposal, $payload);
        });
    }

    /**
     * UTC boundaries of a local calendar day, used to filter proposals
     * by their creation date.
     *
     * @return array{Carbon, Carbon}
     */
    public function localDayRange(string $date): array
    {
        $day = Carbon::parse($date, self::LOCAL_TIMEZONE);

        return [
            $day->copy()->startOfDay()->utc(),
            $day->copy()->endOfDay()->utc(),
        ];
    }

    /**
     * Whether the proposal was created within the given local calendar day.
     */
    public function isCreatedOnDay(Propuesta $proposal, string $date): bool
    {
        [$from, $to] = $this->localDayRange($date);

        return $proposal->created_at !== null && $proposal->created_at->between($from, $to);
    }

    /**
     * Mark a proposal as cancelled (codestado = 0) so it is visually
     * highlighted in the listing.
     */
    public function cancel(Propuesta $proposal): Propuesta
    {
        $proposal->codestado = 0;
        $proposal->save();

        return $proposal;
    }

    /**
     * Shared create/update routine: resolves every entity, computes the
     * derived fields and replaces the proposal lines.
     */
    private function persist(Propuesta $proposal, array $payload): Propuesta
    {
        $coverage = $this->resolveCoverage($payload['cobertura']);
        $policyholder = $this->clients->findOrUpsert($payload['tomador']);
        $insured = $this->resolveInsured($payload['asegurados']);
        $neighborhoods = $this->resolveNeighborhoods(
            $payload['barrios'] ?? [],
            $payload['grupos'] ?? [],
            $coverage->suma,
        );

        $months = (int) $payload['meses'];
        $dateFrom = $this->resolveStartDate($payload['fecha_desde'] ?? null);

        $attributes = [
            'codempresa' => auth()->user()->codempresa ?: 'default',
            'codestado' => 1,
            'documento' => $policyholder->id,
            'nombre' => $policyholder->full_name,
            'num_polizas' => count($insured),
            'meses' => $months,
            'id_cobertura' => $coverage->nombre,
            'id_barrio' => $neighborhoods->isNotEmpty() ? $neighborhoods->first()->id : null,
            'nueva_poliza' => 1,
            'premio' => $coverage->vrMensual,
            'premio_total' => $coverage->prizeForMonths($months) * count($insured),
            'clausula' => $neighborhoods->isNotEmpty() ? 1 : 0,
            'fechaDesde' => $dateFrom->format('Y-m-d 00:00:01'),
            'fechaHasta' => $dateFrom->modify("+{$months} months")->format('Y-m-d 23:59:59'),
            'ultmod' => now(),
            'useredit' => auth()->user()->name ?? 'online',
            'cobertura_suma' => $coverage->suma,
            'cobertura_deducible' => $coverage->deducible,
            'cobertura_gastos' => $coverage->gastos,
            'promocion' => $coverage->promotionLabel($months),
            'data_barrios' => $this->neighborhoodsPayload($neighborhoods),
            'fecha_nacimiento' => $policyholder->fecha_nacimiento,
        ];

        if (! $proposal->exists) {
            $number = $this->nextNumber();
            $proposal->prefijo = self::PREFIX;
            $proposal->idpropuesta = $number;
            $proposal->reg = $number;
            $proposal->version = 1;

            $formadepago = $payload['formadepago'] ?? 'CREDITO';
            $attributes['formadepago'] = $formadepago;

            if ($formadepago === 'CONTADO') {
                $attributes['paga'] = 1;
                $attributes['fecha_paga'] = now()->toDateTimeString();
                $attributes['usuariopaga'] = auth()->user()->name ?? 'online';
            } else {
                $attributes['paga'] = 0;
            }
        } else {
            $proposal->version = (int) $proposal->version + 1;
        }

        $proposal->fill($attributes);
        $proposal->save();

        LineasPropuesta::query()
            ->where('id_propuesta', $proposal->idpropuesta)
            ->where('prefijo', $proposal->prefijo)
            ->where('codempresa', $proposal->codempresa)
            ->delete();

        $this->createLines($proposal, $insured);

        return $proposal;
    }

    private function resolveCoverage(string $name): Cobertura
    {
        $coverage = Cobertura::query()->active()->where('nombre', $name)->first();

        if (! $coverage) {
            throw new \InvalidArgumentException('La cobertura seleccionada no existe.');
        }

        return $coverage;
    }

    /**
     * Upsert every linked client and return them in the original order,
     * including the activity/classification selected per row.
     *
     * @return Collection<int, object{client: cliente, activity: Actividade, classification: Clasificacione}>
     */
    private function resolveInsured(array $rows): Collection
    {
        return collect($rows)->map(function (array $row) {
            $activity = Actividade::query()->findOrFail($row['id_actividad']);
            $classification = Clasificacione::query()
                ->where('id', $row['id_clasificacion'])
                ->where('id_actividad', $activity->id)
                ->firstOrFail();

            return (object) [
                'client' => $this->clients->findOrUpsert($row),
                'activity' => $activity,
                'classification' => $classification,
            ];
        });
    }

    /**
     * Expand selected groups into their neighborhoods and merge them
     * with directly selected neighborhoods, deduplicated by id.
     * Zero neighborhoods is a valid scenario.
     */
    private function resolveNeighborhoods(array $barrioIds, array $groupId, float $coverageSuma): Collection
    {
        $ids = array_values(array_unique(array_filter($barrioIds)));

        if ($groupId !== []) {
            $idsFromGroups = gruposbarrio::query()
                ->active()
                ->whereIn('id', $groupId)
                ->whereNotNull('idbarrio')
                ->where('idbarrio', '!=', '')
                ->pluck('idbarrio')
                ->toArray();

            $ids = array_values(array_unique(array_merge($ids, $idsFromGroups)));
        }

        return barrio::query()
            ->active()
            ->withSumaMuerte()
            ->whereIn('id', $ids)
            ->where('suma_muerte', '<=', $coverageSuma)
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'email', 'suma_muerte', 'suma_gm', 'codestado']);
    }

    /**
     * Build the data_barrios payload, keeping the same shape consumed by
     * the PDF generation and neighborhood-aggregation features.
     */
    private function neighborhoodsPayload(Collection $neighborhoods): array
    {
        return [
            'barrios' => $neighborhoods->map(function (barrio $neighborhood) {
                return [
                    'id' => null,
                    'id_propuesta' => null,
                    'id_barrio' => $neighborhood->id,
                    'nombre' => $neighborhood->nombre,
                    'ultmod' => null,
                    'user_edit' => null,
                    'codestado' => null,
                    'prefijo' => null,
                    'idprefijo' => null,
                    'codempresa' => '',
                    'sumamuerte' => $neighborhood->suma_muerte,
                    'sumagm' => $neighborhood->suma_gm,
                    'email' => $neighborhood->email,
                ];
            })->values()->toArray(),
        ];
    }

    private function createLines(Propuesta $proposal, Collection $insured): void
    {
        foreach ($insured as $row) {
            /** @var cliente $client */
            $client = $row->client;
            /** @var Actividade $activity */
            $activity = $row->activity;
            /** @var Clasificacione $classification */
            $classification = $row->classification;

            LineasPropuesta::query()->create([
                'id_propuesta' => $proposal->idpropuesta,
                'prefijo' => $proposal->prefijo,
                'codempresa' => $proposal->codempresa,
                'documento' => $client->id,
                'tipo_documento' => $client->tipo_id,
                'apellidos' => $client->apellidos,
                'nombres' => $client->nombres,
                'fecha_nacimiento' => $client->fecha_nacimiento,
                'id_actividad' => $activity->reg,
                'id_clasificacion' => $classification->reg,
                'premio' => $proposal->premio,
                'ultmod' => $proposal->ultmod,
                'user_edit' => $proposal->useredit,
                'codestado' => 1,
                'fechaDesde' => $proposal->fechaDesde,
                'fechaHasta' => $proposal->fechaHasta,
                'actividad' => "{$activity->cod} - {$activity->nombre}",
                'clasificacion' => "{$classification->cod} - {$classification->nombre}",
            ]);
        }
    }

    private function nextNumber(): int
    {
        return Propuesta::query()->where('prefijo', self::PREFIX)->max('idpropuesta') + 1;
    }

    private function resolveStartDate(?string $date): \DateTimeImmutable
    {
        if ($date && $date !== '') {
            return new \DateTimeImmutable($date);
        }

        return new \DateTimeImmutable(now());
    }
}
