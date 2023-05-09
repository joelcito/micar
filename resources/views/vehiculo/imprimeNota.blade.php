<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>FACTURA</title>
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
			/*box-shadow: 0 5px #119e4d;*/
			padding: 15px;
		}

		#botonRegresa {
			background: #009efb;
			color: #fff;
			border-radius: 7px;
			/*box-shadow: 0 5px #119e4d;*/
			padding: 15px;
		}

		#botonElimina {
			background: #d34444;
			color: #fff;
			border-radius: 7px;
			/*box-shadow: 0 5px #119e4d;*/
			padding: 15px;
		}

		.botonSi {
			background: #17aa56;
			color: #fff;
			border-radius: 7px;
			/*box-shadow: 0 5px #119e4d;*/
			padding: 15px;
		}

		.botonNo {
			background: #d34444;
			color: #fff;
			border-radius: 7px;
			/*box-shadow: 0 5px #119e4d;*/
			padding: 15px;
		}

		body {
			font-family: Arial, Helvetica, sans-serif;
		}

		#fondo {
			/*background-image: url("{{ asset('assets/images/factura_szone.jpg') }}");*/
			/* width: 892px; */
			/* height: 514px; */
		}

		#tablaProductos {
			font-size: 10pt;
			position: absolute;
			top: 230px;
			left: 0px;
			/* width: 718px; */
		}

		#codigoControlQr {
			font-size: 10pt;
			/* position: relative; */
			/*top: 230px;
			left: 0px;*/
			/* width: 718px; */
		}


		/*estilos para tablas de datos*/
		table.datos {
			font-size: 12pt;
			/*line-height:14px;*/
			/* width: 1000; */
			border-collapse: collapse;
			background-color: #fff;
		}

		.datos th {
			/* height: 10px; */
			background-color: #abd4ed;
			color: #000;
		}

		.datos td {
			height: 24px;
		}

		.datos th,
		.datos td {
			border: 1px solid #000;
			padding: 2px;
			text-align: center;
		}

		.datos tr:nth-child(even) {
			background-color: #f2f2f2;
		}

		#literalTotal {
			font-size: 8pt;
		}

		#datosEmpresaNit {
			/* font-weight: bold; */
			font-size: 10pt;
			position: absolute;
			top: 0px;
			left: 595px;
			padding: 10px;
			border: 2px solid black;
		}

		#datosEmpresaFactura {
			/* font-weight: bold; */
			font-size: 12pt;
			position: absolute;
			top: 165px;
			left: 0px;
			padding: 5px;
			/*border: 1px solid black;*/
			width: 891px;
		}

		#txtOriginal {
			font-weight: bold;
			font-size: 12pt;
			position: absolute;
			top: 90px;
			left: 670px;
			width: 150px;
			text-align: center;
		}

		#txtActividad {
			/* font-weight: bold; */
			font-size: 8pt;
			position: absolute;
			top: 110px;
			left: 680px;
			width: 280px;
			text-align: left;
		}

		#txtFactura {
			font-weight: bold;
			font-size: 22pt;
			position: absolute;
			top: 100px;
			left: 350px;
			width: 150px;
			text-align: center;
		}

		#logo {
			position: absolute;
			top: 20px;
			{{--  left: 20px;  --}}
            right:280px;
		}

		#direccionEmpresa {
			font-weight: bold;
			font-size: 8pt;
			position: absolute;
			top: 25px;
			left: 240px;
			width: 250px;
			text-align: left;
		}
	</style>
	{{--  <link href="{{ asset('assets/libs/sweetalert2/dist/sweetalert2.min.css') }}" rel="stylesheet">
	<script src="{{ asset('js/NumeroALetras.js') }}"></script>
	<script src="{{ asset('dist/js/qrcode.min.js') }}"></script>  --}}
</head>

<body>
{{--  @php
	function fechaCastellano ($fecha) {
	$fecha = substr($fecha, 0, 10);
	$numeroDia = date('d', strtotime($fecha));
	$dia = date('l', strtotime($fecha));
	$mes = date('F', strtotime($fecha));
	$anio = date('Y', strtotime($fecha));
	$dias_ES = array("Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo");
	$dias_EN = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
	$nombredia = str_replace($dias_EN, $dias_ES, $dia);
	$meses_ES = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre",
	"Noviembre", "Diciembre");
	$meses_EN = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October",
	"November", "December");
	$nombreMes = str_replace($meses_EN, $meses_ES, $mes);
	return $numeroDia." de ".$nombreMes." de ".$anio;
	}
@endphp  --}}
	<div id="fondo">

		<div id="logo"><img src="{{ asset('assets/imagenes/logo.jpeg') }}" width="200"></div>

		{{--  <div id="txtOriginal">
			@php
				$hoy = date('Y-m-d');
				if($factura->fecha != $hoy){
					echo 'COPIA';
				}else{
					echo 'ORIGINAL';
				}
			@endphp
		</div>  --}}
		{{--  <div id="txtActividad">EDUCACION SUPERIOR</div>  --}}
		<div id="txtFactura">TIKEC DE REGISTRO</div>
		{{--  <div id="direccionEmpresa">
			<b>INSTITUTO TECNICO "EF-GIPET" S.R.L.</b><br />
			CASA MATRIZ<br />
			AV. VILLAZON PJE. BERNARDO TRIGO No 447<br />
			TELF 2444654 - 2444554<br />
			LA PAZ - BOLIVIA<br />
		</div>  --}}

{{--
			<table id="datosEmpresaNit" width="300">
				<tr>
					<th style="text-align: left;">NIT</th>
					<td> : 178436029</td>
				</tr>
				<tr>
					<th style="text-align: left;">FACTURA N&deg;</th>
					<td> : {{ $factura->numero }}</td>
				</tr>
				<tr>
					<th style="text-align: left;">N&deg; AUTORIZACION</th>
					<td> : {{ $parametros->numero_autorizacion }}</td>
				</tr>
			</table>  --}}

			<table id="datosEmpresaFactura">
				<tr>
					<td style="text-align: left;"><b>Lugar y Fecha: </b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						@php
							$utilidades = new App\librerias\Utilidades();
							$fechaEs = $utilidades->fechaCastellano(date('Y-m-d'));
						@endphp
						La Paz, {{ $fechaEs }}
						</td>
					<td>
                        {{--  <b>NIT/CI:</b> &nbsp;&nbsp;&nbsp;{{ /*$factura->nit*/ }}f  --}}
                    </td>
				</tr>
				<tr>
					<td style="text-align: left;"><b>Se&ntilde;or(es): </b>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						&nbsp;
						{{ $ventas[0]->vehiculo->marca }} | {{ $ventas[0]->vehiculo->placa }}</td>
					<td></td>
				</tr>
			</table>
		<div id="tablaProductos">
		<table class="datos" width="892">
			<thead>
				<tr>
					<th style="padding-top: 5px;padding-bottom: 5px;">N&deg;</th>
					<th>SERVICIO</th>
					<th>LAVADOR</th>
					<th>CANTIDAD</th>
					<th>SUBTOTAL</th>
				</tr>
			</thead>
			<tbody>
                @php
					$total = 0;
				@endphp
                @foreach ($ventas as $key => $v)
                    @php
                        $total += $v->total;
                    @endphp
                    <tr>
                        <td>
                            {{ $key+1 }}
                        </td>
                        <td>
                            @if ($v->servicio)
                                {{ $v->servicio->descripcion }}
                            @endif
                        </td>
                        <td>
                            @if ($v->lavador)
                                {{ $v->lavador->name }}
                            @endif
                        </td>
                        <td>
                            {{ $v->cantidad }}
                        </td>
                        <td>
                            {{ $v->total }}
                        </td>
                    </tr>
                @endforeach

				{{--  @foreach ($cuotasPagadas as $key => $c)
                    @php
                        $total += $c->importe;
                    @endphp
					<tr>
						<td width="25px">&nbsp;&nbsp;
							{{ ++$key }}
						</td>
						<td style="text-align: center;" width="100px">1</td>
						<td width="425px" style="text-align: left;">
							&nbsp;&nbsp;
							@if ($c->servicio_id == 2)
							    {{ $c->mensualidad }}&#176; Mensualidad
							@else
							    {{ $c->servicio->nombre }}
							@endif
						</td>
						<td style="text-align: right;" width="130px">{{ $c->importe }}</td>
						<td style="text-align: right;" width="100px"><b>{{ $c->importe }}</b></td>
					</tr>
				@endforeach  --}}

			</tbody>
			<tfoot>
				{{--  @php
					$utilidad = new App\librerias\NumeroALetras();
					$aLetras = $utilidad->toMoney($total);
				@endphp  --}}
				{{--  <td colspan="3" style="text-align: left;"><b>Son: </b> {{ /*$aLetras*/ }} 00/100 Bolivianos</td>  --}}
				<td style="background-color: #abd4ed;color: #000;"><b>TOTAL Bs.</b></td>
				<td style="text-align: right;font-weight: bold;">
                    {{ number_format($total, 2) }}
                </td>
			</tfoot>

		</table>
		<br />
			<table class="codigoControlQr" width="100%">
				<tr>
					<td style="width: 780px;">
						{{--  <b>NOTA AL CLIENTE: </b> {{ $factura->persona->apellido_paterno }} {{ $factura->persona->apellido_materno }} {{ $factura->persona->nombres }}<br />
						<hr>
						<b>CODIGO DE CONTROL: </b> {{ $factura->codigo_control }}<br />
						<b>FECHA LIMITE DE EMISION: </b> {{ $parametros->fecha_limite }}  --}}
					</td>
					<td>
						<div id="qrcode"></div>
					</td>
				</tr>
			</table>
		<br />
		<center>
			{{--  "ESTA FACTURA CONTRIBUYE AL DESARROLLO DEL PAIS. EL USO ILICITO DE ESTA SERA SANCIONADO DE ACUERDO A LEY"<br />
			<b>Ley N&deg; 453: El proveedor debera suministrar el servicio en las modalidades y terminos ofertados o
				convenidos.</b>
			<p>&nbsp;</p>  --}}
			<div id="btnImprimir">
				<input type="button" id="botonImpresion" value="IMPRIMIR" onClick="window.print()">
				<input type="button" id="botonRegresa" value="VOLVER" onClick="vuelveSistema()">
				{{--  <input type="button" id="botonElimina" value="ANULAR FACTURA" onclick="eliminaFactura(0)">  --}}
			</div>
		</center>
		</div>


		</div>

    <script>
        function vuelveSistema(){
			var oldURL = document.referrer;
			location.href = oldURL;
		}
    </script>
{{--  @php
	$fechaFactura = new DateTime($factura->fecha);
	$fechaQr = $fechaFactura->format('d/m/Y');
@endphp  --}}
{{--  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>  --}}
{{--  <script>
	let valorTotal = Number({{ $factura->total }});
		var options = { year: 'numeric', month: 'long', day: 'numeric' };

		function vuelveSistema(){
			var oldURL = document.referrer;
			location.href = oldURL;
		}

		function eliminaFactura(factura_id){

			const swalWithBootstrapButtons = Swal.mixin({
			  customClass: {
			    confirmButton: 'botonSi',
			    cancelButton: 'botonNo'
			  },
			  buttonsStyling: false
			})

			swalWithBootstrapButtons.fire({
			  title: 'Estas seguro?',
			  text: "Anular esta factura.",
			  icon: 'warning',
			  showCancelButton: true,
			  confirmButtonText: 'Si, Anular',
			  cancelButtonText: 'No, Cancelar',
			  reverseButtons: true
			}).then((result) => {
			  if (result.isConfirmed) {

			  	location.href = "{{ url('Factura/anulaFactura') }}/"+factura_id;

			    swalWithBootstrapButtons.fire(
			      'Factura anulada!',
			      'La factura fue anulada.',
			      'success'
			    )
			  } else if (
			    /* Read more about handling dismissals below */
			    result.dismiss === Swal.DismissReason.cancel
			  ) {
			    swalWithBootstrapButtons.fire(
			      'Cancelado',
			      'Esta operacion fue cancelada :)',
			      'error'
			    )
			  }
			})
		}

		let cadenaQr = "178436029|{{ $factura->numero }}|{{ $parametros->numero_autorizacion }}|{{ $fechaQr }}|{{ number_format($factura->total, 2, '.', '') }}|{{ round($factura->total, 0, PHP_ROUND_HALF_UP) }}|{{ $factura->codigo_control }}|{{ $factura->nit }}|0|0|0|0";
		// console.log(cadenaQr);
		var qrcode = new QRCode("qrcode", {
			text: cadenaQr,
			width: 98,
			height: 90,
			colorDark : "#000000",
			colorLight : "#ffffff",
			correctLevel : QRCode.CorrectLevel.H
		});
</script>  --}}

</body>
</html>
