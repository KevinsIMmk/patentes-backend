@extends('layouts.app')

@section('content')
<div class="container mt-4">
	<h1 class="mb-4">Observaciones</h1>

	{{-- Mensajes --}}
	@if(session('success'))
		<div class="alert alert-success">{{ session('success') }}</div>
	@endif
	@if(session('error'))
		<div class="alert alert-danger">{{ session('error') }}</div>
	@endif

	<div class="card mb-4">
		<div class="card-body">

			{{-- ============================================================ --}}
			{{--  CUANDO VIENE DESDE UNA INSPECCIÓN --}}
			{{-- ============================================================ --}}
			@if(!empty($inspeccion))

				<h5 class="mb-3">Agregar observaciones a la inspección</h5>

				<div class="mb-3">
					<strong>ID Inspección:</strong> {{ $inspeccion['idInspeccion'] ?? $inspeccion['id'] ?? '-' }} <br>
					<strong>Patente:</strong> {{ $inspeccion['patente']['numero_patente'] ?? $inspeccion['numero_patente'] ?? '-' }} <br>
					<strong>Inspector:</strong> {{ $inspeccion['inspector'] ?? '-' }} <br>
					<strong>Fecha:</strong> {{ $inspeccion['fecha_inspeccion'] ?? '-' }}
				</div>

				<form action="{{ route('observaciones.store') }}" method="POST">
					@csrf

					<input type="hidden" name="inspeccion_id" 
						   value="{{ $inspeccion['idInspeccion'] ?? $inspeccion['id'] ?? '' }}">

					<label class="form-label">Observaciones</label>

					<div id="observaciones-list">
						<div class="input-group mb-2 observacion-item">
							<input type="text" name="observaciones[]" class="form-control" placeholder="Observación 1" required>
							<button type="button" class="btn btn-outline-danger ms-2 btn-eliminar-observacion">Eliminar</button>
						</div>
					</div>

					<div class="mb-3">
						<button type="button" id="btnAgregarObservacion" class="btn btn-secondary btn-sm">
							Agregar observación
						</button>
					</div>

					<div class="text-end">
						<button type="submit" class="btn btn-muni">Guardar observaciones</button>
					</div>
				</form>

			@else
			{{-- ============================================================ --}}
			{{--  VISTA NORMAL (NO VIENE DE INSPECCIÓN) --}}
			{{-- ============================================================ --}}

				<form action="{{ route('observaciones.store') }}" method="POST">
					@csrf

					

					<label class="form-label">Observaciones</label>

					<div id="observaciones-list">
						<div class="input-group mb-2 observacion-item">
							<input type="text" name="observaciones[]" class="form-control" placeholder="Observación 1" required>
							<button type="button" class="btn btn-outline-danger ms-2 btn-eliminar-observacion">Eliminar</button>
						</div>
					</div>

					<div class="mb-3">
						<button type="button" id="btnAgregarObservacion" class="btn btn-secondary btn-sm">
							Agregar observación
						</button>
					</div>

					<div class="text-end">
						<button type="submit" class="btn btn-muni">Agregar observaciones</button>
					</div>
				</form>

			@endif

		</div>
	</div>


	{{-- ============================================================ --}}
	{{-- LISTADO DE OBSERVACIONES EXISTENTES --}}
	{{-- ============================================================ --}}
	<div class="card">
		<div class="card-header">Observaciones registradas</div>

		<div class="card-body">
			@if(isset($observaciones) && $observaciones->isNotEmpty())
				<ul class="list-group">
					@foreach($observaciones as $obs)
						<li class="list-group-item">
							<div class="d-flex w-100 justify-content-between">
								<p class="mb-1">{{ $obs->texto ?? $obs['texto'] }}</p>
								<small class="text-muted">{{ $obs->created_at ?? $obs['created_at'] ?? '' }}</small>
							</div>
						</li>
					@endforeach
				</ul>
			@else
				<p class="text-muted mb-0">No hay observaciones aún.</p>
			@endif
		</div>
	</div>

</div>
@endsection


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
	const cont = document.getElementById('observaciones-list');
	const btnAgregar = document.getElementById('btnAgregarObservacion');

	function crearObservacionItem() {
		const wrapper = document.createElement('div');
		wrapper.className = 'input-group mb-2 observacion-item';

		wrapper.innerHTML = `
			<input type="text" name="observaciones[]" class="form-control" placeholder="Escribe una observación..." required>
			<button type="button" class="btn btn-outline-danger ms-2 btn-eliminar-observacion">Eliminar</button>
		`;

		return wrapper;
	}

	// Agregar nueva observación
	if (btnAgregar) {
		btnAgregar.addEventListener('click', () => {
			cont.appendChild(crearObservacionItem());
		});
	}

	// Delegación para eliminar
	if (cont) {
		cont.addEventListener('click', e => {
			if (e.target.classList.contains('btn-eliminar-observacion')) {
				e.target.closest('.observacion-item').remove();
			}
		});
	}
});
</script>
@endpush
