<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Control de incidencias {{ now()->format('Y-m-d H:i:s') }}</title>

    <style>
        :root {
            --dorado: #B89356;
            --gris-borde: #CCCCCC;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: "Helvetica Neue", Arial, sans-serif;
            font-size: 12px;
            margin: 18px 28px;
            color: #000;
        }

        /* -------- cabecera (usa tabla, no flex) -------- */
        .cabecera {
            width: 100%;
        }

        .cabecera td {
            vertical-align: top;
        }

        .titulo {
            font-size: 20px;
            font-weight: 600;
            line-height: 1.3;
        }

        .logo {
            text-align: right;
        }

        .logo img {
            height: 70px;
        }

        /* -------- tabla de datos -------- */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }

        th,
        td {
            font-size: 11px;
            line-height: 1.2;
            padding: 4px 6px;
        }

        td {
            border: 1px solid var(--gris-borde);
            padding: 4px 6px;
        }

        th {
            background: #B89356;
            /* dorado corporativo */
            color: #FFFFFF;
            /* texto blanco */
            font-weight: 600;
            font-size: 11px;
            text-transform: uppercase;
            border: 1px solid var(--dorado);
        }

        tbody tr:nth-child(odd) {
            background: #F4F6F9;
        }

        tbody tr:nth-child(even) {
            background: #FFFFFF;
        }
    </style>
</head>

<body>
    {{-- Cabecera con tabla: título izquierda, logo derecha --}}
    <table class="cabecera">
        <tr>
            <td class="titulo">
                Control de incidencias<br>
                {{ now()->format('Y-m-d H:i:s') }}
            </td>
            <td class="logo">
                <img src="{{ public_path('img/conagua.png') }}" alt="Logo CONAGUA">
            </td>
        </tr>
    </table>

    {{-- Tabla de incidencias --}}
    <table>
        <thead>
            <tr>
                <th style="width:30px">N°</th>
                <th>Folio</th>
                <th>Solicitante</th>
                <th>Contacto</th>
                <th>Tipo</th>
                <th>Estatus</th>
                <th style="width:95px">Fecha</th>
            </tr>
        </thead>
        <tbody>
            @foreach($incidencias as $idx => $inc)
            <tr>
                <td style="text-align:center">{{ $idx+1 }}</td>
                <td>{{ $inc->unique_code }}</td>
                <td>{{ $inc->report->full_name }}</td>
                <td>
                    @php
                    $c = $inc->report->contact;
                    echo trim($c->phone.' '.$c->email);
                    @endphp
                </td>
                <td>{{ $inc->incidentType->name }}</td>
                <td>{{ $inc->incidentStatus->name }}</td>
                <td>{{ $inc->created_at->format('Y-m-d H:i:s') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>