<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title')</title>

    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    @yield('linkstyle')
</head>
<body class="bg-gray-50 text-gray-900 antialiased">
    <div id="app">
        @auth
        <nav class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex space-x-8">
                        <a href="{{ url('/') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600 transition-colors">
                            Home
                        </a>
                        <a href="{{ route('propuesta.listar') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600 transition-colors">
                            Propuestas
                        </a>
                        <div x-data="{ open: false }" class="relative inline-flex items-center">
                            <button @click="open = !open" @keydown.escape="open = false"
                                class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600 transition-colors">
                                Catálogos
                                <svg class="ml-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <div x-show="open" @click.away="open = false"
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95"
                                class="absolute left-0 mt-6 w-56 bg-white rounded-md shadow-lg py-1 z-50"
                                style="display: none;">
                                <a href="{{ route('clientes.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Clientes</a>
                                <a href="{{ route('coberturas.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Coberturas</a>
                                <a href="{{ route('actividades.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Actividades</a>
                                <a href="{{ route('clasificaciones.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Clasificaciones</a>
                                <a href="{{ route('barrios.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Barrios</a>
                                <a href="{{ route('grupos-barrios.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Grupos de Barrios</a>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center">
                        <div x-data="{ open: false }" class="relative">
                            <button
                                @click="open = !open"
                                @keydown.escape="open = false"
                                class="flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600 transition-colors"
                            >
                                {{ Auth::user()->name }}
                                <svg class="ml-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>

                            <div
                                x-show="open"
                                @click.away="open = false"
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95"
                                class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50"
                                style="display: none;"
                            >
                                <a
                                    href="{{ route('logout') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                >
                                    {{ __('Logout') }}
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                    @csrf
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
        @endauth

        <main class="py-6">
            @yield('content')
        </main>
    </div>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
