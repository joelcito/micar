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
    <div class="modal fade" id="modalCliente" tabindex="-1" aria-hidden="true">
        <!--begin::Modal dialog-->
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <!--begin::Modal content-->
            <div class="modal-content">
                <!--begin::Modal header-->
                <div class="modal-header" id="kt_modal_add_user_header">
                    <!--begin::Modal title-->
                    <h2 class="fw-bold">Formulario de cliente</h2>
                    <!--end::Modal title-->
                    <!--begin::Close-->
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
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
                    <form id="formularioCliente">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Nombres</label>
                                    <input type="text" class="form-control" required name="nombres" id="nombres">
                                    <input type="hidden" name="cliente_id" id="cliente_id" value="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Ap Paterno</label>
                                    <input type="text" class="form-control" required name="ap_paterno" id="ap_paterno">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Ap Materno</label>
                                    <input type="text" class="form-control" required name="ap_materno" id="ap_materno">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Cedula</label>
                                    <input type="number" class="form-control" required name="cedula" id="cedula">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Complemento</label>
                                    <input type="text" class="form-control" name="complemento" id="complemento">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="fv-row mb-7">
                                    <label class="fw-semibold fs-6 mb-2">Nit</label>
                                    <input type="text" class="form-control" name="nit" id="nit">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="fv-row mb-7">
                                    <label class="fw-semibold fs-6 mb-2">Razon Social</label>
                                    <input type="text" class="form-control" name="razon_social" id="razon_social">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="fv-row mb-7">
                                    <label class="fw-semibold fs-6 mb-2">Correo</label>
                                    <input type="email" class="form-control" name="correo" id="correo">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="fv-row mb-7">
                                    <label class="fw-semibold fs-6 mb-2">Celular</label>
                                    <input type="number" class="form-control" name="celular" id="celular">
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="fv-row mb-7">
                                    <div class="mb-10">
                                        <label class="required fw-semibold mb-5">Tipo Cliente</label>
                                        <div class="d-flex">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-check form-check-custom form-check-solid">
                                                        <input class="form-check-input me-3" name="tipo_cliente" type="radio" value="cliente" id="cliente" checked='checked' />
                                                        <label class="form-check-label" for="cliente">
                                                            <div class="fw-bold text-gray-800">Cliente</div>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-check form-check-custom form-check-solid">
                                                        <input class="form-check-input me-3" name="tipo_cliente" type="radio" value="lavador" id="lavador" />
                                                        <label class="form-check-label" for="lavador">
                                                            <div class="fw-bold text-gray-800">Lavador</div>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="row">
                        <div class="col-md-12">
                            <button class="btn btn-success w-100" onclick="guardarCliente()">Guardar</button>
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
            <!--begin::Card title-->
            <div class="card-title">
                <h3>Listado de Clientes</h3>
            </div>
            <div class="card-toolbar">
                <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                    <button type="button" class="btn btn-primary" onclick="nuevoCliente()">
                        <i class="ki-duotone ki-plus fs-2"></i>Nuevo Cliente
                    </button>
                </div>
            </div>
            <!--end::Card toolbar-->
        </div>
        <!--end::Card header-->
        <!--begin::Card body-->
        <div class="card-body py-4">
            <div class="row">
                <div class="col-md-3">
                    <label class="fw-semibold fs-6 mb-2">Cedula</label>
                    <input type="text" class="form-control" name="buscar_cedula" id="buscar_cedula">
                </div>
                <div class="col-md-3">
                    <label class="fw-semibold fs-6 mb-2">Ap Paterno</label>
                    <input type="text" class="form-control" name="buscar_paterno" id="buscar_paterno">
                </div>
                <div class="col-md-3">
                    <label class="fw-semibold fs-6 mb-2">Ap Materno</label>
                    <input type="text" class="form-control" name="buscar_materno" id="buscar_materno">
                </div>
                <div class="col-md-3">
                    <label class="fw-semibold fs-6 mb-2">Nombres</label>
                    <input type="text" class="form-control" name="buscar_nombres" id="buscar_nombres">
                </div>
            </div>
            <div id="table_categoria">

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


            $('#buscar_cedula, #buscar_paterno, #buscar_materno, #buscar_nombres').on('keyup', function(){

                var id              = $(this).attr('id');
                var valor           = $(this).val().trim();
                var contadorCedula  = 0;
                var contadorPaterno = 0;
                var contadorMaterno = 0;
                var contadorNombre  = 0;

                switch (id) {
                    case 'buscar_cedula':
                        contadorCedula = valor.length;
                        break;
                    case 'buscar_paterno':
                        contadorPaterno = valor.length;
                        break;
                    case 'buscar_materno':
                        contadorMaterno = valor.length;
                        break;
                    case 'buscar_nombres':
                        contadorNombre = valor.length;
                        break;
                    default:
                        break;
                }

                if (contadorCedula >= 3 || contadorPaterno >= 3 || contadorMaterno >= 3 || contadorNombre >= 3) {
                    ajaxListado();
                    // $('#table_vehiculos').show('toggle');
                    // $('#detalle_ventas').hide('toggle');
                }

            });

        });

        function ajaxListado(){
            let datos = {
                buscar_cedula : $('#buscar_cedula').val(),
                buscar_paterno: $('#buscar_paterno').val(),
                buscar_materno: $('#buscar_materno').val(),
                buscar_nombres: $('#buscar_nombres').val(),
            }
            $.ajax({
                url: "{{ url('cliente/ajaxListado') }}",
                type: 'POST',
                data: datos,
                dataType: 'json',
                success: function(data) {
                    if(data.estado === 'success')
                        $('#table_categoria').html(data.listado);
                }
            });
        }

        function guardarCliente(){
            if($("#formularioCliente")[0].checkValidity()){
                datos = $("#formularioCliente").serializeArray()
                $.ajax({
                    url: "{{ url('cliente/guarda') }}",
                    data:datos,
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
                            $('#modalCliente').modal('hide');
                        }
                    }
                });
            }else{
    			$("#formularioCliente")[0].reportValidity()
            }
        }

        function editarCliente(cliente, nombre, ap_paterno, ap_materno, cedula, complemento, nit, razon_social, correo, celular){
            $('#nombres').val(nombre);
            $('#cliente_id').val(cliente);
            $('#ap_paterno').val(ap_paterno);
            $('#ap_materno').val(ap_materno);
            $('#cedula').val(cedula);
            $('#complemento').val(complemento);
            $('#nit').val(nit);
            $('#razon_social').val(razon_social);
            $('#correo').val(correo);
            $('#celular').val(celular);

            $('#modalCliente').modal('show');

        }

        function nuevoCliente(){
            $('#nombres').val('');
            $('#ap_paterno').val('');
            $('#ap_materno').val('');
            $('#cedula').val('');
            $('#complemento').val('');
            $('#nit').val('');
            $('#razon_social').val('');
            $('#correo').val('');
            $('#celular').val('');
            $('#cliente_id').val(0)

            $('#modalCliente').modal('show');
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
                                $('#table_categoria').html(data.listado);
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
    </script>
@endsection


