<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Egresos</title>
    <style>
        body { 
            font-family: 'Helvetica', Arial, sans-serif; 
            font-size: 12px; 
            margin: 0;
            padding: 0;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #000;
        }
        
        .header-logos {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .logo-left {
            text-align: left;
        }
        
        .logo-left img {
            height: 60px;
            width: auto;
        }
        
        .logo-right {
            text-align: right;
        }
        
        .logo-right img {
            height: 50px;
            width: auto;
        }
        
        .title {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 10px 0 5px 0;
        }
        
        .subtitle {
            font-size: 14px;
            margin: 5px 0 10px 0;
        }
        
        .info-line {
            font-size: 11px;
            color: #666;
        }
        
        .resumen {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .resumen-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            text-align: center;
        }
        
        .resumen-item {
            padding: 10px;
            border-radius: 4px;
        }
        
        .total-general {
            background-color: #dc3545;
            color: white;
            font-weight: bold;
        }
        
        .total-mes {
            background-color: #28a745;
            color: white;
            font-weight: bold;
        }
        
        .total-registros {
            background-color: #17a2b8;
            color: white;
            font-weight: bold;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        
        .table th {
            background-color: #343a40;
            color: white;
            border: 1px solid #454d55;
            padding: 8px;
            text-align: left;
            font-weight: bold;
        }
        
        .table td {
            border: 1px solid #dee2e6;
            padding: 8px;
        }
        
        .table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #dee2e6;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        @media print {
            .header {
                position: fixed;
                top: 0;
                width: 100%;
                background: white;
            }
            
            .resumen {
                page-break-inside: avoid;
            }
            
            .table {
                page-break-inside: auto;
            }
            
            .table tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-logos">
            <div class="logo-left">
                <img src="{{ public_path('ACT_Logo.png') }}" alt="Logo ACT">
            </div>
            <div class="logo-right">
                <img src="{{ public_path('Mascota.png') }}" alt="Mascota">
            </div>
        </div>
        
        <div class="title">REPORTE DE EGRESOS</div>
        <div class="subtitle">{{ $subtitulo }}</div>
        <div class="info-line">
            Generado por: {{ $user->name }} | Fecha: {{ $fecha }}
        </div>
    </div>

    <div class="resumen">
        <div class="resumen-grid">
            <div class="resumen-item total-general">
                <div class="text-lg">TOTAL GENERAL</div>
                <div class="text-xl">L. {{ number_format($total, 2) }}</div>
            </div>
            
            <div class="resumen-item total-mes">
                <div class="text-lg">TOTAL ESTE MES</div>
                <div class="text-xl">L. {{ number_format($total_mes, 2) }}</div>
            </div>
            
            <div class="resumen-item total-registros">
                <div class="text-lg">TOTAL DE REGISTROS</div>
                <div class="text-xl">{{ $egresos->count() }}</div>
            </div>
        </div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th style="width: 80px;">No.</th>
                <th style="width: 100px;">Fecha</th>
                <th>Descripción</th>
                <th style="width: 150px;">Registrado Por</th>
                <th style="width: 120px;" class="text-right">Monto (L.)</th>
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
            <tr style="background-color: #e9ecef; font-weight: bold;">
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