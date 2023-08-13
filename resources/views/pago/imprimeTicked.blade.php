<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Factura</title>
	<style type="text/css">
		@media print {
		  #btnImprimir {
		    display: none;
		  }
		}

		#botonImpresion {
		    background: #17aa56;
		    color: #fff;
		    border-radius: 7px;
		    /box-shadow: 0 5px #119e4d;/
		    padding: 15px;
		}

		body{
				font-family: Arial, Helvetica, sans-serif;
				}

        .facturaPequena{
            font-size: 9pt;
        }

        .textoCentrado{
            text-align: center;
        }

        .textoCentradoNegrita{
            text-align: center;
            font-weight: 800;
        }

		#fondo{
			/background-image: url("{{ asset('assets/images/factura_szone.jpg') }}");/
			/* width: 892px; */
			/* height: 514px; */
		}

		#tablaProductos{
			font-size: 8pt;
			position: absolute;
			top: 230px;
			left: 0px;
			/* width: 718px; */
		}

		#codigoControlQr{
			font-size: 8pt;
			/* position: relative; */
			/*top: 230px;
			left: 0px;*/
			/* width: 718px; */
		}

        .estatico{
            width: 300px;
            height: 50px;
            word-wrap: break-word;
        }


		/estilos para tablas de datos/
        table.datos {
            /font-size: 13px;/
            /line-height:14px;/
            /* width: 1000; */
            border-collapse: collapse;
            background-color: #fff;
        }
        .datos th {
          height: 10px;
          background-color: #fefefe;
          color: #000;
        }
        .datos td {
          height: 12px;
        }
        .datos th, .datos td {
          border: 2px solid #000;
          padding: 2px;
          text-align: center;
        }
        .datos tr:nth-child(even) {background-color: #f2f2f2;}
		#literalTotal{
			font-size: 8pt;
		}

		#datosEmpresaNit{
			/* font-weight: bold; */
			font-size: 10pt;
			position: absolute;
			top: 0px;
			left: 595px;
			padding: 10px;
			border: 1px solid black;
		}

		#datosEmpresaFactura{
			/* font-weight: bold; */
			font-size: 10pt;
			position: absolute;
			top: 180px;
			left: 0px;
			padding: 5px;
			/border: 1px solid black;/
			width: 891px;
		}

		#txtOriginal{
			font-weight: bold;
			font-size: 12pt;
			position: absolute;
			top: 85px;
			left: 670px;
			width: 150px;
			text-align: center;
		}

		#txtActividad{
			/* font-weight: bold; */
			font-size: 6pt;
			position: absolute;
			top: 110px;
			left: 600px;
			width: 280px;
			text-align: left;
		}

		#txtFactura{
			font-weight: bold;
			font-size: 19pt;
			position: absolute;
			top: 140px;
			left: 350px;
			width: 150px;
			text-align: center;
		}

		#logo{
			position: absolute;
			top: 20px;
			left: 50px;
		}

		#direccionEmpresa{
			font-weight: bold;
			font-size: 6pt;
			position: absolute;
			top: 85px;
			left: 20px;
			width: 220px;
			text-align: center;
		}

        #table_nuew_num_fac{
            /* position: absolute; */
            /* right: 20px;
            top: 20px; */
            font-size: 12px;
            width: 300px;
        }

        #table_detalle{
            width: 300px;
        }

        #btonVolver{
            background-color: black;
            color: white;
            padding: 3px 3px;
            border: 1px solid #ff1500 ;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            cursor: pointer;
        }

	</style>
</head>
<body>
    <div style="width: 302px" class="facturaPequena">

        <div class="textoCentrado">
            <b class="textoCentradoNegrita">TICKET</b>                                                             <br>
            MICAELA QUIROZ ESCOBAR                                                      <br>
            CASA MATRIZ                                                                 <br>
            CALLE RIO ESPEJILLOS NRO.S/N ZONA VILLA FATIMA UV:0051 MZA:0049             <br>
            Tel. 73130500                                                               <br>
            Santa Cruz                                                                  <br>
            {{--  - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - <br>  --}}
           {{--  <b class="textoCentradoNegrita">RECIBO N°</b>                                                           <br>  --}}
            {{--  @dd($pagos)  --}}
            {{--  {{ $factura->numero_recibo }}                                               <br>  --}}
            - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - <br>
            <table id="table_nuew_num_fac">
                <tr>
                    <td style="text-align: right; width: 150px"><b>SEÑORES:</b></td>
                    <td width="150px" style="text-align: left">
                        {{ $pagos[0]->vehiculo->cliente->nombres." ".$pagos[0]->vehiculo->cliente->ap_paterno." ".$pagos[0]->vehiculo->cliente->ap_materno }}
                    </td>
                </tr>
                <tr>
                    <td style="text-align: right; width: 150px"><b>PLACA:</b></td>
                    <td width="150px" style="text-align: left">
                        {{ $pagos[0]->vehiculo->placa }}
                    </td>
                </tr>
                <tr>
                    <td style="text-align: right; width: 150px"><b>FECHA DE EMISION:</b></td>
                    <td width="100px" style="text-align: left">
                        <div>
                            @php
                                $fechaHora = $pagos[0]->created_at;
                                $dateTime = new DateTime($fechaHora);
                                $formattedDateTime = $dateTime->format('d/m/Y h:i A');
                            @endphp
                            {{ $formattedDateTime }}
                        </div>
                    </td>
                </tr>
            </table>
            - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - <br>
            <b class="textoCentradoNegrita">SERVICIOS</b><br>
            <div>
                <table id="table_detalle">
                    <thead>
                        <tr>
                            <th>CANT</th>
                            <th>DESCRIPCION</th>
                            <th>P.UNIT.</th>
                            <th>P.TOTAL</th>
                        </tr>
                    </thead>
                    @php
                        $total = 0;
                        $pagosListado = $pagos;
                        $subTotales = 0;
                    @endphp
                    @foreach($pagosListado as $key => $pago)
                        @php
                            $subTotales += (float) $pago->importe;
                        @endphp
                        <tr>
                            <td>{{ $pago->cantidad }}</td>
                            <td>{{ $pago->servicio->descripcion }}</td>
                            <td>{{ $pago->precio }}</td>
                            <td>{{ $pago->importe }}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
            - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - <br>
             {{--  <table id="table_nuew_num_fac">
                <tr>
                    <td style="text-align: right; width: 150px">SUBTOTAL Bs</td>
                    <td width="150px" style="text-align: right">
                        {{  number_format( $subTotales, 2) }}
                    </td>
                </tr>
                <tr>
                    <td style="text-align: right; width: 150px">DESCUENTO Bs</td>
                    <td width="100px" style="text-align: right">
                        {{ number_format( (float) $factura->descuento_adicional, 2) }}
                    </td>
                </tr>
                <tr>
                    <td style="text-align: right; width: 150px">TOTAL Bs</td>
                    <td width="100px" style="text-align: right">
                        {{ number_format( (float) $factura->total, 2) }}
                    </td>
                </tr>
            </table>
            <br>
            <div style="text-align: left">
                @php
                    $to = ((float) $factura->total);
                    $bolivianos = floor($to); // Parte entera del monto
                    $centavos = ($to - $bolivianos) * 100; // Convertir los decimales a centavos
                    $literal = ucfirst((new NumberFormatter('es', NumberFormatter::SPELLOUT))->format($bolivianos));
                @endphp
                <b>Son: {{ $literal }} @if ($centavos > 0){{ round($centavos) }}@else{{ '00' }}@endif/100 Bolivianos</b>
            </div>  --}}


        <div>
            <center>
                <div id="btnImprimir">
                    <input type="button" id="botonImpresion" value="IMPRIMIR" onClick="window.print()">
                    <a id="btonVolver" href="{{ URL::previous() }}" class="btn btn-primary btn-dark">VOLVER</a>
                </div>
            </center>
        </div>

    </div>
</body>
</html>
