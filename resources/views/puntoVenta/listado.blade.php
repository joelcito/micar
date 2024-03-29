@extends('layouts.app')
@section('css')
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('metadatos')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection
@section('content')

    <!--begin::Modal - Add task-->
    <div class="modal fade" id="modalPuntoVenta" tabindex="-1" aria-hidden="true">
        <!--begin::Modal dialog-->
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <!--begin::Modal content-->
            <div class="modal-content">
                <!--begin::Modal header-->
                <div class="modal-header" id="kt_modal_add_user_header">
                    <!--begin::Modal title-->
                    <h2 class="fw-bold">FORMULARIO DE PUNTO DE VENTA</h2>
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

                    <form id="formularioServicio">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Nombre</label>
                                    <input type="text" class="form-control" required name="nombre" id="nombre">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="fv-row mb-4">
                                    <label class="required fw-semibold fs-6 mb-2">Descripcion</label>
                                    <input type="text" class="form-control" required name="descripcion" id="descripcion">
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="row">
                        <div class="col-md-12">
                            <button class="btn btn-success w-100" onclick="guardarPuntoVenta()">Guardar</button>
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
                <h3>Listado de Punto de ventas</h3>
            </div>
            <!--begin::Card title-->
            <!--begin::Card toolbar-->
            <div class="card-toolbar">
                <!--begin::Toolbar-->
                <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                    <button type="button" class="btn btn-primary" onclick="nuevoServicio()">
                    <i class="ki-duotone ki-plus fs-2"></i>Nueva Punto de Venta</button>
                    <!--end::Add user-->
                </div>
            </div>
            <!--end::Card toolbar-->
        </div>
        <!--end::Card header-->
        <!--begin::Card body-->
        <div class="card-body py-4">
            <div id="table_servicios">

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
        });

        function ajaxListado(){
            $.ajax({
                url: "{{ url('puntoVenta/ajaxListado') }}",
                type: 'POST',
                dataType: 'json',
                success: function(data) {
                    if(data.estado === 'success'){
                        $('#table_servicios').html(data.listado);
                    }
                }
            });
        }

        function guardarPuntoVenta(){
            if($('#formularioServicio')[0].checkValidity()){
                let datos = $('#formularioServicio').serializeArray();
                $.ajax({
                    url: "{{ url('puntoVenta/guarda') }}",
                    type: 'POST',
                    data:datos,
                    dataType: 'json',
                    success: function(data) {
                        if(data.estado === 'success'){
                            ajaxListado();
                            $('#modalPuntoVenta').modal('hide')
                        }
                    }
                });
            }else{
                $('#formularioServicio')[0].reportValidity()
            }
        }

        function nuevoServicio(){
            $('#nombre').val('')
            $('#descripcion').val('')
            $('#modalPuntoVenta').modal('show')
        }

        function eliminaPuntoVenta(puntoVenta){
            $.ajax({
                url: "{{ url('puntoVenta/eliminaPuntoVenta') }}",
                type: 'POST',
                data:{
                    cod: puntoVenta
                },
                dataType: 'json',
                success: function(data) {
                    if(data.estado === 'success'){
                        ajaxListado();
                    }
                }
            });
        }

    </script>
@endsection


