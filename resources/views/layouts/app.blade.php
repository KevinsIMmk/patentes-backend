<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet" crossorigin="anonymous">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css"
          crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- 🔹 DataTables + Bootstrap 5 CSS -->
    <link rel="stylesheet"
          href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">

    <style>
        .bg-muni-custom { background-color: #AD0000 !important; }
        .btn-muni {
            background-color: #AD0000 !important;
            color: #fff !important;
            border-color: #AD0000 !important;
        }
        .btn-muni:hover,
        .btn-muni:focus {
            background-color: #880000 !important;
            border-color: #880000 !important;
            color: #fff !important;
        }

        html, body { height: 100%; }
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        #app { flex: 1 0 auto; }
        footer { flex-shrink: 0; }
    </style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg bg-muni-custom" data-bs-theme="dark">
    <div class="container-fluid">

        <a class="navbar-brand d-none d-md-block border-end border-3 border-light pe-3" href="#">
            <img src="https://framework.laserena.cl/img/horizontal-blanco.svg" alt="Logo" />
        </a>

        <a class="navbar-brand d-md-none d-block border-end border-3 border-light pe-3" href="#">
            <img src="https://framework.laserena.cl/img/escudo-blanco.svg" alt="Logo" />
        </a>

        <span class="ms-3 text-light fw-bold fs-4" style="letter-spacing:1px;">
            Sistema de Gestión de Patentes Municipalidad de La Serena 
        </span>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navImls" aria-controls="navImls" aria-expanded="false"
                aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navImls">

            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('patentes.index') ? 'active' : '' }}"
                       href="{{ route('patentes.index') }}">
                        Home
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('inspecciones.index') ? 'active' : '' }}"
                       href="{{ route('inspecciones.index') }}">
                        Registrar Inspecciones
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('inspecciones.editar') ? 'active' : '' }}"
                    href="{{ route('inspecciones.editar') }}">
                        Ver Inspecciones
                    </a>
                </li>

                <li class="nav-item">
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="nav-link btn btn-link text-light"
                                style="text-decoration:none;">
                            Cerrar sesión
                        </button>
                    </form>
                </li>

                <hr/>

                <div class="row d-md-none d-block">
                    <div class="col-12 text-center">
                        <div class="btn-group btn-group-sm">
                            <a href="https://www.facebook.com/Munilaserena/" target="_blank" class="btn">
                                <i class="bi bi-facebook text-light"></i>
                            </a>
                            <a href="https://www.instagram.com/muni_laserena" target="_blank" class="btn">
                                <i class="bi bi-instagram text-light"></i>
                            </a>
                            <a href="https://www.youtube.com/user/munilaserena" target="_blank" class="btn">
                                <i class="bi bi-youtube text-light"></i>
                            </a>
                            <a href="https://x.com/munilaserena" target="_blank" class="btn">
                                <i class="bi bi-twitter-x text-light"></i>
                            </a>
                            <a href="https://www.tiktok.com/@munilaserena" target="_blank" class="btn">
                                <i class="bi bi-tiktok text-light"></i>
                            </a>
                        </div>
                    </div>
                </div>

            </ul>

        </div>
    </div>
</nav>

<div id="app">
    <main class="py-4">
        @yield('content')
    </main>
</div>

<!-- FOOTER -->
<footer class="bg-dark pt-3 pb-3">
    <div class="container">
        <div class="row align-items-center">

            <div class="col-md d-none d-md-block order-2 order-md-1 mb-2">
                <div class="text-white">
                    <ul>
                        <li>
                            <a class="text-white text-decoration-none" target="_blank"
                               href="https://www.portaltransparencia.cl/PortalPdT/directorio-de-organismos-regulados/?org=MU126">
                                Transparencia Activa | Ley de Transparencia
                            </a>
                        </li>
                        <li>
                            <a class="text-white text-decoration-none" target="_blank"
                               href="https://www.portaltransparencia.cl/PortalPdT/web/guest/directorio-de-organismos-regulados?p_p_id=pdtorganismos_WAR_pdtorganismosportlet&orgcode=d67e38e61d6896e0e50080a9baaabf3f">
                                Solicitar Información | Ley de Transparencia
                            </a>
                        </li>
                        <li>
                            <a class="text-white text-decoration-none" target="_blank"
                               href="https://www.leylobby.gob.cl/instituciones/MU126">
                                Plataforma Ley del Lobby
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="col-md-4 ms-auto col-12 order-1 order-md-2 mb-2">
                <div class="text-white text-md-end text-center lh-1">
                    <div class="mb-2">
                        <img src="https://framework.laserena.cl/img/horizontal-blanco.svg"
                             alt="" style="height:40px;" />
                    </div>
                    <div><strong>Ilustre Municipalidad de La Serena</strong></div>
                    <div>Arturo Prat #451</div>
                    <div>(56) 51 - 2 206600</div>
                </div>
            </div>

        </div>
    </div>
</footer>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>

<!-- 🔹 DataTables core -->
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

<!-- 🔹 DataTables + Bootstrap 5 -->
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

@stack('scripts')

</body>
</html>
