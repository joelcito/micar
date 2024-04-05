@extends('layouts.app')
@section('css')
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('metadatos')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection
@section('content')

    <!--end::Modal - New Card-->
    <!--begin::Modal - Add task-->
    <div class="modal fade" id="modalVehiuclo" tabindex="-1" aria-hidden="true">
        <!--begin::Modal dialog-->
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <!--begin::Modal content-->
            <div class="modal-content">
                <!--begin::Modal header-->
                <div class="modal-header" id="kt_modal_add_user_header">
                    <!--begin::Modal title-->
                    <h2 class="fw-bold">Formulario de vehiculo</h2>
                    <!--end::Modal title-->
                    <!--begin::Close-->
                    <div class="btn btn-icon btn-sm btn-active-icon-primary"data-bs-dismiss="modal">
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
                    <form id="formularioVehiuclo">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">PLACA</label>
                                    <input type="text" class="form-control" required name="placa" id="placa">
                                    <input type="text" name="vehiculo_id" id="vehiculo_id">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="fv-row mb-7">
                                    <label class=" fw-semibold fs-6 mb-2">COLOR</label>
                                    <input type="text" class="form-control"  name="color" id="color">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="fv-row mb-7">
                                    <label class="fw-semibold fs-6 mb-2">MARCA</label>
                                    <input type="text" class="form-control"  name="marca" id="marca">
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="row">
                        <div class="col-md-12">
                            <button class="btn btn-success w-100" onclick="guardarVehiouclo()">Guardar</button>
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
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <h1>Perfil de Cliente</h1>
            </div>
            <div class="card-toolbar">

            </div>
        </div>
        <div class="card-body py-4">
            <form id="formularioCliente">
                <div class="row">
                    <div class="col-md-2">
                        <div class="fv-row mb-7">
                            <label class="required fw-semibold fs-6 mb-2">Cedula</label>
                            <input type="text" class="form-control" value="{{ $cliente->cedula }}" id="act_cedula" name="act_cedula">
                            <input type="hidden" id="act_cliente_id" name="act_cliente_id" value="{{ $cliente->id  }}">
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="fv-row mb-7">
                            <label class="required fw-semibold fs-6 mb-2">Complemento</label>
                            <input type="text" class="form-control" value="{{ $cliente->complemento}}" id="act_complemento" name="act_complemento">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="fv-row mb-7">
                            <label class="required fw-semibold fs-6 mb-2">Ap Paterno</label>
                            <input type="text" class="form-control" value="{{ $cliente->ap_paterno }}" id="act_paterno" name="act_paterno">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="fv-row mb-7">
                            <label class="required fw-semibold fs-6 mb-2">Ap Materno</label>
                            <input type="text" class="form-control" value="{{ $cliente->ap_materno }}" id="act_materno" name="act_materno">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="fv-row mb-7">
                            <label class="required fw-semibold fs-6 mb-2">Nombres</label>
                            <input type="text" class="form-control" value="{{ $cliente->nombres }}" id="act_nombres" name="act_nombres">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="fv-row mb-7">
                            <label class="required fw-semibold fs-6 mb-2">Nit</label>
                            <input type="text" class="form-control" value="{{ $cliente->nit }}" id="act_nit" name="act_nit">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="fv-row mb-7">
                            <label class="required fw-semibold fs-6 mb-2">Razon Social</label>
                            <input type="text" class="form-control" value="{{ $cliente->razon_social }}" id="act_razon_social" name="act_razon_social">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="fv-row mb-7">
                            <label class="required fw-semibold fs-6 mb-2">Correo</label>
                            <input type="text" class="form-control" value="{{ $cliente->correo }}"  id="act_correo" name="act_correo">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="fv-row mb-7">
                            <label class="required fw-semibold fs-6 mb-2">Ceular</label>
                            <input type="number" class="form-control" value="{{ $cliente->celular }}"  id="act_celular" name="act_celular">
                        </div>
                    </div>
                </div>
            </form>
            <div class="row">
                <div class="col-md-12">
                    @if (Auth::user()->isEdit())
                        <button class="btn btn-success w-100 btn-sm" type="button" onclick="actualizarCliente()">ACTUALIZAR</button>
                    @endif
                </div>
            </div>
        </div>
        <!--end::Card body-->
    </div>
    <!--end::Card-->


    <!--begin::Card-->
    <div class="card mt-4">
        <div class="card-header border-0 pt-6">
            <div class="card-title">
                <h1>Listado de Vehiculos</h1>
            </div>
            <div class="card-toolbar">
                <button class="btn btn-sm btn-primary" type="button" onclick="nuevoVehiuclo()">Nuevo Vehiculo <i class="fa fa-plus"></i></button>
            </div>
        </div>
        <div class="card-body py-4">
            <div class="row">
                <div class="col-md-12">
                    <div id="tabla_vehiuclos">

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

        // $(function () {
        //     $('#myTab li:last-child a').tab('show')
        //     console.log($('#myTab li:last-child a'))
        // })

        $( document ).ready(function() {
            ajaxListado();
        });

        function ajaxListado(){
            $.ajax({
                url: "{{ url('cliente/ajaxListadoVehiculo') }}",
                type: 'POST',
                data:{
                    cliente:$('#act_cliente_id').val()
                },
                dataType: 'json',
                success: function(data) {
                    if(data.estado === 'success')
                        $('#tabla_vehiuclos').html(data.listado);
                }
            });
        }

        function nuevoVehiuclo(){
            $('#vehiculo_id').val(0)
            $('#placa').val('')
            $('#color').val('')
            $('#marca').val('')
            $('#modalVehiuclo').modal('show')
        }

        function guardarVehiouclo(){
            if($("#formularioVehiuclo")[0].checkValidity()){
                $.ajax({
                    url: "{{ url('vehiculo/guarda') }}",
                    data:{
                        placa      : $('#placa').val(),
                        color      : $('#color').val(),
                        marca      : $('#marca').val(),
                        cliente    : $('#act_cliente_id').val(),
                        vehiculo_id: $('#vehiculo_id').val()
                    },
                    type: 'POST',
                    dataType: 'json',
                    success: function(data) {
                        if(data.estado === 'success'){
                            $('#table_categoria').html(data.listado);
                            Swal.fire({
                                icon: 'success',
                                title: 'Correcto!',
                                text: 'Se registro con exito!',
                                timer: 1500
                            })
                            $('#tabla_vehiuclos').html(data.listado);
                            $('#modalVehiuclo').modal('hide');
                        }
                    }
                });
            }else{
    			$("#formularioVehiuclo")[0].reportValidity()
            }
        }

        function actualizarCliente(){
            if($("#formularioCliente")[0].checkValidity()){
                $.ajax({
                    url: "{{ url('cliente/actualizarUsuario') }}",
                    data: $('#formularioCliente').serializeArray(),
                    type: 'POST',
                    dataType: 'json',
                    success: function(data) {
                        if(data.estado === 'success'){
                            $('#table_categoria').html(data.listado);
                            Swal.fire({
                                icon: 'success',
                                title: 'Correcto!',
                                text: 'Se registro con exito!',
                                timer: 1500
                            })
                            $('#tabla_vehiuclos').html(data.listado);
                            $('#modalVehiuclo').modal('hide');
                        }
                    }
                });
            }else{
    			$("#formularioCliente")[0].reportValidity()
            }
        }

        function editarMovilidad(vehiculo, placa, color, marca){
            $('#vehiculo_id').val(vehiculo)
            $('#placa').val(placa)
            $('#color').val(color)
            $('#marca').val(marca)
            $('#modalVehiuclo').modal('show')
        }

        function eliminarMovilidad(movilidad, placa){
            Swal.fire({
                title: 'Estas seguro de eliminar el vehiculo con placa '+placa+' ?',
                text: "No podrás revertir esto.!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si, eliminar!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('vehiculo/eliminarMovilidad') }}",
                        type: 'POST',
                        data:{id:movilidad},
                        dataType: 'json',
                        success: function(data) {
                            if(data.estado === 'success'){
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Eliminado!',
                                    text: 'El Vehiculo se elimino!',
                                    timer: 1000
                                })
                                ajaxListado();
                            }
                        }
                    });
                }
            })
        }
    </script>
@endsection


