@extends('layouts.app')

@section('title', 'Informes - API Barrios')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Informes</h1>
        <p class="mt-1 text-sm text-gray-500">Genera informes de propuestas pagadas por fecha</p>
    </div>

    <div class="bg-white shadow rounded-lg p-6 mb-8">
        <form action="{{ route('reports.generate') }}" method="GET" class="flex items-end gap-4">
            <div>
                <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Seleccionar fecha</label>
                <input
                    type="date"
                    id="date"
                    name="date"
                    value="{{ $date }}"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                />
            </div>
            <button
                type="submit"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
                Generar informes
            </button>
        </form>
    </div>

    <div class="mb-6">
        <p class="text-sm text-gray-500">Fecha seleccionada: <span class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</span></p>
    </div>

    {{-- Fin del Dia --}}
    <div class="bg-white shadow rounded-lg mb-8">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Fin del Dia</h2>
                <p class="mt-1 text-sm text-gray-500">{{ $reports['fin_del_dia']->count() }} registros</p>
            </div>
            @if($reports['fin_del_dia']->isNotEmpty())
            <a
                href="{{ route('reports.download.fin_del_dia', ['date' => $date]) }}"
                class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
                <svg class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Descargar XLSX
            </a>
            @endif
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nro Propuesta</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cert</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo Doc</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Documento</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">CUIR</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Apellido</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Inicio Vigencia</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fin Vigencia</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Meses</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Costo Cobertura</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Costo Total</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tomador</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Doc Tomador</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dir Tomador</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">CP</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Localidad</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Master</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Organizador</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Productor</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($reports['fin_del_dia'] as $row)
                    <tr class="hover:bg-gray-50">
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ $row['nro_propuesta'] }}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ $row['cert_propuesta'] }}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ $row['tipodoc'] }}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ $row['documento'] }}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ $row['cuir'] }}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ $row['apellido'] }}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ $row['iniciovigencia'] }}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ $row['finvigencia'] }}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ $row['meses'] }}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900 text-right">{{ number_format($row['costocobertura'], 0, ',', '.') }}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900 text-right">{{ number_format($row['costo_total'], 0, ',', '.') }}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ $row['apellidotomador'] }}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ $row['tipodoctomador'] }} - {{ $row['documentotomador'] }}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ $row['direcciontomador'] }}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ $row['cptomador'] }}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ $row['localidadtomador'] }}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ $row['master'] }}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ $row['organizador'] }}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ $row['productor'] }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="19" class="px-3 py-4 text-center text-sm text-gray-500">No hay registros para esta fecha</td>
                    </tr>
                    @endforelse
                </tbody>
                @if($reports['fin_del_dia']->isNotEmpty())
                <tfoot class="bg-gray-50">
                    <tr class="font-semibold">
                        <td colspan="9" class="px-3 py-2 text-right text-sm text-gray-900">TOTAL -&gt;</td>
                        <td class="px-3 py-2 text-right text-sm text-gray-900">{{ number_format($reports['fin_del_dia']->sum('costocobertura'), 0, ',', '.') }}</td>
                        <td class="px-3 py-2 text-right text-sm text-gray-900">{{ number_format($reports['fin_del_dia']->sum('costo_total'), 0, ',', '.') }}</td>
                        <td colspan="9"></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    {{-- Envio Colectivo --}}
    <div class="bg-white shadow rounded-lg mb-8">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Envio Colectivo</h2>
                <p class="mt-1 text-sm text-gray-500">{{ $reports['envio_colectivo']->count() }} registros (propuestas con mas de 1 asegurado)</p>
            </div>
            @if($reports['envio_colectivo']->isNotEmpty())
            <a
                href="{{ route('reports.download.envio_colectivo', ['date' => $date]) }}"
                class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
                <svg class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Descargar XLSX
            </a>
            @endif
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Certificado</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo Doc</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Documento</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">CUIR</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Apellido</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sexo</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fec Nac</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Capital</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">AMF</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cod Actividad</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cod Clasif</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Inicio Vig</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fin Vig</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Edad</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cláusula NR</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Barrio</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Costo</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cobertura</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tomador</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Doc Tomador</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dir Tomador</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Master</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Organizador</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Productor</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($reports['envio_colectivo'] as $row)
                    <tr class="hover:bg-gray-50">
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ $row['certificado'] }}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ $row['tipodocumento'] }}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ $row['documento'] }}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ $row['cuir'] }}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ $row['apellido'] }}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ $row['sexo'] }}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ $row['fechanacimiento'] }}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900 text-right">{{ number_format($row['capital'], 0, ',', '.') }}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900 text-right">{{ number_format($row['amf'], 0, ',', '.') }}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ $row['codactividad'] }}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ $row['codclasifactividad'] }}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ $row['fechainiciovigencia'] }}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ $row['fechafinvigencia'] }}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ $row['edad'] }}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ $row['clausula_norepeticion'] }}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ $row['barrio'] }}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900 text-right">{{ number_format($row['costo'], 0, ',', '.') }}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ $row['cobertura'] }}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ $row['apellidotomador'] }}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ $row['tipodocumentotomador'] }} - {{ $row['documentotomador'] }}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ $row['direcciontomador'] }}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ $row['master'] }}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ $row['organizador'] }}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ $row['productor'] }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="24" class="px-3 py-4 text-center text-sm text-gray-500">No hay registros de envio colectivo para esta fecha</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Envio Individual --}}
    <div class="bg-white shadow rounded-lg mb-8">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Envio Individual</h2>
                <p class="mt-1 text-sm text-gray-500">{{ $reports['envio_individual']->count() }} registros (propuestas con 1 asegurado)</p>
            </div>
            @if($reports['envio_individual']->isNotEmpty())
            <a
                href="{{ route('reports.download.envio_individual', ['date' => $date]) }}"
                class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
                <svg class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Descargar XLSX
            </a>
            @endif
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Certificado</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo Doc</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Documento</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">CUIR</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Apellido</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sexo</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fec Nac</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Capital</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">AMF</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cod Actividad</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cod Clasif</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Inicio Vig</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fin Vig</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Edad</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cláusula NR</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Barrio</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Costo</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cobertura</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tomador</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Doc Tomador</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dir Tomador</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Master</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Organizador</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Productor</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($reports['envio_individual'] as $row)
                    <tr class="hover:bg-gray-50">
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ $row['certificado'] }}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ $row['tipodocumento'] }}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ $row['documento'] }}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ $row['cuir'] }}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ $row['apellido'] }}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ $row['sexo'] }}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ $row['fechanacimiento'] }}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900 text-right">{{ number_format($row['capital'], 0, ',', '.') }}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900 text-right">{{ number_format($row['amf'], 0, ',', '.') }}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ $row['codactividad'] }}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ $row['codclasifactividad'] }}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ $row['fechainiciovigencia'] }}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ $row['fechafinvigencia'] }}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ $row['edad'] }}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ $row['clausula_norepeticion'] }}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ $row['barrio'] }}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900 text-right">{{ number_format($row['costo'], 0, ',', '.') }}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ $row['cobertura'] }}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ $row['apellidotomador'] }}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ $row['tipodocumentotomador'] }} - {{ $row['documentotomador'] }}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ $row['direcciontomador'] }}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ $row['master'] }}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ $row['organizador'] }}</td>
                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ $row['productor'] }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="24" class="px-3 py-4 text-center text-sm text-gray-500">No hay registros de envio individual para esta fecha</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
