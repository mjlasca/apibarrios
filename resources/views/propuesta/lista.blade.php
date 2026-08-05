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
                                @if ((int) $proposal->paga === 0 && (int) $proposal->codestado !== 0)
                                    <button type="button"
                                        class="btn-pay-proposal inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-emerald-700 bg-emerald-100 hover:bg-emerald-200 transition-colors"
                                        data-id="{{ $proposal->id }}"
                                        data-ref="{{ $proposal->prefijo }}-{{ $proposal->idpropuesta }}"
                                        data-amount="${{ number_format((float) $proposal->premio_total, 2, ',', '.') }}"
                                        data-raw="{{ (float) $proposal->premio_total }}">
                                        Pagar
                                    </button>
                                @endif
                                <a href="{{ url('/descargaseguro/' . $proposal->idpropuesta . '/' . $proposal->prefijo) }}"
                                    target="_blank"
                                    class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-sky-700 bg-sky-100 hover:bg-sky-200 transition-colors">
                                    Descargar
                                </a>
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

{{-- MODAL PAGAR PROPUESTA --}}
<div class="fixed inset-0 z-50 hidden" id="pay-modal-overlay" aria-hidden="true">
    <div class="flex items-center justify-center min-h-screen px-4 py-20">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" id="pay-modal-backdrop"></div>

        <div class="relative bg-white rounded-lg shadow-xl w-full md:max-w-[50%] mx-auto overflow-hidden">
            <div class="flex items-center justify-between px-5 py-3 border-b border-gray-200">
                <h2 class="text-base font-semibold text-gray-900">Forma de Pago</h2>
                <button type="button" id="btn-close-pay-modal" aria-label="Cerrar"
                    class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>

            <form method="POST" action="{{ url('/propuesta/pagar') }}" id="pay-form">
                @csrf
                <input type="hidden" name="id" id="pay-id" value="">
                <div class="px-5 py-4 space-y-3">
                    <p class="text-sm text-gray-600">
                        <span id="pay-ref" class="font-semibold text-gray-900"></span>
                        &mdash; <span id="pay-amount" class="font-semibold text-gray-900"></span>
                    </p>

                    <div>
                        <label for="pay-tipopago" class="block text-sm font-medium text-gray-700 mb-1">Forma de pago</label>
                        <select name="tipopago" id="pay-tipopago" required
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="EFECTIVO" selected>Efectivo</option>
                            <option value="CBU">CBU</option>
                            <option value="TRANSFERENCIA">Transferencia</option>
                            <option value="MEDIO DE PAGO">Medio de Pago</option>
                            <option value="OTRO">Otro</option>
                        </select>
                    </div>

                    <div id="pay-fields-extra" class="hidden space-y-3">
                        <div>
                            <label for="pay-compformadepago" class="block text-sm font-medium text-gray-700 mb-1">No. Comprobante</label>
                            <input type="text" name="compformadepago" id="pay-compformadepago" autocomplete="off"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label for="pay-valor_pagado" class="block text-sm font-medium text-gray-700 mb-1">Valor pagado</label>
                                <input type="number" name="valor_pagado" id="pay-valor_pagado" step="0.01" min="0"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            </div>
                            <div>
                                <label for="pay-fecha_comprobante" class="block text-sm font-medium text-gray-700 mb-1">Fecha comprobante</label>
                                <input type="date" name="fecha_comprobante" id="pay-fecha_comprobante"
                                    max="{{ $today }}"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="pay-cuit_pagador" class="block text-sm font-medium text-gray-700 mb-1">CUIT pagador</label>
                        <input type="text" name="cuit_pagador" id="pay-cuit_pagador" autocomplete="off" required
                            placeholder="Ej: 20-12345678-9"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    </div>
                </div>
                <div class="px-5 py-3 border-t border-gray-200 bg-gray-50 flex justify-end gap-3">
                    <button type="button" id="btn-cancel-pay"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors">
                        Aceptar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function () {
    var overlay = document.getElementById('pay-modal-overlay');
    var payId = document.getElementById('pay-id');
    var payRef = document.getElementById('pay-ref');
    var payAmount = document.getElementById('pay-amount');
    var payValorPagado = document.getElementById('pay-valor_pagado');
    var payTipopago = document.getElementById('pay-tipopago');
    var payFieldsExtra = document.getElementById('pay-fields-extra');
    var payCompformadepago = document.getElementById('pay-compformadepago');
    var payFechaComprobante = document.getElementById('pay-fecha_comprobante');
    var today = '{{ $today }}';

    function toggleExtraFields() {
        var isEfectivo = payTipopago.value === 'EFECTIVO';
        payFieldsExtra.classList.toggle('hidden', isEfectivo);
        payCompformadepago.required = !isEfectivo;
        payValorPagado.required = !isEfectivo;
        payFechaComprobante.required = !isEfectivo;
    }

    payTipopago.addEventListener('change', toggleExtraFields);

    function openPayModal(id, ref, amount, rawAmount) {
        payId.value = id;
        payRef.textContent = ref;
        payAmount.textContent = amount;
        payValorPagado.value = rawAmount;
        payFechaComprobante.value = today;
        payTipopago.value = 'EFECTIVO';
        toggleExtraFields();
        overlay.classList.remove('hidden');
        overlay.setAttribute('aria-hidden', 'false');
    }

    function closePayModal() {
        overlay.classList.add('hidden');
        overlay.setAttribute('aria-hidden', 'true');
    }

    document.querySelectorAll('.btn-pay-proposal').forEach(function (btn) {
        btn.addEventListener('click', function () {
            openPayModal(this.dataset.id, this.dataset.ref, this.dataset.amount, this.dataset.raw);
        });
    });

    document.getElementById('btn-close-pay-modal').addEventListener('click', closePayModal);
    document.getElementById('btn-cancel-pay').addEventListener('click', closePayModal);
    overlay.addEventListener('click', function (e) { if (e.target === overlay) closePayModal(); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && !overlay.classList.contains('hidden')) closePayModal(); });
})();
</script>
@endsection
