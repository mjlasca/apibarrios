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
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

class ProposalService
{
    public const PREFIX = 'O';

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
            $coverage = $this->resolveCoverage($payload['cobertura']);
            $policyholder = $this->clients->findOrUpsert($payload['tomador']);
            $insured = $this->resolveInsured($payload['asegurados']);
            $neighborhoods = $this->resolveNeighborhoods(
                $payload['barrios'] ?? [],
                $payload['grupos'] ?? [],
                $coverage->suma,
            );

            $number = $this->nextNumber();
            $months = (int) $payload['meses'];
            $dateFrom = $this->resolveStartDate($payload['fecha_desde'] ?? null);

            $proposal = Propuesta::query()->create([
                'codempresa' => auth()->user()->codempresa ?: 'default',
                'prefijo' => self::PREFIX,
                'idpropuesta' => $number,
                'reg' => $number,
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
                'paga' => 0,
                'data_barrios' => $this->neighborhoodsPayload($neighborhoods),
                'version' => 1,
                'fecha_nacimiento' => $policyholder->fecha_nacimiento,
                'formadepago' => 'CREDITO',
            ]);

            $this->createLines($proposal, $insured);

            Cola::query()->create([
                'entity' => 'propuestas',
                'entity_id' => $proposal->id,
                'codempresa' => $proposal->codempresa,
                'ptoventa' => self::PREFIX,
            ]);

            return $proposal;
        });
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
