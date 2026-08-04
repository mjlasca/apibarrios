<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProposalRequest;
use App\Http\Resources\ActividadResource;
use App\Http\Resources\BarrioResource;
use App\Http\Resources\ClasificacionResource;
use App\Http\Resources\CoberturaResource;
use App\Http\Resources\GrupoBarrioResource;
use App\Models\Propuesta;
use App\Services\ProposalCatalogService;
use App\Services\ProposalEditService;
use App\Services\ProposalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProposalEditController extends Controller
{
    public function __construct(
        private readonly ProposalCatalogService $catalog,
        private readonly ProposalEditService $edit,
        private readonly ProposalService $proposals,
    ) {
    }

    public function edit(Propuesta $propuesta): View
    {
        $this->edit->assertCompanyMatches($propuesta);
        $this->edit->assertEditableToday($propuesta);

        return view('propuesta.emision', [
            'actividades' => ActividadResource::collection($this->catalog->activities())->resolve(),
            'coberturas' => CoberturaResource::collection($this->catalog->coverages())->resolve(),
            'barrios' => BarrioResource::collection($this->catalog->neighborhoods())->resolve(),
            'grupos' => GrupoBarrioResource::collection($this->catalog->neighborhoodGroups())->resolve(),
            'proposal' => $this->edit->payload($propuesta),
        ]);
    }

    public function update(Propuesta $propuesta, StoreProposalRequest $request): JsonResponse
    {
        $this->edit->assertCompanyMatches($propuesta);
        $this->edit->assertEditableToday($propuesta);

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
        $this->edit->assertCompanyMatches($propuesta);

        $proposal = $this->proposals->cancel($propuesta);

        return redirect()
            ->route('propuesta.listar')
            ->with('success', 'Propuesta ' . $proposal->prefijo . '-' . $proposal->idpropuesta . ' anulada correctamente');
    }
}
