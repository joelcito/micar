@extends('layouts.app')
@section('css')
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('metadatos')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection
@section('content')


<!--begin::Modal - Add task-->
<div class="modal fade" id="modalAnular" tabindex="-1" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <!--begin::Modal content-->
        <div class="modal-content">
            <!--begin::Modal header-->
            <div class="modal-header" id="kt_modal_add_user_header">
                <!--begin::Modal title-->
                <h2 class="fw-bold">Formulario de registro nuevo cliente</h2>
                <!--end::Modal title-->
                <!--begin::Close-->
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-users-modal-action="close">
                    <i class="ki-duotone ki-cross fs-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </div>
                <!--end::Close-->
            </div>
            <!--end::Modal header-->
            <!--begin::Modal body-->
            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">

                <form id="formularioNuevoCliente">
                    @csrf
                    <div class="row">
                        <div class="col-md-3">
                            <div class="fv-row mb-7">
                                <label class="required fw-semibold fs-6 mb-2">Ap Paterno</label>
                                <input type="text" class="form-control" required name="paterno" id="paterno">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="fv-row mb-7">
                                <label class="required fw-semibold fs-6 mb-2">Ap Materno</label>
                                <input type="text" class="form-control" required name="materno" id="materno">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="fv-row mb-7">
                                <label class="required fw-semibold fs-6 mb-2">Nombres</label>
                                <input type="text" class="form-control" required name="nombres" id="nombres">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="fv-row mb-7">
                                <label class="required fw-semibold fs-6 mb-2">Cedula</label>
                                <input type="text" class="form-control" required name="cedula" id="cedula">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-5">
                        <div class="col-md-4">
                            <div class="fv-row mb-7">
                                <label class="required fw-semibold fs-6 mb-2">Correo</label>
                                <input type="text" class="form-control" required name="correo" id="correo">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="fv-row mb-7">
                                <label class="required fw-semibold fs-6 mb-2">Celular</label>
                                <input type="text" class="form-control" required name="celular" id="celular">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="fv-row mb-7">
                                <label class="required fw-semibold fs-6 mb-2">Fecha Nacimiento</label>
                                <input type="date" class="form-control" required name="fecha_naci" id="fecha_naci">
                            </div>
                        </div>
                    </div>
                </form>

                <div class="row">
                    <div class="col-md-6">
                        <button class="btn btn-dark w-100" onclick="cancelarRegistroCliente()">Cancelar</button>
                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-success w-100" onclick="guardarRegistroCliente()">Guardar</button>
                    </div>
                </div>
            </div>
            <!--end::Modal body-->
        </div>
        <!--end::Modal content-->
    </div>
    <!--end::Modal dialog-->
</div>
<!--end::Modal - Add task-->


<!--begin::Modal - Add task-->
<div class="modal fade" id="modal_registro_vehiculo" tabindex="-1" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <!--begin::Modal content-->
        <div class="modal-content">
            <!--begin::Modal header-->
            <div class="modal-header" id="kt_modal_add_user_header">
                <!--begin::Modal title-->
                <h2 class="fw-bold">Formulario de registro nuevo vehiculo</h2>
                <!--end::Modal title-->
                <!--begin::Close-->
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-users-modal-action="close">
                    <i class="ki-duotone ki-cross fs-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </div>
                <!--end::Close-->
            </div>
            <!--end::Modal header-->
            <!--begin::Modal body-->
            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">

                <form id="formularioCategoria">
                    @csrf
                    <div class="row">
                        <div class="col-md-11">
                            <label class="required fw-semibold fs-6 mb-2">Cliente</label>
                            <select name="cliente_id" id="cliente_id" class="form-control" required data-control="select2" data-dropdown-parent="#modal_registro_vehiculo" data-placeholder="Select an option" data-allow-clear="true">
                                <option value="">SELECCIONE AL CLIENTE</option>
                                @foreach ($clientes as $c)
                                    <option value="{{ $c->id }}">{{ $c->ap_paterno." ".$c->ap_materno." ".$c->nombres }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-1 mt-9">
                            <button class="btn btn-icon btn-sm btn-success" onclick="modalNuevoUsuario()"><i class="fas fa-user-plus"></i> </button>
                        </div>
                    </div>
                    <div class="row mt-5">
                        <div class="col-md-4">
                            <div class="fv-row mb-7">
                                <label class="required fw-semibold fs-6 mb-2">PLACA</label>
                                <input type="text" class="form-control" required name="placa" id="placa">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="fv-row mb-7">
                                <label class="required fw-semibold fs-6 mb-2">COLOR</label>
                                <input type="text" class="form-control" required name="color" id="color">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="fv-row mb-7">
                                <label class="required fw-semibold fs-6 mb-2">MARCA</label>
                                <input type="text" class="form-control" required name="marca" id="marca">
                            </div>
                        </div>
                    </div>
                </form>

                <div class="row">
                    <div class="col-md-12">
                        <button class="btn btn-success w-100" onclick="guardarCategoria()">Guardar</button>
                    </div>
                </div>
            </div>
            <!--end::Modal body-->
        </div>
        <!--end::Modal content-->
    </div>
    <!--end::Modal dialog-->
</div>
<!--end::Modal - Add task-->


<!--begin::Card-->
<div class="card">
    <!--begin::Card header-->
    <div class="card-header border-0 pt-6">
        <div class="row w-100" >
            <div class="col-md-12s">
                <input type="text" class="form-control" placeholder="Buscar por placa" id="buscar_placa">
            </div>
        </div>
        <!--end::Card toolbar-->
    </div>
    <!--end::Card header-->
    <!--begin::Card body-->
    <div class="card-body py-4">
        <div class="row" id="bloque_cliente" style="display: none;">
            <div class="col-md-4">
                <span class="text-primary"><b>CLIENTE:</b></span><span id="cliente_text"></span>
            </div>
            <div class="col-md-4">
                <span class="text-primary"><b>VEHICULO:</b></span><span id="vehiculo_text"></span>
                <input type="hidden" id="vehiculo_id">
                <input type="text" id="complemento">
            </div>
            <div class="col-md-4">
                <span class="text-primary"><b>PLACA:</b></span><span id="placa_text"></span>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-12 text-center">
                @if ($verificacionSiat->estado === "success")
                    <div class="row">
                        <div class="col-md-6 text-center">
                            <span class="badge bg-success text-white w-100">{{ $verificacionSiat->resultado->RespuestaComunicacion->mensajesList->descripcion }}</span>
                        </div>
                        <div class="col-md-3">
                            CUIS: {{ (session()->has('scuis'))?  session('scuis') : '<span class="text-danger">NO existe la Cuis Vigente</span>'}}
                        </div>
                        <div class="col-md-3">
                            CUFD: {{ session('scodigoControl')." ".str_replace("T", " ",substr(session('sfechaVigenciaCufd'), 0 , 16)) }}
                        </div>
                    </div>
                @else
                    <span class="badge bg-danger text-white w-100">NO HAY CONECCION CON SIAT</span>
                @endif
            </div>
        </div>
        <hr>
        <form id="formularioAgregaVenta">
            <div class="row">
                <div class="col-md-3">
                    <div id="table_agrega_servicios" style="display: none">
                        <label class="required fw-semibold fs-6 mb-2">Servicio</label>
                        <select name="serivicio_id" id="serivicio_id" class="form-control" onchange="identificaSericio(this)" required>
                            <option value="">SELECCIONE</option>
                            @foreach ($servicios as $s)
                            <option value="{{ $s }}">{{ $s->descripcion }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3 haber" style="display: none">
                    <label class="required fw-semibold fs-6 mb-2">Unidad</label>
                    <input type="text" readonly class="form-control" id="unidad" name="unidad">
                </div>
                <div class="col-md-3 haber" style="display: none">
                    <label class="required fw-semibold fs-6 mb-2">Lavador</label>
                    <select name="lavador_id" id="lavador_id" class="form-control" required>
                        <option value="">Seleccione el Lavador</option>
                        @foreach ($lavadores as $l)
                            <option value="{{ $l->id }}">{{ $l->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 haber" style="display: none">
                    <label class="required fw-semibold fs-6 mb-2">Precio</label>
                    <input type="text" readonly class="form-control" id="precio" name="precio" required>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-3 haber" style="display: none">
                    <label class="required fw-semibold fs-6 mb-2">Cantidad</label>
                    <input type="text" class="form-control" id="cantidad" name="cantidad" required>
                </div>
                <div class="col-md-3 haber" style="display: none">
                    <label class="required fw-semibold fs-6 mb-2">Total</label>
                    <input type="text" class="form-control" id="total" name="total" readonly value="0" required>
                </div>
                <div class="col-md-2 haber mt-5" style="display: none">
                    <div class="form-group">
                        <label>Por Cobrar</label>
                        <div class="checkbox-list">
                            <label class="checkbox">
                                <input type="checkbox" name="Checkboxes1"/>
                                <span></span>
                                Registrar por cobrar
                            </label>
                            {{-- <label class="checkbox checkbox-disabled">
                                <input type="checkbox" disabled="disabled" checked="checked" name="Checkboxes1"/>
                                <span></span>
                                Disabled
                            </label> --}}
                            {{-- <label class="checkbox">
                                <input type="checkbox" checked="checked" name="Checkboxes1"/>
                                <span></span>
                                Checked
                            </label> --}}
                        </div>
                    </div>
                </div>
                <div class="col-md-4 haber" style="display: none">
                    <button class="btn btn-success btn-icon btn-sm w-100 mt-10" type="button" onclick="agregarVenta()"><i class="fa fa-car-alt"></i>  Agregar</button>
                </div>
            </div>
        </form>

        <div id="table_vehiculos">

        </div>

        <div class="row">
            <div class="col-md-12">
                <div id="detalle_ventas">

                </div>
            </div>
        </div>
        <hr>
        <form id="formulario_tipo_pagos">
            <div class="row" id="bloque_tipos_pagos" style="display: none">
                <div class="col-md-4">
                    <label for="">Tipo de Pago</label>
                    <select name="tipo_pago" id="tipo_pago" class="form-control">
                        <option value="">Seleccionar</option>
                        <option value="efectivo">Efectivo</option>
                        <option value="tramsferencia">Tramsferencia</option>
                        <option value="qr">Qr</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="monto_pagado">Monto</label>
                    <input type="text" class="form-control" id="miInput" name="miInput" value="0">
                </div>
                <div class="col-md-4">
                    <label for="cambio_devuelto">Cambio</label>
                    <input type="text" class="form-control" readonly value="0" id="cambio" min="0">
                </div>
            </div>
            <div class="row mt-1">
                <div class="col-md-12">
                    <button type="button" class="btn btn-success w-100 btn-sm" onclick="emitirRecibo()" style="display: none" id="boton_enviar_recivo">Enviar R</button>
                </div>
            </div>
        </form>
        <hr>
        <div id="bloqueDatosFactura" style="display: none">
            <form id="formularioGeneraFactura">                
                <div class="row">
                    <div class="col-md-1">
                        <label for="">N Factura</label>
                        <input type="number" class="form-control" id="numero_factura" value="{{ $numFac }}" readonly>
                    </div>
                    <div class="col-md-3">
                        <label for="">Tipo Docuemnto</label>
                        <select name="tipo_documento" id="tipo_documento" class="form-control" onchange="verificaNit()" required>
                            <option value="">SELECCIONE</option>
                            @foreach ($tipoDocumento as $te)
                                <option value="{{ $te->codigo_sin }}">{{ $te->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="">Nit/Cedula</label>
                        <input type="number" class="form-control" id="nit_factura" name="nit_factura" onchange="verificaNit()">
                        <small style="display: none;" class="text-danger" id="nitnoexiste">NIT INVALIDO</small>
                        <small style="display: none;" class="text-success" id="nitsiexiste">NIT VALIDO</small>
                    </div>
                    <div class="col-md-2">
                        <label for="">Razon Social</label>
                        <input type="text" class="form-control" id="razon_factura" name="razon_factura">
                    </div>
                    <div class="col-md-2">
                        <label for="">Tipo Factura</label>
                        <select name="tipo_facturacion" id="tipo_facturacion" class="form-control" onchange="bloqueCAFC()">
                            <option value="online">En Linea</option>
                            <option value="offline">Fuera de Linea</option>
                        </select>
                    </div>
                    <div class="col-md-2" style="display: none;" id="bloque_cafc">
                        <label for="">Uso del CAFC?</label>
                        <div class="row mt-5">
                            <div class="col-md-6">
                                <label for="radioNo">No</label>
                                <input type="radio" name="uso_cafc" id="radioNo" value="No" checked>
                            </div>
                            <div class="col-md-6">
                                <label for="radioSi">Si</label>
                                <input type="radio" name="uso_cafc" id="radioSi" value="Si">
                                <input type="hidden" id="codigo_cafc_contingencia" name="codigo_cafc_contingencia">
                            </div>
                        </div>
                    </div>
                </div>
                {{-- <h3 class="text-center text-info">PAGO</h3> --}}                
                <div class="row" style="display: none" id="bloque_exepcion">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label">Enviar con execpcion?</label>
                            <input type="checkbox" name="execpcion" id="execpcion" required readonly>
                        </div>
                    </div>
                </div>
                
            </form>
            <div class="row mt-2">
                <div class="col-md-12">
                    <button class="btn btn-sm w-100 btn-success" onclick="emitirFactura()" style="display: none" id="boton_enviar_factura">Enviar F</button>
                </div>
            </div>
        </div>        
    </div>
    <!--end::Card body-->
</div>
<!--end::Card-->

@stop()

@section('js')
    <script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script>
        $.ajaxSetup({
            // definimos cabecera donde estarra el token y poder hacer nuestras operaciones de put,post...
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })

        var arrayProductos          = [];
        var arrayPagos              = [];
        let valorIniDescuento       = 0;

        $( document ).ready(function() {
            ajaxListado();

            $('#cantidad').on('input',function(){
                let total = $('#cantidad').val()*$('#precio').val();
                $('#total').val(total)
            })

            $('#buscar_placa').on('keyup input', function() {
                buscarVehiculo();
                $('#table_vehiculos').show('toggle');
            });

            // Agregar un evento para el enfoque (cuando el usuario hace clic en el campo)
            $("#miInput").on("keyup", function() {
                console.log($(this).val())
                let dato = $(this).val() - $("#motoTotalFac").val()
                $('#cambio').val(dato)
                // Puedes realizar cualquier otra acción que desees aquí
            });

        });

        function ajaxListado(){
            $.ajax({
                url: "{{ url('vehiculo/ajaxListado') }}",
                type: 'POST',
                dataType: 'json',
                success: function(data) {
                    if(data.estado === 'success')
                        $('#table_vehiculos').html(data.listado);
                }
            });
        }

        function guardarCategoria(){
            if($("#formularioCategoria")[0].checkValidity()){
                datos = $("#formularioCategoria").serializeArray()
                $.ajax({
                    url: "{{ url('categoria/guarda') }}",
                    data:datos,
                    type: 'POST',
                    dataType: 'json',
                    success: function(data) {
                        if(data.estado === 'success'){
                            $('#table_vehiculos').html(data.listado);
                            Swal.fire({
                                icon: 'success',
                                title: 'Correcto!',
                                text: 'Se cambio  con exito!',
                                timer: 1500
                            })
                            $('#kt_modal_add_categoria').modal('hide');
                        }
                    }
                });
            }else{
    			$("#formularioCategoria")[0].reportValidity()
            }
        }

        function editarCategoria(categoria, nombre, descripcion){
            $('#nombre').val(nombre);
            $('#descripcion').val(descripcion);
            $('#categoria_id').val(categoria)

            $('#kt_modal_add_categoria').modal('show');

        }

        function nuevoCategoria(){
            $('#nombre').val('');
            $('#descripcion').val('');
            $('#categoria_id').val(0)

            $('#kt_modal_add_categoria').modal('show');
        }

        function eliminrCategoria(categoria){
            Swal.fire({
                title: 'Estas seguro de eliminar la categoria ?',
                text: "No podrás revertir esto.!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si, eliminar!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('categoria/eliminar') }}",
                        type: 'POST',
                        data:{id:categoria},
                        dataType: 'json',
                        success: function(data) {
                            if(data.estado === 'success'){
                                $('#table_vehiculos').html(data.listado);
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Eliminado!',
                                    text: 'La categoria se elimino!',
                                    timer: 1000
                                })
                            }
                        }
                    });
                }
            })
        }

        function sacaNitRazonSocial(cliente){
            $.ajax({
                url: "{{ url('vehiculo/obtenerNitRazonSocial') }}",
                type: 'POST',
                data:{id:cliente},
                dataType: 'json',
                success: function(data) {
                    if(data.estado === 'success'){
                        $('#nit_factura').val(data.nit);
                        $('#razon_factura').val(data.razon_social);
                    }
                }
            });
        }

        // function agregarServicio(placa, marca, ap, am, nombre, vehiculo, nit, razon_socal, cliente){
        function agregarServicio(placa, marca, ap, am, nombre, vehiculo, cliente, complemento){
            $.ajax({
                url: "{{ url('vehiculo/consultaPagosPorCobrar') }}",
                type: 'POST',
                data:{id:vehiculo},
                dataType: 'json',
                success: function(data) {
                    if(data.estado === 'success'){
                        $('#detalle_ventas').html(data.listado_ventas);
                        $('#bloqueDatosFactura').hide('toggle');
                        $('#table_vehiculos').hide('toggle');
                        $('#table_agrega_servicios').show('toggle');
                        $('#bloque_cliente').show('toggle');
                        $('#cliente_text').text(ap+" "+am+" "+nombre);
                        $('#vehiculo_text').text(marca);
                        $('#placa_text').text(placa);
                        $('#vehiculo_id').val(vehiculo);
                        $('#complemento').val(complemento);
                        sacaNitRazonSocial(cliente);
                        arrayPagos = [];
                    }
                }
            });
        }

        function identificaSericio(selected){
            var json = JSON.parse(selected.value);
            $('#unidad').val(json.unidad_venta)
            $('#precio').val(json.precio)
            $('#cantidad').val(1)
            $('#total').val((1*json.precio))
            $('.haber').show('toggle');
        }

        function agregarVenta(){

            if($('#formularioAgregaVenta')[0].checkValidity()){

                let servicio_id = (JSON.parse($('#serivicio_id').val())).id
                let lavador_id = $('#lavador_id').val();
                let vehiculo_id = $('#vehiculo_id').val();
                let precio = $('#precio').val();
                let cantidad = $('#cantidad').val();
                let total = $('#total').val();
                let pago_id = $('#pago_id').val();

                console.log(
                servicio_id,
                lavador_id,
                vehiculo_id,
                precio,
                cantidad,
                total)

                $.ajax({
                    url: "{{ url('vehiculo/ajaxRegistraVenta') }}",
                    type: 'POST',
                    data:{
                        servicio_id     :servicio_id,
                        lavador_id      :lavador_id,
                        vehiculo_id     :vehiculo_id,
                        precio          :precio,
                        cantidad        :cantidad,
                        total           :total,
                        pago_id         :pago_id
                    },
                    dataType: 'json',
                    success: function(data) {
                        if(data.estado === 'success'){
                            $('#pago_id').val(data.pago_id);
                            $('#detalle_ventas').html(data.listado_ventas);

                            $('#serivicio_id').val('')
                            $('#precio').val(0)
                            $('#cantidad').val(0)
                            $('#total').val(0)

                            $('#bloqueDatosFactura').hide('toggle');
                        }
                    }
                });

            }else{
    			$("#formularioAgregaVenta")[0].reportValidity()
            }

        }

        function eliminarPago(pago){
            $.ajax({
                url: "{{ url('pago/eliminarPago') }}",
                type: 'POST',
                data:{
                    id      :pago,
                    vehiculo:$('#vehiculo_id').val()
                },
                dataType: 'json',
                success: function(data) {
                    if(data.estado === 'success'){
                        $('#detalle_ventas').html(data.listado);
                        $('#bloqueDatosFactura').hide('toggle');
                    }
                }
            });
        }

        function buscarVehiculo(){
            let placa = $('#buscar_placa').val();
            $.ajax({
                url: "{{ url('vehiculo/buscarVehiculo') }}",
                type: 'POST',
                data:{
                    placa     :placa,
                },
                dataType: 'json',
                success: function(data) {
                    if(data.estado === 'success'){
                        $('#table_vehiculos').html(data.listado)
                        console.log(data)
                    }
                }
            });
        }

        function registraNuevoVehiculo(){
            $('#modal_registro_vehiculo').modal('show');
            {{--  console.log("este es nuevo registro de vehiulo")  --}}
        }

        function modalNuevoUsuario(){
            $('#modal_registro_vehiculo').modal('hide');
            $('#modal_registro_usuario').modal('show');
        }

        function cancelarRegistroCliente(){
            $('#modal_registro_usuario').modal('hide');
            $('#modal_registro_vehiculo').modal('show');
        }

        function guardarRegistroCliente(){
            if($("#formularioNuevoCliente")[0].checkValidity()){
                datos = $("#formularioNuevoCliente").serializeArray()
                $.ajax({
                    url: "{{ url('categoria/guarda') }}",
                    data:datos,
                    type: 'POST',
                    dataType: 'json',
                    success: function(data) {
                        if(data.estado === 'success'){
                            $('#table_vehiculos').html(data.listado);
                            Swal.fire({
                                icon: 'success',
                                title: 'Correcto!',
                                text: 'Se cambio  con exito!',
                                timer: 1500
                            })
                            $('#kt_modal_add_categoria').modal('hide');
                        }
                    }
                });
            }else{
    			$("#formularioNuevoCliente")[0].reportValidity()
            }
        }

        function muestraDatosFactura(){
            $('#bloqueDatosFactura').show('toggle')
            $('#bloque_tipos_pagos').show('toggle')
            $('#boton_enviar_factura').show('toggle')
            $('#boton_enviar_recivo').hide('toggle')
            $.ajax({
                url: "{{ url('factura/arrayCuotasPagar') }}",
                data:{
                    vehiculo : $('#vehiculo_id').val()
                },
                type: 'POST',
                dataType: 'json',
                success: function(data) {
                    if(data.estado === 'success'){
                        arrayPagos = data.pagos;
                        arrayProductos = JSON.parse(data.lista)
                    }
                }
            });
        }

        function muestraDatosTipoPago(){
            $('#bloque_tipos_pagos').show('toggle')
            $('#boton_enviar_recivo').show('toggle')
            $('#bloqueDatosFactura').hide('toggle')
            $('#boton_enviar_factura').hide('toggle')
        }

        function emitirFactura(){

            if($("#formularioGeneraFactura")[0].checkValidity()){

                //PONEMOS TODO AL MODELO DEL SIAT EL DETALLE
                detalle = [];
                arrayProductos.forEach(function (prod){
                    detalle.push({
                        actividadEconomica  :   prod.codigoActividad,
                        codigoProductoSin   :   prod.codigoProducto,
                        codigoProducto      :   prod.servicio_id,
                        descripcion         :   prod.descripcion,
                        cantidad            :   prod.cantidad,
                        unidadMedida        :   prod.unidadMedida,
                        precioUnitario      :   prod.precio,
                        montoDescuento      :   prod.descuento,
                        subTotal            :   ((prod.cantidad*prod.precio)-prod.descuento),
                        numeroSerie         :   null,
                        numeroImei          :   null
                    })
                })

                let numero_factura                  = $('#numero_factura').val();
                let cuf                             = "123456789";//cambiar
                let cufd                            = "{{ session('scufd') }}";  //solo despues de que aga
                let direccion                       = "{{ session('sdireccion') }}";//solo despues de que aga
                var tzoffset                        = ((new Date()).getTimezoneOffset()*60000);
                let fechaEmision                    = ((new Date(Date.now()-tzoffset)).toISOString()).slice(0,-1);
                let nombreRazonSocial               = $('#razon_factura').val();
                let codigoTipoDocumentoIdentidad    = $('#tipo_documento').val()
                let numeroDocumento                 = $('#nit_factura').val();

                let complemento;
                var complementoValue = $("#complemento").val();
                if (complementoValue === null || complementoValue.trim() === ""){
                    complemento                     = null;
                }else{
                    if($('#tipo_documento').val()==5){
                        complemento                     = null;
                    }else{
                        complemento                     = $('#complemento').val();
                    }
                }

                let montoTotal                      = $('#motoTotalFac').val();
                let descuentoAdicional              = $('#descuento_adicional').val();
                let leyenda                         = "Ley N° 453: El proveedor deberá suministrar el servicio en las modalidades y términos ofertados o convenidos.";
                let usuario                         = "{{ Auth::user()->name }}";
                let nombreEstudiante                = $('#nombreCompletoEstudiante').val();
                let periodoFacturado                = detalle[(detalle.length)-1].descripcion+" / "+$('#anio_vigente_cuota_pago').val();

                let codigoExcepcion;
                if ($('#execpcion').is(':checked'))
                    codigoExcepcion                 = 1;
                else
                    codigoExcepcion                 = 0;


                var factura = [];
                factura.push({
                    cabecera: {
                        nitEmisor                       :"5427648016",
                        razonSocialEmisor               :'MICAELA QUIROZ ESCOBAR',
                        municipio                       :"Santa Cruz",
                        telefono                        :"73130500",
                        numeroFactura                   :numero_factura,
                        cuf                             :cuf,
                        cufd                            :cufd,
                        codigoSucursal                  :0,
                        direccion                       :direccion ,
                        codigoPuntoVenta                :0,
                        fechaEmision                    :fechaEmision,
                        nombreRazonSocial               :nombreRazonSocial,
                        codigoTipoDocumentoIdentidad    :codigoTipoDocumentoIdentidad,
                        numeroDocumento                 :numeroDocumento,
                        // complemento                     :null,
                        complemento                     :complemento,
                        codigoCliente                   :numeroDocumento,
                        codigoMetodoPago                :1,
                        numeroTarjeta                   :null,
                        montoTotal                      :montoTotal,
                        montoTotalSujetoIva             :montoTotal,

                        codigoMoneda                    :1,
                        tipoCambio                      :1,
                        montoTotalMoneda                :montoTotal,

                        montoGiftCard                   :null,
                        descuentoAdicional              :descuentoAdicional,//ver llenado
                        codigoExcepcion                 :codigoExcepcion,
                        cafc                            :null,
                        leyenda                         :leyenda,
                        usuario                         :usuario,
                        codigoDocumentoSector           :1
                    }
                })

                detalle.forEach(function (prod1){
                    factura.push({
                        detalle:prod1
                    })
                })

                var datos = {factura};

                var datosVehiculo = {
                    'vehiculo_id'   :   $('#vehiculo_id').val(),
                    'pagos'         :   arrayPagos
                };

                var datosRecepcion = {
                    'uso_cafc'                  :$('input[name="uso_cafc"]:checked').val(),
                    'codigo_cafc_contingencia'  :$('#codigo_cafc_contingencia').val()
                };


                $.ajax({
                    url: "{{ url('factura/emitirFactura') }}",
                    data: {
                        datos           :datos,
                        datosVehiculo   :datosVehiculo,
                        datosRecepcion  :datosRecepcion,
                        modalidad       : $('#tipo_facturacion').val(),

                        tipo_pago           : $('#tipo_pago').val(),
                        monto_pagado        : $('#miInput').val(),
                        cambio              : $('#cambio').val()
                    },
                    type: 'POST',
                    dataType:'json',
                    success: function(data) {

                        if(data.estado === "VALIDADA"){
                            Swal.fire({
                                icon: 'success',
                                title: 'Excelente!',
                                text: 'LA FACTURA FUE VALIDADA',
                                timer: 3000
                            })
                            window.location.href = "{{ url('pago/listado')}}"
                        }else if(data.estado === "error_email"){
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: data.msg,
                            })
                        }else if(data.estado === "OFFLINE"){
                            Swal.fire({
                                icon: 'warning',
                                title: 'Exito!',
                                text: 'LA FACTURA FUERA DE LINEA FUE REGISTRADA',
                            })
                            window.location.href = "{{ url('pago/listado')}}"
                            // location.reload();
                        }else{
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'LA FACTURA FUE RECHAZADA',
                            })
                        }
                    }
                });
            }else{
                $("#formularioGeneraFactura")[0].reportValidity();
            }
        }

        function emitirRecibo(){
            $.ajax({
                url: "{{ url('factura/emitirRecibo') }}",
                type: 'POST',
                data:{
                    vehiculo            : $('#vehiculo_id').val(),
                    monto               : $('#motoTotalFac').val(),
                    descuento_adicional : $('#descuento_adicional').val(),
                    // datos               : $('#formulario_tipo_pagos').serializeArray()
                    tipo_pago           : $('#tipo_pago').val(),
                    monto_pagado        : $('#miInput').val(),
                    cambio              : $('#cambio').val()
                },
                dataType: 'json',
                success: function(data) {
                    if(data.estado === 'success'){
                        let url = "{{ asset('factura/imprimeRecibo') }}/"+data.factura;
                        window.location.href = url;
                    }
                }
            });
        }

        function funcionNueva(input, pago, total){
            let valorEnviado;
            if($("#formularioDescuentos")[0].checkValidity()){
                valorEnviado = input.value;
            }else{
                let idinput = input.id
                $('#'+idinput).val(valorIniDescuento)
                valorEnviado = valorIniDescuento;
                $("#formularioDescuentos")[0].reportValidity();
            }

            $.ajax({
                url: "{{ url('factura/actualizaDescuento') }}",
                data: {
                    pago_id: pago,
                    valor: valorEnviado,
                    // valor: input.value,
                    },
                type: 'POST',
                dataType:'json',
                success: function(data) {
                    if(data.estado === 'success'){
                        var k = (total-input.value).toFixed(2);
                        $('#subTotalCalculdo_'+pago).text(k);
                        $('#motoTotalFac').val(parseFloat(((data.valor)-$('#descuento_adicional').val()).toFixed(2)).toFixed(2))
                    }
                }
            });
        }

        function guardarValorInicial(input) {
            valorIniDescuento = input.value;
            console.log("Valor inicial guardado: " + valorIniDescuento);
        }

        function caluculaTotal(event){
            $.ajax({
                url: "{{ url('factura/sumaTotalMonto') }}",
                data: {
                    //anio: $('#anio_vigente_cuota_pago').val(),
                    vehiculo: $('#vehiculo_id').val(),
                },
                type: 'POST',
                dataType:'json',
                success: function(data) {
                    $('#motoTotalFac').val((data.valor)-$('#descuento_adicional').val())
                }
            });
        }

        function bloqueCAFC(){
            console.log($('#tipo_facturacion').val())
            if($('#tipo_facturacion').val() === "offline"){
                $('#bloque_cafc').show('toggle')
            }else{
                $('#bloque_cafc').hide('toggle')
            }
        }

        // Agregar un evento para verificar el radio seleccionado al cambiar
        $('input[name="uso_cafc"]').on('change', function() {
            verificarRadioSeleccionado();
        });

        function verificarRadioSeleccionado() {
            var valorSeleccionado = $('input[name="uso_cafc"]:checked').val();
            if (valorSeleccionado === 'No') {
                console.log('El radio seleccionado es "No"');
                $.ajax({
                    url: "{{ url('factura/sacaNumeroFactura') }}",
                    method: "POST",
                    dataType: 'json',
                    success: function (data) {
                        if(data.estado === "success"){
                            $("#numero_factura").val(data.numero);
                            $("#codigo_cafc_contingencia").val("");
                        }else{
                            Swal.fire({
                                icon:   'error',
                                title:  'Error!',
                                text:   "Algo fallo"
                            })
                        }
                    }
                })
            } else if (valorSeleccionado === 'Si') {
                console.log('El radio seleccionado es "Si"');
                $.ajax({
                    url: "{{ url('factura/sacaNumeroCafcUltimo') }}",
                    method: "POST",
                    dataType: 'json',
                    success: function (data) {
                        if(data.estado === "success"){
                            $("#numero_factura").val(data.numero);
                            $("#codigo_cafc_contingencia").val("10122205E166E");
                        }else{
                            Swal.fire({
                                icon:   'error',
                                title:  'Error!',
                                text:   "Algo fallo"
                            })
                        }
                    }
                })
            }
        }

        function verificaNit(){
            console.log($('#tipo_documento').val())
            let tipoDocumento = $('#tipo_documento').val();
            if(tipoDocumento === "5"){
                let nit = $('#nit_factura').val();
                $.ajax({
                    url: "{{ url('factura/verificaNit') }}",
                    method: "POST",
                    data:{nit:nit},
                    dataType: 'json',
                    success: function (data) {
                        if(data.estado === "success"){
                            if(!data.verificacion){
                                // Marcar el checkbox con jQuery
                                $('#execpcion').prop('checked', true);
                                $('#nitnoexiste').show('toggle');
                                $('#nitsiexiste').hide('toggle');
                                $('#bloque_exepcion').show('toggle');
                            }else{
                                $('#execpcion').prop('checked', false);
                                $('#nitsiexiste').show('toggle');
                                $('#nitnoexiste').hide('toggle');
                                $('#bloque_exepcion').hide('toggle');
                            }
                        }else{

                        }

                    }
                })
            }else{
                $('#bloque_exepcion').hide('toggle');
            }
        }

        function emitirTicket(){
            let vehiculo = $('#vehiculo_id').val()
            let url = "{{ asset('factura/imprimeTicked') }}/"+vehiculo;
            window.location.href = url;
        }

        function emitirPorCobrar(){
            $.ajax({
                url: "{{ url('pago/emitirPorCobrar') }}",
                data: {
                    vehiculo: $('#vehiculo_id').val(),
                },
                type: 'POST',
                dataType:'json',
                success: function(data) {
                    if(data.estado === "success"){
                        Swal.fire({
                            icon:   'success',
                            title:  'Exito!',
                            text:   "Se agrego el registro POR COBRAR",
                            timer: 2500
                        })
                        setTimeout(function() {
                            location.reload();
                        }, 3000); // 3000 milisegundos = 3 segundos
                    }
                }
            });
        }

    </script>
@endsection


