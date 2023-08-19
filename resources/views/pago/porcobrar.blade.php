@extends('layouts.app')
@section('css')
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('metadatos')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection
@section('content')

    <!--begin::Modal - Add task-->
    {{--  <div class="modal fade" id="kt_modal_add_user" tabindex="-1" aria-hidden="true">
        <!--begin::Modal dialog-->
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <!--begin::Modal content-->
            <div class="modal-content">
                <!--begin::Modal header-->
                <div class="modal-header" id="kt_modal_add_user_header">
                    <!--begin::Modal title-->
                    <h2 class="fw-bold">Formulario de rol</h2>
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

                    <form id="formularioRol">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Nombre</label>
                                    <input type="text" id="nombre" name="nombre" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="Administrador">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Descripcion</label>
                                    <input type="text" id="descripcion" name="descripcion" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="Administrador del sistema">
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="row">
                        <div class="col-md-12">
                            <button class="btn btn-success w-100" onclick="guardarVenta()">Guardar</button>
                        </div>
                    </div>
                </div>
                <!--end::Modal body-->
            </div>
            <!--end::Modal content-->
        </div>
        <!--end::Modal dialog-->
    </div>  --}}
    <!--end::Modal - Add task-->


    <!--begin::Card-->
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
                </div>
                <div class="col-md-2">
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
            ajaxListado()
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


