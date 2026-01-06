<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 10px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .title { font-size: 14px; font-weight: bold; }
        .table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .table th { background-color: #eee; border: 1px solid #000; padding: 5px; text-align: left; }
        .table td { border: 1px solid #ccc; padding: 5px; }
        .total-row { font-weight: bold; background-color: #f2f2f2; font-size: 12px; }
        .text-right { text-align: right; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: right; font-size: 8px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">{{ $titulo }}</div>
        <div style="font-size: 12px;">{{ $subtitulo }}</div>
        <div>Sede: {{ $entidad->sede->nombre ?? 'N/A' }} | Generado: {{ $fecha }}</div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>N°</th>
                <th>Fecha</th>
                <th>Estudiante</th>
                <th>Concepto / Mes</th>
                <th>Método</th>
                <th>Referencia</th>
                <th class="text-right">Monto L.</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pagos as $pago)
            <tr>
                <td>{{ $loop->iteration }}</td>
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

    <div class="footer">
        Sistema de Control Académico
    </div>
</body>
</html>