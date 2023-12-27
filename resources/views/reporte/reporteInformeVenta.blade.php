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

                    </span>
                    <span style="font-size: 8pt;">
                        FECHA: {{ date('d/m/Y') }}
                    </span>
                </td>
            </tr>

            <tr>
                <td colspan="2" style="text-align: center; border-top:#000000 solid 1px; border-bottom: #000000 solid 1px;">
                    <span style="font-size: 15pt;">
                        REPORTE INFORME DE VENTAS
                    </span>
                </td>
            </tr>
            <tr>
                <td style="text-align: center; border-top:#000000 solid 1px; border-bottom: #000000 solid 1px;">
                    <span style="font-size: 10pt;">
                        Desde: {{ $fecha_ini }}
                    </span>
                </td>
                <td style="text-align: center; border-top:#000000 solid 1px; border-bottom: #000000 solid 1px;">
                    <span style="font-size: 10pt;">
                        Hasta: {{ $fecha_fin }}
                    </span>
                </td>
            </tr>

        </table>
        <table class="notas">
            <thead>
                <tr>
                    <th width="25px">Nº</th>
                    <th class="textCentrado" width="50px">FECHA VENTA</th>
                    <th class="textCentrado" width="30px">NUN FAC/REC</th>
                    <th class="textCentrado" width="50px">FECHA FACT</th>
                    <th class="textCentrado" width="100px">NOM CLIENTE</th>
                    <th class="textCentrado" width="60px">NIT</th>
                    <th class="textCentrado" width="60px">RAZON SOCIAL</th>
                    <th class="textCentrado" width="40px">IMP TOTAL</th>
                    <th class="textCentrado" width="40px">IMP PAGADO</th>
                    <th class="textCentrado" width="40px">TOT SALDO</th>
                    <th class="textCentrado" width="60px">RESPONSABLES/LAVADOR</th>
                    <th class="textCentrado" width="60px">USU REGISTRO</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalImporte = 0;
                    $totalPagado  = 0;
                    $totalSaldo   = 0;
                @endphp
                @foreach ( $ventas as $v)
                    @php
                        $dif = $v->total - $v->pagos->sum('monto');
                    @endphp
                    <tr>
                        <td>{{ $v->id }}</td>
                        <td>{{ $v->fecha }}</td>
                        <td>{{ $v->facturado == 'Si' ? "Fac. ".$v->numero  : "Rec. ".$v->numero_recibo }}</td>
                        <td>{{ $v->fecha }}</td>
                        <td>{{ $v->cliente->nombres." ".$v->cliente->ap_paterno." ".$v->cliente->ap_materno }}</td>
                        <td>{{ $v->nit }}</td>
                        <td>{{ $v->razon_social }}</td>
                        <td>{{ number_format($v->total, 2) }}</td>
                        <td>{{ number_format($v->pagos->sum('monto'), 2) }}</td>
                        <td>{{ number_format($dif,2) }}</td>
                        <td>
                            @foreach ($v->detalles  as $det)
                                {{ "[ ".$det->lavador->nombres." ".$det->lavador->ap_paterno." ".$det->lavador->ap_materno." ]" }} <br>
                            @endforeach
                        </td>
                        <td>{{ $v->creador->name }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </main>
</body>
</html>
