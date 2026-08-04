<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lista de Propuestas</title>
    <link href="{{ asset('css/proposal-emision.css') }}" rel="stylesheet">
    <link href="{{ asset('css/proposal-list.css') }}" rel="stylesheet">
</head>
<body>

<div class="container">
    <header>
        <h1>Propuestas</h1>
        <div class="meta-badges">
            <div class="badge">Usuario: {{ auth()->user()->name ?? '—' }}</div>
        </div>
    </header>

    <div class="filters-bar">
        <form method="GET" action="{{ url('/propuesta/listar') }}" class="filters-form">
            <div class="form-group">
                <label for="desde">Desde</label>
                <input type="date" id="desde" name="desde" value="{{ $from }}" max="{{ $today }}">
            </div>
            <div class="form-group">
                <label for="hasta">Hasta</label>
                <input type="date" id="hasta" name="hasta" value="{{ $to }}" max="{{ $today }}">
            </div>
            <div class="form-group">
                <label for="q">Tomador o documento</label>
                <input type="text" id="q" name="q" value="{{ $keyword }}" placeholder="Buscar por nombre o documento..." autocomplete="off">
            </div>
            <button type="submit" class="btn-action btn-filter">Filtrar</button>
        </form>
        <a href="{{ route('propuesta.emision') }}" class="btn-action btn-new">Nueva Propuesta</a>
    </div>

    @if (session('success'))
        <div class="flash-success">{{ session('success') }}</div>
    @endif

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Referencia</th>
                    <th>Tomador</th>
                    <th>Cobertura</th>
                    <th>Asegurados</th>
                    <th>Premio</th>
                    <th>Vigencia</th>
                    <th>Creada</th>
                    <th>Fecha Paga</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($proposals as $proposal)
                    <tr class="{{ (int) $proposal->codestado === 0 ? 'row-cancelled' : '' }}">
                        <td><strong>{{ $proposal->prefijo }}-{{ $proposal->idpropuesta }}</strong></td>
                        <td>
                            {{ $proposal->nombre }}
                            <br><small class="muted">Doc: {{ $proposal->documento }}</small>
                        </td>
                        <td>{{ $proposal->id_cobertura }}</td>
                        <td>{{ $proposal->num_polizas }}</td>
                        <td>${{ number_format((float) $proposal->premio_total, 2, ',', '.') }}</td>
                        <td>
                            <small>
                                {{ \Illuminate\Support\Carbon::parse($proposal->fechaDesde)->format('d/m/Y') }}
                                al
                                {{ \Illuminate\Support\Carbon::parse($proposal->fechaHasta)->format('d/m/Y') }}
                            </small>
                        </td>
                        <td>
                            <small>{{ \Illuminate\Support\Carbon::parse($proposal->created_at)->format('d/m/Y H:i') }}</small>
                        </td>
                        <td>
                            @if ($proposal->fecha_paga)
                                <small>{{ \Illuminate\Support\Carbon::parse($proposal->fecha_paga)->format('d/m/Y H:i') }}</small>
                            @else
                                <small class="muted">—</small>
                            @endif
                        </td>
                        <td>
                            <div class="row-actions">
                                @if ($proposal->created_at !== null && $proposal->created_at->between($todayRange[0], $todayRange[1]) && (int) $proposal->codestado !== 0)
                                    <a href="{{ url('/propuesta/' . $proposal->id . '/editar') }}" class="btn-action btn-edit">Modificar</a>
                                @endif
                                @if ((int) $proposal->codestado !== 0)
                                    <form method="POST" action="{{ url('/propuesta/' . $proposal->id . '/anular') }}" class="inline-form"
                                          onsubmit="return confirm('¿Anular la propuesta {{ $proposal->prefijo }}-{{ $proposal->idpropuesta }}?');">
                                        @csrf
                                        <button type="submit" class="btn-action btn-cancel">Anular</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="empty-cell">Sin propuestas para los filtros seleccionados</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
