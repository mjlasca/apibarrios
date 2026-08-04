@extends('layouts.app')
@section('title', 'Emisión de Propuesta')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-32">
    <header class="mb-6">
        <div class="flex items-center gap-3">
            <h1 class="text-2xl font-bold text-gray-900">Emisión de Propuesta</h1>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                Prefijo: <span class="ml-1 font-bold">O</span>
            </span>
            @if (isset($proposal) && $proposal)
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                    Editando: {{ $proposal['prefijo'] }}-{{ $proposal['idpropuesta'] }}
                </span>
            @endif
        </div>
    </header>

    <form id="proposal-form" aria-label="Formulario de nueva propuesta">
        @csrf

        {{-- SECCIÓN TOMADOR --}}
        <fieldset class="border border-gray-200 rounded-lg p-5 mb-6 bg-white shadow-sm">
            <legend class="text-base font-semibold text-gray-900 px-2">Datos del Tomador</legend>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label for="t-tipo" class="block text-sm font-medium text-gray-700 mb-1">Tipo Doc.</label>
                    <select id="t-tipo" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option>DNI</option>
                        <option>CUIT</option>
                    </select>
                </div>
                <div class="relative">
                    <label for="t-doc" class="block text-sm font-medium text-gray-700 mb-1">Nº Documento</label>
                    <input type="text" id="t-doc" placeholder="Buscar o ingresar..." autocomplete="off"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    <div class="save-client-indicator mt-1 hidden items-center gap-1.5 text-xs text-amber-600" id="save-indicator">
                        <span class="h-2 w-2 rounded-full bg-amber-400 inline-block"></span>
                        <span>Cliente nuevo — se guardará al emitir la propuesta</span>
                    </div>
                    <button type="button" class="mt-2 inline-flex items-center px-3 py-1 text-xs font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 transition-colors" id="btn-add-tomador" style="display:none">
                        Agregar a la póliza
                    </button>
                </div>
                <div>
                    <label for="t-nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre(s)</label>
                    <input type="text" id="t-nombre" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                </div>
                <div>
                    <label for="t-apellido" class="block text-sm font-medium text-gray-700 mb-1">Apellido(s)</label>
                    <input type="text" id="t-apellido" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                </div>
                <div>
                    <label for="t-fecha-nacimiento" class="block text-sm font-medium text-gray-700 mb-1">Fecha de Nacimiento</label>
                    <input type="date" id="t-fecha-nacimiento" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                </div>
                <div>
                    <label for="t-tel" class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                    <input type="text" id="t-tel" placeholder="11XXXXXXXX" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                </div>
                <div>
                    <label for="t-mail" class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
                    <input type="email" id="t-mail" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                </div>
            </div>
        </fieldset>

        {{-- SECCIÓN PRODUCTO --}}
        <fieldset class="border border-gray-200 rounded-lg p-5 mb-6 bg-white shadow-sm">
            <legend class="text-base font-semibold text-gray-900 px-2">Cobertura y Vigencia</legend>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label for="c-cobertura" class="block text-sm font-medium text-gray-700 mb-1">Cobertura</label>
                    <select id="c-cobertura" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">Seleccione cobertura...</option>
                        @foreach ($coberturas as $cobertura)
                            <option value="{{ $cobertura['nombre'] }}">
                                {{ $cobertura['nombre'] }}
                                — Suma: ${{ number_format($cobertura['suma'], 0, ',', '.') }}
                                — Mensual: ${{ number_format($cobertura['vrMensual'], 2, ',', '.') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="c-meses" class="block text-sm font-medium text-gray-700 mb-1">Meses</label>
                    <select id="c-meses" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="1">1 mes</option>
                        <option value="2">2 meses</option>
                        <option value="3">3 meses</option>
                        <option value="6">6 meses</option>
                    </select>
                </div>
                <div>
                    <label for="v-desde" class="block text-sm font-medium text-gray-700 mb-1">Vigencia Desde</label>
                    <input type="date" id="v-desde" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                </div>
            </div>
        </fieldset>

        {{-- SECCIÓN CLIENTES VINCULADOS --}}
        <section class="border border-gray-200 rounded-lg p-5 mb-6 bg-white shadow-sm" aria-label="Grilla de Clientes Asegurados">
            <h2 class="text-base font-semibold text-gray-900 mb-4">Clientes Vinculados a la Póliza</h2>

            <div class="flex flex-wrap items-end gap-3 mb-4 p-3 bg-gray-50 rounded-lg">
                <div class="relative">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nº Documento</label>
                    <input type="text" id="q-doc" placeholder="Buscar/Crear..." autocomplete="off"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    <div class="save-client-indicator mt-1 hidden items-center gap-1.5 text-xs text-amber-600" id="q-save-indicator">
                        <span class="h-2 w-2 rounded-full bg-amber-400 inline-block"></span>
                        <span>Cliente nuevo — se guardará al emitir</span>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                    <select id="q-tipo" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option>DNI</option>
                        <option>CUIT</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                    <input type="text" id="q-nombre" placeholder="Autocompleta o escribe"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Apellido</label>
                    <input type="text" id="q-apellido" placeholder="Autocompleta o escribe"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Nacimiento</label>
                    <input type="date" id="q-fecha-nacimiento" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Actividad</label>
                    <select id="q-actividad" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">Seleccione actividad...</option>
                        @foreach ($actividades as $actividad)
                            <option value="{{ $actividad['id'] }}">{{ $actividad['cod'] }} - {{ $actividad['nombre'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Clasificación</label>
                    <select id="q-clasificacion" disabled class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm disabled:bg-gray-100 disabled:cursor-not-allowed">
                        <option value="">Primero elija actividad...</option>
                    </select>
                </div>
                <button type="button" id="btn-add-line" title="Agregar cliente a la grilla"
                    class="inline-flex items-center justify-center h-9 w-9 rounded-md border border-transparent text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    +
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Documento</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tipo</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Cliente</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Actividad</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Clasificación</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider" style="width: 80px;">Acción</th>
                        </tr>
                    </thead>
                    <tbody id="lines-table-body" class="bg-white divide-y divide-gray-200">
                        <tr id="lines-empty-row">
                            <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-400">
                                Sin clientes vinculados aún
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        {{-- SECCIÓN BARRIOS --}}
        <fieldset class="border border-gray-200 rounded-lg p-5 mb-6 bg-white shadow-sm">
            <legend class="text-base font-semibold text-gray-900 px-2">Barrios / Grupos de Barrios</legend>
            <div class="flex items-center gap-3 mb-3">
                <button type="button" id="btn-open-modal"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    Seleccionar barrios / grupos
                </button>
                <span class="text-sm text-gray-400">0 o varios. Cada grupo aporta todos sus barrios.</span>
            </div>
            <div class="flex flex-wrap gap-2" id="chips-container">
                <span class="text-sm text-gray-400 italic" id="chips-empty">Ningún barrio seleccionado</span>
            </div>
        </fieldset>
    </form>
</div>

{{-- PIE DE ACCIONES FIJAS --}}
<footer class="fixed bottom-0 inset-x-0 bg-white border-t border-gray-200 shadow-lg z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-6">
            <div>
                <span class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Premio Unitario</span>
                <div class="flex items-center gap-2">
                    <span id="unit-premio" class="text-lg font-bold text-gray-900">$0,00</span>
                    <span id="promo-badge" class="hidden inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">2x1</span>
                </div>
            </div>
            <div>
                <span class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Premio Total Estimado</span>
                <span id="total-premio" class="text-lg font-bold text-gray-900">$0,00</span>
            </div>
        </div>
        <button type="button" id="btn-save-proposal"
            class="inline-flex items-center px-6 py-2.5 border border-transparent text-sm font-medium rounded-md text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors">
            Guardar Propuesta
        </button>
    </div>
</footer>

{{-- MODAL BARRIOS / GRUPOS --}}
<div class="fixed inset-0 z-50 overflow-y-auto hidden" id="modal-overlay" aria-hidden="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" id="modal-backdrop"></div>

        <div class="relative bg-white rounded-lg shadow-xl w-full max-w-2xl mx-auto overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h2 id="modal-title" class="text-lg font-semibold text-gray-900">Seleccionar Barrios y Grupos</h2>
                <button type="button" id="btn-close-modal" aria-label="Cerrar"
                    class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>

            <div class="px-6 py-4 max-h-96 overflow-y-auto">
                <h3 class="text-sm font-semibold text-gray-900 mb-3">Grupos de barrios</h3>
                <div class="space-y-2 mb-6" id="modal-grupos-list">
                    @forelse ($grupos as $grupo)
                        <label class="flex items-center gap-3 p-2 rounded-md hover:bg-gray-50 cursor-pointer transition-colors">
                            <input type="checkbox" value="{{ $grupo['id'] }}" data-kind="grupo" data-name="{{ $grupo['nombre'] }}"
                                class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                            <span class="text-sm text-gray-700">{{ $grupo['nombre'] }}</span>
                        </label>
                    @empty
                        <p class="text-sm text-gray-400 text-center py-4">Sin grupos disponibles</p>
                    @endforelse
                </div>

                <h3 class="text-sm font-semibold text-gray-900 mb-3">Barrios</h3>
                <div class="mb-3">
                    <input type="text" id="modal-barrios-search" placeholder="Buscar barrio por nombre o CUIT..."
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                </div>
                <div class="space-y-2" id="modal-barrios-list">
                    <p class="text-sm text-gray-400 text-center py-4">Cargando...</p>
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                <button type="button" id="btn-confirm-modal"
                    class="inline-flex items-center px-5 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    Aplicar selección
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    window.EMISSION_DATA = {
        activities: @json($actividades),
        coverages: @json($coberturas),
        neighborhoods: @json($barrios),
        groups: @json($grupos),
        proposal: @json($proposal ?? null)
    };
</script>
<script src="{{ asset('js/proposal-emision.js') }}"></script>
@endsection
