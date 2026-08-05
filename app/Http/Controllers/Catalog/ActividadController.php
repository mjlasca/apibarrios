<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Requests\ActividadFormRequest;
use App\Models\Actividade;
use App\Services\CatalogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ActividadController extends Controller
{
    public function __construct(
        private readonly CatalogService $catalog,
    ) {
    }

    public function index(): View
    {
        $items = $this->catalog->list(
            Actividade::class,
            ['cod', 'nombre'],
            request('q'),
        );

        return view('catalog.actividades.index', [
            'items' => $items,
            'keyword' => request('q', ''),
        ]);
    }

    public function create(): View
    {
        return view('catalog.actividades.form', [
            'item' => null,
        ]);
    }

    public function store(ActividadFormRequest $request): RedirectResponse
    {
        $this->catalog->create(Actividade::class, $request->validated());

        return redirect()
            ->route('actividades.index')
            ->with('success', 'Actividad creada correctamente');
    }

    public function edit(int $id): View
    {
        $item = $this->catalog->find(Actividade::class, $id);

        return view('catalog.actividades.form', [
            'item' => $item,
        ]);
    }

    public function update(int $id, ActividadFormRequest $request): RedirectResponse
    {
        $item = $this->catalog->find(Actividade::class, $id);
        $this->catalog->update($item, $request->validated());

        return redirect()
            ->route('actividades.index')
            ->with('success', 'Actividad actualizada correctamente');
    }

    public function deactivate(int $id): RedirectResponse
    {
        $item = $this->catalog->find(Actividade::class, $id);
        $this->catalog->deactivate($item);

        return redirect()
            ->route('actividades.index')
            ->with('success', 'Actividad anulada correctamente');
    }
}
