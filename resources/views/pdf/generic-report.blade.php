{{-- resources/views/pdf/generic-report.blade.php --}}
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Reporte {{ $table }} – {{ now()->format('Y-m-d H:i:s') }}</title>

    <style>
        :root {
            --dorado: rgb(163, 114, 35);
            --gris-borde: #CCCCCC;
            --texto-claro: #FFFFFF;
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

        /* cabecera */
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

        /* tabla genérica */
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
            border: 1px solid var(--gris-borde);
        }

        th {
            background: rgb(163, 114, 35);
            color: var(--texto-claro) !important;
            font-weight: 600;
            text-transform: uppercase;
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

    {{-- Cabecera: título + logo --}}
    <table class="cabecera">
        <tr>
            <td class="titulo">
                Reporte dinámico: <strong>{{ Str::headline($table) }}</strong><br>
                {{ now()->format('Y-m-d H:i:s') }}
            </td>
            <td class="logo">
                <img src="{{ public_path('img/conagua.png') }}" alt="Logo CONAGUA">
            </td>
        </tr>
    </table>

    {{-- Tabla dinámica --}}
    <table>
        <thead>
            <tr>
                <th style="width:30px">N°</th>
                @foreach ($columns as $col)
                <th>{{ Str::headline($col) }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($rows as $idx => $row)
            {{-- $row puede ser objeto stdClass o modelo; cast a array --}}
            @php $data = (array) $row; @endphp
            <tr>
                <td style="text-align:center">{{ $idx + 1 }}</td>
                @foreach ($columns as $col)
                <td>{{ $data[$col] ?? '' }}</td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>