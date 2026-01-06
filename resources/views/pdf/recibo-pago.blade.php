<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 13px; }
        .recibo-container { border: 2px solid #333; padding: 20px; width: 100%; }
        .header { text-align: center; border-bottom: 1px solid #ccc; margin-bottom: 20px; }
        .numero-recibo { color: red; font-weight: bold; font-size: 16px; }
        .table { width: 100%; margin-top: 15px; border-collapse: collapse; }
        .table td { padding: 8px; border-bottom: 1px solid #eee; }
        .total-box { background: #f2f2f2; padding: 15px; margin-top: 20px; text-align: right; font-size: 18px; font-weight: bold; }
        .footer { margin-top: 50px; text-align: center; font-size: 11px; }
        .firma { margin-top: 40px; border-top: 1px solid #333; display: inline-block; width: 200px; }
    </style>
</head>
<body>
    <div class="recibo-container">
        <div class="header">
            <h2>COMPROBANTE DE PAGO</h2>
            <div class="numero-recibo">N° 000{{ $pago->id }}</div>
            <p>Honduras, {{ $fecha }}</p>
        </div>

        <table class="table">
            <tr>
                <td><strong>Estudiante:</strong></td>
                <td>{{ strtoupper($pago->matricula->estudiante->nombre ?? $pago->matriculaTutoria->estudiante->nombre) }} {{ strtoupper($pago->matricula->estudiante->apellido ?? $pago->matriculaTutoria->estudiante->apellido) }}</td>
            </tr>
            <tr>
                <td><strong>Concepto:</strong></td>
                <td>PAGO DE {{ $tipo }}: {{ $nombre }} ({{ ucfirst($pago->tipo) }})</td>
            </tr>
            <tr>
                <td><strong>Método de Pago:</strong></td>
                <td>{{ ucfirst($pago->metodo_pago) }} {{ $pago->numero_transaccion ? '- Trans: '.$pago->numero_transaccion : '' }}</td>
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

        <div style="margin-top: 10px; font-size: 11px;">
            <strong>Monto del cargo:</strong> L. {{ number_format($pago->monto, 2) }} <br>
            <strong>Cambio entregado:</strong> L. {{ number_format($pago->cambio, 2) }}
        </div>

        <div class="footer">
            <div class="firma">Firma Autorizada / Sello</div>
            <p>Gracias por su pago. Este documento es un comprobante oficial.</p>
        </div>
    </div>
</body>
</html>