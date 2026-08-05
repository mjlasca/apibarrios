<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Requests\GrupoBarrioFormRequest;
use App\Models\barrio;
use App\Models\gruposbarrio;
use App\Services\CatalogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class GrupoBarrioController extends Controller
{
    public function __construct(
        private readonly CatalogService $catalog,
    ) {
    }

    public function index(): View
    {
        $items = gruposbarrio::query()
            ->where('codestado', '1')
            ->when(request('q'), function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('nombre', 'like', "%{$keyword}%")
                        ->orWhere('nombrebarrio', 'like', "%{$keyword}%");
                });
            })
            ->orderByDesc('ultmod')
            ->paginate(30)
            ->withQueryString();

        return view('catalog.grupos-barrios.index', [
            'items' => $items,
            'keyword' => request('q', ''),
        ]);
    }

    public function create(): View
    {
        return view('catalog.grupos-barrios.form', [
            'item' => null,
            'barrios' => barrio::query()->active()->orderBy('nombre')->get(['id', 'nombre']),
        ]);
    }

    public function store(GrupoBarrioFormRequest $request): RedirectResponse
    {
        $barrio = barrio::query()->where('id', $request->input('idbarrio'))->first();
        $data = $request->validated();
        $data['nombrebarrio'] = $barrio?->nombre ?? '';

        $this->catalog->create(gruposbarrio::class, $data);

        return redirect()
            ->route('grupos-barrios.index')
            ->with('success', 'Grupo de barrio creado correctamente');
    }

    public function edit(int $id): View
    {
        $item = $this->catalog->find(gruposbarrio::class, $id);

        return view('catalog.grupos-barrios.form', [
            'item' => $item,
            'barrios' => barrio::query()->active()->orderBy('nombre')->get(['id', 'nombre']),
        ]);
    }

    public function update(int $id, GrupoBarrioFormRequest $request): RedirectResponse
    {
        $item = $this->catalog->find(gruposbarrio::class, $id);

        $barrio = barrio::query()->where('id', $request->input('idbarrio'))->first();
        $data = $request->validated();
        $data['nombrebarrio'] = $barrio?->nombre ?? '';

        $this->catalog->update($item, $data);

        return redirect()
            ->route('grupos-barrios.index')
            ->with('success', 'Grupo de barrio actualizado correctamente');
    }

    public function deactivate(int $id): RedirectResponse
    {
        $item = $this->catalog->find(gruposbarrio::class, $id);
        $this->catalog->deactivate($item);

        return redirect()
            ->route('grupos-barrios.index')
            ->with('success', 'Grupo de barrio anulado correctamente');
    }
}
