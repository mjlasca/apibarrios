@extends('layouts.app')
@section('title', $item ? 'Editar Cobertura' : 'Nueva Cobertura')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
    <header class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">{{ $item ? 'Editar Cobertura' : 'Nueva Cobertura' }}</h1>
    </header>

    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-md text-sm">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ $item ? route('coberturas.update', $item->reg) : route('coberturas.store') }}" class="bg-white rounded-lg shadow-md p-6">
        @csrf
        @if ($item)
            @method('PUT')
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
                <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $item->nombre ?? '') }}" required
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
            <div>
                <label for="suma" class="block text-sm font-medium text-gray-700 mb-1">Suma Asegurada *</label>
                <input type="number" id="suma" name="suma" value="{{ old('suma', $item->suma ?? '') }}" required min="0" step="0.01"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
            <div>
                <label for="gastos" class="block text-sm font-medium text-gray-700 mb-1">Gastos *</label>
                <input type="number" id="gastos" name="gastos" value="{{ old('gastos', $item->gastos ?? '') }}" required min="0" step="0.01"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
            <div>
                <label for="deducible" class="block text-sm font-medium text-gray-700 mb-1">Deducible *</label>
                <input type="number" id="deducible" name="deducible" value="{{ old('deducible', $item->deducible ?? '') }}" required min="0" step="0.01"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
            <div>
                <label for="vrMensual" class="block text-sm font-medium text-gray-700 mb-1">Valor Mensual *</label>
                <input type="number" id="vrMensual" name="vrMensual" value="{{ old('vrMensual', $item->vrMensual ?? '') }}" required min="0" step="0.01"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
            <div>
                <label for="vrTrimestral" class="block text-sm font-medium text-gray-700 mb-1">Valor Trimestral</label>
                <input type="number" id="vrTrimestral" name="vrTrimestral" value="{{ old('vrTrimestral', $item->vrTrimestral ?? '') }}" min="0" step="0.01"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
            <div>
                <label for="vrSemestral" class="block text-sm font-medium text-gray-700 mb-1">Valor Semestral</label>
                <input type="number" id="vrSemestral" name="vrSemestral" value="{{ old('vrSemestral', $item->vrSemestral ?? '') }}" min="0" step="0.01"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
            <div>
                <label for="x21" class="block text-sm font-medium text-gray-700 mb-1">Promo 2x1</label>
                <input type="number" id="x21" name="x21" value="{{ old('x21', $item->x21 ?? '') }}" min="0" step="0.01"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
            <div>
                <label for="x32" class="block text-sm font-medium text-gray-700 mb-1">Promo 3x2</label>
                <input type="number" id="x32" name="x32" value="{{ old('x32', $item->x32 ?? '') }}" min="0" step="0.01"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
            <div>
                <label for="x64" class="block text-sm font-medium text-gray-700 mb-1">Promo 6x4</label>
                <input type="number" id="x64" name="x64" value="{{ old('x64', $item->x64 ?? '') }}" min="0" step="0.01"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
        </div>

        <div class="mt-6 flex items-center gap-3">
            <button type="submit"
                class="inline-flex items-center px-5 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                {{ $item ? 'Actualizar' : 'Guardar' }}
            </button>
            <a href="{{ route('coberturas.index') }}"
                class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                Cancelar
            </a>
        </div>
    </form>
</div>
@endsection
