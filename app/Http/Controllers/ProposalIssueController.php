<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveClientRequest;
use App\Http\Requests\StoreProposalRequest;
use App\Http\Resources\ActividadResource;
use App\Http\Resources\BarrioResource;
use App\Http\Resources\ClasificacionResource;
use App\Http\Resources\ClienteResource;
use App\Http\Resources\CoberturaResource;
use App\Http\Resources\GrupoBarrioResource;
use App\Models\Actividade;
use App\Models\cliente;
use App\Services\ClientService;
use App\Services\ProposalCatalogService;
use App\Services\ProposalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProposalIssueController extends Controller
{
    public function __construct(
        private readonly ProposalCatalogService $catalog,
        private readonly ClientService $clients,
        private readonly ProposalService $proposals,
    ) {
    }

    public function create(): View
    {
        return view('propuesta.emision', [
            'actividades' => ActividadResource::collection($this->catalog->activities())->resolve(),
            'coberturas' => CoberturaResource::collection($this->catalog->coverages())->resolve(),
            'barrios' => BarrioResource::collection($this->catalog->neighborhoods())->resolve(),
            'grupos' => GrupoBarrioResource::collection($this->catalog->neighborhoodGroups())->resolve(),
        ]);
    }

    public function searchClients(Request $request): JsonResponse
    {
        $query = trim((string) $request->input('q', ''));

        if (mb_strlen($query) < 2) {
            return ClienteResource::collection([])->response();
        }

        return ClienteResource::collection($this->clients->search($query))->response();
    }

    public function resolveClient(string $document): JsonResponse
    {
        $client = $this->clients->findByDocument(trim($document));

        if (! $client) {
            return response()->json(['message' => 'Cliente no encontrado'], 404);
        }

        return (new ClienteResource($client))->response();
    }

    public function classificationsByActivity(Actividade $actividad): JsonResponse
    {
        return ClasificacionResource::collection(
            $this->catalog->classificationsForActivity($actividad->id)
        )->response();
    }

    public function saveClient(SaveClientRequest $request): JsonResponse
    {
        $client = $this->clients->findOrUpsert($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Cliente guardado correctamente',
            'data' => new ClienteResource($client),
        ]);
    }

    public function store(StoreProposalRequest $request): JsonResponse
    {
        try {
            $proposal = $this->proposals->create($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Propuesta guardada correctamente',
                'data' => [
                    'prefijo' => $proposal->prefijo,
                    'idpropuesta' => $proposal->idpropuesta,
                ],
            ], 201);
        } catch (\Throwable $exception) {
            report($exception);

            return response()->json([
                'success' => false,
                'message' => 'No se pudo guardar la propuesta. Intente nuevamente.',
            ], 500);
        }
    }
}
