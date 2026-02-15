@extends('layouts.app')

@section('content')

<div class="container mt-4">

    <h2 class="text-center mb-4">Gestión de Inspecciones</h2>

    @if(session('success'))
        <div class="alert alert-success text-center">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger text-center">{{ session('error') }}</div>
    @endif

    <div class="card">
        <div class="card-header fw-bold">
            Listado de Inspecciones
        </div>

        <div class="card-body table-responsive">

            <table id="tabla" class="table table-bordered align-middle">

                <thead class="table-light text-center">
                    <tr>
                        <th>Patente</th>
                        <th>Inspector</th>
                        <th>Fecha</th>
                        <th>Trimestre</th>
                        <th>Estado</th>
                        <th>Tipo Documento</th>
                        <th>Observaciones</th>
                        <th class="no-hover">Acción</th>
                    </tr>
                </thead>

                <tbody>
                @forelse ($inspecciones as $insp)

                    @php
                        $estadoNombre = $insp['estado_inspeccion']['estado_inspeccion'] ?? '';
                        $finalizada = strtolower($estadoNombre) === 'finalizada';
                    @endphp

                    <tr>
                        <td>{{ $insp['patente']['numero_patente'] ?? '—' }}</td>

                        <td>
                            {{ $insp['inspector'] ?? 'No asignado' }}
                        </td>

                        <td>{{ $insp['fecha_inspeccion'] }}</td>

                        <td>{{ $insp['trimestre'] }}</td>

                        <td>
                            <span class="badge {{ $finalizada ? 'bg-secondary' : 'bg-primary' }}">
                                {{ $estadoNombre ?: 'Pendiente' }}
                            </span>
                        </td>

                        <td>
                            {{ $insp['tipo_documento']['tipo_documento'] ?? 'No asignado' }}
                        </td>

                        <td>
                            @if(!empty($insp['observaciones']))
                                <ul class="mb-0">
                                    @foreach($insp['observaciones'] as $obs)
                                        <li>{{ $obs['descripcion'] }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <span class="text-muted">Sin observaciones</span>
                            @endif
                        </td>

                        <td class="no-hover text-center">
                            <div class="btn-group">

                                <button type="button"
                                    class="btn btn-success btn-sm"
                                    {{ $finalizada ? 'disabled' : '' }}
                                    data-bs-toggle="tooltip"
                                    data-bs-title="{{ $finalizada ? 'Inspección finalizada' : 'Editar inspección' }}"
                                    onclick="abrirEdicion({{ json_encode($insp) }})">
                                    <i class="bi bi-pen-fill"></i>
                                </button>

                                <button type="button"
                                    class="btn btn-info btn-sm"
                                    data-bs-toggle="tooltip"
                                    data-bs-title="Agregar observación"
                                    onclick="abrirObservaciones({{ json_encode($insp) }})">
                                    <i class="bi bi-chat-left-text"></i>
                                </button>

                            </div>
                        </td>
                    </tr>

                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted">
                            No hay inspecciones registradas
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>

        </div>
    </div>

</div>

{{-- ================= MODAL EDITAR ================= --}}
<div class="modal fade" id="modalEditar" tabindex="-1">
    <div class="modal-dialog">

        <form id="formEditar" method="POST">
            @csrf
            @method('PUT')

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Inspección</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                

                    <div class="mb-3">
                        <label class="form-label">Estado</label>
                        <select name="estado_inspeccion_idestado_inspeccion" id="edit_estado" class="form-select">
                            @foreach($estados as $estado)
                                <option value="{{ $estado['idestado_inspeccion'] }}">
                                    {{ $estado['estado_inspeccion'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tipo Documento</label>
                        <select name="tipo_documento_idtipo_documento" id="edit_tipo_documento" class="form-select">
                            @foreach($tiposDocumento as $doc)
                                <option value="{{ $doc['idtipo_documento'] }}">
                                    {{ $doc['tipo_documento'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button id="btnGuardar" class="btn btn-success" type="submit">
                        Guardar Cambios
                    </button>
                </div>
            </div>
        </form>

    </div>
</div>

{{-- ================= MODAL OBSERVACIONES ================= --}}
<div class="modal fade" id="modalObservaciones" tabindex="-1">
    <div class="modal-dialog">

        <form id="formObservaciones" method="POST">
            @csrf

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Agregar Observación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="obs_id">
                    <textarea name="descripcion" id="obs_texto" rows="5" class="form-control"></textarea>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary" type="submit">Guardar Observación</button>
                </div>
            </div>
        </form>

    </div>
</div>

@endsection

@push('scripts')
<script>
function abrirEdicion(inspeccion) {
    const estadoNombre = inspeccion.estado_inspeccion?.estado_inspeccion?.toLowerCase() || '';
    const finalizada = estadoNombre === 'finalizada';

    edit_estado.value = inspeccion.estado_inspeccion_idestado_inspeccion ?? '';
    edit_tipo_documento.value = inspeccion.tipo_documento_idtipo_documento ?? '';

    edit_estado.disabled = finalizada;
    edit_tipo_documento.disabled = finalizada;
    btnGuardar.disabled = finalizada;

    formEditar.action = `/inspecciones/${inspeccion.idinspecciones}`;
    new bootstrap.Modal('#modalEditar').show();
}

function abrirObservaciones(inspeccion) {
    obs_id.value = inspeccion.idinspecciones;
    obs_texto.value = '';
    formObservaciones.action = `/inspecciones/${inspeccion.idinspecciones}/observaciones`;
    new bootstrap.Modal('#modalObservaciones').show();
}

$(function () {
    $('#tabla').DataTable({
        language: { url: "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json" }
    });

    document.querySelectorAll('[data-bs-toggle="tooltip"]')
        .forEach(el => new bootstrap.Tooltip(el));
});
</script>

<style>
#tabla td.no-hover:hover {
    background: inherit !important;
}
</style>
@endpush
