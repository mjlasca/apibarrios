(function() {
    var docInput = document.getElementById('t-doc');
    var dropdown = document.getElementById('autocomplete-dropdown');
    var saveIndicator = document.getElementById('save-indicator');
    var btnSave = document.getElementById('btn-save-client');
    var searchTimeout = null;
    var selectedClientId = null;

    function getFields() {
        return {
            tipo: document.getElementById('t-tipo'),
            nombre: document.getElementById('t-nombre'),
            apellido: document.getElementById('t-apellido'),
            fechaNac: document.getElementById('t-fecha-nacimiento'),
            tel: document.getElementById('t-tel'),
            mail: document.getElementById('t-mail'),
        };
    }

    function fillClient(client) {
        var f = getFields();
        selectedClientId = client.id;
        f.tipo.value = client.tipo_id || 'DNI';
        f.nombre.value = client.nombres || '';
        f.apellido.value = client.apellidos || '';
        f.fechaNac.value = client.fecha_nacimiento || '';
        f.tel.value = client.telefono || '';
        f.mail.value = client.email || '';
        saveIndicator.classList.remove('visible');
    }

    function clearFields() {
        var f = getFields();
        f.nombre.value = '';
        f.apellido.value = '';
        f.fechaNac.value = '';
        f.tel.value = '';
        f.mail.value = '';
    }

    async function searchClients(query) {
        try {
            var res = await fetch('/propuesta/emision/clientes/search?q=' + encodeURIComponent(query));
            return await res.json();
        } catch (e) {
            return [];
        }
    }

    function renderDropdown(clients) {
        dropdown.innerHTML = '';
        if (clients.length === 0) {
            dropdown.style.display = 'none';
            return;
        }
        clients.forEach(function(client) {
            var item = document.createElement('div');
            item.className = 'autocomplete-item';
            item.innerHTML = '<strong>' + escapeHtml(client.id) + '</strong> \u2014 ' + escapeHtml(client.nombres || '') + ' ' + escapeHtml(client.apellidos || '') + ' <small>' + escapeHtml(client.tipo_id || '') + '</small>';
            item.addEventListener('click', function() {
                fillClient(client);
                docInput.value = client.id;
                dropdown.style.display = 'none';
            });
            dropdown.appendChild(item);
        });
        dropdown.style.display = 'block';
    }

    function escapeHtml(str) {
        if (!str) return '';
        var div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    docInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        var query = this.value.trim();
        selectedClientId = null;

        if (query.length < 3) {
            dropdown.style.display = 'none';
            saveIndicator.classList.remove('visible');
            return;
        }

        searchTimeout = setTimeout(function() {
            searchClients(query).then(function(clients) {
                var exactMatch = clients.find(function(c) { return c.id === query; });
                if (exactMatch) {
                    fillClient(exactMatch);
                    dropdown.style.display = 'none';
                } else {
                    clearFields();
                    renderDropdown(clients);
                    if (clients.length === 0) {
                        saveIndicator.classList.add('visible');
                    } else {
                        saveIndicator.classList.remove('visible');
                    }
                }
            });
        }, 350);
    });

    docInput.addEventListener('blur', function() {
        setTimeout(function() { dropdown.style.display = 'none'; }, 200);
    });

    docInput.addEventListener('focus', function() {
        if (dropdown.children.length > 0) {
            dropdown.style.display = 'block';
        }
    });

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.autocomplete-wrapper')) {
            dropdown.style.display = 'none';
        }
    });

    btnSave.addEventListener('click', async function() {
        var f = getFields();
        var doc = docInput.value.trim();
        var nombres = f.nombre.value.trim();
        var apellidos = f.apellido.value.trim();
        var fechaNac = f.fechaNac.value;

        if (!doc) { alert('Ingresa un N\u00ba de documento'); return; }
        if (!nombres) { alert('Ingresa el nombre'); return; }
        if (!apellidos) { alert('Ingresa el apellido'); return; }
        if (!fechaNac) { alert('Ingresa la fecha de nacimiento'); return; }

        btnSave.disabled = true;
        btnSave.textContent = 'Guardando...';

        try {
            var res = await fetch('/propuesta/emision/clientes/save', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: JSON.stringify({
                    id: doc,
                    tipo_id: f.tipo.value,
                    nombres: nombres,
                    apellidos: apellidos,
                    fecha_nacimiento: fechaNac,
                    telefono: f.tel.value.trim(),
                    email: f.mail.value.trim(),
                })
            });
            var data = await res.json();
            if (data.success) {
                selectedClientId = doc;
                saveIndicator.classList.remove('visible');
                alert('Cliente guardado correctamente');
            } else {
                alert(data.message || 'Error al guardar el cliente');
            }
        } catch (e) {
            alert('Error de conexi\u00f3n al guardar el cliente');
        } finally {
            btnSave.disabled = false;
            btnSave.textContent = 'Guardar Cliente';
        }
    });
})();
