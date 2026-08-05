@extends('layouts.app')
@section('title', $item ? 'Editar Grupo de Barrio' : 'Nuevo Grupo de Barrio')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
    <header class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">{{ $item ? 'Editar Grupo de Barrio' : 'Nuevo Grupo de Barrio' }}</h1>
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

    <form method="POST" action="{{ $item ? route('grupos-barrios.update', $item->reg) : route('grupos-barrios.store') }}" class="bg-white rounded-lg shadow-md p-6">
        @csrf
        @if ($item)
            @method('PUT')
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre del Grupo *</label>
                <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $item->nombre ?? '') }}" required
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
            <div>
                <label for="idbarrio" class="block text-sm font-medium text-gray-700 mb-1">Barrio *</label>
                <select id="idbarrio" name="idbarrio" required
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    <option value="">Seleccione barrio...</option>
                    @foreach ($barrios as $barrioItem)
                        <option value="{{ $barrioItem->id }}"
                            {{ old('idbarrio', $item->idbarrio ?? '') == $barrioItem->id ? 'selected' : '' }}>
                            {{ $barrioItem->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mt-6 flex items-center gap-3">
            <button type="submit"
                class="inline-flex items-center px-5 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                {{ $item ? 'Actualizar' : 'Guardar' }}
            </button>
            <a href="{{ route('grupos-barrios.index') }}"
                class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                Cancelar
            </a>
        </div>
    </form>
</div>
@endsection
