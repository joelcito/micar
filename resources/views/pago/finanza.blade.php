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
                                    <input type="number" id="monto" name="monto" class="form-control form-control-solid mb-3 mb-lg-0" min="0.1" step="0.01" value="0" required>
                                    <input type="hidden" id="tipo" name="tipo" required>
                                    <input type="hidden" value="{{ $vender }}" id="caja_abierto_ingre_cerra" name="caja_abierto_ingre_cerra" required>
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
                <div class="modal-body scroll-y">
                    <form id="formularioCierreCaja">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <label class="required fw-semibold fs-6 mb-2">Usuario Cargo</label>
                                <input type="text" class="form-control" value="{{ Auth::user()->name }}" readonly>
                                <input type="hidden" value="{{ $vender }}" name="caja_abierto_cierre" id="caja_abierto_cierre" >
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
                @if ($vender != 0 )
                    <button class="btn btn-danger btn-icon btn-sm" onclick="modalCierreCaja()" title="Cierre de caja"><i class="fa-solid fa-solar-panel"></i></button>
                    <button class="btn btn-success btn-icon btn-sm" onclick="modalIngreso()"><i class="fas fa-money-bill"></i> <i class="fas fa-arrow-down"></i></button>
                    <button class="btn btn-danger btn-icon btn-sm" onclick="modalSalida()"><i class="fas fa-money-bill"></i> <i class="fas fa-arrow-up"></i></button>
                @endif
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
                    <div class="col-md-2">
                        <label for="">Fecha Inicio</label>
                        <input type="date" class="form-control" id="fechaIni" name="fechaIni" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-md-2">
                        <label for="">Fecha Fin</label>
                        <input type="date" class="form-control" id="fechaFin" name="fechaFin" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-md-2">
                        <label for="">Tipo de pago</label>
                        <select name="tipo_pago" id="tipo_pago" class="form-control">
                            <option value="">Seleccionar</option>
                            <option value="efectivo">Efectivo</option>
                            <option value="tramsferencia">Tramsferencia</option>
                            <option value="qr">Qr</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="">Cajero</label>
                        <select name="cajero_id" id="cajero_id" class="form-control">
                            <option value="">SELECCIONE</option>
                            @foreach ($cajeros as $c)
                                <option value="{{ $c->id }}" {{ (Auth::user()->id == $c->id)? 'selected':'' }}>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
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

                            location.reload();
                        }
                    }
                });
            }else{
                $("#formularioCierreCaja")[0].reportValidity();
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


