```html
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        @page {
            margin: 30px 50px;
        }

        body {
            font-family: Helvetica, sans-serif;
            font-size: 13px;
            color: #000;
            text-align: center;
        }

        .recibo-container {
            border: 2px solid #333;
            padding: 20px 25px;
            position: relative;
            text-align: left;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .logo-group {
            display: inline-block;
            vertical-align: middle;
            margin-bottom: 5px;
        }

        .logo {
            width: 100px;
            vertical-align: middle;
        }

        .mascota-centro {
            width: 70px;
            vertical-align: middle;
            margin-left: 10px;
            opacity: 0.9;
        }

        h2 {
            margin: 5px 0;
            font-size: 18px;
            letter-spacing: 1px;
        }

        .numero-recibo {
            color: #b30000;
            font-weight: bold;
            font-size: 15px;
            margin-bottom: 2px;
        }

        .fecha {
            margin: 0;
            font-size: 12px;
        }

        .table {
            width: 100%;
            margin-top: 10px;
            border-collapse: collapse;
        }

        .table td {
            padding: 7px;
            border-bottom: 1px solid #eee;
        }

        .total-box {
            background: #f2f2f2;
            padding: 10px;
            margin-top: 15px;
            text-align: right;
            font-size: 16px;
            font-weight: bold;
            border: 1px solid #ccc;
        }

        .detalles-pago {
            margin-top: 8px;
            font-size: 11px;
        }

        .bloque-firma {
            margin-top: 45px;
            text-align: center;
        }

        .contenedor-imagenes {
            margin-bottom: -28px; 
            position: relative;
            z-index: 10;
            height: 80px;
        }

        .firma {
            width: 170px;
            position: absolute;
            left: 60%;
            margin-left: -120px;
            bottom: -35px; 
            z-index: 20;
        }

        .sello {
            width: 105px;
            position: absolute;
            left: 50%;
            margin-left: 8px;
            bottom: -5px;
            opacity: 0.78;

        }

        .linea {
            margin: 0 auto;
            width: 380px;
            border-top: 1.5px solid #000;
            font-size: 11px;
            padding-top: 5px; 
            position: relative;
            z-index: 5;
            clear: both;
        }

        .texto-legal {
            font-size: 10px;
            line-height: 1.2;
            margin-top: 10px;
        }
    </style>
</head>

<body>
<div class="recibo-container">

    <div class="header">
        <div class="logo-group">
            <img class="logo" src="{{ public_path('Logo/ACT_Logo.png') }}">
            <img class="mascota-centro" src="{{ public_path('Logo/Mascota.png') }}">
        </div>
        
        <h2>COMPROBANTE DE PAGO</h2>
        
        <div class="numero-recibo">N° 000{{ $pago->id }}</div>
        <p class="fecha">
            Honduras, {{ \Carbon\Carbon::now('America/Tegucigalpa')->format('d/m/Y') }}
        </p>
    </div>

    <table class="table">
        <tr>
            <td style="width: 25%;"><strong>Estudiante:</strong></td>
            <td>
                {{ strtoupper($pago->matricula->estudiante->nombre ?? $pago->matriculaTutoria->estudiante->nombre) }}
                {{ strtoupper($pago->matricula->estudiante->apellido ?? $pago->matriculaTutoria->estudiante->apellido) }}
            </td>
        </tr>
        <tr>
            <td><strong>Concepto:</strong></td>
            <td>PAGO DE {{ $tipo }}: {{ $nombre }} ({{ ucfirst($pago->tipo) }})</td>
        </tr>
        <tr>
            <td><strong>Método de Pago:</strong></td>
            <td>
                {{ ucfirst($pago->metodo_pago) }}
                {{ $pago->numero_transaccion ? '- Trans: '.$pago->numero_transaccion : '' }}
            </td>
        </tr>
        @if($pago->mes_pagado)
        <tr>
            <td><strong>Mes Aplicado:</strong></td>
            <td>{{ strtoupper($pago->mes_pagado) }}</td>
        </tr>
        @endif
    </table>

    <div class="total-box">
        TOTAL PAGADO: L. {{ number_format($pago->monto_pagado, 2) }}
    </div>

    <div class="detalles-pago">
        <strong>Monto del cargo:</strong> L. {{ number_format($pago->monto, 2) }}<br>
        <strong>Cambio entregado:</strong> L. {{ number_format($pago->cambio, 2) }}
    </div>

    <div class="bloque-firma">
        <div class="contenedor-imagenes">
            <img class="firma" src="{{ public_path('Logo/Firma.png') }}">
            <img class="sello" src="{{ public_path('Logo/Sello.png') }}">
        </div>
        
        <div class="linea">Firma Autorizada / Sello Institucional</div>

        <div class="texto-legal">
            <p>Este recibo fue firmado electrónicamente el {{ \Carbon\Carbon::now('America/Tegucigalpa')->format('d/m/Y H:i') }}.</p>
        </div>
    </div>

</div>
</body>
</html>

