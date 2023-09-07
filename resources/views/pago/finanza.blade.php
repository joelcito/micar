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
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold">Formulario de Salida</h2>
                </div>
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
    </div> 
    <!--end::Modal - Add task-->


    <!--begin::Card-->
    <div class="card">
        <div class="card-header border-0 pt-6 bg-light-primary">
            <div class="card-title ">
                <h1>FLUJO DE CAJA</h1>
            </div>
            <!-- Propiedades adicionales -->
            {{-- <div class="card-subtitle text-muted">
                Informe financiero
            </div> --}}
            <div class="card-actions">
                <button class="btn btn-success btn-icon btn-sm" onclick="modalIngreso()"><i class="fas fa-money-bill"></i> <i class="fas fa-arrow-down"></i></button>
                <button class="btn btn-danger btn-icon btn-sm"><i class="fas fa-money-bill"></i> <i class="fas fa-arrow-up"></i></button>
            </div>
             <!-- Iconos o imágenes -->
            {{-- <div class="card-icons">
                <img src="https://static.vecteezy.com/system/resources/previews/018/930/626/non_2x/whatsapp-logo-whatsapp-icon-whatsapp-transparent-free-png.png" width="5%" alt="Ícono de flujo de caja">
            </div>
            
            <!-- Descripción adicional -->
            <div class="card-description">
                Este informe detalla los ingresos y gastos de la empresa durante el último trimestre.
            </div>
            
            <!-- Enlaces relacionados -->
            <div class="card-links">
                <a href="#">Ver detalles</a>
                <a href="#">Descargar PDF</a>
            </div>
            <!-- Datos adicionales -->
            <div class="card-data">
                <p>Fecha de creación: 01/09/2023</p>
                <p>Autor: John Doe</p>
            </div>
            
            <!-- Etiquetas o categorías -->
            <div class="card-tags">
                <span class="badge badge-info">Finanzas</span>
                <span class="badge badge-secondary">Informe</span>
            </div>
            <!-- Opciones de clasificación -->
            <div class="card-sort">
                <label for="sort-select">Ordenar por:</label>
                <select id="sort-select" class="form-control">
                    <option value="fecha">Fecha</option>
                    <option value="tipo">Tipo</option>
                    <option value="monto">Monto</option>
                </select>
            </div>
            <!-- Botón de favorito -->
            <div class="card-favorite">
                <button class="btn btn-outline-warning">Agregar a favoritos</button>
            </div>
            
            <!-- Información de contacto -->
            <div class="card-contact">
                <p>Para más información, contacte a:</p>
                <p>Correo electrónico: info@empresa.com</p>
                <p>Teléfono: +1234567890</p>
            </div> --}}
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
            $('#modalIngreso').modal('show')
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


