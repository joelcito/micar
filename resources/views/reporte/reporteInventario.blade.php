<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reporte</title>
    <style>
        @page {
            margin: 0cm 0cm;
            font-family: Arial;
        }

        body {
            margin: 1cm 1cm 2cm;
            font-size: 6pt;
            font-family: Arial, Helvetica, sans-serif;
        }

        header {
            position: fixed;
            top: 0cm;
            left: 0cm;
            right: 0cm;
            height: 0cm;
            background-color: #ff0000;
            color: black;
            text-align: center;
            line-height: 5px;
        }

        /*body {
            margin: 3cm 2cm 2cm;
        }*/

        footer {
            position: fixed;
            bottom: 0cm;
            left: 1cm;
            right: 1cm;
            height: 1cm;
            background-color: #fff;
            color: black;
            text-align: center;
            line-height: 35px;
        }

        table.notas {
            /* width: 100%; */
            background-color: #fff;
            /* border: 1px solid; */
            border-collapse: collapse;
        }

        .notas th,
        .notas td {
            border: 1px solid #000000;
            padding: 2px;
            /* text-align: left; */
        }

        .textCentrado{
            text-align: center;
        }
        .celdaVacia{
            padding: 5px;
            height: 10px;
        }
    </style>
</head>

<body>
    <header>

        {{-- @dd($servicios[0]->movimientos->where('fecha', '2024-01-07')->sum('ingreso')) --}}

    </header>
    <main>
        <table width="100%">
            <tr>
                <td width="25%"><img src="{{ asset('assets/imagenes/logo.jpeg') }}" height="80"></td>

                <td width="25%" style="text-align: right;">
                    <span style="font-size: 13px;">
                        MICAR AUTOLAVADO
                    </span>
                    <br>
                    <span style="font-size: 8pt;">
                        DESDE: {{ $fecha_ini }}
                    </span>
                    <span style="font-size: 8pt;">
                        AL: {{ $fecha_fin }}
                    </span>
                </td>
            </tr>

            <tr>
                <td colspan="2" style="text-align: center; border-top:#000000 solid 1px; border-bottom: #000000 solid 1px;">
                    <span style="font-size: 15pt;">
                        REPORTE DE INVENTARIO INGRESOS Y SALIDAS
                    </span>
                </td>
            </tr>
        </table>
        <table class="notas">
            <thead>
                <tr>
                    <th>Nº</th>
                    <th class="textCentrado" width="225px">DESCRIPCION</th>
                    <th class="textCentrado" width="60px">ALMACEN</th>
                    <th class="textCentrado" width="60px">INGRESO</th>
                    <th class="textCentrado" width="60px">SALIDAS</th>
                    <th class="textCentrado" width="60px">SALDO ACTUAL</th>
                    <th class="textCentrado" width="60px">PRECIO VENTA</th>
                    <th class="textCentrado" width="60px">PRECIO VENTA TOAL</th>
                    {{-- <th class="textCentrado" width="108px">OBSERVACION</th> --}}
                </tr>
            </thead>
            <tbody>
                @php
                    $totalImporte = 0;
                    $totalPagado  = 0;
                    $totalSaldo   = 0;
                @endphp
                @foreach ($servicios as $s)
                <tr>
                    <td>{{ $s->id }}</td>
                    <td>{{ $s->descripcion }}</td>
                    <td>
                        @php
                            $ingresoFecha = $s->movimientos->whereBetween('fecha', [$fecha_ini." 00:00:00", $fecha_fin." 23:59:59"])
                                                            ->sum('ingreso');
                            $salidaFecha = $s->movimientos->whereBetween('fecha', [$fecha_ini." 00:00:00", $fecha_fin." 23:59:59"])
                                                            ->sum('salida');

                            $ingresoFechaNot = $s->movimientos->where('fecha', '<',$fecha_ini)
                                                                ->whereNotBetween('fecha', [$fecha_ini." 00:00:00", $fecha_fin." 23:59:59"])
                                                                ->sum('ingreso');
                            $salidaFechaNot = $s->movimientos->where('fecha', '<',$fecha_ini)
                                                                ->whereNotBetween('fecha', [$fecha_ini." 00:00:00", $fecha_fin." 23:59:59"])
                                                                ->sum('salida');
                        @endphp
                        {{ $ingresoFechaNot - $salidaFechaNot }}
                    </td>
                    <td>{{ $ingresoFecha }}</td>
                    <td>{{ $salidaFecha }}</td>
                    <td>{{ ($ingresoFecha - $salidaFecha) + ($ingresoFechaNot - $salidaFechaNot) }}</td>
                    <td>{{ $s->precio }}</td>
                    <td>{{ (int)($ingresoFecha - $salidaFecha) + ($ingresoFechaNot - $salidaFechaNot) * (int)$s->precio }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </main>
</body>

</html>
