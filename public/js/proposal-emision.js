(function () {
    'use strict';

    var DATA = window.EMISSION_DATA || { activities: [], coverages: [], neighborhoods: [], groups: [] };
    var CSRF = document.querySelector('meta[name="csrf-token"]').content;

    var state = {
        insured: [],
        selectedBarrios: new Set(),
        selectedGrupos: new Set(),
        resolvedTomador: null
    };

    /* ---------------------------------------------------------------- */
    /*  Búsqueda simple de cliente por documento                        */
    /*  Si el documento existe, rellena los campos; si no, los limpia.  */
    /* ---------------------------------------------------------------- */

    function initClientLookup(config) {
        var docInput = config.docInput;
        var fill = config.fill;
        var clear = config.clear;
        var saveIndicator = config.saveIndicator;
        var searchTimeout = null;

        function resolveClient(query) {
            return fetch('/propuesta/emision/clientes/resolve/' + encodeURIComponent(query))
                .then(function (res) {
                    if (!res.ok) return null;
                    return res.json();
                })
                .catch(function () { return null; });
        }

        function lookup(query) {
            resolveClient(query).then(function (client) {
                if (client) {
                    docInput.value = client.data.id.trim();
                    fill(client.data);
                    hideSaveIndicator();
                    if (config.onResolved) config.onResolved(client.data);
                } else {
                    clear();
                    showSaveIndicator();
                    if (config.onCleared) config.onCleared();
                }
            });
        }

        function handleInput() {
            clearTimeout(searchTimeout);
            var query = this.value.trim();

            if (query.length < 3) {
                clear();
                hideSaveIndicator();
                if (config.onCleared) config.onCleared();
                return;
            }

            searchTimeout = setTimeout(function () { lookup(query); }, 500);
        }

        docInput.addEventListener('input', handleInput);

        docInput.addEventListener('blur', function () {
            clearTimeout(searchTimeout);
            var query = this.value.trim();
            if (query.length >= 3) lookup(query);
        });

        function showSaveIndicator() {
            if (saveIndicator) saveIndicator.classList.add('visible');
        }

        function hideSaveIndicator() {
            if (saveIndicator) saveIndicator.classList.remove('visible');
        }
    }

    function escapeHtml(str) {
        if (!str) return '';
        var div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    /* ---------------------------------------------------------------- */
    /*  Tomador                                                          */
    /* ---------------------------------------------------------------- */

    function tomadorFields() {
        return {
            tipo: document.getElementById('t-tipo'),
            nombre: document.getElementById('t-nombre'),
            apellido: document.getElementById('t-apellido'),
            fechaNac: document.getElementById('t-fecha-nacimiento'),
            tel: document.getElementById('t-tel'),
            mail: document.getElementById('t-mail')
        };
    }

    function fillTomador(client) {
        var f = tomadorFields();
        f.tipo.value = client.tipo_id || f.tipo.value || 'DNI';
        f.nombre.value = client.nombres || '';
        f.apellido.value = client.apellidos || '';
        f.fechaNac.value = client.fecha_nacimiento || '';
        f.tel.value = client.telefono || '';
        f.mail.value = client.email || '';
    }

    function clearTomador() {
        var f = tomadorFields();
        f.nombre.value = '';
        f.apellido.value = '';
        f.fechaNac.value = '';
        f.tel.value = '';
        f.mail.value = '';
    }

    function initTomadorButton() {
        var button = document.getElementById('btn-add-tomador');

        button.addEventListener('click', function () {
            var client = state.resolvedTomador;
            if (!client) return;

            var f = quickFields();
            var actividad = f.actividad.value;
            var clasificacion = f.clasificacion.value;
            var alreadyFilled = f.doc.value.trim() === client.id.trim();

            if (!alreadyFilled) {
                f.doc.value = client.id.trim();
                f.tipo.value = client.tipo_id || f.tipo.value || 'DNI';
                f.nombre.value = client.nombres || '';
                f.apellido.value = client.apellidos || '';
                f.fechaNac.value = client.fecha_nacimiento || '';
            }

            if (!actividad || !clasificacion) {
                f.actividad.focus();
                alert('Elija la actividad y la clasificación del tomador y presione + para agregarlo a la póliza');
                return;
            }

            pushInsured({
                tipo_id: f.tipo.value,
                documento: client.id.trim(),
                nombres: client.nombres || '',
                apellidos: client.apellidos || '',
                fecha_nacimiento: client.fecha_nacimiento || '',
                id_actividad: parseInt(actividad, 10),
                id_clasificacion: parseInt(clasificacion, 10),
                actividad_nombre: (function () {
                    var activity = DATA.activities.find(function (a) { return String(a.id) === String(actividad); });
                    return activity ? activity.nombre : '';
                })(),
                clasificacion_nombre: f.clasificacion.selectedOptions[0].textContent
            });
        });

        return {
            show: function () { button.style.display = ''; },
            hide: function () { button.style.display = 'none'; }
        };
    }

    var tomadorButton = initTomadorButton();

    initClientLookup({
        docInput: document.getElementById('t-doc'),
        saveIndicator: document.getElementById('save-indicator'),
        fill: fillTomador,
        clear: clearTomador,
        onResolved: function (client) {
            state.resolvedTomador = client;
            tomadorButton.show();
        },
        onCleared: function () {
            state.resolvedTomador = null;
            tomadorButton.hide();
        }
    });

    /* ---------------------------------------------------------------- */
    /*  Fila rápida de clientes vinculados                               */
    /* ---------------------------------------------------------------- */

    function quickFields() {
        return {
            doc: document.getElementById('q-doc'),
            tipo: document.getElementById('q-tipo'),
            nombre: document.getElementById('q-nombre'),
            apellido: document.getElementById('q-apellido'),
            fechaNac: document.getElementById('q-fecha-nacimiento'),
            actividad: document.getElementById('q-actividad'),
            clasificacion: document.getElementById('q-clasificacion')
        };
    }

    function fillQuick(client) {
        var f = quickFields();
        f.tipo.value = client.tipo_id || f.tipo.value || 'DNI';
        f.nombre.value = client.nombres || '';
        f.apellido.value = client.apellidos || '';
        f.fechaNac.value = client.fecha_nacimiento || '';
        f.actividad.value = '';
        resetClasificacionSelect();
    }

    function clearQuick() {
        var f = quickFields();
        f.nombre.value = '';
        f.apellido.value = '';
        f.fechaNac.value = '';
        f.actividad.value = '';
        resetClasificacionSelect();
    }

    initClientLookup({
        docInput: quickFields().doc,
        saveIndicator: document.getElementById('q-save-indicator'),
        fill: fillQuick,
        clear: clearQuick
    });

    function resetClasificacionSelect() {
        var clasificacion = quickFields().clasificacion;
        clasificacion.innerHTML = '<option value="">Seleccione actividad...</option>';
        clasificacion.disabled = true;
    }

    document.getElementById('q-actividad').addEventListener('change', function () {
        var activityId = this.value;
        var clasificacion = quickFields().clasificacion;
        clasificacion.innerHTML = '<option value="">Cargando...</option>';
        clasificacion.disabled = true;

        if (!activityId) {
            resetClasificacionSelect();
            return;
        }

        fetch('/propuesta/emision/actividades/' + encodeURIComponent(activityId) + '/clasificaciones')
            .then(function (res) { return res.json(); })
            .then(function (classifications) {
                clasificacion.innerHTML = '<option value="">Seleccione clasificación...</option>';
                classifications.data.forEach(function (item) {
                    const option = document.createElement('option');
                    option.value = item.id;
                    option.textContent = item.cod + ' - ' + item.nombre;
                    clasificacion.appendChild(option);
                });
                clasificacion.disabled = classifications.data.length === 0;
            })
            .catch(function () {
                clasificacion.innerHTML = '<option value="">Error cargando clasificaciones</option>';
                clasificacion.disabled = true;
            });
    });

    document.getElementById('btn-add-line').addEventListener('click', addInsuredLine);

    function pushInsured(data) {
        var existing = state.insured.find(function (line) { return line.documento === data.documento; });
        if (existing) {
            alert('El documento ' + data.documento + ' ya está en la grilla');
            return false;
        }

        state.insured.push(data);

        var f = quickFields();
        f.doc.value = '';
        clearQuick();
        renderLines();
        return true;
    }

    function addInsuredLine() {
        var f = quickFields();
        var doc = f.doc.value.trim();
        var nombre = f.nombre.value.trim();
        var apellido = f.apellido.value.trim();
        var fechaNac = f.fechaNac.value;
        var actividad = f.actividad.value;
        var clasificacion = f.clasificacion.value;

        if (!doc || !nombre || !apellido || !fechaNac) {
            alert('Complete documento, nombre, apellido y fecha de nacimiento del cliente vinculado');
            return;
        }
        if (!actividad || !clasificacion) {
            alert('Seleccione actividad y clasificación');
            return;
        }

        var activity = DATA.activities.find(function (a) { return String(a.id) === String(actividad); });
        var classificationLabel = f.clasificacion.selectedOptions[0].textContent;

        pushInsured({
            tipo_id: f.tipo.value,
            documento: doc,
            nombres: nombre,
            apellidos: apellido,
            fecha_nacimiento: fechaNac,
            id_actividad: parseInt(actividad, 10),
            id_clasificacion: parseInt(clasificacion, 10),
            actividad_nombre: activity ? activity.nombre : '',
            clasificacion_nombre: classificationLabel
        });
    }

    function renderLines() {
        var body = document.getElementById('lines-table-body');
        var emptyRow = document.getElementById('lines-empty-row');

        body.querySelectorAll('tr:not(#lines-empty-row)').forEach(function (row) { row.remove(); });
        emptyRow.style.display = state.insured.length ? 'none' : '';

        state.insured.forEach(function (line, index) {
            var row = document.createElement('tr');
            row.innerHTML =
                '<td><strong>' + escapeHtml(line.documento) + '</strong></td>' +
                '<td>' + escapeHtml(line.tipo_id) + '</td>' +
                '<td>' + escapeHtml(line.nombres) + ' ' + escapeHtml(line.apellidos) + '</td>' +
                '<td>' + escapeHtml(line.actividad_nombre) + '</td>' +
                '<td>' + escapeHtml(line.clasificacion_nombre) + '</td>' +
                '<td style="text-align: center;"><button type="button" class="btn-remove" data-index="' + index + '" title="Quitar cliente">\u2715</button></td>';
            row.querySelector('.btn-remove').addEventListener('click', function () {
                state.insured.splice(parseInt(this.dataset.index, 10), 1);
                renderLines();
            });
            body.appendChild(row);
        });

        updateTotals();
    }

    /* ---------------------------------------------------------------- */
    /*  Modal barrios / grupos                                           */
    /* ---------------------------------------------------------------- */

    var modalOverlay = document.getElementById('modal-overlay');
    var modalBarriosList = document.getElementById('modal-barrios-list');
    var modalSearch = document.getElementById('modal-barrios-search');

    document.getElementById('btn-open-modal').addEventListener('click', function () {
        modalOverlay.classList.add('open');
        modalOverlay.setAttribute('aria-hidden', 'false');
        modalSearch.value = '';
        syncModalCheckboxes();
        renderModalNeighborhoods('');
    });

    document.getElementById('btn-close-modal').addEventListener('click', closeModal);

    modalOverlay.addEventListener('click', function (e) {
        if (e.target === modalOverlay) closeModal();
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modalOverlay.classList.contains('open')) closeModal();
    });

    function closeModal() {
        modalOverlay.classList.remove('open');
        modalOverlay.setAttribute('aria-hidden', 'true');
    }

    modalSearch.addEventListener('input', function () {
        renderModalNeighborhoods(this.value.trim());
    });

    function syncModalCheckboxes() {
        document.querySelectorAll('#modal-grupos-list input[data-kind="grupo"]').forEach(function (input) {
            input.checked = state.selectedGrupos.has(parseInt(input.value, 10));
        });
        document.querySelectorAll('#modal-barrios-list input[data-kind="barrio"]').forEach(function (input) {
            input.checked = state.selectedBarrios.has(input.value);
        });
    }

    function renderModalNeighborhoods(query) {
        var lower = query.toLowerCase();
        var all = DATA.neighborhoods.filter(function (neighborhood) {
            if (!lower) return true;
            return neighborhood.nombre.toLowerCase().indexOf(lower) !== -1 || neighborhood.id.indexOf(query) !== -1;
        });

        modalBarriosList.innerHTML = '';

        if (all.length === 0) {
            var empty = document.createElement('p');
            empty.className = 'empty-cell';
            empty.textContent = 'Sin coincidencias';
            modalBarriosList.appendChild(empty);
            return;
        }

        var visible = all.slice(0, 150);
        visible.forEach(function (neighborhood) {
            var label = document.createElement('label');
            label.className = 'modal-check-item';

            var input = document.createElement('input');
            input.type = 'checkbox';
            input.value = neighborhood.id;
            input.dataset.kind = 'barrio';
            input.dataset.name = neighborhood.nombre;
            input.checked = state.selectedBarrios.has(neighborhood.id);
            input.addEventListener('change', function () {
                toggleBarrio(neighborhood.id, input.checked);
            });

            var span = document.createElement('span');
            span.textContent = neighborhood.nombre + ' (' + neighborhood.id + ')';

            label.appendChild(input);
            label.appendChild(span);
            modalBarriosList.appendChild(label);
        });

        if (visible.length < all.length) {
            var more = document.createElement('p');
            more.className = 'empty-cell';
            more.textContent = 'Refine la búsqueda para ver más resultados';
            modalBarriosList.appendChild(more);
        }
    }

    function toggleBarrio(id, checked) {
        if (checked) {
            state.selectedBarrios.add(id);
        } else {
            state.selectedBarrios.delete(id);
        }
    }

    document.getElementById('modal-grupos-list').addEventListener('change', function (e) {
        var input = e.target;
        if (input.dataset.kind !== 'grupo') return;
        var groupId = parseInt(input.value, 10);
        if (input.checked) {
            state.selectedGrupos.add(groupId);
        } else {
            state.selectedGrupos.delete(groupId);
        }
    });

    document.getElementById('btn-confirm-modal').addEventListener('click', function () {
        closeModal();
        renderChips();
    });

    function renderChips() {
        var container = document.getElementById('chips-container');
        container.querySelectorAll('.chip').forEach(function (chip) { chip.remove(); });
        var empty = document.getElementById('chips-empty');

        state.selectedBarrios.forEach(function (id) {
            var neighborhood = DATA.neighborhoods.find(function (n) { return n.id === id; });
            addChip(container, (neighborhood ? neighborhood.nombre : id), function () {
                state.selectedBarrios.delete(id);
                renderChips();
            });
        });

        state.selectedGrupos.forEach(function (groupId) {
            var group = DATA.groups.find(function (g) { return g.id === groupId; });
            addChip(container, 'Grupo: ' + (group ? group.nombre : groupId), function () {
                state.selectedGrupos.delete(groupId);
                renderChips();
            });
        });

        empty.style.display = (state.selectedBarrios.size + state.selectedGrupos.size) ? 'none' : '';
    }

    function addChip(container, label, onRemove) {
        var chip = document.createElement('span');
        chip.className = 'chip';
        chip.innerHTML = escapeHtml(label) + ' <button type="button" class="chip-remove" aria-label="Quitar">\u2715</button>';
        chip.querySelector('.chip-remove').addEventListener('click', onRemove);
        container.appendChild(chip);
    }

    /* ---------------------------------------------------------------- */
    /*  Premio estimado                                                  */
    /* ---------------------------------------------------------------- */

    function updateTotals() {
        var cobertura = document.getElementById('c-cobertura').value;
        var months = parseInt(document.getElementById('c-meses').value, 10) || 1;
        var coverage = DATA.coverages.find(function (c) { return c.nombre === cobertura; });
        var total = 0;

        if (coverage) {
            var prize = coverage.vrMensual * months;
            if (months === 2 && coverage.x21 > 0) prize = coverage.x21;
            if (months === 3 && coverage.x32 > 0) prize = coverage.x32;
            if (months === 6 && coverage.x64 > 0) prize = coverage.x64;
            total = prize * state.insured.length;
        }

        document.getElementById('total-premio').textContent = formatMoney(total);
    }

    document.getElementById('c-cobertura').addEventListener('change', updateTotals);
    document.getElementById('c-meses').addEventListener('change', updateTotals);

    function formatMoney(value) {
        return '$' + Number(value).toLocaleString('es-AR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    /* ---------------------------------------------------------------- */
    /*  Guardar propuesta                                                */
    /* ---------------------------------------------------------------- */

    document.getElementById('btn-save-proposal').addEventListener('click', saveProposal);

    function tomadorPayload() {
        var f = tomadorFields();
        return {
            tipo_id: f.tipo.value,
            documento: document.getElementById('t-doc').value.trim(),
            nombres: f.nombre.value.trim(),
            apellidos: f.apellido.value.trim(),
            fecha_nacimiento: f.fechaNac.value,
            telefono: f.tel.value.trim(),
            email: f.mail.value.trim()
        };
    }

    async function saveProposal() {
        var tomador = tomadorPayload();

        if (!tomador.documento || !tomador.nombres || !tomador.apellidos || !tomador.fecha_nacimiento) {
            alert('Complete el documento, nombre(s), apellido(s) y fecha de nacimiento del tomador');
            return;
        }
        if (!document.getElementById('c-cobertura').value) {
            alert('Seleccione una cobertura');
            return;
        }
        if (state.insured.length === 0) {
            alert('Agregue al menos un cliente vinculado');
            return;
        }

        var button = document.getElementById('btn-save-proposal');
        button.disabled = true;
        button.textContent = 'Guardando...';

        try {
            var res = await fetch('/propuesta/emision/store', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': CSRF
                },
                body: JSON.stringify({
                    tomador: tomador,
                    cobertura: document.getElementById('c-cobertura').value,
                    meses: parseInt(document.getElementById('c-meses').value, 10),
                    fecha_desde: document.getElementById('v-desde').value || null,
                    asegurados: state.insured.map(function (line) {
                        return {
                            tipo_id: line.tipo_id,
                            documento: line.documento,
                            nombres: line.nombres,
                            apellidos: line.apellidos,
                            fecha_nacimiento: line.fecha_nacimiento,
                            id_actividad: line.id_actividad,
                            id_clasificacion: line.id_clasificacion
                        };
                    }),
                    barrios: Array.from(state.selectedBarrios),
                    grupos: Array.from(state.selectedGrupos)
                })
            });

            var data = await res.json();

            if (data.success) {
                alert('Propuesta ' + data.data.prefijo + '-' + data.data.idpropuesta + ' guardada correctamente');
                resetForm();
            } else {
                alert(data.message || 'Error al guardar la propuesta');
            }
        } catch (e) {
            alert('Error de conexión al guardar la propuesta');
        } finally {
            button.disabled = false;
            button.textContent = 'Guardar Propuesta';
        }
    }

    function resetForm() {
        document.getElementById('t-doc').value = '';
        clearTomador();
        state.resolvedTomador = null;
        tomadorButton.hide();
        document.getElementById('v-desde').value = '';
        document.getElementById('c-cobertura').value = '';
        document.getElementById('c-meses').value = '1';

        quickFields().doc.value = '';
        clearQuick();

        state.insured = [];
        state.selectedBarrios.clear();
        state.selectedGrupos.clear();
        renderLines();
        renderChips();
    }
})();
