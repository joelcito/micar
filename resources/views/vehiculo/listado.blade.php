@extends('layouts.app')
@section('css')
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('metadatos')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection
@section('content')


<!--begin::Modal - Add task-->
<div class="modal fade" id="modal_registro_usuario" tabindex="-1" aria-hidden="true">
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
            {{--  <div class="col-md-4">
                <button class="btn btn-success w-100" onclick="buscarVehiculo()">Buscar</button>
            </div>  --}}
        </div>
        <!--end::Card toolbar-->
    </div>
    <!--end::Card header-->
    <!--begin::Card body-->
    <div class="card-body py-4">
        <div class="row" id="bloque_cliente" style="display: none;">
            <div class="col-md-4">
                <span class="text-primary"><b>CLIENTE:</b></span><span id="cliente"></span>
            </div>
            <div class="col-md-4">
                <span class="text-primary"><b>VEHICULO:</b></span><span id="vehiculo"></span>
                <input type="text" id="vehiculo_id">
            </div>
            <div class="col-md-4">
                <span class="text-primary"><b>PLACA:</b></span><span id="placa"></span>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-3">
                <div id="table_agrega_servicios" style="display: none">
                    <label class="required fw-semibold fs-6 mb-2">Servicio</label>
                    <select name="serivicio_id" id="serivicio_id" class="form-control" onchange="identificaSericio(this)">
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
                <select name="lavador_id" id="lavador_id" class="form-control">
                    <option value="">Seleccione el Lavador</option>
                    @foreach ($lavadores as $l)
                        <option value="{{ $l->id }}">{{ $l->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 haber" style="display: none">
                <label class="required fw-semibold fs-6 mb-2">Precio</label>
                <input type="text" readonly class="form-control" id="precio" name="precio">
            </div>
        </div>
        <div class="row">
            <div class="col-md-3 haber" style="display: none">
                <label class="required fw-semibold fs-6 mb-2">Cantidad</label>
                <input type="text" class="form-control" id="cantidad" name="cantidad">
            </div>
            <div class="col-md-3 haber" style="display: none">
                <label class="required fw-semibold fs-6 mb-2">Total</label>
                <input type="text" class="form-control" id="total" name="total" readonly>
                <input type="text" id="pago_id" value="0">
            </div>
            <div class="col-md-6 haber" style="display: none">
                <button class="btn btn-success btn-icon btn-sm w-100 mt-10" onclick="agregarVenta()"><i class="fa fa-car-alt"></i>  Agregar</button>
            </div>
        </div>

        <div id="table_vehiculos">

        </div>

        <div class="row">
            <div class="col-md-12">
                <div id="detalle_ventas">

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

        $( document ).ready(function() {
            ajaxListado();

            $('#cantidad').on('input',function(){
                let total = $('#cantidad').val()*$('#precio').val();
                $('#total').val(total)
            })

            $('#buscar_placa').on('keyup input', function() {
                buscarVehiculo();
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

        function agregarServicio(placa, marca, ap, am, nombre, vehiculo){
            $('#table_vehiculos').hide('toggle');
            $('#table_agrega_servicios').show('toggle');
            $('#bloque_cliente').show('toggle');

            $('#cliente').text(ap+" "+am+" "+nombre);
            $('#vehiculo').text(marca);
            $('#placa').text(placa);
            $('#vehiculo_id').val(vehiculo);
        }

        function identificaSericio(selected){
            var json = JSON.parse(selected.value);
            $('#unidad').val(json.unidad_venta)
            $('#precio').val(json.precio)
            $('.haber').show('toggle');
        }

        function agregarVenta(){

            console.log("haber!!");

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

        function eliminarVenta(venta, pago){
            Swal.fire({
                title: 'Estas seguro de eliminar el servicio ?',
                text: "No podrás revertir esto.!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si, eliminar!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('vehiculo/eliminarVenta') }}",
                        type: 'POST',
                        data:{
                            id:venta,
                            pago:pago
                        },
                        dataType: 'json',
                        success: function(data) {
                            if(data.estado === 'success'){
                                $('#pago_id').val(data.pago_id);
                                $('#detalle_ventas').html(data.listado_ventas);
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

        function imprimeNota(){
            let pago = $('#pago_id').val();
            let url = "{{ asset('vehiculo/imprimeNota') }}/"+pago;
            window.location.href = url;
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
    </script>
@endsection


