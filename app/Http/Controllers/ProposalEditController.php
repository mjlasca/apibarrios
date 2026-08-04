<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProposalRequest;
use App\Http\Resources\ActividadResource;
use App\Http\Resources\BarrioResource;
use App\Http\Resources\ClasificacionResource;
use App\Http\Resources\CoberturaResource;
use App\Http\Resources\GrupoBarrioResource;
use App\Models\LineasPropuesta;
use App\Models\Propuesta;
use App\Services\ClientService;
use App\Services\ProposalCatalogService;
use App\Services\ProposalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProposalEditController extends Controller
{
    public function __construct(
        private readonly ProposalCatalogService $catalog,
        private readonly ClientService $clients,
        private readonly ProposalService $proposals,
    ) {
    }

    public function edit(Propuesta $propuesta): View
    {
        $this->authorizeCompany($propuesta);
        $this->authorizeEdit($propuesta);

        return view('propuesta.emision', [
            'actividades' => ActividadResource::collection($this->catalog->activities())->resolve(),
            'coberturas' => CoberturaResource::collection($this->catalog->coverages())->resolve(),
            'barrios' => BarrioResource::collection($this->catalog->neighborhoods())->resolve(),
            'grupos' => GrupoBarrioResource::collection($this->catalog->neighborhoodGroups())->resolve(),
            'proposal' => $this->buildProposalPayload($propuesta),
        ]);
    }

    public function update(Propuesta $propuesta, StoreProposalRequest $request): JsonResponse
    {
        $this->authorizeCompany($propuesta);
        $this->authorizeEdit($propuesta);

        try {
            $proposal = $this->proposals->update($propuesta, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Propuesta actualizada correctamente',
                'data' => [
                    'prefijo' => $proposal->prefijo,
                    'idpropuesta' => $proposal->idpropuesta,
                ],
            ]);
        } catch (\Throwable $exception) {
            report($exception);

            return response()->json([
                'success' => false,
                'message' => 'No se pudo actualizar la propuesta. Intente nuevamente.',
            ], 500);
        }
    }

    public function cancel(Propuesta $propuesta): RedirectResponse
    {
        $this->authorizeCompany($propuesta);

        $proposal = $this->proposals->cancel($propuesta);

        return redirect()
            ->route('propuesta.listar')
            ->with('success', 'Propuesta ' . $proposal->prefijo . '-' . $proposal->idpropuesta . ' anulada correctamente');
    }

    /**
     * Proposals may only be modified on the local day they were created.
     */
    private function authorizeEdit(Propuesta $propuesta): void
    {
        abort_unless(
            $this->proposals->isCreatedOnDay($propuesta, now(ProposalService::LOCAL_TIMEZONE)->toDateString()),
            403,
            'Solo se pueden modificar propuestas creadas el día de hoy.',
        );
    }

    /**
     * The prefijo/idpropuesta numbers are shared across companies, so every
     * operation must be scoped to the company of the authenticated user.
     */
    private function authorizeCompany(Propuesta $propuesta): void
    {
        abort_unless(
            $propuesta->codempresa === (auth()->user()->codempresa ?: 'default'),
            403,
            'La propuesta pertenece a otra empresa.',
        );
    }

    /**
     * Build the payload consumed by the emission view to prefill the form.
     */
    private function buildProposalPayload(Propuesta $propuesta): array
    {
        $client = $this->clients->findByDocument($propuesta->documento);

        return [
            'id' => $propuesta->id,
            'prefijo' => $propuesta->prefijo,
            'idpropuesta' => $propuesta->idpropuesta,
            'cobertura' => $propuesta->id_cobertura,
            'meses' => (int) $propuesta->meses,
            'fecha_desde' => $propuesta->fechaDesde !== null
                ? substr((string) $propuesta->fechaDesde, 0, 10)
                : null,
            'tomador' => [
                'tipo_id' => $client->tipo_id ?? 'DNI',
                'documento' => $propuesta->documento,
                'nombres' => $client->nombres ?? '',
                'apellidos' => $client->apellidos ?? '',
                'fecha_nacimiento' => $client?->fecha_nacimiento?->format('Y-m-d')
                    ?? $propuesta->fecha_nacimiento?->format('Y-m-d'),
                'telefono' => $client->telefono ?? '',
                'email' => $client->email ?? '',
            ],
            'asegurados' => $propuesta->lineas()
                ->where('prefijo', $propuesta->prefijo)
                ->where('codempresa', $propuesta->codempresa)
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
            })->values()->all(),
            'barrios' => array_values(array_map(
                fn (array $row) => (string) $row['id_barrio'],
                $propuesta->data_barrios['barrios'] ?? [],
            )),
        ];
    }
}
