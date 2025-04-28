<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Incidencias {{ $anio }}</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1em;
        }

        th,
        td {
            border: 1px solid #444;
            padding: 4px;
        }

        th {
            background: #eee;
        }
    </style>
</head>

<body>
    <h1>Incidencias Año {{ $anio }}</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Código</th>
                <th>Estado</th>
                <th>Descripción</th>
                <th>Tipo</th>
                <th>Geom.</th>
                <th>Coords.</th>
                <th>Reporta</th>
                <th>Email</th>
                <th>Lat</th>
                <th>Lon</th>
                <th>Fecha CREA</th>
            </tr>
        </thead>
        <tbody>
            @foreach($incidencias as $i)
            <tr>
                <td>{{ $i->id }}</td>
                <td>{{ $i->unique_code }}</td>
                <td>{{ $i->incidentStatus->name }}</td>
                <td>{{ $i->description }}</td>
                <td>{{ $i->incidentType->name }}</td>
                <td>{{ $i->location->type }}</td>
                <td>{{ json_encode($i->location->coordinates) }}</td>
                <td>{{ $i->report->full_name }}</td>
                <td>{{ $i->report->contact->email }}</td>
                <td>{{ $i->location->lat }}</td>
                <td>{{ $i->location->lon }}</td>
                <td>{{ $i->created_at->format('Y-m-d') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>