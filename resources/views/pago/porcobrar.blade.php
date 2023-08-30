@extends('layouts.app')
@section('css')
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('metadatos')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection
@section('content')

    <div class="modal fade" id="modalCobrar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" id="kt_modal_add_user_header">
                    <h2 class="fw-bold">Formulario de Pago</h2>
                </div>
                <div class="modal-body scroll-y">
                    <form id="formulario_cobro">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="fv-row mb-7">
                                    <label class="fw-semibold fs-6 mb-2">Cliente</label>
                                    <input type="text" class="form-control" name="cliente" id="cliente" disabled>
                                    <input type="hidden" name="factura_id" id="factura_id">
                                </div> 
                            </div>
                            <div class="col-md-6">
                                <div class="fv-row mb-7">
                                    <label class="fw-semibold fs-6 mb-2">Palca</label>
                                    <input type="text" class="form-control" name="placa" id="placa" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="fv-row mb-7">
                                    <label class="fw-semibold fs-6 mb-2">Importe Total</label>
                                    <input type="text" class="form-control" name="impor_total" id="impor_total" disabled>
                                </div> 
                            </div>
                            <div class="col-md-4">
                                <div class="fv-row mb-7">
                                    <label class="fw-semibold fs-6 mb-2">Importe Pagado</label>
                                    <input type="text" class="form-control" name="impor_pagado" id="impor_pagado" disabled>
                                </div> 
                            </div>
                            <div class="col-md-4">
                                <div class="fv-row mb-7">
                                    <label class="fw-semibold fs-6 mb-2">Importe Saldo</label>
                                    <input type="text" class="form-control" name="impor_saldo" id="impor_saldo" disabled>
                                </div> 
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Fecha</label>
                                    <input type="date" class="form-control" name="fecha_pago" id="fecha_pago" readonly required value="{{ date('Y-m-d') }}">
                                </div> 
                            </div>
                            <div class="col-md-4">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Tipo Pago</label>
                                    <select name="tipo_pago" id="tipo_pago" class="form-control" required>
                                        <option value="">Seleccionar</option>
                                        <option value="efectivo">Efectivo</option>
                                        <option value="tramsferencia">Tramsferencia</option>
                                        <option value="qr">Qr</option>
                                    </select>
                                </div> 
                            </div>
                            <div class="col-md-4">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Importe a pagar</label>
                                    <input type="number" type="button" class="form-control" name="importe_pagar" id="importe_pagar" required min="1" value="0">
                                </div> 
                            </div>
                        </div>
                    </form>
                    <div class="row">
                        <div class="col-md-12">
                            <button class="btn btn-sm btn-success w-100" onclick="pagarCuenta()">Pagar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- <div class="modal fade" id="enviarFacturaMasa" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header" id="kt_modal_add_user_header">
                    <h2 class="fw-bold">SERVICIOS DEL CLIENTE</h2>
                </div>
                <div class="modal-body scroll-y">
                    <div id="table_servicios">

                    </div>
                </div>
            </div>
        </div>
    </div> --}}

    <div class="card">
        <div class="card-header border-0 pt-6 bg-light-primary ">
            <div class="card-title">
                <h1>Cuentas por cobrar</h1>
            </div>
            <div class="card-toolbar">
            </div>
        </div>
        <div class="card-body py-4">
            <div class="row">
                <div class="col-md-2">
                    <label for="">Nombres</label>
                    <input type="text" class="form-control" id="buscar_nombre" name="buscar_nombre">
                </div>
                <div class="col-md-2">
                    <label for="">Apellido Paterno</label>
                    <input type="text" class="form-control" id="buscar_appaterno" name="buscar_appaterno">
                </div>                <div class="col-md-2">
                    <label for="">Apellido Materno</label>
                    <input type="text" class="form-control" id="buscar_apmaterno" name="buscar_apmaterno">
                </div>
                <div class="col-md-2">
                    <label for="">Cedula</label>
                    <input type="text" class="form-control" id="buscar_cedula" name="buscar_cedula">
                </div>
                <div class="col-md-2">
                    <label for="">Placa</label>
                    <input type="text" class="form-control" id="buscar_placa" name="buscar_placa">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-success w-100 btn-sm mt-7" onclick="ajaxListado()"><i class="fa fa-search"></i>Buscar</button>
                </div>
            </div>
            <hr>
            <div id="table_porcobrar">

            </div>
        </div>
        <!--end::Card body-->
    </div>
    <!--end::Card-->
@stop()

@section('js')
    <script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>

    <script type="text/javascript">

        $.ajaxSetup({
            // definimos cabecera donde estarra el token y poder hacer nuestras operaciones de put,post...
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })

        $( document ).ready(function() {
            ajaxListado();

            var arrayProductos          = [];
            var arrayPagos              = [];
            let valorIniDescuento       = 0;

        });


        function ajaxListado(){
            $.ajax({
                url: "{{ url('pago/ajaxBuscarPorCobrar') }}",
                type: 'POST',
                data:{
                    nombre      :   $('#buscar_nombre').val(),
                    appaterno   :   $('#buscar_appaterno').val(),
                    apmaterno   :   $('#buscar_apmaterno').val(),
                    cedula      :   $('#buscar_cedula').val(),
                    placa       :   $('#buscar_placa').val(),
                },
                dataType: 'json',
                success: function(data) {
                    if(data.estado === 'success'){
                        $('#table_porcobrar').html(data.listado)
                    }
                }
            });
        }

        function abreModalPagar(factura, cliente, placa, total, pagado){
            $('#cliente').val(cliente)
            $('#factura_id').val(factura)
            $('#placa').val(placa)
            $('#impor_total').val(total)
            $('#impor_pagado').val(pagado)
            $('#impor_saldo').val((total)-(pagado))
            $("#importe_pagar").attr("max", (total)-(pagado));
            $('#modalCobrar').modal('show')
        }

        function pagarCuenta(){
            if($("#formulario_cobro")[0].checkValidity()){
                datos = $("#formulario_cobro").serializeArray()
                $.ajax({
                    url: "{{ url('pago/pagarCuenta') }}",
                    data:datos,
                    type: 'POST',
                    dataType: 'json',
                    success: function(data) {
                        if(data.estado === 'success'){
                            Swal.fire({
                                icon: 'success',
                                title: "Se guardo con exito!",
                                showConfirmButton: false, // No mostrar botón de confirmación
                                timer: 1500, // 5 segundos
                                timerProgressBar: true
                            });
                            ajaxListado();
                            $('#modalCobrar').modal('hide')
                        }
                    }
                });
            }else{
    			$("#formulario_cobro")[0].reportValidity()
            }
        }

        // function enviarModalPorPagar(vehiculo){
        //     $.ajax({
        //         url: "{{ url('pago/ajaxServiciosMasa') }}",
        //         type: 'POST',
        //         data:{
        //             vehiculo:vehiculo
        //         },
        //         dataType: 'json',
        //         success: function(data) {
        //             if(data.estado === 'success'){
        //                 $('#table_servicios').html(data.listado)
        //                 $('#enviarFacturaMasa').modal('show');
        //             }
        //         }
        //     });
        // }

        // function muestraDatosFactura() {

        //     $.ajax({
        //         url: "{{ url('pago/arrayCuotasPorCobrar') }}",
        //         data:{
        //             vehiculo : $('#vehiculo_id').val()
        //         },
        //         type: 'POST',
        //         dataType: 'json',
        //         success: function(data) {
        //             if(data.estado === 'success'){
        //                 arrayPagos = data.pagos;
        //                 arrayProductos = JSON.parse(data.lista)
        //                 $('#bloqueDatosFactura').show('toggle')
        //             }
        //         }
        //     });
        // }

        // function emitirFactura(){
        //     if($("#formularioGeneraFactura")[0].checkValidity()){

        //         //PONEMOS TODO AL MODELO DEL SIAT EL DETALLE
        //         detalle = [];
        //         arrayProductos.forEach(function (prod){
        //             detalle.push({
        //                 actividadEconomica  :   prod.codigoActividad,
        //                 codigoProductoSin   :   prod.codigoProducto,
        //                 codigoProducto      :   prod.servicio_id,
        //                 descripcion         :   prod.descripcion,
        //                 cantidad            :   prod.cantidad,
        //                 unidadMedida        :   prod.unidadMedida,
        //                 precioUnitario      :   prod.precio,
        //                 montoDescuento      :   prod.descuento,
        //                 subTotal            :   ((prod.cantidad*prod.precio)-prod.descuento),
        //                 numeroSerie         :   null,
        //                 numeroImei          :   null
        //             })
        //         })

        //         let numero_factura                  = $('#numero_factura').val();
        //         let cuf                             = "123456789";//cambiar
        //         let cufd                            = "{{ session('scufd') }}";  //solo despues de que aga
        //         let direccion                       = "{{ session('sdireccion') }}";//solo despues de que aga
        //         var tzoffset                        = ((new Date()).getTimezoneOffset()*60000);
        //         let fechaEmision                    = ((new Date(Date.now()-tzoffset)).toISOString()).slice(0,-1);
        //         let nombreRazonSocial               = $('#razon_factura').val();
        //         let codigoTipoDocumentoIdentidad    = $('#tipo_documento').val()
        //         let numeroDocumento                 = $('#nit_factura').val();

        //         let complemento;
        //         var complementoValue = $("#complemento").val();
        //         if (complementoValue === null || complementoValue.trim() === ""){
        //             complemento                     = null;
        //         }else{
        //             if($('#tipo_documento').val()==5){
        //                 complemento                     = null;
        //             }else{
        //                 complemento                     = $('#complemento').val();
        //             }
        //         }

        //         let montoTotal                      = $('#motoTotalFac').val();
        //         let descuentoAdicional              = $('#descuento_adicional').val();
        //         let leyenda                         = "Ley N° 453: El proveedor deberá suministrar el servicio en las modalidades y términos ofertados o convenidos.";
        //         let usuario                         = "{{ Auth::user()->name }}";
        //         // let nombreEstudiante                = $('#nombreCompletoEstudiante').val();
        //         // let periodoFacturado                = detalle[(detalle.length)-1].descripcion+" / "+$('#anio_vigente_cuota_pago').val();

        //         let codigoExcepcion;
        //         if ($('#execpcion').is(':checked'))
        //             codigoExcepcion                 = 1;
        //         else
        //             codigoExcepcion                 = 0;


        //         var factura = [];
        //         factura.push({
        //             cabecera: {
        //                 nitEmisor                       :"5427648016",
        //                 razonSocialEmisor               :'MICAELA QUIROZ ESCOBAR',
        //                 municipio                       :"Santa Cruz",
        //                 telefono                        :"73130500",
        //                 numeroFactura                   :numero_factura,
        //                 cuf                             :cuf,
        //                 cufd                            :cufd,
        //                 codigoSucursal                  :0,
        //                 direccion                       :direccion ,
        //                 codigoPuntoVenta                :0,
        //                 fechaEmision                    :fechaEmision,
        //                 nombreRazonSocial               :nombreRazonSocial,
        //                 codigoTipoDocumentoIdentidad    :codigoTipoDocumentoIdentidad,
        //                 numeroDocumento                 :numeroDocumento,
        //                 // complemento                     :null,
        //                 complemento                     :complemento,
        //                 codigoCliente                   :numeroDocumento,
        //                 codigoMetodoPago                :1,
        //                 numeroTarjeta                   :null,
        //                 montoTotal                      :montoTotal,
        //                 montoTotalSujetoIva             :montoTotal,

        //                 codigoMoneda                    :1,
        //                 tipoCambio                      :1,
        //                 montoTotalMoneda                :montoTotal,

        //                 montoGiftCard                   :null,
        //                 descuentoAdicional              :descuentoAdicional,//ver llenado
        //                 codigoExcepcion                 :codigoExcepcion,
        //                 cafc                            :null,
        //                 leyenda                         :leyenda,
        //                 usuario                         :usuario,
        //                 codigoDocumentoSector           :1
        //             }
        //         })

        //         detalle.forEach(function (prod1){
        //             factura.push({
        //                 detalle:prod1
        //             })
        //         })

        //         var datos = {factura};

        //         var datosVehiculo = {
        //             'vehiculo_id'   :   $('#vehiculo_id').val(),
        //             'pagos'         :   arrayPagos
        //         };

        //         var datosRecepcion = {
        //             'uso_cafc'                  :$('input[name="uso_cafc"]:checked').val(),
        //             'codigo_cafc_contingencia'  :$('#codigo_cafc_contingencia').val()
        //         };

        //         console.log(datosVehiculo,datosRecepcion)


        //         $.ajax({
        //             url: "{{ url('factura/emitirFactura') }}",
        //             data: {
        //                 datos           :datos,
        //                 datosVehiculo   :datosVehiculo,
        //                 datosRecepcion  :datosRecepcion,
        //                 modalidad       : $('#tipo_facturacion').val()
        //             },
        //             type: 'POST',
        //             dataType:'json',
        //             success: function(data) {

        //                 if(data.estado === "VALIDADA"){
        //                     Swal.fire({
        //                         icon: 'success',
        //                         title: 'Excelente!',
        //                         text: 'LA FACTURA FUE VALIDADA',
        //                         timer: 3000
        //                     })
        //                     window.location.href = "{{ url('pago/listado')}}"
        //                 }else if(data.estado === "error_email"){
        //                     Swal.fire({
        //                         icon: 'error',
        //                         title: 'Error!',
        //                         text: data.msg,
        //                     })
        //                 }else if(data.estado === "OFFLINE"){
        //                     Swal.fire({
        //                         icon: 'warning',
        //                         title: 'Exito!',
        //                         text: 'LA FACTURA FUERA DE LINEA FUE REGISTRADA',
        //                     })
        //                     window.location.href = "{{ url('pago/listado')}}"
        //                     // location.reload();
        //                 }else{
        //                     Swal.fire({
        //                         icon: 'error',
        //                         title: 'Error!',
        //                         text: 'LA FACTURA FUE RECHAZADA',
        //                     })
        //                 }
        //             }
        //         });
        //     }else{
        //         $("#formularioGeneraFactura")[0].reportValidity();
        //     }
        // }

        // function verificaNit(){
        //     let tipoDocumento = $('#tipo_documento').val();
        //     if(tipoDocumento === "5"){
        //         let nit = $('#nit_factura').val();
        //         $.ajax({
        //             url: "{{ url('factura/verificaNit') }}",
        //             method: "POST",
        //             data:{nit:nit},
        //             dataType: 'json',
        //             success: function (data) {
        //                 if(data.estado === "success"){
        //                     if(!data.verificacion){
        //                         // Marcar el checkbox con jQuery
        //                         $('#execpcion').prop('checked', true);
        //                         $('#nitnoexiste').show('toggle');
        //                         $('#nitsiexiste').hide('toggle');
        //                         $('#bloque_exepcion').show('toggle');
        //                     }else{
        //                         $('#execpcion').prop('checked', false);
        //                         $('#nitsiexiste').show('toggle');
        //                         $('#nitnoexiste').hide('toggle');
        //                         $('#bloque_exepcion').hide('toggle');
        //                     }
        //                 }else{

        //                 }

        //             }
        //         })
        //     }else{
        //         $('#bloque_exepcion').hide('toggle');
        //     }
        // }

    //    function guardarVenta(){
    //         if($("#formularioRol")[0].checkValidity()){
    //             datos = $("#formularioRol").serializeArray()
    //             $.ajax({
    //                 url: "{{ url('rol/guarda') }}",
    //                 data:datos,
    //                 type: 'POST',
    //                 dataType: 'json',
    //                 success: function(data) {
    //                     if(data.estado === 'success')
    //                         $('#table_roles').html(data.listado);
    //                 }
    //             });
    //         }else{
    // 			$("#formularioRol")[0].reportValidity()
    //         }
    //     }

    //     function eliminar(rol){
    //         $.ajax({
    //             url: "{{ url('rol/eliminar') }}",
    //             type: 'POST',
    //             data:{id:rol},
    //             dataType: 'json',
    //             success: function(data) {
    //                 if(data.estado === 'success')
    //                     $('#table_roles').html(data.listado);
    //             }
    //         });
    //     }

    //     function imprimeNota(){
    //         let pago = $('#pago_id').val();
    //         let url = "{{ asset('vehiculo/imprimeNota') }}/"+pago;
    //         window.location.href = url;
    //     }
    </script>
@endsection


