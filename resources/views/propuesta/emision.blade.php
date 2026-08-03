<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generador de Propuestas Ultra-Ágil</title>
    <link href="{{ asset('css/proposal-emision.css') }}" rel="stylesheet">
</head>
<body>

<div class="container">
    <!-- CABECERA -->
    <header>
        <h1>Emisión de Propuesta</h1>
        <div class="meta-badges">
            <div class="badge">Propuesta Nº: <span>AUT-2026-098</span></div>
            <div class="badge">Referencia: <span>REF-WEB-99</span></div>
        </div>
    </header>

    <form aria-label="Formulario de nueva propuesta">
        @csrf
        
        <!-- SECCIÓN TOMADOR -->
        <fieldset>
            <legend>Datos del Tomador</legend>
            <div class="grid-layout">
                <div class="form-group">
                    <label for="t-tipo">Tipo Doc.</label>
                    <select id="t-tipo"><option>DNI</option><option>CUIT</option></select>
                </div>
                <div class="form-group autocomplete-wrapper">
                    <label for="t-doc">Nº Documento</label>
                    <input type="text" id="t-doc" placeholder="Buscar o ingresar..." autocomplete="off">
                    <div class="autocomplete-dropdown" id="autocomplete-dropdown"></div>
                    <div class="save-client-indicator" id="save-indicator">
                        <span class="dot"></span>
                        <span>Cliente nuevo — completa los campos y guárdalo</span>
                        <button type="button" class="btn-save-client" id="btn-save-client">Guardar Cliente</button>
                    </div>
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

        <!-- SECCIÓN LA GRILLA (PRODUCTO / ASEGURADOS) -->
        <section class="grid-section" aria-label="Grilla de Clientes Asegurados">
            <div class="grid-header-title">Clientes Vinculados a la Póliza (Venta Masiva / Individual)</div>
            
            <!-- Fila de Entrada Rápida Avanzada -->
            <div class="quick-add-row">
                <div class="form-group">
                    <label>Nº Documento</label>
                    <input type="text" placeholder="Buscar/Crear..." autofocus>
                </div>
                <div class="form-group">
                    <label>Tipo</label>
                    <select><option>DNI</option><option>CUIT</option></select>
                </div>
                <div class="form-group">
                    <label>Nombre</label>
                    <input type="text" placeholder="Autocompleta o escribe">
                </div>
                <div class="form-group">
                    <label>Apellido</label>
                    <input type="text" placeholder="Autocompleta o escribe">
                </div>
                <!-- Reemplazo ágil de ListBox 1 con búsqueda -->
                <div class="form-group">
                    <label>Actividad (Filtro rápido)</label>
                    <select>
                        <option value="">Seleccione actividad...</option>
                        <option>Administrativo Comercial</option>
                        <option>Operario Técnico</option>
                        <option>Logística y Distribución</option>
                    </select>
                </div>
                <!-- Reemplazo ágil de ListBox 2 con búsqueda -->
                <div class="form-group">
                    <label>Clasificación</label>
                    <select>
                        <option value="">Seleccione clase...</option>
                        <option>Riesgo Bajo (Clase A)</option>
                        <option>Riesgo Medio (Clase B)</option>
                        <option>Riesgo Alto (Clase C)</option>
                    </select>
                </div>
                <button type="button" class="btn-add" title="Agregar cliente a la grilla">+</button>
            </div>

            <!-- Tabla de datos resultantes -->
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Documento</th>
                            <th>Tipo</th>
                            <th>Cliente</th>
                            <th>Actividad asignada</th>
                            <th>Clasificación</th>
                            <th style="width: 80px; text-align: center;">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>20-39448552-1</strong></td>
                            <td>DNI</td>
                            <td>Carlos Mendoza</td>
                            <td>Administrativo Comercial</td>
                            <td>Riesgo Bajo (Clase A)</td>
                            <td style="text-align: center;"><button type="button" class="btn-remove" title="Quitar cliente">✕</button></td>
                        </tr>
                        <tr>
                            <td><strong>27-41009223-4</strong></td>
                            <td>DNI</td>
                            <td>Ana María Gómez</td>
                            <td>Logística y Distribución</td>
                            <td>Riesgo Medio (Clase B)</td>
                            <td style="text-align: center;"><button type="button" class="btn-remove" title="Quitar cliente">✕</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- SECCIÓN PARAMETROS Y REQUISITOS -->
        <fieldset>
            <legend>Vigencia y Condiciones</legend>
            <div class="grid-layout" style="grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));">
                <div class="form-group">
                    <label for="v-desde">Vigencia Desde</label>
                    <input type="date" id="v-desde" value="2026-07-06">
                </div>
                <div class="form-group">
                    <label for="v-hasta">Vigencia Hasta</label>
                    <input type="date" id="v-hasta">
                </div>
                <div class="form-group" style="grid-column: span 2;">
                    <label>Configuraciones Adicionales</label>
                    <div class="flex-options">
                        <label class="checkbox-label"><input type="checkbox" checked> Alta en póliza colectiva</label>
                        <label class="checkbox-label"><input type="checkbox"> Nueva póliza</label>
                        <label class="checkbox-label"><input type="checkbox"> Imprimir cláusula de no repetición</label>
                        <label class="checkbox-label"><input type="checkbox" checked> Paga</label>
                    </div>
                </div>
            </div>
        </fieldset>

        <!-- PIE DE ACCIONES FIJAS -->
        <footer class="footer-bar">
            <div class="totals-group">
                <div class="total-item">
                    <label>Premio Base</label>
                    <div class="total-value">$24.500,00</div>
                </div>
                <div class="total-item">
                    <label>Premio Total</label>
                    <div class="total-value" style="color: var(--success);">$29.645,00</div>
                </div>
            </div>
            <div class="action-buttons">
                <button type="button" class="btn-action btn-save">Guardar Propuesta</button>
                <button type="button" class="btn-action btn-pdf">Generar Recibo PDF</button>
                <button type="button" class="btn-action btn-policy">Emitir Póliza PDF</button>
            </div>
        </footer>

    </form>
</div>

<script src="{{ asset('js/proposal-emision.js') }}"></script>
</body>
</html>
