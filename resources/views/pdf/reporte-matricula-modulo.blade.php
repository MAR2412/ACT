<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        @page {
            /* Aumentamos el margen superior para que quepa el header en todas las hojas */
            margin: 110px 50px 60px 50px;
        }

        body { 
            font-family: 'Helvetica', sans-serif; 
            font-size: 11px; 
            color: #333; 
            margin: 0;
        }
        
        /* El header se repetirá en cada página */
        .header { 
            position: fixed;
            top: -90px; /* Lo subimos al área del margen */
            left: 0;
            right: 0;
            height: 85px;
            text-align: center; 
            border-bottom: 2px solid #444; 
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
        
        .content { 
            width: 100%;
        }

        .info-table { 
            width: 100%; 
            margin-bottom: 15px; 
            border: 1px solid #eee; 
            padding: 10px; 
            border-radius: 5px;
        }
        
        .main-table { 
            width: 100%; 
            border-collapse: collapse; 
        }

        /* Crucial: Esto hace que los títulos de la tabla se repitan en cada hoja */
        .main-table thead { 
            display: table-header-group; 
        }

        .main-table th { 
            background-color: #f8f9fa; 
            border: 1px solid #ddd; 
            padding: 8px; 
            text-align: left; 
            font-size: 10px; 
        }

        .main-table td { 
            border: 1px solid #ddd; 
            padding: 6px; 
            page-break-inside: avoid; /* Evita que una fila se corte a la mitad */
        }

        .footer { 
            position: fixed; 
            bottom: -40px; 
            left: 0;
            right: 0;
            height: 30px;
            text-align: center; 
            font-size: 9px; 
            color: #999; 
            border-top: 1px solid #eee;
            padding-top: 5px;
        }

        .sexo-f { color: #d63384; font-weight: bold; }
        .sexo-m { color: #0d6efd; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <img class="header-logo-left" src="{{ public_path('Logo/ACT_Logo.png') }}">
        <img class="header-logo-right" src="{{ public_path('Logo/Mascota.png') }}">
        
        <div class="title">Listado de Matrícula por Módulo</div>
        <div style="font-size: 12px;">{{ $modulo->nombre }} ({{ $modulo->codigo }})</div>
    </div>

    <div class="footer">
        Sistema de Control Académico - Honduras - {{ date('Y') }} - Generado electrónicamente
    </div>

    <div class="content">
        <table class="info-table">
            <tr>
                <td><strong>Nivel:</strong> {{ $modulo->nivel }}</td>
                <td><strong>Sede:</strong> {{ $modulo->sede->nombre ?? 'N/A' }}</td>
                <td><strong>Modalidad:</strong> {{ $modulo->modalidad->nombre ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td><strong>Fecha Reporte:</strong> {{ $fecha }}</td>
                <td><strong>Duración:</strong> {{ $modulo->duracion_meses }} Mes(es)</td>
                <td><strong>Total Alumnos:</strong> {{ $matriculas->count() }}</td>
            </tr>
        </table>

        <table class="main-table">
            <thead>
                <tr>
                    <th style="width: 25px; text-align: center;">N°</th>
                    <th style="width: 90px;">DNI</th>
                    <th>Nombre del Estudiante</th>
                    <th style="width: 35px; text-align: center;">Sexo</th>
                    <th style="width: 80px;">Teléfono</th>
                    <th>Tutor Responsable</th>
                </tr>
            </thead>
            <tbody>
                @foreach($matriculas as $item)
                <tr>
                    <td style="text-align: center;">{{ $loop->iteration }}</td>
                    <td>{{ $item->estudiante->dni }}</td>
                    <td>{{ strtoupper($item->estudiante->nombre) }} {{ strtoupper($item->estudiante->apellido) }}</td>
                    <td class="{{ $item->estudiante->sexo == 'F' ? 'sexo-f' : 'sexo-m' }}" style="text-align: center;">
                        {{ $item->estudiante->sexo }}
                    </td>
                    <td>{{ $item->estudiante->telefono ?? '---' }}</td>
                    <td>{{ $item->estudiante->nombre_tutor ?? 'No registrado' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="footer">
        Sistema de Control Académico - Honduras - {{ date('Y') }} - Generado electrónicamente
    </div>
</body>
</html>