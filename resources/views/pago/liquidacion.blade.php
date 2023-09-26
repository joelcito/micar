@extends('layouts.app')
@section('css')
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('metadatos')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection
@section('content')

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
            //ajaxListadoFinanzas();
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

        function selecionarLavador(lavador){
            $.ajax({
                url: "{{ url('pago/selecionarLavador') }}",
                type: 'POST',
                data:{
                    lavador: lavador,
                    fecha  : $('#fecha_lavador_'+lavador).val()
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


