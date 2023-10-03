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
                                <input type="text" name="factura_id" id="factura_id">
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
                                <input type="number" type="button" class="form-control" name="importe_pagar" id="importe_pagar" required min="1" value="0">
                            </div>
                        </div>
                    </div>
                </form>
                <div class="row">
                    <div class="col-md-12">
                        <button class="btn btn-sm btn-success w-100" onclick="pagarCuenta()">Pagar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


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
            <div id="datos_lavador">

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
            $(".formBus").on("input", function () {
                var inputText = $(this).val();
                if (inputText.length >= 3) {
                    $('#table_lavadores').show('toogle')
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

            // $('#cuentas_por_cobrar_pagar').on("input", function() {
            //     console.log("haber")
            //     // let total_servico           = $("#total_servicios_lavador").val();
            //     // let total_acuenta_porcobrar = $(this).val();
            //     // console.log(total_servico, total_acuenta_porcobrar, (total_servico - total_acuenta_porcobrar))
            // });
        });

        function realizarCalculo(){
            let total_servico           = $("#total_servicios_lavador").val();
            let total_acuenta_porcobrar = $("#cuentas_por_cobrar_pagar").val();
            let total                   = total_servico - total_acuenta_porcobrar;
            $("#total_liquido_pagable").val(total)
        }

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

        function selecionarLavador(lavador){
            $.ajax({
                url: "{{ url('pago/selecionarLavador') }}",
                type: 'POST',
                data:{
                    lavador  : lavador,
                    fecha_ini: $('#fecha_lavador_ini_'+lavador).val(),
                    fecha_fin: $('#fecha_lavador_fin_'+lavador).val()
                },
                dataType: 'json',
                success: function(data) {
                    if(data.estado === 'success'){
                        $('#table_lavadores').hide('toogle')
                        $('#datos_lavador').html(data.listado);
                    }
                }
            });
        }

        function buscarCuentasPorCobrar(){
            let lavador = $('#cliente_lavador').val()
            $.ajax({
                url: "{{ url('pago/buscarCuentasPorCobrar') }}",
                type: 'POST',
                data:{
                    lavador: lavador
                },
                dataType: 'json',
                success: function(data) {
                    if(data.estado === 'success'){
                        $('#facturas_pendientes').html(data.listado)
                        $('#facturas_pendientes').show('toogle')
                        $("#cuentas_por_cobrar_pagar").attr("max", $('#cuenta_por_cobrar_total').val());
                    }
                }
            });
        }

        function abreModalPagar(){
            $('#cliente').val(cliente)
            $('#factura_id').val(factura)
            $('#placa').val(placa)
            $('#impor_total').val(total)
            $('#impor_pagado').val(pagado)
            $('#impor_saldo').val((total)-(pagado))
            $("#importe_pagar").attr("max", (total)-(pagado));
            $('#modalCobrar').modal('show')
        }
        
    </script>
@endsection


