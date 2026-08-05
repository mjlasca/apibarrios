<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClasificacionFormRequest;
use App\Models\Actividade;
use App\Models\Clasificacione;
use App\Services\CatalogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ClasificacionController extends Controller
{
    public function __construct(
        private readonly CatalogService $catalog,
    ) {
    }

    public function index(): View
    {
        $items = Clasificacione::query()
            ->where('codestado', '1')
            ->with('actividad:id,nombre')
            ->when(request('q'), function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('cod', 'like', "%{$keyword}%")
                        ->orWhere('nombre', 'like', "%{$keyword}%");
                });
            })
            ->orderByDesc('ultmod')
            ->paginate(30)
            ->withQueryString();

        return view('catalog.clasificaciones.index', [
            'items' => $items,
            'keyword' => request('q', ''),
        ]);
    }

    public function create(): View
    {
        return view('catalog.clasificaciones.form', [
            'item' => null,
            'actividades' => Actividade::query()->active()->orderBy('nombre')->get(['id', 'cod', 'nombre']),
        ]);
    }

    public function store(ClasificacionFormRequest $request): RedirectResponse
    {
        $this->catalog->create(Clasificacione::class, $request->validated());

        return redirect()
            ->route('clasificaciones.index')
            ->with('success', 'Clasificación creada correctamente');
    }

    public function edit(int $id): View
    {
        $item = $this->catalog->find(Clasificacione::class, $id);

        return view('catalog.clasificaciones.form', [
            'item' => $item,
            'actividades' => Actividade::query()->active()->orderBy('nombre')->get(['id', 'cod', 'nombre']),
        ]);
    }

    public function update(int $id, ClasificacionFormRequest $request): RedirectResponse
    {
        $item = $this->catalog->find(Clasificacione::class, $id);
        $this->catalog->update($item, $request->validated());

        return redirect()
            ->route('clasificaciones.index')
            ->with('success', 'Clasificación actualizada correctamente');
    }

    public function deactivate(int $id): RedirectResponse
    {
        $item = $this->catalog->find(Clasificacione::class, $id);
        $this->catalog->deactivate($item);

        return redirect()
            ->route('clasificaciones.index')
            ->with('success', 'Clasificación anulada correctamente');
    }
}
