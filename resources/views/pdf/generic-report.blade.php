{{-- resources/views/pdf/generic-report.blade.php --}}
@php
use Illuminate\Support\Str;
@endphp
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>
        {{ Str::title(Str::replace('_',' ', $table)) }}
        {{ $anio ? "($anio)" : '' }} – {{ now()->format('Y-m-d H:i:s') }}
    </title>

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

        /* Cabecera */
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

        /* Tabla de datos */
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
        }

        th {
            background: var(--dorado);
            color: #FFF;
            font-weight: 600;
            text-transform: uppercase;
            border: 1px solid var(--dorado);
        }

        tbody tr:nth-child(odd) {
            background: #F4F6F9;
        }

        tbody tr:nth-child(even) {
            background: #FFF;
        }
    </style>
</head>

<body>
    {{-- Cabecera --}}
    <table class="cabecera">
        <tr>
            <td class="titulo">
                {{ Str::title(Str::replace('_',' ', $table)) }}<br>
                {{ $anio ? "Año $anio – " : '' }}{{ now()->format('Y-m-d H:i:s') }}
                @isset($total)
                <br><small>Registros: {{ $total }}{{ $limit && $total > $limit ? " (mostrando $limit)" : '' }}</small>
                @endisset
            </td>
            <td class="logo">
                <img src="{{ public_path('img/conagua.png') }}" alt="Logo CONAGUA">
            </td>
        </tr>
    </table>

    {{-- Tabla dinámica --}}
    @if($rows->isEmpty())
    <p style="margin-top:24px;text-align:center;">Sin registros para los criterios seleccionados.</p>
    @else
    <table>
        <thead>
            <tr>
                <th style="width:30px">N°</th>
                @foreach($columns as $col)
                <th>{{ Str::upper(Str::replace('_',' ', $col)) }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $idx => $row)
            <tr>
                <td style="text-align:center">{{ $idx + 1 }}</td>
                @foreach($columns as $c)
                @php $v = $row->$c; @endphp
                <td>
                    {{ $v instanceof \Carbon\Carbon
                                    ? $v->format('Y-m-d H:i:s')
                                    : (is_bool($v) ? ($v ? 'Sí' : 'No') : ($v ?? '')) }}
                </td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</body>

</html>