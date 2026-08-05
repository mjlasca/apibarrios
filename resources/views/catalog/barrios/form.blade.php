@extends('layouts.app')
@section('title', $item ? 'Editar Barrio' : 'Nuevo Barrio')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
    <header class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">{{ $item ? 'Editar Barrio' : 'Nuevo Barrio' }}</h1>
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

    <form method="POST" action="{{ $item ? route('barrios.update', $item->reg) : route('barrios.store') }}" class="bg-white rounded-lg shadow-md p-6">
        @csrf
        @if ($item)
            @method('PUT')
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="id" class="block text-sm font-medium text-gray-700 mb-1">ID Barrio *</label>
                <input type="text" id="id" name="id" value="{{ old('id', $item->id ?? '') }}" required
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
            <div>
                <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $item->nombre ?? '') }}" required
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
            <div>
                <label for="telefono" class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                <input type="text" id="telefono" name="telefono" value="{{ old('telefono', $item->telefono ?? '') }}"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email', $item->email ?? '') }}"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
            <div class="sm:col-span-2">
                <label for="direccion" class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
                <input type="text" id="direccion" name="direccion" value="{{ old('direccion', $item->direccion ?? '') }}"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
            <div>
                <label for="sub_barrio" class="block text-sm font-medium text-gray-700 mb-1">Sub Barrio</label>
                <input type="text" id="sub_barrio" name="sub_barrio" value="{{ old('sub_barrio', $item->sub_barrio ?? '') }}"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
            <div>
                <label for="clase_barrio" class="block text-sm font-medium text-gray-700 mb-1">Clase Barrio</label>
                <input type="text" id="clase_barrio" name="clase_barrio" value="{{ old('clase_barrio', $item->clase_barrio ?? '') }}"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
            <div>
                <label for="suma_muerte" class="block text-sm font-medium text-gray-700 mb-1">Suma Muerte</label>
                <input type="number" id="suma_muerte" name="suma_muerte" value="{{ old('suma_muerte', $item->suma_muerte ?? '') }}" min="0" step="0.01"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
            <div>
                <label for="suma_gm" class="block text-sm font-medium text-gray-700 mb-1">Suma GM</label>
                <input type="number" id="suma_gm" name="suma_gm" value="{{ old('suma_gm', $item->suma_gm ?? '') }}" min="0" step="0.01"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
            <div>
                <label for="suma_rc" class="block text-sm font-medium text-gray-700 mb-1">Suma RC</label>
                <input type="number" id="suma_rc" name="suma_rc" value="{{ old('suma_rc', $item->suma_rc ?? '') }}" min="0" step="0.01"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
            <div>
                <label for="exige" class="block text-sm font-medium text-gray-700 mb-1">Exige</label>
                <input type="text" id="exige" name="exige" value="{{ old('exige', $item->exige ?? '') }}"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
            <div class="sm:col-span-2">
                <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-1">Observaciones</label>
                <textarea id="observaciones" name="observaciones" rows="3"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">{{ old('observaciones', $item->observaciones ?? '') }}</textarea>
            </div>
        </div>

        <div class="mt-6 flex items-center gap-3">
            <button type="submit"
                class="inline-flex items-center px-5 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                {{ $item ? 'Actualizar' : 'Guardar' }}
            </button>
            <a href="{{ route('barrios.index') }}"
                class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                Cancelar
            </a>
        </div>
    </form>
</div>
@endsection
