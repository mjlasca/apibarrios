@extends('layouts.app')
@section('title', 'Grupos de Barrios')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <header class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Grupos de Barrios</h1>
        <a href="{{ route('grupos-barrios.create') }}"
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors">
            Nuevo Grupo
        </a>
    </header>

    <form method="GET" action="{{ route('grupos-barrios.index') }}" class="mb-6">
        <div class="flex items-end gap-3">
            <div class="flex-1 max-w-md">
                <label for="q" class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                <input type="text" id="q" name="q" value="{{ $keyword }}" placeholder="Nombre del grupo o barrio..."
                    autocomplete="off"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
            <button type="submit"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                Filtrar
            </button>
        </div>
    </form>

    @if (session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-md text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nombre</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Barrio</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($items as $item)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 text-sm font-semibold text-gray-900">{{ $item->nombre }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $item->nombrebarrio ?? '—' }}</td>
                        <td class="px-4 py-3 text-sm whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('grupos-barrios.edit', $item->reg) }}"
                                    class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-indigo-700 bg-indigo-100 hover:bg-indigo-200 transition-colors">
                                    Modificar
                                </a>
                                <form method="POST" action="{{ route('grupos-barrios.deactivate', $item->reg) }}" class="inline-block"
                                      onsubmit="return confirm('¿Anular el grupo {{ $item->nombre }}?');">
                                    @csrf
                                    <button type="submit"
                                        class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-red-700 bg-red-100 hover:bg-red-200 transition-colors">
                                        Anular
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-8 text-center text-sm text-gray-400">
                            Sin grupos de barrios para los filtros seleccionados
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4 flex items-center justify-between text-sm text-gray-500">
        <span>
            Mostrando {{ $items->firstItem() ?? 0 }}–{{ $items->lastItem() ?? 0 }} de {{ $items->total() }} grupos
        </span>
        <div>
            {{ $items->links('pagination::tailwind') }}
        </div>
    </div>
</div>
@endsection
