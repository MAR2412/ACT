<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Egresos</title>
    <style>
        @page {
            margin: 30px 50px;
        }

        body { 
            font-family: 'Helvetica', Arial, sans-serif; 
            font-size: 12px; 
            margin: 0;
            padding: 0;
            color: #333;
        }
        
        /* Contenedor del encabezado usando tabla para márgenes perfectos */
        .header-table {
            width: 100%;
            border-bottom: 2px solid #000;
            margin-bottom: 15px;
            padding-bottom: 10px;
        }
        
        .logo-left img {
            height: 65px; /* Tamaño ajustado */
            width: auto;
        }
        
        .logo-right img {
            height: 65px; /* Tamaño simétrico al izquierdo */
            width: auto;
        }
        
        .header-info {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .title {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 5px 0;
        }
        
        .subtitle {
            font-size: 13px;
            margin-bottom: 5px;
        }
        
        .info-line {
            font-size: 10px;
            color: #666;
        }
        
        /* Resumen reemplazando Grid por Tabla para estabilidad */
        .resumen-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 10px 0;
            margin-bottom: 20px;
        }
        
        .resumen-item {
            padding: 12px;
            text-align: center;
            border-radius: 5px;
            color: white;
            width: 33%;
        }
        
        .total-general { background-color: #dc3545; }
        .total-mes { background-color: #28a745; }
        .total-registros { background-color: #17a2b8; }
        
        .text-lg { font-size: 10px; font-weight: bold; margin-bottom: 4px; }
        .text-xl { font-size: 14px; font-weight: bold; }
        
        /* Estilos de la Tabla de Datos */
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        .table th {
            background-color: #343a40;
            color: white;
            padding: 10px 8px;
            text-align: left;
            font-size: 11px;
            border: 1px solid #333;
        }
        
        .table td {
            border: 1px solid #dee2e6;
            padding: 8px;
            font-size: 11px;
        }
        
        .table tr:nth-child(even) { background-color: #f8f9fa; }
        
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #888;
            padding-top: 10px;
            border-top: 1px solid #eee;
        }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td class="logo-left" style="width: 30%;">
                <img src="{{ public_path('Logo/ACT_Logo.png') }}" alt="Logo ACT">
            </td>
            <td style="width: 40%; text-align: center;">
                <div class="title">REPORTE DE EGRESOS</div>
                <div class="subtitle">{{ $subtitulo }}</div>
            </td>
            <td class="logo-right" style="width: 30%; text-align: right;">
                <img src="{{ public_path('Logo/Mascota.png') }}" alt="Mascota">
            </td>
        </tr>
    </table>

    <div class="header-info">
        <div class="info-line">
            Generado por: <strong>{{ $user->name }}</strong> | Fecha: {{ $fecha }}
        </div>
    </div>

    <table class="resumen-table">
        <tr>
            <td class="resumen-item total-general">
                <div class="text-lg">TOTAL GENERAL</div>
                <div class="text-xl">L. {{ number_format($total, 2) }}</div>
            </td>
            <td class="resumen-item total-mes">
                <div class="text-lg">TOTAL ESTE MES</div>
                <div class="text-xl">L. {{ number_format($total_mes, 2) }}</div>
            </td>
            <td class="resumen-item total-registros">
                <div class="text-lg">TOTAL DE REGISTROS</div>
                <div class="text-xl">{{ $egresos->count() }}</div>
            </td>
        </tr>
    </table>

    <table class="table">
        <thead>
            <tr>
                <th style="width: 40px;" class="text-center">No.</th>
                <th style="width: 80px;">Fecha</th>
                <th>Descripción</th>
                <th style="width: 130px;">Registrado Por</th>
                <th style="width: 110px;" class="text-right">Monto (L.)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($egresos as $index => $egreso)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $egreso->fecha_egreso->format('d/m/Y') }}</td>
                <td>{{ $egreso->descripcion }}</td>
                <td>{{ $egreso->creator->name ?? 'N/A' }}</td>
                <td class="text-right">L. {{ number_format($egreso->monto_utilizado, 2) }}</td>
            </tr>
            @endforeach
            <tr style="background-color: #eee; font-weight: bold;">
                <td colspan="4" class="text-right">TOTAL GENERAL:</td>
                <td class="text-right">L. {{ number_format($total, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        Sistema de Control Académico ACT | Documento generado automáticamente
    </div>
</body>
</html>