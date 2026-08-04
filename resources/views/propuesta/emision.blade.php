<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Emisión de Propuesta</title>
    <link href="{{ asset('css/proposal-emision.css') }}" rel="stylesheet">
</head>
<body>

<div class="container">
    <header>
        <h1>Emisión de Propuesta</h1>
        <div class="meta-badges">
            <div class="badge">Prefijo: <span>O</span></div>
        </div>
    </header>

    <form id="proposal-form" aria-label="Formulario de nueva propuesta">
        @csrf

        <!-- SECCIÓN TOMADOR -->
        <fieldset>
            <legend>Datos del Tomador</legend>
            <div class="grid-layout">
                <div class="form-group">
                    <label for="t-tipo">Tipo Doc.</label>
                    <select id="t-tipo">
                        <option>DNI</option>
                        <option>CUIT</option>
                    </select>
                </div>
                <div class="form-group autocomplete-wrapper">
                    <label for="t-doc">Nº Documento</label>
                    <input type="text" id="t-doc" placeholder="Buscar o ingresar..." autocomplete="off">
                    <div class="save-client-indicator" id="save-indicator">
                        <span class="dot"></span>
                        <span>Cliente nuevo — se guardará al emitir la propuesta</span>
                    </div>
                    <button type="button" class="btn-add-policy" id="btn-add-tomador" style="display:none">Agregar a la póliza</button>
                </div>
                <div class="form-group">
                    <label for="t-nombre">Nombre(s)</label>
                    <input type="text" id="t-nombre">
                </div>
                <div class="form-group">
                    <label for="t-apellido">Apellido(s)</label>
                    <input type="text" id="t-apellido">
                </div>
                <div class="form-group">
                    <label for="t-fecha-nacimiento">Fecha de Nacimiento</label>
                    <input type="date" id="t-fecha-nacimiento">
                </div>
                <div class="form-group">
                    <label for="t-tel">Teléfono</label>
                    <input type="text" id="t-tel" placeholder="11XXXXXXXX">
                </div>
                <div class="form-group">
                    <label for="t-mail">E-mail</label>
                    <input type="email" id="t-mail">
                </div>
            </div>
        </fieldset>

        <!-- SECCIÓN PRODUCTO -->
        <fieldset>
            <legend>Cobertura y Vigencia</legend>
            <div class="grid-layout">
                <div class="form-group">
                    <label for="c-cobertura">Cobertura</label>
                    <select id="c-cobertura">
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
                <div class="form-group">
                    <label for="c-meses">Meses</label>
                    <select id="c-meses">
                        <option value="1">1 mes</option>
                        <option value="2">2 meses</option>
                        <option value="3">3 meses</option>
                        <option value="6">6 meses</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="v-desde">Vigencia Desde</label>
                    <input type="date" id="v-desde">
                </div>
            </div>
        </fieldset>

        <!-- SECCIÓN CLIENTES VINCULADOS -->
        <section class="grid-section" aria-label="Grilla de Clientes Asegurados">
            <div class="grid-header-title">Clientes Vinculados a la Póliza</div>

            <div class="quick-add-row">
                <div class="form-group autocomplete-wrapper">
                    <label>Nº Documento</label>
                    <input type="text" id="q-doc" placeholder="Buscar/Crear..." autocomplete="off">
                    <div class="save-client-indicator" id="q-save-indicator">
                        <span class="dot"></span>
                        <span>Cliente nuevo — se guardará al emitir</span>
                    </div>
                </div>
                <div class="form-group">
                    <label>Tipo</label>
                    <select id="q-tipo">
                        <option>DNI</option>
                        <option>CUIT</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Nombre</label>
                    <input type="text" id="q-nombre" placeholder="Autocompleta o escribe">
                </div>
                <div class="form-group">
                    <label>Apellido</label>
                    <input type="text" id="q-apellido" placeholder="Autocompleta o escribe">
                </div>
                <div class="form-group">
                    <label>Fecha Nacimiento</label>
                    <input type="date" id="q-fecha-nacimiento">
                </div>
                <div class="form-group">
                    <label>Actividad</label>
                    <select id="q-actividad">
                        <option value="">Seleccione actividad...</option>
                        @foreach ($actividades as $actividad)
                            <option value="{{ $actividad['id'] }}">{{ $actividad['cod'] }} - {{ $actividad['nombre'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Clasificación</label>
                    <select id="q-clasificacion" disabled>
                        <option value="">Primero elija actividad...</option>
                    </select>
                </div>
                <button type="button" class="btn-add" id="btn-add-line" title="Agregar cliente a la grilla">+</button>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Documento</th>
                            <th>Tipo</th>
                            <th>Cliente</th>
                            <th>Actividad</th>
                            <th>Clasificación</th>
                            <th style="width: 80px; text-align: center;">Acción</th>
                        </tr>
                    </thead>
                    <tbody id="lines-table-body">
                        <tr id="lines-empty-row">
                            <td colspan="6" class="empty-cell">Sin clientes vinculados aún</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- SECCIÓN BARRIOS -->
        <fieldset>
            <legend>Barrios / Grupos de Barrios</legend>
            <div class="flex-options">
                <button type="button" class="btn-action btn-pdf" id="btn-open-modal">Seleccionar barrios / grupos</button>
                <span class="hint-text">0 o varios. Cada grupo aporta todos sus barrios.</span>
            </div>
            <div class="chips-container" id="chips-container">
                <span class="chip-placeholder" id="chips-empty">Ningún barrio seleccionado</span>
            </div>
        </fieldset>

        <!-- PIE DE ACCIONES FIJAS -->
        <footer class="footer-bar">
            <div class="totals-group">
                <div class="total-item">
                    <label>Premio Total Estimado</label>
                    <div class="total-value" id="total-premio">$0,00</div>
                </div>
            </div>
            <div class="action-buttons">
                <button type="button" class="btn-action btn-save" id="btn-save-proposal">Guardar Propuesta</button>
            </div>
        </footer>
    </form>
</div>

<!-- MODAL BARRIOS / GRUPOS -->
<div class="modal-overlay" id="modal-overlay" aria-hidden="true">
    <div class="modal" role="dialog" aria-modal="true" aria-labelledby="modal-title">
        <div class="modal-header">
            <h2 id="modal-title">Seleccionar Barrios y Grupos</h2>
            <button type="button" class="modal-close" id="btn-close-modal" aria-label="Cerrar">&times;</button>
        </div>

        <div class="modal-body">
            <h3>Grupos de barrios</h3>
            <div class="modal-list" id="modal-grupos-list">
                @forelse ($grupos as $grupo)
                    <label class="modal-check-item">
                        <input type="checkbox" value="{{ $grupo['id'] }}" data-kind="grupo" data-name="{{ $grupo['nombre'] }}">
                        <span>{{ $grupo['nombre'] }}</span>
                    </label>
                @empty
                    <p class="empty-cell">Sin grupos disponibles</p>
                @endforelse
            </div>

            <h3>Barrios</h3>
            <div class="form-group">
                <input type="text" id="modal-barrios-search" placeholder="Buscar barrio por nombre o CUIT...">
            </div>
            <div class="modal-list" id="modal-barrios-list">
                <p class="empty-cell">Cargando...</p>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn-action btn-save" id="btn-confirm-modal">Aplicar selección</button>
        </div>
    </div>
</div>

<script>
    window.EMISSION_DATA = {
        activities: @json($actividades),
        coverages: @json($coberturas),
        neighborhoods: @json($barrios),
        groups: @json($grupos)
    };
</script>
<script src="{{ asset('js/proposal-emision.js') }}"></script>
</body>
</html>
