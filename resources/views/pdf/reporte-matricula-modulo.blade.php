<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #444; margin-bottom: 15px; padding-bottom: 10px; }
        .title { font-size: 16px; font-weight: bold; text-transform: uppercase; }
        .info-table { width: 100%; margin-bottom: 15px; border: 1px solid #eee; padding: 10px; border-radius: 5px; }
        .main-table { width: 100%; border-collapse: collapse; }
        .main-table th { background-color: #f8f9fa; border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 10px; text-transform: uppercase; }
        .main-table td { border: 1px solid #ddd; padding: 6px; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 9px; color: #999; }
        .sexo-f { color: #d63384; font-weight: bold; }
        .sexo-m { color: #0d6efd; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Listado de Matrícula por Módulo</div>
        <div>{{ $modulo->nombre }} ({{ $modulo->codigo }})</div>
    </div>

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
                <th style="width: 20px;">N°</th>
                <th style="width: 80px;">DNI</th>
                <th>Nombre del Estudiante</th>
                <th style="width: 35px;">Sexo</th>
                <th style="width: 70px;">Teléfono</th>
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

    <div class="footer">
        Sistema de Control Académico - Honduras - {{ date('Y') }} - Página 1
    </div>
</body>
</html>