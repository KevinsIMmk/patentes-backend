<div class="container mt-4">

    {{-- ALERTAS DE MENSAJES --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- ====================== --}}
    {{-- BUSCAR PATENTE PARA PAGO --}}
    {{-- ====================== --}}
    <h2 class="mb-3">Registrar Pago de Patente</h2>
    <form method="GET" action="{{ route('pagos.buscar') }}" class="row g-2 mb-3">
        <div class="col-md-4">
            <input type="text" name="numero" value="{{ request('numero') }}" placeholder="Número de Patente (ej: PAT-001-2025)" class="form-control">
        </div>
        <div class="col-md-4">
            <input type="text" name="rut" value="{{ request('rut') }}" placeholder="RUT del Contribuyente (ej: 11.111.111-1)" class="form-control">
        </div>
        <div class="col-md-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary">Buscar</button>
            <a href="{{ route('pagos.index') }}" class="btn btn-secondary">Limpiar</a>
        </div>
    </form>

    {{-- RESULTADO DE LA BÚSQUEDA --}}
    @if(isset($patenteEncontrada) && $patenteEncontrada)
        <div class="alert alert-success">
            ✅ Patente encontrada: <strong>{{ $patenteEncontrada['numero_patente'] }}</strong> — RUT: {{ $patenteEncontrada['contribuyente']['rut'] ?? 'N/A' }}
        </div>

        {{-- ====================== --}}
        {{-- FORMULARIO DE REGISTRO DE PAGO --}}
        {{-- ====================== --}}
        <form method="POST" action="{{ route('pagos.store') }}" class="border p-3 rounded mb-4">
            @csrf
            <input type="hidden" name="patente_idPatente" value="{{ $patenteEncontrada['idPatente'] }}">

            <div class="mb-2">
                <label>Monto Pagado</label>
                <input type="number" step="0.01" name="monto_pagado" class="form-control" required>
            </div>

            <div class="mb-2">
                <label>Semestre</label>
                <input type="text" name="semestre" class="form-control" placeholder="1-2025" required>
            </div>

            <div class="mb-2">
                <label>Estado de Pago</label>
                <select name="estado_pago_idestado_pago" class="form-control" required>
                    <option value="">-- Seleccione --</option>
                    @foreach($estados as $estado)
                        <option value="{{ $estado['idestado_pago'] }}">{{ $estado['nombre_estado'] }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-success mt-2">💰 Registrar Pago</button>
        </form>
    @elseif(isset($patenteEncontrada) && !$patenteEncontrada)
        <div class="alert alert-danger">❌ No se encontró ninguna patente con los datos proporcionados.</div>
    @endif

    {{-- ====================== --}}
    {{-- LISTADO DE PAGOS --}}
    {{-- ====================== --}}
    <h2 class="mt-4 mb-3">Listado de Pagos Registrados</h2>

    <form method="GET" action="{{ route('pagos.index') }}" class="mb-3 d-flex gap-2">
        <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Buscar por número de patente" class="form-control" />
        <button type="submit" class="btn btn-primary">Filtrar</button>
        <a href="{{ route('pagos.index') }}" class="btn btn-secondary">Limpiar</a>
    </form>

    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th>Monto Pagado</th>
                <th>Semestre</th>
                <th>Patente</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pagos as $pago)
                <tr>
                    <td>${{ number_format($pago['monto_pagado'], 0, ',', '.') }}</td>
                    <td>{{ $pago['semestre'] }}</td>
                    <td>{{ $pago['patente']['numero_patente'] ?? 'Sin patente' }}</td>
                    <td>{{ $pago['estado_pago']['nombre_estado'] ?? 'Sin estado' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">No hay pagos registrados</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

