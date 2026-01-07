<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        @page {
            margin: 110px 50px 60px 50px;
        }

        body { 
            font-family: 'Helvetica', sans-serif; 
            font-size: 11px; 
            color: #333; 
            margin: 0;
        }
        
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

        .title { font-size: 16px; font-weight: bold; text-transform: uppercase; margin-top: 10px; }
        
        .info-table { width: 100%; margin-bottom: 20px; border: 1px solid #eee; padding: 10px; border-radius: 5px; }
        
        .main-table { width: 100%; border-collapse: collapse; }
        
        .main-table thead { 
            display: table-header-group; 
        }

        .main-table th { 
            background-color: #f8f9fa; 
            border: 1px solid #ddd; 
            padding: 8px; 
            text-align: left; 
            font-size: 10px; 
            text-transform: uppercase;
        }

        .main-table td { 
            border: 1px solid #ddd; 
            padding: 6px; 
            page-break-inside: avoid;
        }

        .footer { 
            position: fixed; 
            bottom: -40px; 
            left: 0;
            right: 0;
            height: 30px;
            font-size: 9px; 
            color: #999; 
            border-top: 1px solid #eee;
            padding-top: 5px;
        }

        .pagenum:before {
            content: counter(page);
        }

        .sexo-f { color: #d63384; font-weight: bold; }
        .sexo-m { color: #0d6efd; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <img class="header-logo-left" src="{{ public_path('Logo/ACT_Logo.png') }}">
        <img class="header-logo-right" src="{{ public_path('Logo/Mascota.png') }}">
        
        <div class="title">Listado Oficial de Matrícula</div>
        <div style="font-size: 12px;">{{ $tutoria->nombre }}</div>
    </div>

    <div class="footer">
        <table style="width: 100%;">
            <tr>
                <td style="text-align: left;">Sistema de Control Académico - Honduras</td>
                <td style="text-align: right;">Página <span class="pagenum"></span></td>
            </tr>
        </table>
    </div>

    <div class="content">
        <table class="info-table">
            <tr>
                <td><strong>Materia:</strong> {{ $tutoria->materia ?? 'N/A' }}</td>
                <td><strong>Sede:</strong> {{ $tutoria->sede->nombre ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td><strong>Fecha Reporte:</strong> {{ $fecha }}</td>
                <td><strong>Total Inscritos:</strong> {{ $matriculas->count() }}</td>
            </tr>
        </table>

        <table class="main-table">
            <thead>
                <tr>
                    <th style="width: 25px; text-align: center;">N°</th>
                    <th style="width: 90px;">DNI</th>
                    <th>Estudiante</th>
                    <th style="width: 35px; text-align: center;">Sexo</th>
                    <th style="width: 80px;">Teléfono</th>
                    <th>Tutor</th>
                </tr>
            </thead>
            <tbody>
                @php $contador = 1; @endphp
                @foreach($matriculas as $item)
                <tr>
                    <td style="text-align: center;">{{ $contador++ }}</td>
                    <td>{{ $item->estudiante->dni }}</td>
                    <td>{{ strtoupper($item->estudiante->nombre) }} {{ strtoupper($item->estudiante->apellido) }}</td>
                    <td class="{{ $item->estudiante->sexo == 'F' ? 'sexo-f' : 'sexo-m' }}" style="text-align: center;">
                        {{ $item->estudiante->sexo }}
                    </td>
                    <td>{{ $item->estudiante->telefono ?? '---' }}</td>
                    <td>{{ $item->estudiante->nombre_tutor ?? 'No tiene' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>