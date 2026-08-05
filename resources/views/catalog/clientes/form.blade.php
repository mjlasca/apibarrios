@extends('layouts.app')
@section('title', $item ? 'Editar Cliente' : 'Nuevo Cliente')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
    <header class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">{{ $item ? 'Editar Cliente' : 'Nuevo Cliente' }}</h1>
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

    <form method="POST" action="{{ $item ? route('clientes.update', $item->reg) : route('clientes.store') }}" class="bg-white rounded-lg shadow-md p-6">
        @csrf
        @if ($item)
            @method('PUT')
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="id" class="block text-sm font-medium text-gray-700 mb-1">Nº Documento *</label>
                <input type="text" id="id" name="id" value="{{ old('id', $item->id ?? '') }}" required
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
            <div>
                <label for="tipo_id" class="block text-sm font-medium text-gray-700 mb-1">Tipo Documento</label>
                <select id="tipo_id" name="tipo_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    <option value="DNI" {{ old('tipo_id', $item->tipo_id ?? '') === 'DNI' ? 'selected' : '' }}>DNI</option>
                    <option value="CUIT" {{ old('tipo_id', $item->tipo_id ?? '') === 'CUIT' ? 'selected' : '' }}>CUIT</option>
                    <option value="LC" {{ old('tipo_id', $item->tipo_id ?? '') === 'LC' ? 'selected' : '' }}>LC</option>
                    <option value="LE" {{ old('tipo_id', $item->tipo_id ?? '') === 'LE' ? 'selected' : '' }}>LE</option>
                </select>
            </div>
            <div>
                <label for="nombres" class="block text-sm font-medium text-gray-700 mb-1">Nombre(s) *</label>
                <input type="text" id="nombres" name="nombres" value="{{ old('nombres', $item->nombres ?? '') }}" required
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
            <div>
                <label for="apellidos" class="block text-sm font-medium text-gray-700 mb-1">Apellido(s) *</label>
                <input type="text" id="apellidos" name="apellidos" value="{{ old('apellidos', $item->apellidos ?? '') }}" required
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
            <div>
                <label for="fecha_nacimiento" class="block text-sm font-medium text-gray-700 mb-1">Fecha de Nacimiento *</label>
                <input type="date" id="fecha_nacimiento" name="fecha_nacimiento"
                    value="{{ old('fecha_nacimiento', isset($item->fecha_nacimiento) ? \Illuminate\Support\Carbon::parse($item->fecha_nacimiento)->format('Y-m-d') : '') }}"
                    required
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
            <div>
                <label for="sexo" class="block text-sm font-medium text-gray-700 mb-1">Sexo</label>
                <select id="sexo" name="sexo" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    <option value="">Seleccione...</option>
                    <option value="M" {{ old('sexo', $item->sexo ?? '') === 'M' ? 'selected' : '' }}>Masculino</option>
                    <option value="F" {{ old('sexo', $item->sexo ?? '') === 'F' ? 'selected' : '' }}>Femenino</option>
                </select>
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
                <label for="codpostal" class="block text-sm font-medium text-gray-700 mb-1">Código Postal</label>
                <input type="text" id="codpostal" name="codpostal" value="{{ old('codpostal', $item->codpostal ?? '') }}"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
            <div>
                <label for="localidad" class="block text-sm font-medium text-gray-700 mb-1">Localidad</label>
                <input type="text" id="localidad" name="localidad" value="{{ old('localidad', $item->localidad ?? '') }}"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
            <div>
                <label for="ciudad" class="block text-sm font-medium text-gray-700 mb-1">Ciudad</label>
                <input type="text" id="ciudad" name="ciudad" value="{{ old('ciudad', $item->ciudad ?? '') }}"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
        </div>

        <div class="mt-6 flex items-center gap-3">
            <button type="submit"
                class="inline-flex items-center px-5 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                {{ $item ? 'Actualizar' : 'Guardar' }}
            </button>
            <a href="{{ route('clientes.index') }}"
                class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                Cancelar
            </a>
        </div>
    </form>
</div>
@endsection
