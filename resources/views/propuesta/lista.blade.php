@extends('layouts.app')
@section('title', 'Lista de Propuestas')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <header class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Propuestas</h1>
    </header>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <form method="GET" action="{{ url('/propuesta/listar') }}" class="flex flex-wrap items-end gap-3">
            <div>
                <label for="desde" class="block text-sm font-medium text-gray-700 mb-1">Desde</label>
                <input type="date" id="desde" name="desde" value="{{ $from }}" max="{{ $today }}"
                    class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
            <div>
                <label for="hasta" class="block text-sm font-medium text-gray-700 mb-1">Hasta</label>
                <input type="date" id="hasta" name="hasta" value="{{ $to }}" max="{{ $today }}"
                    class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
            <div>
                <label for="q" class="block text-sm font-medium text-gray-700 mb-1">Tomador o documento</label>
                <input type="text" id="q" name="q" value="{{ $keyword }}" placeholder="Buscar por nombre o documento..." autocomplete="off"
                    class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
            <button type="submit"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                Filtrar
            </button>
        </form>
        <a href="{{ route('propuesta.emision') }}"
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors">
            Nueva Propuesta
        </a>
    </div>

    @if (session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-md text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Referencia</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tomador</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Cobertura</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Premio</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Vigencia</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Creada</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Fecha Paga</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($proposals as $proposal)
                    <tr class="{{ (int) $proposal->codestado === 0 ? 'bg-red-50 opacity-60' : 'hover:bg-gray-50' }} transition-colors">
                        <td class="px-4 py-3 text-sm font-semibold text-gray-900 whitespace-nowrap">
                            {{ $proposal->prefijo }}-{{ $proposal->idpropuesta }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700">
                            {{ $proposal->nombre }}
                            <br><span class="text-xs text-gray-400">Doc: {{ $proposal->documento }}</span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700 whitespace-normal">{{ $proposal->id_cobertura }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700 whitespace-nowrap">${{ number_format((float) $proposal->premio_total, 2, ',', '.') }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500 whitespace-nowrap">
                            {{ \Illuminate\Support\Carbon::parse($proposal->fechaDesde)->format('d/m/Y') }}
                            al
                            {{ \Illuminate\Support\Carbon::parse($proposal->fechaHasta)->format('d/m/Y') }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500 whitespace-nowrap">
                            {{ \Illuminate\Support\Carbon::parse($proposal->created_at)->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500 whitespace-nowrap">
                            @if ($proposal->fecha_paga)
                                {{ \Illuminate\Support\Carbon::parse($proposal->fecha_paga)->format('d/m/Y H:i') }}
                            @else
                                <span class="text-gray-300">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                @if ($proposal->created_at !== null && $proposal->created_at->between($todayRange[0], $todayRange[1]) && (int) $proposal->codestado !== 0)
                                    <a href="{{ url('/propuesta/' . $proposal->id . '/editar') }}"
                                        class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-indigo-700 bg-indigo-100 hover:bg-indigo-200 transition-colors">
                                        Modificar
                                    </a>
                                @endif
                                @if ((int) $proposal->codestado !== 0)
                                    <form method="POST" action="{{ url('/propuesta/' . $proposal->id . '/anular') }}" class="inline-block"
                                          onsubmit="return confirm('¿Anular la propuesta {{ $proposal->prefijo }}-{{ $proposal->idpropuesta }}?');">
                                        @csrf
                                        <button type="submit"
                                            class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-red-700 bg-red-100 hover:bg-red-200 transition-colors">
                                            Anular
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-sm text-gray-400">
                            Sin propuestas para los filtros seleccionados
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4 flex items-center justify-between text-sm text-gray-500">
        <span>
            Mostrando {{ $proposals->firstItem() ?? 0 }}–{{ $proposals->lastItem() ?? 0 }} de {{ $proposals->total() }} propuestas
        </span>
        <div>
            {{ $proposals->links('pagination::tailwind') }}
        </div>
    </div>
</div>
@endsection
