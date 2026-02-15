<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informe Patente {{ $patente['numero_patente'] }}</title>

    <style>
        /* ================= CONFIG DOMPDF ================= */
        @page {
            margin: 110px 35px 80px 35px;
        }

        body {
            font-family: DejaVu Sans, Arial, Helvetica, sans-serif;
            font-size: 13px;
            color: #222;
            background: #f7f7fa;
        }

        /* ================= HEADER ================= */
        header {
            position: fixed;
            top: -95px;
            left: 0;
            right: 0;
            height: 90px;
        }

        .header-bar {
            background: #AD0000;
            height: 90px;
            position: relative;
        }

        .logo-vertical {
            position: absolute;
            top: 8px;
            left: 12px;
            height: 70px;
        }

        .header-title {
            text-align: right;
            padding: 18px 18px 0 110px;
            color: #fff;
        }

        .doc-title {
            font-size: 16px;
            font-weight: bold;
            margin: 0;
        }

        .doc-subtitle {
            font-size: 11px;
            margin: 4px 0 0 0;
            opacity: 0.95;
        }

        /* ================= FOOTER ================= */
        footer {
            position: fixed;
            bottom: -55px;
            left: 0;
            right: 0;
            height: 50px;
            background: #111;
            color: #fff;
            font-size: 10px;
        }

        footer table {
            width: 100%;
            border-collapse: collapse;
            padding: 10px 12px;
        }

        .pagenum:before { content: counter(page); }
        .pagecount:before { content: counter(pages); }

        /* ================= CONTENIDO ================= */
        .titulo {
            text-align: center;
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 18px;
            color: #AD0000;
            letter-spacing: 1px;
        }

        .meta {
            text-align: center;
            font-size: 12px;
            color: #666;
            margin-bottom: 22px;
        }

        .section {
            border: 1.5px solid #e0e0e0;
            border-radius: 10px;
            margin-bottom: 22px;
            background: #fff;
            box-shadow: 0 2px 6px #0001;
        }

        .section-header {
            background: #f8f8fa;
            padding: 10px 16px;
            font-weight: bold;
            font-size: 15px;
            border-bottom: 1.5px solid #e0e0e0;
            color: #AD0000;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }

        .section-body {
            padding: 16px 16px 10px 16px;
        }

        table.data {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 6px;
            background: #fcfcfc;
        }

        table.data th,
        table.data td {
            border: 1px solid #e0e0e0;
            padding: 8px 10px;
            vertical-align: top;
        }

        table.data th {
            background: #f3f3f3;
            width: 33%;
            text-align: left;
            color: #AD0000;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 11px;
            color: #fff;
            background: #AD0000;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .insp-title {
            font-size: 14px;
            font-weight: bold;
            margin: 0 0 8px 0;
            color: #AD0000;
        }

        .obs-item {
            font-size: 12px;
            color: #333;
        }

        .divider {
            height: 12px;
        }

        .muted {
            color: #999;
        }

        .avoid-break {
            page-break-inside: avoid;
        }
    </style>
</head>

<body>

<!-- ================= HEADER ================= -->
<header>
    <div class="header-bar">



                   <!-- LOGO HORIZONTAL BLANCO (PNG, esquina superior izquierda, compatible con DomPDF) -->
                   <img src="https://framework.laserena.cl/img/horizontal-blanco.png"
                       style="position:absolute; top:10px; left:10px; height:60px; z-index:10;" 
                       alt="Logo Municipalidad de La Serena" />

        <div class="header-title">
            <p class="doc-title">Sistema de Cobranza Municipal</p>
            <p class="doc-subtitle">Informe Oficial de Patente Comercial</p>
        </div>
    </div>
</header>

<!-- ================= FOOTER ================= -->
<footer>
    <table>
        <tr>
            <td>
                Ilustre Municipalidad de La Serena · Arturo Prat #451 · (56) 51 2206600
            </td>
            <td style="text-align:right;">
                Página <span class="pagenum"></span> de <span class="pagecount"></span>
            </td>
        </tr>
    </table>
</footer>



<!-- ================= CONTENIDO ================= -->
<h1 class="titulo">Informe de Patente</h1>

<div class="meta">
    N° Patente: <strong>{{ $patente['numero_patente'] }}</strong>
    · Generado: <strong>{{ date('d-m-Y') }}</strong>
</div>

<!-- ================= DATOS PATENTE ================= -->
<div class="section avoid-break">
    <div class="section-header">Datos de la Patente</div>
    <div class="section-body">
        <table class="data">
            <tr><th>Número</th><td>{{ $patente['numero_patente'] }}</td></tr>
            <tr><th>Actividad</th><td>{{ $patente['actividad_patente'] }}</td></tr>
            <tr><th>Dirección</th><td>{{ $patente['direccion_comercial'] }}</td></tr>
            <tr>
                <th>Estado</th>
                <td><span class="badge">{{ $patente['estado_patente']['estado'] ?? '-' }}</span></td>
            </tr>
            <tr><th>Tipo Patente</th><td>{{ $patente['tipo_patente']['tipo_patente'] ?? '-' }}</td></tr>
        </table>
    </div>
</div>

<!-- ================= CONTRIBUYENTE ================= -->
<div class="section avoid-break">
    <div class="section-header">Contribuyente</div>
    <div class="section-body">
        <table class="data">
            <tr><th>RUT</th><td>{{ $patente['contribuyente']['rut'] ?? '-' }}</td></tr>
            <tr><th>Razón Social</th><td>{{ $patente['contribuyente']['razon_social'] ?? '-' }}</td></tr>
            <tr><th>Representante Legal</th><td>{{ $patente['contribuyente']['representante_legal'] ?? '-' }}</td></tr>
            <tr><th>Dirección</th><td>{{ $patente['contribuyente']['direccion'] ?? '-' }}</td></tr>
        </table>
    </div>
</div>

<!-- ================= INSPECCIONES ================= -->
<div class="section">
    <div class="section-header">Inspecciones Realizadas</div>
    <div class="section-body">

        @forelse ($patente['inspecciones'] as $index => $insp)

            <div class="avoid-break">
                <p class="insp-title">Inspección Nº {{ $index + 1 }}</p>

                <table class="data">
                    <tr><th>Fecha</th><td>{{ $insp['fecha_inspeccion'] }}</td></tr>
                    <tr><th>Inspector</th><td>{{ $insp['inspector'] ?? '-' }}</td></tr>
                    <tr><th>Trimestre</th><td>{{ $insp['trimestre'] ?? '-' }}</td></tr>
                    <tr><th>Tipo Documento</th><td>{{ $insp['tipo_documento']['tipo_documento'] ?? 'Sin tipo' }}</td></tr>
                    <tr><th>Motivo</th><td>{{ $insp['motivo'] ?? '-' }}</td></tr>
                </table>

                <div class="divider"></div>

                <p class="insp-title" style="font-size:12px;">Observaciones</p>

                @if (!empty($insp['observaciones']))
                    <table class="data">
                        @foreach ($insp['observaciones'] as $obs)
                            <tr>
                                <td class="obs-item">• {{ $obs['descripcion'] }}</td>
                            </tr>
                        @endforeach
                    </table>
                @else
                    <p class="muted">Sin observaciones registradas.</p>
                @endif

                <div class="divider"></div>
            </div>

        @empty
            <p class="muted">No existen inspecciones registradas.</p>
        @endforelse

    </div>
</div>

</body>
</html>
