@extends('layouts.app')
@section('title', 'Clasificaciones')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <header class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Clasificaciones</h1>
        <a href="{{ route('clasificaciones.create') }}"
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors">
            Nueva Clasificación
        </a>
    </header>

    <form method="GET" action="{{ route('clasificaciones.index') }}" class="mb-6">
        <div class="flex items-end gap-3">
            <div class="flex-1 max-w-md">
                <label for="q" class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                <input type="text" id="q" name="q" value="{{ $keyword }}" placeholder="Código o nombre..."
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
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Código</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nombre</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Actividad</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($items as $item)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 text-sm font-semibold text-gray-900 whitespace-nowrap">{{ $item->cod }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $item->nombre }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $item->actividad?->nombre ?? '—' }}</td>
                        <td class="px-4 py-3 text-sm whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('clasificaciones.edit', $item->id) }}"
                                    class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-indigo-700 bg-indigo-100 hover:bg-indigo-200 transition-colors">
                                    Modificar
                                </a>
                                <form method="POST" action="{{ route('clasificaciones.deactivate', $item->id) }}" class="inline-block"
                                      onsubmit="return confirm('¿Anular la clasificación {{ $item->nombre }}?');">
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
                        <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-400">
                            Sin clasificaciones para los filtros seleccionados
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4 flex items-center justify-between text-sm text-gray-500">
        <span>
            Mostrando {{ $items->firstItem() ?? 0 }}–{{ $items->lastItem() ?? 0 }} de {{ $items->total() }} clasificaciones
        </span>
        <div>
            {{ $items->links('pagination::tailwind') }}
        </div>
    </div>
</div>
@endsection
