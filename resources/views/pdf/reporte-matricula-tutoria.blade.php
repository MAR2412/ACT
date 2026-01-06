<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #444; margin-bottom: 20px; padding-bottom: 10px; }
        .title { font-size: 18px; font-weight: bold; text-transform: uppercase; }
        .info-table { width: 100%; margin-bottom: 20px; }
        .main-table { width: 100%; border-collapse: collapse; }
        .main-table th { background-color: #f5f5f5; border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 11px; }
        .main-table td { border: 1px solid #ddd; padding: 6px; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 10px; color: #777; }
        .sexo-f { color: #d63384; font-weight: bold; }
        .sexo-m { color: #0d6efd; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Listado Oficial de Matrícula</div>
        <div>{{ $tutoria->nombre }}</div>
    </div>

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
                <th>N°</th>
                <th>DNI</th>
                <th>Estudiante</th>
                <th>Sexo</th>
                <th>Teléfono</th>
                <th>Tutor</th>
            </tr>
        </thead>
        <tbody>
            @php $contador = 1; @endphp
            @foreach($matriculas as $item)
            <tr>
                <td>{{ $contador++ }}</td>
                <td>{{ $item->estudiante->dni }}</td>
                <td>{{ strtoupper($item->estudiante->nombre) }} {{ $item->estudiante->apellido }}</td>
                <td class="{{ $item->estudiante->sexo == 'F' ? 'sexo-f' : 'sexo-m' }}">
                    {{ $item->estudiante->sexo }}
                </td>
                <td>{{ $item->estudiante->telefono ?? '---' }}</td>
                <td>{{ $item->estudiante->nombre_tutor ?? 'No tiene' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Generado automáticamente por el Sistema de Control Académico - {{ date('Y') }}
    </div>
</body>
</html>