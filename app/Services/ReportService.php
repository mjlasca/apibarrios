<?php

namespace App\Services;

use App\Models\barrio;
use App\Models\cliente;
use App\Models\LineasPropuesta;
use App\Models\Propuesta;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class ReportService
{
    public const LOCAL_TIMEZONE = 'America/Argentina/Buenos_Aires';

    public function __construct(
        private readonly ProposalService $proposalService,
    ) {
    }

    /**
     * Get proposals paid on the given date, grouped by type.
     *
     * @return array{fin_del_dia: Collection, envio_colectivo: Collection, envio_individual: Collection}
     */
    public function getReportsByDate(string $date): array
    {
        [$from, $to] = $this->proposalService->localDayRange($date);

        $proposals = Propuesta::query()
            ->where('paga', 1)
            ->where('codestado', '>', 0)
            ->where('fecha_paga', '>=', $from)
            ->where('fecha_paga', '<=', $to)
            ->orderBy('idpropuesta')
            ->get();

        $policyholderIds = $proposals->pluck('documento')->unique()->values()->toArray();
        $policyholders = cliente::query()->whereIn('id', $policyholderIds)->groupBy('id')->get()->keyBy('id');

        $proposalIds = $proposals->pluck('id')->toArray();
        $lines = LineasPropuesta::query()
            ->whereIn('id_propuesta', function ($query) use ($proposals) {
                $query->select('idpropuesta')->from('propuestas')
                    ->whereIn('id', $proposals->pluck('id')->toArray());
            })
            ->orderBy('id_propuesta')
            ->get();

        $insuredIds = $lines->pluck('documento')->unique()->values()->toArray();
        $insuredClients = cliente::query()->whereIn('id', $insuredIds)->groupBy('id')->get()->keyBy('id');

        $barriosByProposal = $this->resolveBarriosForProposals($proposals);

        $finDelDia = $this->buildFinDelDia($proposals, $lines, $policyholders, $insuredClients);
        $envioColectivo = $this->buildEnvio($proposals, $lines, $policyholders, $insuredClients, $barriosByProposal, true);
        $envioIndividual = $this->buildEnvio($proposals, $lines, $policyholders, $insuredClients, $barriosByProposal, false);

        return [
            'fin_del_dia' => $finDelDia,
            'envio_colectivo' => $envioColectivo,
            'envio_individual' => $envioIndividual,
        ];
    }

    private function buildFinDelDia(
        Collection $proposals,
        Collection $lines,
        Collection $policyholders,
        Collection $insuredClients,
    ): Collection {
        $grouped = $lines->groupBy(fn (LineasPropuesta $line) => "{$line->prefijo}-{$line->id_propuesta}");

        $rows = collect();

        foreach ($proposals as $proposal) {
            $key = "{$proposal->prefijo}-{$proposal->idpropuesta}";
            $proposalLines = $grouped->get($key, collect());
            $policyholder = $policyholders->get($proposal->documento);
            $certificate = 0;

            foreach ($proposalLines as $line) {
                $certificate++;
                $insured = $insuredClients->get($line->documento);

                $rows->push([
                    'nro_propuesta' => $proposal->prefijo . $proposal->idpropuesta,
                    'cert_propuesta' => $certificate,
                    'tipodoc' => $line->tipo_documento ?? '',
                    'documento' => $line->documento ?? '',
                    'cuir' => $insured->cuir ?? '',
                    'apellido' => trim(($line->apellidos ?? '') . ' ' . ($line->nombres ?? '')),
                    'iniciovigencia' => $this->formatDate($proposal->fechaDesde),
                    'finvigencia' => $this->formatDate($proposal->fechaHasta),
                    'meses' => $proposal->meses,
                    'costocobertura' => $line->premio ?? 0,
                    'costo_total' => ($line->premio ?? 0) * $proposal->meses,
                    'apellidotomador' => $proposal->nombre ?? '',
                    'tipodoctomador' => $policyholder->tipo_id ?? '',
                    'documentotomador' => $proposal->documento ?? '',
                    'direcciontomador' => $policyholder->direccion ?? '',
                    'cptomador' => $policyholder->codpostal ?? '',
                    'localidadtomador' => $policyholder->localidad ?? '',
                    'master' => $proposal->master ?? '',
                    'organizador' => $proposal->organizador ?? '',
                    'productor' => $proposal->productor ?? '',
                ]);
            }
        }

        return $rows;
    }

    private function buildEnvio(
        Collection $proposals,
        Collection $lines,
        Collection $policyholders,
        Collection $insuredClients,
        array $barriosByProposal,
        bool $collective,
    ): Collection {
        $filtered = $collective
            ? $proposals->where('num_polizas', '>', 1)
            : $proposals->where('num_polizas', '=', 1);

        $grouped = $lines->groupBy(fn (LineasPropuesta $line) => "{$line->prefijo}-{$line->id_propuesta}");

        $rows = collect();

        foreach ($filtered as $proposal) {
            $key = "{$proposal->prefijo}-{$proposal->idpropuesta}";
            $proposalLines = $grouped->get($key, collect());
            $policyholder = $policyholders->get($proposal->documento);
            $certificate = 0;

            $barrios = $barriosByProposal[$proposal->id] ?? collect();
            $barrioNombre = $barrios->pluck('nombre')->first() ?? '';
            $barrioCuit = $barrios->pluck('id_barrio')->first() ?? '';

            foreach ($proposalLines as $line) {
                $certificate++;
                $insured = $insuredClients->get($line->documento);

                $activityCode = $this->extractCodeFromField($line->actividad ?? '');
                $classificationCode = $this->extractCodeFromField($line->clasificacion ?? '');

                $age = $line->fecha_nacimiento
                    ? Carbon::parse($line->fecha_nacimiento)->age
                    : 0;

                $rows->push([
                    'certificado' => $certificate,
                    'tipodocumento' => $line->tipo_documento ?? '',
                    'documento' => $line->documento ?? '',
                    'cuir' => $insured->cuir ?? '',
                    'apellido' => trim(($line->apellidos ?? '') . ' ' . ($line->nombres ?? '')),
                    'sexo' => $insured->sexo ?? '',
                    'fechanacimiento' => $this->formatDate($line->fecha_nacimiento),
                    'capital' => $proposal->cobertura_suma ?? 0,
                    'amf' => $proposal->cobertura_gastos ?? 0,
                    'subsidio' => '',
                    'renta' => 0,
                    'fechavigencia' => $this->formatDate($proposal->fechaDesde),
                    'codactividad' => $activityCode,
                    'codclasifactividad' => $classificationCode,
                    'codtarea' => '',
                    'apellidobenef' => 'Herederos Legales',
                    'tipodocumentobenef' => '',
                    'documentobenef' => '',
                    'fechanacimientobenef' => '',
                    'direccionbenef' => '',
                    'cpbenef' => '',
                    'localidadbenef' => '',
                    'direccion' => '',
                    'localidad' => '',
                    'cp' => 0,
                    'matricula' => 0,
                    'nrocolegiado' => 0,
                    'codgrupo' => 0,
                    'fechainiciovigencia' => $this->formatDate($proposal->fechaDesde),
                    'fechafinvigencia' => $this->formatDate($proposal->fechaHasta),
                    'antiguedad' => 0,
                    'edad' => $age,
                    'clausula_norepeticion' => $proposal->clausula ? 'S' : 'N',
                    'barrio' => $barrioNombre,
                    'cuit_barrio' => $barrioCuit,
                    'barrio_beneficiario' => $proposal->barrio_beneficiario ? 'S' : 'N',
                    'costo' => $line->premio ?? 0,
                    'cobertura' => $proposal->id_cobertura ?? '',
                    'grupoestadistico' => $this->resolveStatGroup($barrios),
                    'apellidotomador' => $proposal->nombre ?? '',
                    'tipodocumentotomador' => $policyholder->tipo_id ?? '',
                    'documentotomador' => $proposal->documento ?? '',
                    'fechanacimientotomador' => $this->formatDate($policyholder->fecha_nacimiento ?? null),
                    'direcciontomador' => $policyholder->direccion ?? '',
                    'cptomador' => $policyholder->codpostal ?? '',
                    'localidadtomador' => $policyholder->localidad ?? '',
                    'master' => $proposal->master ?? '',
                    'organizador' => $proposal->organizador ?? '',
                    'productor' => $proposal->productor ?? '',
                ]);
            }
        }

        return $rows;
    }

    private function resolveBarriosForProposals(Collection $proposals): array
    {
        $barriosMap = [];

        foreach ($proposals as $proposal) {
            $barriosList = collect();

            if (! empty($proposal->data_barrios)) {
                $decoded = is_string($proposal->data_barrios)
                    ? json_decode($proposal->data_barrios, true)
                    : $proposal->data_barrios;

                if (is_array($decoded) && isset($decoded['barrios'])) {
                    foreach ($decoded['barrios'] as $b) {
                        $barriosList->push((object) [
                            'id_barrio' => $b['id_barrio'] ?? '',
                            'nombre' => $b['nombre'] ?? '',
                            'sumamuerte' => $b['sumamuerte'] ?? 0,
                        ]);
                    }
                }
            }

            $barriosMap[$proposal->id] = $barriosList;
        }

        return $barriosMap;
    }

    private function extractCodeFromField(string $field): string
    {
        $parts = explode(' - ', $field, 2);
        return trim($parts[0] ?? '');
    }

    private function resolveStatGroup(Collection $barrios): int
    {
        $first = $barrios->first();
        if ($first && isset($first->sumamuerte)) {
            return (int) ($first->sumamuerte / 1000000);
        }
        return 484;
    }

    private function formatDate(?string $date): string
    {
        if (empty($date)) {
            return '';
        }

        try {
            return Carbon::parse($date)->format('d/m/Y');
        } catch (\Throwable) {
            return '';
        }
    }
}
