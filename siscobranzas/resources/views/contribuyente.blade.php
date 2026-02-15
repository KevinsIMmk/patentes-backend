<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Crear Contribuyente</title>
    <style>
        .container { max-width:700px; margin:2rem auto; font-family:Arial,Helvetica,sans-serif; }
        .form-row { margin-bottom: .75rem; }
        label { display:block; margin-bottom:.25rem; }
        input[type="text"], input[type="email"] { width:100%; padding:.5rem; box-sizing:border-box; }
        .btn { padding:.5rem 1rem; cursor:pointer; }
        .alert { padding:.5rem 1rem; margin-bottom:1rem; border-radius:4px; }
        .alert-success { background:#d4edda; color:#155724; }
        .alert-error { background:#f8d7da; color:#721c24; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Registrar Contribuyente</h1>

        <div id="messages"></div>

        <form id="createForm" onsubmit="return false;">
            <div class="form-row">
                <label for="rut">RUT</label>
                <input type="text" id="rut" name="rut" value="{{ $rut ?? '' }}" required>
            </div>

            <div class="form-row">
                <label for="razon_social">Razón social</label>
                <input type="text" id="razon_social" name="razon_social" required>
            </div>

            <div class="form-row">
                <label for="representante_legal">Representante legal</label>
                <input type="text" id="representante_legal" name="representante_legal" required>
            </div>

            <div class="form-row">
                <label for="direccion">Dirección</label>
                <input type="text" id="direccion" name="direccion" required>
            </div>

            <div class="form-row">
                <label for="poblacion">Población</label>
                <input type="text" id="poblacion" name="poblacion" required>
            </div>

            <div class="form-row">
                <label for="contacto">Contacto</label>
                <input type="text" id="contacto" name="contacto" required>
            </div>

            <div class="form-row">
                <button type="button" class="btn btn-primary" id="submitBtn">Crear Contribuyente</button>
                <a href="/patente" style="margin-left:1rem;">Volver</a>
            </div>
        </form>
    </div>

    @php
        $apiBase = env('API_URL', 'http://127.0.0.1:8000'); // URL de tu API
    @endphp

    <script>
        const apiBase = "{{ $apiBase }}".replace(/\/$/, '');
        const form = document.getElementById('createForm');
        const submitBtn = document.getElementById('submitBtn');
        const messages = document.getElementById('messages');

        function showMessage(text, type='success') {
            messages.innerHTML = `<div class="alert ${type==='success' ? 'alert-success' : 'alert-error'}">${text}</div>`;
        }

        submitBtn.addEventListener('click', function() {
            messages.innerHTML = '';

            if (!apiBase) {
                showMessage('URL de API no configurada.', 'error');
                return;
            }

            const payload = {
                rut: document.getElementById('rut').value.replace(/[.\-\s]/g, ''),
                razon_social: document.getElementById('razon_social').value,
                representante_legal: document.getElementById('representante_legal').value,
                direccion: document.getElementById('direccion').value,
                poblacion: document.getElementById('poblacion').value,
                contacto: document.getElementById('contacto').value
            };

            fetch(apiBase + '/api/contribuyentes', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify(payload)
            })
            .then(async resp => {
                const body = await resp.json().catch(()=>null);
                if (resp.ok) {
                    showMessage('Contribuyente creado correctamente.');
                    setTimeout(()=> { window.location.href = '/patente'; }, 1000);
                } else {
                    const msg = (body && (body.message || JSON.stringify(body))) || `Error: ${resp.status}`;
                    showMessage(msg, 'error');
                }
            })
            .catch(err => {
                console.error(err);
                showMessage('Error de conexión con la API.', 'error');
            });
        });
    </script>
</body>
</html>
