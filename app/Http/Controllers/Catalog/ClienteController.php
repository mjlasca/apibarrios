<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClienteFormRequest;
use App\Models\cliente;
use App\Services\CatalogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ClienteController extends Controller
{
    public function __construct(
        private readonly CatalogService $catalog,
    ) {
    }

    public function index(): View
    {
        $items = $this->catalog->list(
            cliente::class,
            ['id', 'nombres', 'apellidos'],
            request('q'),
        );

        return view('catalog.clientes.index', [
            'items' => $items,
            'keyword' => request('q', ''),
        ]);
    }

    public function create(): View
    {
        return view('catalog.clientes.form', [
            'item' => null,
        ]);
    }

    public function store(ClienteFormRequest $request): RedirectResponse
    {
        $this->catalog->create(cliente::class, $request->validated());

        return redirect()
            ->route('clientes.index')
            ->with('success', 'Cliente creado correctamente');
    }

    public function edit(int $id): View
    {
        $item = $this->catalog->find(cliente::class, $id);

        return view('catalog.clientes.form', [
            'item' => $item,
        ]);
    }

    public function update(int $id, ClienteFormRequest $request): RedirectResponse
    {
        $item = $this->catalog->find(cliente::class, $id);
        $this->catalog->update($item, $request->validated());

        return redirect()
            ->route('clientes.index')
            ->with('success', 'Cliente actualizado correctamente');
    }

    public function deactivate(int $id): RedirectResponse
    {
        $item = $this->catalog->find(cliente::class, $id);
        $this->catalog->deactivate($item);

        return redirect()
            ->route('clientes.index')
            ->with('success', 'Cliente anulado correctamente');
    }
}
