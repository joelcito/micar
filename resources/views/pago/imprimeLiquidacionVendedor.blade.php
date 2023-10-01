<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Liquidacion</title>
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
            <b class="textoCentradoNegrita">LIQUIDACION VENDEDOR</b>                                                             <br>
            {{--  MICAELA QUIROZ ESCOBAR                                                      <br>  --}}
            CASA MATRIZ                                                                 <br>
            CALLE RIO ESPEJILLOS NRO.S/N ZONA VILLA FATIMA UV:0051 MZA:0049             <br>
            Tel. 73130500                                                               <br>
            Santa Cruz                                                                  <br>
            - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - <br>
            <b class="textoCentradoNegrita">LIQUIDACION N°</b> {{ $liquidacion_vendedor_pago->id }}<br>
            - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - <br>
            <table id="table_nuew_num_fac">
                <tr>
                    <td style="text-align: right; width: 150px"><b>LAVADOR:</b></td>
                    <td width="150px" style="text-align: left">
                        {{ $liquidacion_vendedor_pago->lavador->nombres." ".$liquidacion_vendedor_pago->lavador->ap_paterno." ".$liquidacion_vendedor_pago->lavador->ap_materno }}
                    </td>
                </tr>
                <tr>
                    <td style="text-align: right; width: 150px"><b>FECHA DE TRABAJO:</b></td>
                    <td width="100px" style="text-align: left">
                        <div>
                            @php
                                $fechaHora = $liquidacion_vendedor_pago->fecha_pagado;
                                $dateTime = new DateTime($fechaHora);
                                $formattedDateTime = $dateTime->format('d/m/Y');
                            @endphp
                            {{ $formattedDateTime }}
                        </div>
                    </td>
                </tr>
            </table>
            - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - <br>
            <b class="textoCentradoNegrita">DETALLE</b><br>

            <div>
                <table id="table_detalle">
                    <thead>
                        <tr>
                            {{--  <th>Prec.</th>  --}}
                            <th>Cant.</th>
                            {{--  <th>Mon. Lav.</th>  --}}
                            <th>Des.</th>
                            {{--  <th>%</th>  --}}
                            <th>Mont.</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $sumaTatalPagar = 0;
                        @endphp
                        @foreach ( $detalles as $d)
                            <tr>
                                @php
                                    $sw = true;

                                    // MONTO DEL LAVADO
                                    $montoLavado = $d->precio * $d->cantidad;

                                    // PARA PORCENTAJE Y PRECIO FINAL
                                    if ($d->tipoLiquidacionServicio == 'porcentaje'){
                                        $montoporcentaje = $d->liquidacionServicio;
                                        $montoFinal =   (float)($montoLavado * $montoporcentaje) / 100;
                                    }
                                    else if($d->tipoLiquidacionServicio == 'depende'){
                                        $montoporcentaje = $d->liquidacionLl;
                                        $montoFinal =   (float)($montoLavado * $montoporcentaje) / 100;
                                    }
                                    else{
                                        $montoporcentaje = $d->liquidacionServicio;
                                        $montoFinal =   (float)($d->cantidad * $d->liquidacionServicio);
                                        $sw = false;
                                    }

                                    // PARA EL TOTAL PAGAR
                                    $sumaTatalPagar += $montoFinal;
                                @endphp
                                {{--  <td>{{ $d->precio }}</td>  --}}
                                <td>{{ $d->cantidad }}</td>
                                {{--  <td>{{ number_format($montoLavado, 2) }}</td>  --}}
                                <td>{{ $d->descripcion }}</td>
                                {{--  <td>
                                    {{ $montoporcentaje }} {{ ($sw)? '%' : '' }}
                                </td>  --}}
                                <td>
                                    {{ number_format($montoFinal,2)  }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - <br>
             <table id="table_nuew_num_fac">
                <tr>
                    <td style="text-align: right; width: 150px">TOTAL SERVICIOS Bs</td>
                    <td width="150px" style="text-align: right">
                        {{ number_format( (float) $sumaTatalPagar,2)  }}
                    </td>
                </tr>
                <tr>
                    <td style="text-align: right; width: 150px">CUEN. POR COBRAR Bs</td>
                    <td width="100px" style="text-align: right">
                        {{ number_format( (float) $liquidacion_vendedor_pago->cuenta_por_pagar, 2) }}
                    </td>
                </tr>
                <tr>
                    <td style="text-align: right; width: 150px">LIQ. PAGABLE Bs</td>
                    <td width="100px" style="text-align: right">
                        {{ number_format( (float) ($sumaTatalPagar - $liquidacion_vendedor_pago->cuenta_por_pagar), 2) }}
                    </td>
                </tr>
            </table>
            <br>
            <div style="text-align: left">
                {{--  @php
                    $to = ((float) $factura->total);
                    $bolivianos = floor($to); // Parte entera del monto
                    $centavos = ($to - $bolivianos) * 100; // Convertir los decimales a centavos
                    $literal = ucfirst((new NumberFormatter('es', NumberFormatter::SPELLOUT))->format($bolivianos));
                @endphp
                <b>Son: {{ $literal }} @if ($centavos > 0){{ round($centavos) }}@else{{ '00' }}@endif/100 Bolivianos</b>  --}}
            </div>
            - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - <br>
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
