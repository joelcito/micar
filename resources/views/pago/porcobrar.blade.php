@extends('layouts.app')
@section('css')
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('metadatos')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection
@section('content')

    <div class="modal fade" id="modalCobrar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" id="kt_modal_add_user_header">
                    <h2 class="fw-bold">Formulario de Pago</h2>
                </div>
                <div class="modal-body scroll-y">
                    <form id="formulario_cobro">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="fv-row mb-7">
                                    <label class="fw-semibold fs-6 mb-2">Cliente</label>
                                    <input type="text" class="form-control" name="cliente" id="cliente" disabled>
                                    <input type="hidden" name="factura_id" id="factura_id">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="fv-row mb-7">
                                    <label class="fw-semibold fs-6 mb-2">Palca</label>
                                    <input type="text" class="form-control" name="placa" id="placa" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="fv-row mb-7">
                                    <label class="fw-semibold fs-6 mb-2">Importe Total</label>
                                    <input type="text" class="form-control" name="impor_total" id="impor_total" disabled>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="fv-row mb-7">
                                    <label class="fw-semibold fs-6 mb-2">Importe Pagado</label>
                                    <input type="text" class="form-control" name="impor_pagado" id="impor_pagado" disabled>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="fv-row mb-7">
                                    <label class="fw-semibold fs-6 mb-2">Importe Saldo</label>
                                    <input type="text" class="form-control" name="impor_saldo" id="impor_saldo" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Fecha</label>
                                    <input type="date" class="form-control" name="fecha_pago" id="fecha_pago" readonly required value="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Tipo Pago</label>
                                    <select name="tipo_pago" id="tipo_pago" class="form-control" required>
                                        <option value="">Seleccionar</option>
                                        <option value="efectivo">Efectivo</option>
                                        <option value="tramsferencia">Tramsferencia</option>
                                        <option value="qr">Qr</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Importe a pagar</label>
                                    <input type="number" type="button" class="form-control" name="importe_pagar" id="importe_pagar" required min="0.1" step="0.01" value="0">
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="row">
                        <div class="col-md-12">
                            <button class="btn btn-sm btn-success w-100" id="boton_pagarCuenta" onclick="pagarCuenta()">Pagar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
                <div class="col-md-1">
                    <label for="">Cedula</label>
                    <input type="text" class="form-control" id="buscar_cedula" name="buscar_cedula">
                </div>
                <div class="col-md-1">
                    <label for="">Placa</label>
                    <input type="text" class="form-control" id="buscar_placa" name="buscar_placa">
                </div>
                <div class="col-md-3">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="">Fecha Inicio</label>
                            <input type="date" class="form-control" id="buscar_fecha_ini" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-6">
                            <label for="">Fecha Fin</label>
                            <input type="date" class="form-control" id="buscar_fecha_fin" value="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                </div>
                <div class="col-md-1">
                    <button class="btn btn-success btn-sm mt-7 w-100" onclick="ajaxListado()"><i class="fa fa-search"></i></button>
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
            ajaxListado();

            var arrayProductos          = [];
            var arrayPagos              = [];
            let valorIniDescuento       = 0;

        });


        function ajaxListado(){
            $.ajax({
                url: "{{ url('pago/ajaxBuscarPorCobrar') }}",
                type: 'POST',
                data:{
                    nombre   : $('#buscar_nombre').val(),
                    appaterno: $('#buscar_appaterno').val(),
                    apmaterno: $('#buscar_apmaterno').val(),
                    cedula   : $('#buscar_cedula').val(),
                    placa    : $('#buscar_placa').val(),
                    fecha_ini: $('#buscar_fecha_ini').val(),
                    fecha_fin: $('#buscar_fecha_fin').val(),
                },
                dataType: 'json',
                success: function(data) {
                    if(data.estado === 'success'){
                        $('#table_porcobrar').html(data.listado)
                    }
                }
            });
        }

        function abreModalPagar(factura, cliente, placa, total, pagado){
            $('#cliente').val(cliente)
            $('#factura_id').val(factura)
            $('#placa').val(placa)
            $('#impor_total').val(total)
            $('#impor_pagado').val(pagado)
            $('#impor_saldo').val((total)-(pagado))
            $("#importe_pagar").val(0);
            $("#tipo_pago").val("");
            $("#importe_pagar").attr("max", (total)-(pagado));

            // Obtén el botón y el icono de carga
            var boton = $("#boton_pagarCuenta");
            var iconoCarga = boton.find("i");
            // Deshabilita el botón y muestra el icono de carga
            boton.attr("disabled", false);
            iconoCarga.show();

            $('#modalCobrar').modal('show')
        }

        function pagarCuenta(){
            if($("#formulario_cobro")[0].checkValidity()){
                
                // Obtén el botón y el icono de carga
                var boton = $("#boton_pagarCuenta");
                var iconoCarga = boton.find("i");
                // Deshabilita el botón y muestra el icono de carga
                boton.attr("disabled", true);
                iconoCarga.show();

                datos = $("#formulario_cobro").serializeArray()
                $.ajax({
                    url: "{{ url('pago/pagarCuenta') }}",
                    data:datos,
                    type: 'POST',
                    dataType: 'json',
                    success: function(data) {
                        if(data.estado === 'success'){
                            Swal.fire({
                                icon: 'success',
                                title: "Se guardo con exito!",
                                showConfirmButton: false, // No mostrar botón de confirmación
                                timer: 1500, // 5 segundos
                                timerProgressBar: true
                            });
                            ajaxListado();
                            $('#modalCobrar').modal('hide')
                        }
                    }
                });
            }else{
    			$("#formulario_cobro")[0].reportValidity()
            }
        }

        function verQueDebe(){

        }
    </script>
@endsection


