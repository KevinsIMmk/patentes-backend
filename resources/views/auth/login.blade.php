@extends('layouts.app')

@section('content')
<style>
    body {
        background: #b80000 !important;
    }
    .login-bg {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #b80000;
    }
    .login-card {
        border-radius: 8px;
        overflow: hidden;
        min-width: 400px;
        max-width: 450px;
        margin: 0 auto;
    }
    .login-header {
        background: #b80000;
        color: #fff;
        display: flex;
        align-items: center;
        padding: 1rem 1.5rem;
        font-weight: bold;
        font-size: 1.4rem;
        border-bottom: 1px solid #eee;
    }
    .login-header img {
        max-height: 40px;
        margin-right: 1rem;
        background: transparent;
    }
    .login-card .card-body {
        padding: 2rem 1.5rem 1rem 1.5rem;
        background: #fff;
    }
    .login-card .card-footer {
        background: #f8f9fa;
        border-top: 1px solid #eee;
        padding: 1rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .btn-success {
        background: #198754;
        border: none;
    }
    .btn-danger {
        background: #dc3545;
        border: none;
    }
    .btn-dark {
        background: #212529;
        border: none;
    }
    .btn:focus {
        box-shadow: none;
    }
    .form-label {
        font-weight: 500;
        margin-bottom: 0.3rem;
    }
    .form-control {
        border-radius: 6px;
        margin-bottom: 1.2rem;
    }
</style>

<div class="login-bg">
    <div class="card login-card shadow">
        <div class="login-header">
            <img src="https://framework.laserena.cl/img/horizontal-blanco.svg" alt="Logo Municipalidad La Serena" />
            <span>Login Municipal</span>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <label class="form-label">Usuario</label>
                <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Ingrese su usuario" value="{{ old('email') }}" required autofocus />
                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror

                <label class="form-label">Clave</label>
                <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Ingrese su clave" required />
                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
        </div>
        <div class="card-footer">
            @if (Route::has('password.request'))
                <a class='btn btn-dark' href="{{ route('password.request') }}">Recuperar clave</a>
            @endif
            <div class="btn-group" role="group">
                <a href='/' class='btn btn-danger'>Cancelar</a>
                <button type="submit" class='btn btn-success'>Ingresar</button>
            </div>
        </div>
            </form>
    </div>
</div>
@endsection
