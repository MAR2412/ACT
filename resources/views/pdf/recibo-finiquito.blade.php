<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Recibo - {{ $estudiante->nombre }} {{ $estudiante->apellido }}</title>
    <style>
        @page { 
            margin: 40px 50px; 
        }
        body { 
            font-family: Helvetica, sans-serif; 
            font-size: 13px; 
            color: #000; 
            margin: 0;
            padding: 0;
        }
        .recibo-container { 
            border: 1.5px solid #333; 
            padding: 25px;
            position: relative;
        }
        .header { 
            text-align: center; 
            margin-bottom: 20px; 
        }
        .logo-group { 
            display: block; 
            text-align: center; 
            margin-bottom: 15px; 
        }
        .logo { 
            width: 100px; 
            height: auto; 
            display: inline-block; 
            vertical-align: middle; 
        }
        .mascota-centro { 
            width: 75px; 
            height: auto; 
            display: inline-block; 
            vertical-align: middle; 
            margin-left: 10px; 
        }

        h2 { margin: 5px 0; font-size: 20px; color: #b30000; font-weight: bold; text-transform: uppercase; }
        .subtitulo { color: #0066cc; font-size: 15px; font-weight: bold; margin-bottom: 5px; text-align: center;}
        .numero-recibo { color: #b30000; font-weight: bold; font-size: 15px; text-align: center;}
        .fecha-header { font-size: 12px; text-align: center; margin-bottom: 15px;}

        .table-info { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .table-info td { padding: 8px; border-bottom: 1px solid #eee; }
        .label { font-weight: bold; width: 25%; }

        .table-pagos { 
            width: 100%; 
            border-collapse: collapse; 
            margin: 15px 0; 
            font-size: 11px;
        }
        .table-pagos thead { display: table-header-group; }
        .table-pagos th { background: #b30000; color: white; padding: 10px; font-weight: bold; text-align: center; }
        .table-pagos td { padding: 8px; border-bottom: 1px solid #ddd; text-align: center; }

        .total-box { 
            background: #f2f2f2; 
            padding: 12px; 
            margin-top: 10px; 
            text-align: right; 
            font-size: 16px; 
            font-weight: bold; 
            border: 1px solid #ccc; 
        }

        .bloque-firma { 
            margin-top: 60px; 
            text-align: center; 
            width: 100%; 
            page-break-inside: avoid;
        }
        .contenedor-firmas { 
            height: 80px; 
            position: relative; 
            width: 350px;
            margin: 0 auto;
        }
        .firma-img { 
            width: 180px; 
            position: absolute; 
            left: 0; 
            top: -25px; 
            z-index: 2; 
        }
        .sello-img { 
            width: 100px; 
            position: absolute; 
            right: 10px; 
            top: -15px; 
            z-index: 1; 
            opacity: 0.85; 
        }
        .linea { 
            border-top: 1.5px solid #000; 
            width: 400px; 
            margin: 0 auto; 
            padding-top: 5px; 
            font-size: 11px;
            font-weight: bold; 
        }
        
        .estado-aprobado { color: #28a745; font-weight: bold; }
    </style>
</head>
<body>
<div class="recibo-container">
    
    <div class="header">
        <div class="logo-group">
            <img class="logo" src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('Logo/ACT_Logo.png'))) }}">
            <img class="mascota-centro" src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('Logo/Mascota.png'))) }}">
        </div>
        
        <h2>RECIBO DE FINALIZACIÓN DE PAGOS</h2>
        <div class="subtitulo">MÓDULO COMPLETAMENTE PAGADO</div>
        <div class="numero-recibo">FIN-{{ str_pad($matricula->id, 6, '0', STR_PAD_LEFT) }}</div>
        <div class="fecha-header">Tegucigalpa, {{ $fecha }} - {{ $hora }}</div>
    </div>

    <table class="table-info">
        <tr>
            <td class="label">Estudiante:</td>
            <td>{{ strtoupper($estudiante->nombre) }} {{ strtoupper($estudiante->apellido) }}</td>
        </tr>
        <tr>
            <td class="label">Módulo:</td>
            <td>{{ strtoupper($modulo->nombre) }} @if($matricula->aprobado) <span class="estado-aprobado">(APROBADO)</span> @endif</td>
        </tr>
        <tr>
            <td class="label">Identidad:</td>
            <td>{{ $estudiante->dni ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Sede / Modalidad:</td>
            <td>{{ strtoupper($modulo->sede->nombre ?? 'N/A') }} - {{ strtoupper($modulo->modalidad->nombre ?? 'N/A') }}</td>
        </tr>
    </table>

    <h4 style="margin: 20px 0 5px 0; color: #b30000; text-align: left;">DETALLE DE PAGOS</h4>
    <table class="table-pagos">
        <thead>
            <tr>
                <th width="10%">#</th>
                <th width="20%">Fecha</th>
                <th width="20%">Mes</th>
                <th width="30%">Concepto</th>
                <th width="20%">Monto</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pagos as $index => $pago)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y') }}</td>
                <td>{{ $pago->mes_pagado ?? 'N/A' }}</td>
                <td>{{ ucfirst($pago->tipo) }}</td>
                <td>L. {{ number_format($pago->monto_pagado, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-box">
        TOTAL CANCELADO: L. {{ number_format($totalPagado, 2) }}
    </div>

    <div class="bloque-firma">
        <div class="contenedor-firmas">
            <img class="firma-img" src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('Logo/Firma.png'))) }}">
            <img class="sello-img" src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('Logo/Sello.png'))) }}">
        </div>
        <div class="linea">Firma Autorizada / Sello Institucional</div>
        <p style="font-size: 10px; color: #666; margin-top: 8px;">
            Este documento es una constancia oficial de pago total del módulo.
        </p>
    </div>

</div>
</body>
</html>