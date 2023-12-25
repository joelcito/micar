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
                    <th>Nº</th>
                    <th class="textCentrado" width="20px">FECHA VENTA</th>
                    <th class="textCentrado" width="20px">NUN FAC/REC</th>
                    <th class="textCentrado" width="20px">FECHA FACT</th>
                    <th class="textCentrado" width="20px">NOM CLIENTE</th>
                    <th class="textCentrado" width="20px">NIT</th>
                    <th class="textCentrado" width="20px">RAZON SOCIAL</th>
                    <th class="textCentrado" width="20px">IMP TOTAL</th>
                    <th class="textCentrado" width="20px">IMP PAGADO</th>
                    <th class="textCentrado" width="20px">TOT SALDO</th>
                    <th class="textCentrado" width="20px">RESPONSABLES</th>
                    <th class="textCentrado" width="20px">USU REGISTRO</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalImporte = 0;
                    $totalPagado  = 0;
                    $totalSaldo   = 0;
                @endphp
                @foreach ( $ventas as $v)
                    <tr>
                        <td>{{ $v->id }}</td>
                        <td>{{ $v->id }}</td>
                        <td>{{ $v->id }}</td>
                        <td>{{ $v->id }}</td>
                        <td>{{ $v->id }}</td>
                        <td>{{ $v->id }}</td>
                        <td>{{ $v->id }}</td>
                        <td>{{ $v->id }}</td>
                        <td>{{ $v->id }}</td>
                        <td>{{ $v->id }}</td>
                        <td>{{ $v->id }}</td>
                        <td>{{ $v->id }}</td>
                    </tr>
                @endforeach
                {{-- @foreach($facturas as $key => $f)
                    <tr>
                       <td>{{ $key + 1 }}</td>
                       <td>{{ $f->fecha }}</td>
                       <td>{{ $f->cliente->nombres." ".$f->cliente->ap_paterno }}</td>
                       <td>{{ $f->nit }}</td>
                       <td>{{ $f->razon_social }}</td>
                       <td>
                            @foreach ( $f->detalles as $det )
                                @php
                                    $servicio = App\Models\Servicio::find($det->servicio_id);
                                    echo " [ ".$servicio->descripcion." ] ";
                                @endphp
                            @endforeach
                       </td>
                       <td colspan="5">
                            <table class="notas">
                                <thead>
                                    <tr>
                                        <th width="60px">Nro Fac / Rec</th>
                                        <th width="60px">Imp Total</th>
                                        <th width="60px">Imp Pagado</th>
                                        <th width="60px">saldo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $pagos      = App\Models\Pago::where('factura_id', $f->id)->get();
                                        $num        = ($f->facturado == "Si")? "Factura: ".$f->numero:"Recibo: ".$f->numero_recibo;
                                        $pagado     = App\Models\Pago::where('factura_id', $f->id)->sum('monto');
                                        $sumMontos  = 0;
                                    @endphp
                                    @foreach ($pagos as $p)
                                        <tr>
                                            <td>
                                                {{ $num }}
                                            </td>
                                            <td>{{ number_format($f->total, 2) }}</td>
                                            <td>{{ number_format($p->monto, 2) }}</td>
                                            <td>{{ number_format(((int)$f->total - (int) ($p->monto + $sumMontos) ), 2) }}</td>
                                        </tr>
                                        @php
                                            $sumMontos+=$p->monto;
                                        @endphp
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="2">TOTAL :{{ number_format($f->total, 2) }}</th>
                                        <th>PAGADO {{ number_format($sumMontos, 2) }}</th>
                                        <th>SALDO {{ number_format(((int)$f->total - (int)$pagado), 2) }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </td>
                    </tr>
                    @php
                        $totalImporte += $f->total;
                        $totalPagado  += $sumMontos;
                        $totalSaldo   += (int)$f->total - (int)$pagado;
                    @endphp
                @endforeach --}}
            </tbody>
            {{-- <tfoot>
                <tr>
                    <th colspan="6">TOTAL</th>
                    <th colspan="5">
                        <table class="notas">
                            <thead>
                                <tr>
                                    <th width="80px">TOTAL IMPROTE</th>
                                    <th width="80px">TOTAL PAGADO</th>
                                    <th width="80px">TOTAL SALDO</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ number_format($totalImporte , 2) }}</td>
                                    <td>{{ number_format($totalPagado , 2) }}</td>
                                    <td>{{ number_format($totalSaldo , 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </th>
                </tr>
            </tfoot> --}}
        </table>
    </main>
</body>

</html>
