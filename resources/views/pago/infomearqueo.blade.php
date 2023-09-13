@extends('layouts.app')
@section('css')
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('metadatos')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection
@section('content')

    <!--begin::Modal - Add task-->
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


    <!--begin::Card-->
    <div class="card">
        <div class="card-header border-0 pt-6 bg-light-primary">
            <div class="card-title ">
                <h1>INFORME DE ARQUEO</h1>
            </div>
            <div class="card-actions">
                {{--  <button class="btn btn-success btn-icon btn-sm" onclick="modalIngreso()"><i class="fas fa-money-bill"></i> <i class="fas fa-arrow-down"></i></button>
                <button class="btn btn-danger btn-icon btn-sm" onclick="modalSalida()"><i class="fas fa-money-bill"></i> <i class="fas fa-arrow-up"></i></button>  --}}
            </div>
             <!-- Iconos o imágenes -->
           
        </div>
        <!--begin::Card body-->
        <div class="card-body py-4">
            <form id="formularioBusqeuda">
                <div class="row">
                    <div class="col-md-3">
                        <label for="">Fecha Inicio</label>
                        <input type="date" class="form-control" id="fechaIni" name="fechaIni">
                    </div>
                    <div class="col-md-3">
                        <label for="">Fecha Fin</label>
                        <input type="date" class="form-control" id="fechaFin" name="fechaFin">
                    </div>
                    <div class="col-md-3">
                        <label for="">Tipo de pago</label>
                        <select name="tipo_pago" id="tipo_pago" class="form-control">
                            <option value="">Seleccionar</option>
                            <option value="efectivo">Efectivo</option>
                            <option value="tramsferencia">Tramsferencia</option>
                            <option value="qr">Qr</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-success w-100 btn-sm mt-7" type="button" onclick="ajaxListadoFinanzas()"><i class="fa fa-search"></i></button>
                    </div>
                </div>
            </form>
            <div id="table_pagos">

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
            ajaxListadoFinanzas();
        });

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


