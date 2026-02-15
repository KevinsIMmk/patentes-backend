@extends('layouts.app')

@section('content')
<div class="container mt-4">

    <h1 class="mb-4">Patentes</h1>

    {{-- MENSAJES --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- ============================= --}}
    {{-- IMPORTAR EXCEL --}}
    {{-- ============================= --}}
    <div class="card mb-4">
        <div class="card-header bg-dark text-white">
            Importar Patentes desde Excel
        </div>

        <div class="card-body">
            <form action="{{ route('patentes.import-excel') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Seleccione archivo Excel</label>
                        <input type="file" name="archivo_excel" class="form-control" accept=".xlsx,.xls" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Tipo de Patente</label>
                        <select name="tipo_patente_idtipo_patente" class="form-select" required>
                            <option value="">Seleccione un tipo</option>
                            @foreach($tiposPatente as $tipo)
                                <option value="{{ $tipo['idtipo_patente'] }}">
                                    {{ $tipo['tipo_patente'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <button class="btn btn-success px-4" type="submit">
                    Subir Excel e Insertar Patentes
                </button>
            </form>
        </div>
    </div>

    {{-- ============================= --}}
    {{-- TABLA --}}
    {{-- ============================= --}}
    <div class="table-responsive">
        <table id="tabla" class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Número</th>
                    <th>Dirección</th>
                    <th>Actividad</th>
                    <th>Contribuyente</th>
                    <th>Tipo</th>
                    <th>Estado</th>
                    <th>Acción</th>
                </tr>
            </thead>

            <tbody>
            @forelse($patentes as $p)
                @php
                    $estado = strtolower($p['estado_patente']['estado'] ?? 'desconocido');
                @endphp

                <tr>
                    <td>{{ $p['numero_patente'] }}</td>
                    <td>{{ $p['direccion_comercial'] }}</td>
                    <td>{{ $p['actividad_patente'] }}</td>
                    <td>{{ $p['contribuyente']['razon_social'] ?? '-' }}</td>
                    <td>{{ $p['tipo_patente']['tipo_patente'] ?? '-' }}</td>

                    <td>
                        @if($estado === 'habilitado')
                            <span class="badge bg-success">Habilitado</span>
                        @elseif($estado === 'deshabilitado')
                            <span class="badge bg-danger">Deshabilitado</span>
                        @else
                            <span class="badge bg-secondary">{{ ucfirst($estado) }}</span>
                        @endif
                    </td>

                    <td>
                        <div class="btn-group">
                            <button class="btn btn-success btn-sm btnEditarPatente"
                                data-id="{{ $p['idPatente'] }}"
                                data-numero="{{ $p['numero_patente'] }}"
                                data-direccion="{{ $p['direccion_comercial'] }}"
                                data-actividad="{{ $p['actividad_patente'] }}"
                                data-contribuyente-id="{{ $p['contribuyente']['idcontribuyente'] ?? '' }}"
                                data-contribuyente-nombre="{{ $p['contribuyente']['razon_social'] ?? '' }}"
                                data-tipo="{{ $p['tipo_patente']['idtipo_patente'] ?? '' }}"
                                data-estado="{{ $p['estado_patente']['idestado_patente'] ?? '' }}"
                                data-bs-toggle="modal"
                                data-bs-target="#modalEditarPatente">
                                <i class="bi bi-pencil-square"></i>
                            </button>

                            <a href="{{ route('patentes.pdf', $p['idPatente']) }}" class="btn btn-muni btn-sm">
                                <i class="bi bi-file-earmark-pdf"></i>
                            </a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">No hay patentes registradas.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ============================= --}}
{{-- MODAL EDITAR --}}
{{-- ============================= --}}
<div class="modal fade" id="modalEditarPatente" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-header bg-success text-white">
        <h5 class="modal-title">Editar Patente</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form id="formEditarPatente" method="POST">
          @csrf
          @method('PUT')

          <div class="modal-body row g-3">

              <div class="col-12">
                  <strong>N° Patente:</strong> <span id="edit_numero"></span>
              </div>

              <input type="hidden" id="edit_id">
              <input type="hidden" name="contribuyente_idcontribuyente" id="edit_contribuyente_id">

              <div class="col-md-6">
                  <label class="form-label">Dirección Comercial</label>
                  <input type="text" class="form-control" name="direccion_comercial" id="edit_direccion" required>
              </div>

              <div class="col-md-6">
                  <label class="form-label">Actividad</label>
                  <input type="text" class="form-control" name="actividad_patente" id="edit_actividad" required>
              </div>

              <div class="col-md-6">
                  <label class="form-label">Contribuyente</label>
                  <input type="text" class="form-control" id="edit_contribuyente_nombre" readonly>
              </div>

              <div class="col-md-6">
                  <label class="form-label">Tipo de Patente</label>
                  <select class="form-select" name="tipo_patente_idtipo_patente" id="edit_tipo" required>
                      @foreach($tiposPatente as $t)
                          <option value="{{ $t['idtipo_patente'] }}">
                              {{ $t['tipo_patente'] }}
                          </option>
                      @endforeach
                  </select>
              </div>

              <div class="col-md-6">
                  <label class="form-label">Estado</label>
                  <select class="form-select" name="estado_patente_idestado_patente" id="edit_estado" required>
                      @foreach($estados as $e)
                          <option value="{{ $e['idestado_patente'] }}">
                              {{ $e['estado'] }}
                          </option>
                      @endforeach
                  </select>
              </div>

          </div>

          <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-success">Guardar Cambios</button>
          </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
    $('#tabla').DataTable({
        language: { url: "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json" }
    });
});

$(document).on("click", ".btnEditarPatente", function () {

    let id = $(this).data("id");

    $("#edit_id").val(id);
    $("#edit_numero").text($(this).data("numero"));
    $("#edit_direccion").val($(this).data("direccion"));
    $("#edit_actividad").val($(this).data("actividad"));

    $("#edit_contribuyente_id").val($(this).data("contribuyente-id"));
    $("#edit_contribuyente_nombre").val($(this).data("contribuyente-nombre"));

    $("#edit_tipo").val($(this).data("tipo")).change();
    $("#edit_estado").val($(this).data("estado")).change();

    $("#formEditarPatente").attr("action", "/patentes/" + id + "/editar");
});
</script>
@endpush
