<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Requests\CoberturaFormRequest;
use App\Models\Cobertura;
use App\Services\CatalogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CoberturaController extends Controller
{
    public function __construct(
        private readonly CatalogService $catalog,
    ) {
    }

    public function index(): View
    {
        $items = $this->catalog->list(
            Cobertura::class,
            ['nombre'],
            request('q'),
        );

        return view('catalog.coberturas.index', [
            'items' => $items,
            'keyword' => request('q', ''),
        ]);
    }

    public function create(): View
    {
        return view('catalog.coberturas.form', [
            'item' => null,
        ]);
    }

    public function store(CoberturaFormRequest $request): RedirectResponse
    {
        $this->catalog->create(Cobertura::class, $request->validated());

        return redirect()
            ->route('coberturas.index')
            ->with('success', 'Cobertura creada correctamente');
    }

    public function edit(int $id): View
    {
        $item = $this->catalog->find(Cobertura::class, $id);

        return view('catalog.coberturas.form', [
            'item' => $item,
        ]);
    }

    public function update(int $id, CoberturaFormRequest $request): RedirectResponse
    {
        $item = $this->catalog->find(Cobertura::class, $id);
        $this->catalog->update($item, $request->validated());

        return redirect()
            ->route('coberturas.index')
            ->with('success', 'Cobertura actualizada correctamente');
    }

    public function deactivate(int $id): RedirectResponse
    {
        $item = $this->catalog->find(Cobertura::class, $id);
        $this->catalog->deactivate($item);

        return redirect()
            ->route('coberturas.index')
            ->with('success', 'Cobertura anulada correctamente');
    }
}
