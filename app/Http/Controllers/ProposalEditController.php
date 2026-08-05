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
use Illuminate\Http\Request;
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

    public function pay(Request $request): RedirectResponse
    {
        $isEfectivo = $request->input('tipopago') === 'EFECTIVO';

        $rules = [
            'id' => ['required', 'integer', 'exists:propuestas,id'],
            'tipopago' => ['required', 'string', 'max:20'],
            'cuit_pagador' => ['required', 'string', 'max:20'],
        ];

        if (! $isEfectivo) {
            $rules['compformadepago'] = ['required', 'string', 'max:50'];
            $rules['valor_pagado'] = ['required', 'numeric', 'min:0'];
            $rules['fecha_comprobante'] = ['required', 'date', 'before_or_equal:today'];
        }

        $request->validate($rules);

        $proposal = Propuesta::findOrFail($request->input('id'));

        $this->edit->assertCompanyMatches($proposal);

        $data = [
            'paga' => 1,
            'fecha_paga' => now()->toDateTimeString(),
            'usuariopaga' => auth()->user()->name ?? 'online',
            'tipopago' => $request->input('tipopago'),
            'formadepago' => 'CREDITO',
            'cuit_pagador' => $request->input('cuit_pagador'),
        ];

        if (! $isEfectivo) {
            $data['compformadepago'] = $request->input('compformadepago');
            $data['valor_pagado'] = $request->input('valor_pagado');
            $data['fecha_comprobante'] = $request->input('fecha_comprobante');
        }

        $proposal->update($data);

        return redirect()
            ->route('propuesta.listar')
            ->with('success', 'Propuesta ' . $proposal->prefijo . '-' . $proposal->idpropuesta . ' pagada correctamente');
    }
}
