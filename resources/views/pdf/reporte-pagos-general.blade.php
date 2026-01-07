<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        @page {
            /* Mantiene el espacio para el encabezado en cada página */
            margin: 110px 50px 60px 50px;
        }

        body { 
            font-family: 'Helvetica', sans-serif; 
            font-size: 10px; 
            color: #333; 
            margin: 0;
        }

        /* Header sin la línea negra (border-bottom eliminado) */
        .header { 
            position: fixed;
            top: -90px;
            left: 0;
            right: 0;
            height: 85px;
            text-align: center; 
            padding-bottom: 10px;
        }

        .header-logo-left {
            position: absolute;
            left: 0;
            top: 0;
            width: 70px;
        }

        .header-logo-right {
            position: absolute;
            right: 0;
            top: 0;
            width: 55px;
        }

        .title { 
            font-size: 14px; 
            font-weight: bold; 
            text-transform: uppercase; 
            margin-top: 10px; 
        }
        
        .table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 10px;
        }

        .table thead { 
            display: table-header-group; 
        }

        .table th { 
            background-color: #f8f9fa; 
            border: 1px solid #ddd; 
            padding: 5px; 
            text-align: left; 
            text-transform: uppercase;
            font-size: 9px;
        }

        .table td { 
            border: 1px solid #eee; 
            padding: 5px; 
            page-break-inside: avoid; 
        }

        .total-row { 
            font-weight: bold; 
            background-color: #f2f2f2; 
            font-size: 11px; 
        }
        
        .text-right { text-align: right; }

        .footer { 
            position: fixed; 
            bottom: -40px; 
            left: 0;
            right: 0;
            height: 30px;
            text-align: right; 
            font-size: 8px; 
            color: #999;
            border-top: 1px solid #eee; /* Línea tenue opcional solo en pie de página */
            padding-top: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <img class="header-logo-left" src="{{ public_path('Logo/ACT_Logo.png') }}">
        <img class="header-logo-right" src="{{ public_path('Logo/Mascota.png') }}">
        
        <div class="title">{{ $titulo }}</div>
        <div style="font-size: 12px;">{{ $subtitulo }}</div>
        <div style="font-size: 10px; margin-top: 5px;">
            Sede: {{ $entidad->sede->nombre ?? 'N/A' }} | Generado: {{ $fecha }}
        </div>
    </div>

    <div class="footer">
        Sistema de Control Académico - Honduras - {{ date('Y') }}
    </div>

    <table class="table">
        <thead>
            <tr>
                <th style="width: 25px; text-align: center;">N°</th>
                <th style="width: 65px;">Fecha</th>
                <th>Estudiante</th>
                <th>Concepto / Mes</th>
                <th style="width: 70px;">Método</th>
                <th style="width: 80px;">Referencia</th>
                <th class="text-right" style="width: 80px;">Monto L.</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pagos as $pago)
            <tr>
                <td style="text-align: center;">{{ $loop->iteration }}</td>
                <td>{{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y') }}</td>
                <td>
                    {{ strtoupper($pago->matricula->estudiante->nombre ?? $pago->matriculaTutoria->estudiante->nombre) }}
                    {{ strtoupper($pago->matricula->estudiante->apellido ?? $pago->matriculaTutoria->estudiante->apellido) }}
                </td>
                <td>{{ ucfirst($pago->tipo) }} {{ $pago->mes_pagado ? '('.$pago->mes_pagado.')' : '' }}</td>
                <td>{{ ucfirst($pago->metodo_pago) }}</td>
                <td>{{ $pago->numero_transaccion ?? 'S/N' }}</td>
                <td class="text-right">{{ number_format($pago->monto_pagado, 2) }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="6" class="text-right">TOTAL GENERAL:</td>
                <td class="text-right">L. {{ number_format($total, 2) }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>