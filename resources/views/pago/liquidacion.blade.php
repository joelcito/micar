@extends('layouts.app')
@section('css')
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('metadatos')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection
@section('content')


    {{--  <!--begin::Modal - Add task-->
     <div class="modal fade" id="modalIngreso" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold">Formulario de <span id="text_tipoo_modal" class="text-info"></span></h2>
                </div>
                <div class="modal-body scroll-y">
                    <form id="formularioIngresoSalida">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Monto</label>
                                    <input type="number" id="monto" name="monto" class="form-control form-control-solid mb-3 mb-lg-0">
                                    <input type="text" id="tipo" name="tipo">
                                    <input type="text" value="{{ $vender }}" id="caja_abierto_ingre_cerra" name="caja_abierto_ingre_cerra">
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Descripcion</label>
                                    <input type="text" id="descripcion" name="descripcion" class="form-control form-control-solid mb-3 mb-lg-0">
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="row">
                        <div class="col-md-12">
                            <button class="btn btn-success w-100" onclick="guardarTipoIngresoSalida()">Guardar</button>
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
    <div class="modal fade" id="modalCierreCaja" tabindex="-1" aria-hidden="true">
        <!--begin::Modal dialog-->
        <div class="modal-dialog modal-dialog-centered">
            <!--begin::Modal content-->
            <div class="modal-content">
                <!--begin::Modal header-->
                <div class="modal-header" id="kt_modal_add_user_header">
                    <!--begin::Modal title-->
                    <h2 class="fw-bold">Formulario de cierre de caja</h2>
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
                <div class="modal-body scroll-y">
                    <form id="formularioCierreCaja">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <label class="required fw-semibold fs-6 mb-2">Usuario Cargo</label>
                                <input type="text" class="form-control" value="{{ Auth::user()->name }}" readonly>
                                <input type="text" value="{{ $vender }}" name="caja_abierto_cierre" id="caja_abierto_cierre" >
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-4">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Monto</label>
                                    <input type="text" class="form-control" required name="monto_cie_caja" id="monto_cie_caja">
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Descripcion</label>
                                    <input type="text" class="form-control" required name="descripcion_cie_caja" id="descripcion_cie_caja">
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="row">
                        <div class="col-md-12">
                            <button class="btn btn-success w-100 btn-sm" onclick="registrarCajaCierre()">Gurdar</button>
                        </div>
                    </div>
                </div>
                <!--end::Modal body-->
            </div>
            <!--end::Modal content-->
        </div>
        <!--end::Modal dialog-->
    </div>
    <!--end::Modal - Add task-->  --}}


    <!--begin::Card-->
    <div class="card">
        <div class="card-header border-0 pt-6 bg-light-primary">
            <div class="card-title ">
                <h1>LIQUIDACION POR VENTA DE SERVICIOS</h1>
            </div>
            <div class="card-actions">
                {{--  <button class="btn btn-danger btn-icon btn-sm" onclick="modalCierreCaja()" title="Cierre de caja"><i class="fa-solid fa-solar-panel"></i></button>  --}}
                {{--  @if ($vender != 0)
                    <button class="btn btn-success btn-icon btn-sm" onclick="modalIngreso()"><i class="fas fa-money-bill"></i> <i class="fas fa-arrow-down"></i></button>
                    <button class="btn btn-danger btn-icon btn-sm" onclick="modalSalida()"><i class="fas fa-money-bill"></i> <i class="fas fa-arrow-up"></i></button>
                @endif  --}}
            </div>
        </div>
        <!--begin::Card body-->
        <div class="card-body py-4">
            <form id="formularioBusqeuda">
                <div class="row">
                    <form id="formularioBusqueda">
                        <div class="col-md-3">
                            <label for="">Cedula</label>
                            <input type="number" class="form-control formBus" id="cedula" name="cedula">
                        </div>
                        <div class="col-md-3">
                            <label for="">Paterno</label>
                            <input type="text" class="form-control formBus" id="paterno" name="paterno">
                        </div>
                        <div class="col-md-3">
                            <label for="">Materno</label>
                            <input type="text" class="form-control formBus" id="materno" name="materno">
                        </div>
                        <div class="col-md-3">
                            <label for="">Nombre</label>
                            <input type="text" class="form-control formBus" id="nombre" name="nombre">
                        </div>
                    </form>
                </div>
            </form>
            <div id="table_lavadores">

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
            //ajaxListadoFinanzas();
            $(".formBus").on("input", function () {
                var inputText = $(this).val();
                if (inputText.length >= 3) {
                    console.log("Texto ingresado en uno de los campos: " + inputText)
                    $.ajax({
                        url: "{{ url('pago/buscarServicios') }}",
                        type: 'POST',
                        data:{
                            cedula : $('#cedula').val(),
                            paterno: $('#paterno').val(),
                            materno: $('#materno').val(),
                            nombre : $('#nombre').val()
                        },
                        dataType: 'json',
                        success: function(data) {
                            if(data.estado === 'success'){
                                $('#table_lavadores').html(data.listado)
                            }
                        }
                    });
                }
            });
        });

        function buscarServicios(){
            let datos = $('#formularioBusqueda').serializeArray();
            $.ajax({
                url: "{{ url('pago/buscarServicios') }}",
                type: 'POST',
                data:datos,
                dataType: 'json',
                success: function(data) {
                    if(data.estado === 'success'){
                        $('#table_pagos').html(data.listado);
                    }
                }
            });
        }

        /*
        function ajaxListadoFinanzas(){
            let datos = $('#formularioBusqeuda').serializeArray();
            $.ajax({
                url: "{{ url('pago/ajaxListadoFinanzas') }}",
                type: 'POST',
                data:datos,
                dataType: 'json',
                success: function(data) {
                    if(data.estado === 'success')
                        $('#table_pagos').html(data.listado);
                }
            });
        }

        function modalIngreso(){
            $('#tipo').val('Ingreso')
            $('#text_tipoo_modal').text('Ingreso')
            $('#monto').val(0)
            $('#descripcion').val('')
            $('#modalIngreso').modal('show')
        }

        function modalSalida(){
            $('#tipo').val('Salida')
            $('#monto').val(0)
            $('#descripcion').val('')
            $('#text_tipoo_modal').text('Salida')
            $('#modalIngreso').modal('show')
        }

        function guardarTipoIngresoSalida(){
            if($("#formularioIngresoSalida")[0].checkValidity()){
                datos = $("#formularioIngresoSalida").serializeArray()
                $.ajax({
                    url: "{{ url('pago/guardarTipoIngresoSalida') }}",
                    data:datos,
                    type: 'POST',
                    dataType: 'json',
                    success: function(data) {
                        if(data.estado === 'success'){
                            Swal.fire({
                                icon             : 'success',
                                title            : 'Se registro con exito',
                                showConfirmButton: false,       // No mostrar botón de confirmación
                                timer            : 2000,        // 5 segundos
                                timerProgressBar : true
                            });
                            $('#modalIngreso').modal('hide')
                            ajaxListadoFinanzas();
                        }
                    }
                });
            }else{
    			$("#formularioIngresoSalida")[0].reportValidity()
            }
        }

        function modalCierreCaja(){
            $('#modalCierreCaja').modal('show')
        }

        function registrarCajaCierre(){
            if($("#formularioCierreCaja")[0].checkValidity()){
                let datos = $("#formularioCierreCaja").serializeArray();
                $.ajax({
                    url: "{{ url('pago/cierreCaja') }}",
                    data: datos,
                    type: 'POST',
                    dataType:'json',
                    success: function(data) {
                        if(data.estado === 'success'){

                            Swal.fire({
                                icon: 'success',
                                title: 'SE CERRO CON EXTIO',
                                showConfirmButton: false, // No mostrar botón de confirmación
                                timer: 2000, // 5 segundos
                                timerProgressBar: true
                            });
                            $('#modalCierreCaja').modal('hide')
                            //buscarVehiculo()

                        }
                    }
                });
            }else{
                $("#formularioCierreCaja")[0].reportValidity();
            }
        }
        */
    </script>
@endsection


