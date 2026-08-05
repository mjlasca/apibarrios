<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Requests\BarrioFormRequest;
use App\Models\barrio;
use App\Services\CatalogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BarrioController extends Controller
{
    public function __construct(
        private readonly CatalogService $catalog,
    ) {
    }

    public function index(): View
    {
        $items = $this->catalog->list(
            barrio::class,
            ['id', 'nombre'],
            request('q'),
        );

        return view('catalog.barrios.index', [
            'items' => $items,
            'keyword' => request('q', ''),
        ]);
    }

    public function create(): View
    {
        return view('catalog.barrios.form', [
            'item' => null,
        ]);
    }

    public function store(BarrioFormRequest $request): RedirectResponse
    {
        $this->catalog->create(barrio::class, $request->validated());

        return redirect()
            ->route('barrios.index')
            ->with('success', 'Barrio creado correctamente');
    }

    public function edit(int $id): View
    {
        $item = $this->catalog->find(barrio::class, $id);

        return view('catalog.barrios.form', [
            'item' => $item,
        ]);
    }

    public function update(int $id, BarrioFormRequest $request): RedirectResponse
    {
        $item = $this->catalog->find(barrio::class, $id);
        $this->catalog->update($item, $request->validated());

        return redirect()
            ->route('barrios.index')
            ->with('success', 'Barrio actualizado correctamente');
    }

    public function deactivate(int $id): RedirectResponse
    {
        $item = $this->catalog->find(barrio::class, $id);
        $this->catalog->deactivate($item);

        return redirect()
            ->route('barrios.index')
            ->with('success', 'Barrio anulado correctamente');
    }
}
