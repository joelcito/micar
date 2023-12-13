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
                <h1>INFORME DE ARQUEO</h1>
            </div>
            <div class="card-actions">

            </div>
        </div>
        <!--begin::Card body-->
        <div class="card-body py-4">
            <form id="formularioBusqeuda">
                <div class="row">
                    <div class="col-md-4">
                        <label for="">Fecha Inicio</label>
                        <input type="date" class="form-control" id="fechaIni" name="fechaIni">
                    </div>
                    <div class="col-md-4">
                        <label for="">Fecha Fin</label>
                        <input type="date" class="form-control" id="fechaFin" name="fechaFin">
                    </div>
                    <div class="col-md-4">
                        <button class="btn btn-success w-100 btn-sm mt-7" type="button" onclick="ajaxListadoCajas()"><i class="fa fa-search"></i></button>
                    </div>
                </div>
            </form>
            <div id="table_cajas">

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
            ajaxListadoCajas();
        });

        function ajaxListadoCajas(){
            let datos = $('#formularioBusqeuda').serializeArray();
            $.ajax({
                url: "{{ url('pago/ajaxListadoCajas') }}",
                type: 'POST',
                data:datos,
                dataType: 'json',
                success: function(data) {
                    if(data.estado === 'success')
                        $('#table_cajas').html(data.listado);
                }
            });
        }

        // function modalIngreso(){
        //     $('#tipo').val('Ingreso')
        //     $('#text_tipoo_modal').text('Ingreso')
        //     $('#monto').val(0)
        //     $('#descripcion').val('')
        //     $('#modalIngreso').modal('show')
        // }

        // function modalSalida(){
        //     $('#tipo').val('Salida')
        //     $('#monto').val(0)
        //     $('#descripcion').val('')
        //     $('#text_tipoo_modal').text('Salida')
        //     $('#modalIngreso').modal('show')
        // }

        // function guardarTipoIngresoSalida(){
        //     if($("#formularioIngresoSalida")[0].checkValidity()){
        //         datos = $("#formularioIngresoSalida").serializeArray()
        //         $.ajax({
        //             url: "{{ url('pago/guardarTipoIngresoSalida') }}",
        //             data:datos,
        //             type: 'POST',
        //             dataType: 'json',
        //             success: function(data) {
        //                 if(data.estado === 'success'){
        //                     Swal.fire({
        //                         icon             : 'success',
        //                         title            : 'Se registro con exito',
        //                         showConfirmButton: false,       // No mostrar botón de confirmación
        //                         timer            : 2000,        // 5 segundos
        //                         timerProgressBar : true
        //                     });
        //                     $('#modalIngreso').modal('hide')
        //                     ajaxListadoFinanzas();
        //                 }
        //             }
        //         });
        //     }else{
    	// 		$("#formularioIngresoSalida")[0].reportValidity()
        //     }
        // }

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


