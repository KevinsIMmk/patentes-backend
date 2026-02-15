{{-- resources/views/inspecciones.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1 class="mb-4 text-center">Registrar Inspección</h1>

    {{-- Mensajes de sesión --}}
    @if(session('success'))
        <div class="alert alert-success text-center">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger text-center">{{ session('error') }}</div>
    @endif

    <div class="card mx-auto" style="max-width: 900px;">
        <div class="card-body">

            <form id="formInspeccion"
                  action="{{ route('inspecciones.store') }}"
                  method="POST"
                  novalidate>
                @csrf

                {{-- ================= TRIMESTRE ================= --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Trimestre</label>
                        <select name="trimestre" id="trimestre" class="form-select" required>
                            <option value="">Seleccione un trimestre</option>
                            <option value="2025-1">2025-1</option>
                            <option value="2025-2">2025-2</option>
                            <option value="2025-3">2025-3</option>
                        </select>
                    </div>
                </div>

                {{-- ================= BUSCAR PATENTE ================= --}}
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label class="form-label">Buscar Patente</label>

                        <input type="text"
                               id="buscarPatente"
                               class="form-control"
                               placeholder="Escriba para filtrar...">

                        <select name="patente_idPatente"
                                id="comboPatente"
                                class="form-select mt-2"
                                required>
                            <option value="">Seleccione una patente</option>

                            @foreach($patentes as $patente)
                                <option value="{{ $patente['idPatente'] }}">
                                    {{ $patente['numero_patente'] }}
                                    - {{ $patente['contribuyente']['razon_social'] ?? '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- ================= FECHA ================= --}}
                <div class="mb-3">
                    <label class="form-label">Fecha de Inspección</label>
                    <input type="date"
                           name="fecha_inspeccion"
                           id="fecha_inspeccion"
                           class="form-control"
                           required>
                    <small class="text-muted" id="rangoTexto"></small>
                </div>

                {{-- ================= MOTIVO ================= --}}
                <div class="mb-3">
                    <label class="form-label">Motivo</label>
                    <textarea name="motivo"
                              id="motivo"
                              class="form-control"
                              rows="3"
                              placeholder="Escriba el motivo (opcional)"></textarea>
                </div>

                {{-- ================= BOTONES ================= --}}
                <div class="text-center">
                    <button 
                        id="btnGuardar"
                        class="btn btn-success btn-lg"
                        type="button"
                        data-bs-toggle="tooltip"
                        data-bs-placement="top"
                        data-bs-custom-class="tooltip-success"
                        data-bs-title="GUARDAR Registro"
                    >
                        <i class="bi bi-floppy2-fill"></i>
                    </button>

                    <a href="{{ route('patentes.index') }}"
                       class="btn btn-secondary px-5">
                        Volver
                    </a>
                </div>

            </form>

        </div>
    </div>
</div>
@endsection



@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // ===============================
    // FILTRO DE PATENTES
    // ===============================
    const buscarInput = document.getElementById('buscarPatente');
    buscarInput?.addEventListener('input', function () {
        const filtro = this.value.toLowerCase();
        const opciones = document.querySelectorAll('#comboPatente option');
        opciones.forEach(opcion => {
            if (!opcion.value) {
                opcion.style.display = '';
                return;
            }
            const texto = opcion.textContent.toLowerCase();
            opcion.style.display = texto.includes(filtro) ? '' : 'none';
        });
    });

    // ===============================
    // LIMITAR FECHA POR TRIMESTRE
    // ===============================
    const trimestreSelect = document.getElementById('trimestre');
    const fechaInput = document.getElementById('fecha_inspeccion');
    const rangoTexto = document.getElementById('rangoTexto');
    const rangosTrimestre = {
        "2025-1": { min: "2025-01-01", max: "2025-04-30", texto: "Enero — Abril 2025" },
        "2025-2": { min: "2025-05-01", max: "2025-08-30", texto: "Mayo — Agosto 2025" },
        "2025-3": { min: "2025-09-01", max: "2025-12-31", texto: "Septiembre — Diciembre 2025" },
    };
    trimestreSelect?.addEventListener("change", function () {
        const value = this.value;
        if (!rangosTrimestre[value]) {
            fechaInput.value = "";
            fechaInput.removeAttribute("min");
            fechaInput.removeAttribute("max");
            rangoTexto.textContent = "";
            return;
        }
        fechaInput.min = rangosTrimestre[value].min;
        fechaInput.max = rangosTrimestre[value].max;
        rangoTexto.textContent =
            "Solo se permiten fechas entre " + rangosTrimestre[value].texto;
        if (fechaInput.value &&
            (fechaInput.value < fechaInput.min || fechaInput.value > fechaInput.max)) {
            fechaInput.value = "";
        }
    });

    // ===============================
    // BOTÓN GUARDAR CON SWEETALERT
    // ===============================
    const btnGuardar = document.getElementById('btnGuardar');
    const form = document.getElementById('formInspeccion');
    btnGuardar?.addEventListener('click', function () {
        // Validación simple antes de enviar
        const requiredFields = [
            'trimestre',
            'patente_idPatente',
            'fecha_inspeccion'
        ];
        for (const name of requiredFields) {
            const el = form.querySelector('[name="' + name + '"]');
            if (!el || !el.value) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Faltan datos',
                    text: 'Por favor completa todos los campos obligatorios.'
                });
                return;
            }
        }
        Swal.fire({
            title: 'Guardando...',
            text: 'Por favor, espera.',
            showConfirmButton: false,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        // Enviar el formulario real
        form.submit();
    });

    // ===============================
    // ENVÍO DEL FORMULARIO (opcional, si quieres mantener la validación original)
    // ===============================
    /*
    form?.addEventListener('submit', function (e) {
        e.preventDefault();
        const requiredFields = [
            'trimestre',
            'patente_idPatente',
            'fecha_inspeccion'
        ];
        for (const name of requiredFields) {
            const el = form.querySelector('[name="' + name + '"]');
            if (!el || !el.value) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Faltan datos',
                    text: 'Por favor completa todos los campos obligatorios.'
                });
                return;
            }
        }
        Swal.fire({
            title: 'Guardando...',
            text: 'Espere por favor',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });
        fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            credentials: 'same-origin'
        })
        .then(res => {
            if (res.redirected) {
                window.location.href = res.url;
                return;
            }
            return res.json().catch(() => ({}));
        })
        .then(data => {
            Swal.close();
            if (data?.success) {
                Swal.fire('Guardado', 'Inspección creada correctamente', 'success')
                    .then(() => window.location.reload());
            } else {
                Swal.fire('Error', data?.message || 'Error al crear inspección', 'error');
            }
        })
        .catch(() => {
            Swal.fire('Error', 'No se pudo conectar con el servidor', 'error');
        });
    });
    */
});
</script>
@endpush
