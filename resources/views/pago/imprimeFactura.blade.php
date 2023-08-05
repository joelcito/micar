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

	</style>
	<script src="{{ asset('js/NumeroALetras.js') }}"></script>
	<script src="{{ asset('dist/js/qrcode.min.js') }}"></script>
</head>
<body>
@php
	// function fechaCastellano ($fecha) {
    //     $fecha = substr($fecha, 0, 10);
    //     $numeroDia = date('d', strtotime($fecha));
    //     $dia = date('l', strtotime($fecha));
    //     $mes = date('F', strtotime($fecha));
    //     $anio = date('Y', strtotime($fecha));
    //     $dias_ES = array("Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo");
    //     $dias_EN = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
    //     $nombredia = str_replace($dias_EN, $dias_ES, $dia);
    //     $meses_ES = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre",
    //     "Noviembre", "Diciembre");
    //     $meses_EN = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October",
    //     "November", "December");
    //     $nombreMes = str_replace($meses_EN, $meses_ES, $mes);
    //     return $numeroDia." de ".$nombreMes." de ".$anio;
	// }
@endphp

    {{--  @dd(auth()->user()->almacen_id, auth()->user()->name)  --}}

    {{-- @if (auth()->user()->almacen_id == 2 || auth()->user()->almacen_id == 12 || auth()->user()->almacen_id == 3) --}}
    @if (true)

        <div style="width: 302px" class="facturaPequena">

            <div class="textoCentrado">
                <b class="textoCentradoNegrita">FACTURA </b>                                                             <br>
                <b class="textoCentradoNegrita">CON DERECHO A CREDITO FISCAL </b>                                        <br>
                {{ $archivoXML->cabecera->razonSocialEmisor }}                              <br>
                CASA MATRIZ                                                                 <br>
                No. Punto de Venta {{ $archivoXML->cabecera->codigoPuntoVenta }}            <br>
                {{ $archivoXML->cabecera->direccion }}                                      <br>
                Tel. {{ $archivoXML->cabecera->telefono }}                                  <br>
                {{ $archivoXML->cabecera->municipio }}                                      <br>
                - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - <br>
                <b class="textoCentradoNegrita">NIT</b>                                                                  <br>
                {{ $archivoXML->cabecera->nitEmisor }}                                      <br>
                <b class="textoCentradoNegrita">FACTURA N°</b>                                                           <br>
                {{ $archivoXML->cabecera->numeroFactura }}                                  <br>
                <b class="textoCentradoNegrita">CÓD. AUTORIZACIÓN</b>                                                    <br>
                <div class="estatico">
                    {{ $archivoXML->cabecera->cuf }}
                </div>                                                                      
                - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - <br>
                <table id="table_nuew_num_fac">
                    <tr>
                        <td style="text-align: right; width: 150px"><b>NOMBRE/RAZÓN SOCIAL:</b></td>
                        <td width="150px" style="text-align: left">
                            {{ $archivoXML->cabecera->nombreRazonSocial }}
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: right; width: 150px"><b>NIT/CI/CEX:</b></td>
                        <td width="100px" style="text-align: left">
                            {{ $archivoXML->cabecera->numeroDocumento }}
                            @if (!empty($archivoXML->cabecera->complemento))
                                - {{ $archivoXML->cabecera->complemento }}
                            @endif    
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: right; width: 150px"><b>COD. CLIENTE:</b></td>
                        <td width="100px" style="text-align: left">
                            {{ $archivoXML->cabecera->numeroDocumento }}
                            @if (!empty($archivoXML->cabecera->complemento))
                                - {{ $archivoXML->cabecera->complemento }}
                            @endif  
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: right; width: 150px"><b>FECHA DE EMISION:</b></td>
                        <td width="100px" style="text-align: left">
                            <div>
                                @php
                                    $fechaHora = $archivoXML->cabecera->fechaEmision;
                                    $dateTime = new DateTime($fechaHora);
                                    $formattedDateTime = $dateTime->format('d/m/Y h:i A');
                                @endphp
                                {{ $formattedDateTime }}
                            </div>
                        </td>
                    </tr>
                </table>
                - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - <br>
                <b class="textoCentradoNegrita">DETALLE</b><br>
                <table>
                    <thead>
                        <th>
                            <td>N°</td>
                            <td>P/U</td>
                            <td>CANT</td>
                            <td>DAE</td>
                        </th>
                    </thead>
                </table>







                {{-- {{ $datosEmpresa->nombre }}<br /><br />
                {{ $datosEmpresa->direccion }} --}}
                
                DATOS EMPRESA<br /><br />
                DIRECCION EMPRESA
            </div>
            <p class="textoCentradoNegrita">FACTURA ORIGINAL</p>
            <hr />

            {{-- <div class="textoCentradoNegrita">NIT: {{ $datosEmpresa->nit }}</div> --}}
            <div class="textoCentradoNegrita">NIT: NIT ENPRESA</div>
            {{-- <div class="textoCentradoNegrita">FACTURA N&deg;: {{ $datosFactura->numero_factura }}</div> --}}
            <div class="textoCentradoNegrita">FACTURA N&deg;: NUM FACTURA</div>
            <div class="textoCentrado">N&deg; AUTORIZACION: DATOA UTORIZACION</div>

            <hr />
            {{-- <div class="textoCentrado">{{ $datosEmpresa->actividad }}</div> --}}
            <div class="textoCentrado">ACTIVIODAD</div>
            <br />

            {{-- <div>Fecha: {{ $datosEmpresa->ciudad }}, {{ fechaCastellano($datosVenta->fecha) }}</div> --}}
            <div>Fecha: CIUDAD EMPRESAA, FECHA</div>
            {{-- <div>Se&ntilde;or(es): {{ $datosVenta->cliente->razon_social }}</div> --}}
            <div>Se&ntilde;or(es): RAZON SOCIAL</div>
            {{-- <div>NIT/CI: {{ $datosFactura->nit_cliente }}</div> --}}
            <div>NIT/CI: NIT CLIENTE</div>

            <div>
                <table class="datos" width="302">
                    <thead>
                        <tr>
                            <th>CANT.</th>
                            <th>DESCRIPCION</th>
                            <th>P/U</th>
                            <th>SUBTOTAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $sumaSubTotal = 0;
                        @endphp
                        {{-- @foreach ($productosVenta as $con => $pv)
                        <tr>
                            <td style="text-align: right;" width="100px">
                                <span class="text-info"><b>{{ ($pv->precio_cobrado_mayor>0)?$pv->escala->nombre:"" }}</b></span>
                                <span class="text-success"><b>{{ ($pv->combo_id != null)?$pv->combo->nombre:"" }}</b></span>
                                &nbsp;&nbsp;&nbsp; <b>{{ $pv->cantidad }}
                            </td>
                            <td width="425px" style="text-align: left;">{{ $pv->producto->nombre }}</td>

                            @php
                            if ($pv->precio_cobrado_mayor>0) {
                            $precio_costo = $pv->precio_cobrado_mayor;
                            }else{
                            $precio_costo = $pv->precio_cobrado;
                            }
                            $subTotal = $precio_costo * $pv->cantidad;
                            $sumaSubTotal += $subTotal;
                            @endphp

                            <td style="text-align: right;" width="160px">{{ $precio_costo }}</td>
                            <td style="text-align: right;" width="100px"><b>{{ number_format($subTotal, 2, '.', '') }}</b></td>

                        </tr>
                        @endforeach --}}
                        @php
                        // $numeroParaDecimal = number_format($subTotal, 2, '.', '');
                        // list($numero, $decimal) = explode('.', $numeroParaDecimal);
                        @endphp
                    </tbody>
                    <tfoot>
                        <td colspan="2" style="text-align: left;"></td>
                        <td style="background-color: #fefefe;color: #000;">TOTAL</td>
                        <td style="text-align: right;font-size: 9pt;font-weight: bold;">
                            {{-- {{ number_format($sumaSubTotal, 2, '.','') }} --}}
                        </td>
                    </tfoot>

                </table>
                {{-- SON: <span id="literalTotal"> </span>{{ $decimal }}/100 BOLIVIANOS --}}
                SON: <span id="literalTotal"> </span>decimal/100 BOLIVIANOS
                <br />
                <br />

                {{-- <div>Codigo de Control: {{ $datosFactura->codigo_control }}</div> --}}
                <div>Codigo de Control: CODIGO CONTROL</div>
                {{-- <div>Fecha limite de Emision: {{ $datosFactura->fecha_limite }}</div> --}}
                <div>Fecha limite de Emision: FECHA LIMITE DE EMISION</div>
                <br />
                <center>
                <div id="qrcode"></div>
                </center>


                <br />
                <center>
                    "ESTA FACTURA CONTRIBUYE AL DESARROLLO DEL PAIS. EL USO ILICITO DE ESTA SERA SANCIONADO DE ACUERDO A
                    LEY"<br />
                    <b>Ley N&deg; 453: El proveedor debera suministrar el servicio en las modalidades y terminos ofertados o
                        convenidos.</b>
                    <p>&nbsp;</p>
                    <div id="btnImprimir">
                        <input type="button" id="botonImpresion" value="IMPRIMIR" onClick="window.print()">
                    </div>

                    <div>
                        <input type="button" value="VOLVER">
                    </div>
                </center>
            </div>

        </div>

    @else

        <div id="fondo">
            <div id="logo"><img src="{{ asset('assets/images/logoSmartZone.jpg') }}" width="150"></div>

            <table id="datosEmpresaNit" width="300">
                <tr>
                    <th style="text-align: left;">NIT:</th>
                    <td>{{ $datosEmpresa->nit }}</td>
                </tr>
                <tr>
                    <th style="text-align: left;">FACTURA N&deg;:</th>
                    <td>{{ $datosFactura->numero_factura }}</td>
                </tr>
                <tr>
                    <th style="text-align: left;">N&deg; AUTORIZACION:</th>
                    <td>{{ $datosFactura->numero_autorizacion }}</td>
                </tr>
            </table>

            <table id="datosEmpresaFactura">
                <tr>
                    <td style="text-align: left;"><b>Lugar y Fecha:</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $datosEmpresa->ciudad
                        }}, {{ fechaCastellano($datosVenta->fecha) }}</span></td>
                    <td><b>NIT/CI:<b /> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $datosFactura->nit_cliente }}</td>
                </tr>
                <tr>
                    <td style="text-align: left;"><b>Se&ntilde;or(es):</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{
                        $datosVenta->cliente->razon_social }}</td>
                    <td></td>
                </tr>
            </table>
            <div id="tablaProductos">
                <table class="datos" width="892">
                    <thead>
                        <tr>
                            <th style="padding-top: 5px;padding-bottom: 5px;">N&deg;</th>
                            <th>CANTIDAD</th>
                            <th>DESCRIPCION</th>
                            <th>PRECIO UNITARIO</th>
                            <th>SUBTOTAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $sumaSubTotal = 0;
                        @endphp
                        @foreach ($productosVenta as $con => $pv)
                        <tr>
                            <td width="25px">{{ ++$con }}</td>
                            <td style="text-align: right;" width="100px">
                                <span class="text-info"><b>{{ ($pv->precio_cobrado_mayor>0)?$pv->escala->nombre:"" }}</b></span>
                                <span class="text-success"><b>{{ ($pv->combo_id != null)?$pv->combo->nombre:"" }}</b></span>
                                &nbsp;&nbsp;&nbsp; <b>{{ $pv->cantidad }}
                            </td>
                            <td width="425px" style="text-align: left;">{{ $pv->producto->nombre }}</td>

                            @php
                            if ($pv->precio_cobrado_mayor>0) {
                            $precio_costo = $pv->precio_cobrado_mayor;
                            }else{
                            $precio_costo = $pv->precio_cobrado;
                            }
                            $subTotal = $precio_costo * $pv->cantidad;
                            $sumaSubTotal += $subTotal;
                            @endphp

                            <td style="text-align: right;" width="160px">{{ $precio_costo }}</td>
                            <td style="text-align: right;" width="100px"><b>{{ number_format($subTotal, 2, '.', '') }}</b></td>

                        </tr>
                        @endforeach
                        @php
                        $numeroParaDecimal = number_format($subTotal, 2, '.', '');
                        list($numero, $decimal) = explode('.', $numeroParaDecimal);
                        @endphp
                    </tbody>
                    <tfoot>
                        <td colspan="3" style="text-align: left;">SON: <span id="literalTotal"> </span>{{ $decimal }}/100
                            BOLIVIANOS</td>
                        <td style="background-color: #abd4ed;color: #000;">TOTAL Bs.</td>
                        <td style="text-align: right;font-size: 9pt;font-weight: bold;">{{ number_format($sumaSubTotal, 2, '.',
                            '') }}</td>
                    </tfoot>

                </table>
                <br />
                <table class="codigoControlQr" width="100%">
                    <tr>
                        <td>
                            Codigo de Control: {{ $datosFactura->codigo_control }}<br />
                            Fecha limite de Emision: {{ $datosFactura->fecha_limite }}
                        </td>
                        <td>
                            <div id="qrcode"></div>
                        </td>
                    </tr>
                </table>
                <br />
                <center>
                    "ESTA FACTURA CONTRIBUYE AL DESARROLLO DEL PAIS. EL USO ILICITO DE ESTA SERA SANCIONADO DE ACUERDO A
                    LEY"<br />
                    <b>Ley N&deg; 453: El proveedor debera suministrar el servicio en las modalidades y terminos ofertados o
                        convenidos.</b>
                    <p>&nbsp;</p>
                    <div id="btnImprimir">
                        <input type="button" id="botonImpresion" value="IMPRIMIR" onClick="window.print()">
                    </div>
                </center>
            </div>

            <div id="txtOriginal">ORIGINAL</div>
            <div id="txtActividad">{{ $datosEmpresa->actividad }}</div>
            <div id="txtFactura">FACTURA</div>
            <div id="direccionEmpresa">
                <span style="font-size: 8pt;">{{ $datosEmpresa->nombre }}</span><br>
                {{ $datosEmpresa->direccion }}
            </div>
        </div>

    @endif

	@php
		// $fechaFecha = $datosVenta->fecha;
		// $fechaFactura = new DateTime($datosVenta->fecha);
		// // $datosFecha = explode("/", "$fechaFecha");
		// // $nuevaFecha = $datosFecha[2]."/".$datosFecha[1]."/".$datosFecha[0];
		// $fechaQr = $fechaFactura->format('d/m/Y');
	@endphp

	<script>
        {{-- 
		let valorTotal = Number({{ $sumaSubTotal }});
		var options = { year: 'numeric', month: 'long', day: 'numeric' };
		// var options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };

		function numerosALetras() {

			// generamos los numeros a letras
		    valorLiteral = numeroALetras(valorTotal, {
		        plural: ' ',
		        singular: ' ',
		        centPlural: 'Centavos',
		        centSingular: 'Centavo'
		    });
			// console.log(valorLiteral);
			document.getElementById("literalTotal").innerHTML = valorLiteral;

			// cambiamos la fecha para mejor lectura
			let fecha = new Date("{{ $datosVenta->fecha }}");
			document.getElementById("lugarFecha").innerHTML = "La Paz, " + fecha.toLocaleDateString("es-ES", options);
		}

		window.onload = numerosALetras;
		let cadenaQr = "{{ $datosEmpresa->nit }}|{{ $datosFactura->numero_factura }}|{{ $datosFactura->numero_autorizacion }}|{{ $fechaQr }}|{{ number_format($sumaSubTotal, 2, '.', '') }}|{{ round($sumaSubTotal, 0, PHP_ROUND_HALF_UP) }}|{{ $datosFactura->codigo_control }}|{{ $datosFactura->nit_cliente }}|0|0|0|0";
		// console.log(cadenaQr);
		var qrcode = new QRCode("qrcode", {
			text: cadenaQr,
			width: 90,
			height: 90,
			colorDark : "#000000",
			colorLight : "#ffffff",
			correctLevel : QRCode.CorrectLevel.H
		});
        --}}
	</script>

</body>
</html>