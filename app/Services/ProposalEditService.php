<?php

namespace App\Services;

use App\Models\LineasPropuesta;
use App\Models\Propuesta;

class ProposalEditService
{
    public function __construct(
        private readonly ClientService $clients,
        private readonly ProposalService $proposals,
    ) {
    }

    /**
     * The prefijo/idpropuesta numbers are shared across companies, so every
     * operation must be scoped to the company of the authenticated user.
     */
    public function assertCompanyMatches(Propuesta $proposal): void
    {
        abort_unless(
            $proposal->codempresa === (auth()->user()->codempresa ?: 'default'),
            403,
            'La propuesta pertenece a otra empresa.',
        );
    }

    /**
     * Proposals may only be modified on the local day they were created.
     */
    public function assertEditableToday(Propuesta $proposal): void
    {
        abort_unless(
            $this->proposals->isCreatedOnDay($proposal, now(ProposalService::LOCAL_TIMEZONE)->toDateString()),
            403,
            'Solo se pueden modificar propuestas creadas el día de hoy.',
        );
    }

    /**
     * Build the payload consumed by the emission view to prefill the form.
     */
    public function payload(Propuesta $proposal): array
    {
        $client = $this->clients->findByDocument($proposal->documento);

        return [
            'id' => $proposal->id,
            'prefijo' => $proposal->prefijo,
            'idpropuesta' => $proposal->idpropuesta,
            'cobertura' => $proposal->id_cobertura,
            'meses' => (int) $proposal->meses,
            'fecha_desde' => $proposal->fechaDesde !== null
                ? substr((string) $proposal->fechaDesde, 0, 10)
                : null,
            'tomador' => [
                'tipo_id' => $client->tipo_id ?? 'DNI',
                'documento' => $proposal->documento,
                'nombres' => $client->nombres ?? '',
                'apellidos' => $client->apellidos ?? '',
                'fecha_nacimiento' => $client?->fecha_nacimiento?->format('Y-m-d')
                    ?? $proposal->fecha_nacimiento?->format('Y-m-d'),
                'telefono' => $client->telefono ?? '',
                'email' => $client->email ?? '',
            ],
            'asegurados' => $this->insuredRows($proposal),
            'formadepago' => $proposal->formadepago ?? 'CREDITO',
            'barrios' => array_values(array_map(
                fn (array $row) => (string) $row['id_barrio'],
                $proposal->data_barrios['barrios'] ?? [],
            )),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function insuredRows(Propuesta $proposal): array
    {
        return $proposal->lineas()
            ->where('prefijo', $proposal->prefijo)
            ->where('codempresa', $proposal->codempresa)
            ->get()
            ->map(function (LineasPropuesta $line) {
                return [
                    'tipo_id' => $line->tipo_documento,
                    'documento' => $line->documento,
                    'nombres' => $line->nombres,
                    'apellidos' => $line->apellidos,
                    'fecha_nacimiento' => $line->fecha_nacimiento?->format('Y-m-d'),
                    'id_actividad' => (int) $line->id_actividad,
                    'id_clasificacion' => (int) $line->id_clasificacion,
                    'actividad_nombre' => $line->actividad,
                    'clasificacion_nombre' => $line->clasificacion,
                ];
            })
            ->values()
            ->all();
    }
}
