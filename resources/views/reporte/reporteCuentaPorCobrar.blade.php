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
                <td width="25%"><img src="{{ asset('assets/imagenes/logo.jpeg') }}" height="50"></td>

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
                        ESTADO DE CUENTAS POR COBRAR
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
        {{-- <table>
            <tbody>
                <tr>
                    <td>BIMESTRE</td>
                    <td>:</td>
                    <td>{{ $bimestre }}º Bimestre</td>
                </tr>
                <tr>
                    <td>ASIGNATURA</td>
                    <td>:</td>
                    <td>{{ $notapropuesta->asignatura->nombre }}</td>
                </tr>
                <tr>
                    <td>CURSO</td>
                    <td>:</td>
                    <td>{{ $notapropuesta->asignatura->gestion }}º Año</td>
                </tr>
                <tr>
                    <td>TURNO</td>
                    <td>:</td>
                    <td>{{ $notapropuesta->turno->descripcion }}</td>
                </tr>
                <tr>
                    <td>PARALELO</td>
                    <td>:</td>
                    <td>( {{ $notapropuesta->paralelo }} )</td>
                </tr>
                <tr>
                    <td>DOCENTE</td>
                    <td>:</td>
                    <td>{{ $notapropuesta->docente->apellido_paterno." ".$notapropuesta->docente->apellido_materno." ".$notapropuesta->docente->nombres }}</td>
                </tr>
            </tbody>
        </table> --}}

        <table class="notas">
            <thead>
                <tr>
                    <th>Nº</th>
                    <th class="textCentrado" width="50px">FECHA</th>
                    <th class="textCentrado" width="78px">CLIENTE</th>
                    <th class="textCentrado" width="70px">NIT</th>
                    <th class="textCentrado" width="108px">NOMBRE O RAZON SOCIAL</th>
                    <th class="textCentrado" width="400px" colspan="6">DETALLES</th>
                    {{-- <th class="textCentrado" width="200px">NOMBRE DEL SERVICIO</th>
                    <th class="textCentrado" width="50px">DOC VENTA</th>
                    <th class="textCentrado" width="50px">N FAC/REC</th>
                    <th class="textCentrado" width="50px">IMP. TOTAL</th>
                    <th class="textCentrado" width="50px">IMP. PAGADO</th>
                    <th class="textCentrado" width="50px">SAL. ACTUAL</th> --}}
                </tr>
            </thead>
            <tbody>
                @foreach($facturas as $key => $f)
                    <tr>
                       <td>{{ $key + 1 }}</td>
                       <td>{{ $f->fecha }}</td>
                       <td>{{ $f->cliente->nombres." ".$f->cliente->ap_paterno }}</td>
                       <td>{{ $f->nit }}</td>
                       <td>{{ $f->razon_social }}</td>
                       <td colspan="6">
                            <table class="notas">
                                <thead>
                                    <tr>
                                        <th width="100px">Servicio</th>
                                        <th width="50px">Doc. Venta</th>
                                        <th width="50px">Nro</th>
                                        <th width="50px">Imp Total</th>
                                        <th width="50px">Imp Pagado</th>
                                        <th width="50px">saldo</th>
                                    </tr>
                                </thead>
                            </table>
                        </td>
                       {{-- <td>{{ $f->id }}</td>
                       <td>{{ $f->id }}</td>
                       <td>{{ $f->id }}</td>
                       <td>{{ $f->id }}</td>
                       <td>{{ $f->id }}</td>
                       <td>{{ $f->id }}</td>  --}}
                    </tr>
                @endforeach
                {{-- @php
                    $contador  = 1;
                @endphp
                @foreach ($inscritos as $key => $inscrito )
                    @if ($inscrito->estado != "ABANDONO" && $inscrito->estado != "ABANDONO TEMPORAL" && $inscrito->estado != "CONGELADO")
                        <tr>
                            <td>{{ $contador }}</td>
                            <td>{{ $inscrito->apellido_paterno." ".$inscrito->apellido_materno." ".$inscrito->nombres }}</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        @php
                            $contador++;
                        @endphp
                    @endif
                @endforeach
                @for ($i = 1 ; $i <= 10; $i++)
                    <tr>
                        <td class="celdaVacia"></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                @endfor --}}
            </tbody>
        </table>

        <br>
        <table>
            <tbody>
                <tr>
                    <td><b>OBSERVACIONES</b></td>
                    <td><b>:</b></td>
                    <td>El registro de la presencia y/o ausencia de los alumnos se realizara segun las siguientes consideraciones:</td>
                </tr>
                <tr>
                    <td><b>ASISTENCIA</b></td>
                    <td><b>:</b></td>
                    <td>Registrar (A) dentro los 15 Min "Turno Mañana" y "Turno Tarde", 30 Min "Turno Noche"</td>
                </tr>
                <tr>
                    <td><b>FALTA</b></td>
                    <td><b>:</b></td>
                    <td>Registrar (F) Ausencia del alumno</td>
                </tr>
                <tr>
                    <td><b>PERMISO</b></td>
                    <td><b>:</b></td>
                    <td>Registrar (P) por una clase con autorizacion del Docente, mas de un dia, autorizacion con nota academica</td>
                </tr>
            </tbody>
        </table>
    </main>
</body>

</html>