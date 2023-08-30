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
        <div class="card-body py-4">
            <div class="row">
                <div class="col-md-4">
                    <h4 class="text-center">Tipo de Documento</h4>
                    <div id="tabla_tipo_documento">

                    </div>
                    <button class="btn btn-primary btn-sm w-100" onclick="sincronizarTipoDocumento()">Sincronizar</button>
                </div>
                <div class="col-md-4">
                    <h4 class="text-center">Motivo Anulacion</h4>
                    <div id="tabla_motivo_anulacion">

                    </div>
                    <button class="btn btn-primary btn-sm w-100" onclick="sincronizarMotivoAnulacion()">Sincronizar</button>
                </div>
                <div class="col-md-4">
                    <h4 class="text-center">Tipo de Evento</h4>
                    <div id="tabla__tipo_evento">

                    </div>
                    <button class="btn btn-primary btn-sm w-100" onclick="sincronizarTipoEvento()">Sincronizar</button>
                </div>
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
            ajaxListadoTipoDocumento();
            ajaxListadoMotivoAnulacion();
            ajaxListadoTipoEvento();
        });

        function ajaxListadoTipoDocumento(){
            $.ajax({
                url: "{{ url('sincronizacionCatalogo/ajaxListadoTipoDocumento') }}",
                type: 'POST',
                dataType: 'json',
                success: function(data) {
                    if(data.estado === 'success')
                        $('#tabla_tipo_documento').html(data.listado);
                }
            });
        }

        function ajaxListadoMotivoAnulacion(){
            $.ajax({
                url: "{{ url('sincronizacionCatalogo/ajaxListadoMotivoAnulacion') }}",
                type: 'POST',
                dataType: 'json',
                success: function(data) {
                    if(data.estado === 'success')
                        $('#tabla_motivo_anulacion').html(data.listado);
                }
            });
        }

        function ajaxListadoTipoEvento(){
            $.ajax({
                url: "{{ url('sincronizacionCatalogo/ajaxListadoTipoEvento') }}",
                type: 'POST',
                dataType: 'json',
                success: function(data) {
                    if(data.estado === 'success')
                        $('#tabla__tipo_evento').html(data.listado);
                }
            });
        }

        function sincronizarTipoDocumento(){
            $.ajax({
                url: "{{ url('sincronizacionCatalogo/sincronizarTipoDocumento') }}",
                type: 'POST',
                dataType: 'json',
                success: function(data) {
                    if(data.estado === 'success'){
                        Swal.fire({
                            icon             : 'success',
                            title            : data.msg,
                            showConfirmButton: false,       // No mostrar botón de confirmación
                            timer            : 2000,        // 5 segundos
                            timerProgressBar : true
                        });
                        ajaxListadoTipoDocumento();
                    }else{
                        Swal.fire({
                            icon             : 'error',
                            title            : data.msg,
                            showConfirmButton: false,      // No mostrar botón de confirmación
                            timer            : 2000,       // 5 segundos
                            timerProgressBar : true
                        });
                    }
                }
            });
        }

        function sincronizarMotivoAnulacion(){
            $.ajax({
                url: "{{ url('sincronizacionCatalogo/sincronizarMotivoAnulacion') }}",
                type: 'POST',
                dataType: 'json',
                success: function(data) {
                    if(data.estado === 'success'){
                        Swal.fire({
                            icon: 'success',
                            title: data.msg,
                            showConfirmButton: false, // No mostrar botón de confirmación
                            timer: 2000, // 5 segundos
                            timerProgressBar: true
                        });
                        ajaxListadoMotivoAnulacion();
                    }else{
                        Swal.fire({
                            icon: 'error',
                            title: data.msg,
                            showConfirmButton: false, // No mostrar botón de confirmación
                            timer: 2000, // 5 segundos
                            timerProgressBar: true
                        });
                    }
                }
            });
        }

        function sincronizarTipoEvento(){
            $.ajax({
                url: "{{ url('sincronizacionCatalogo/sincronizarTipoEvento') }}",
                type: 'POST',
                dataType: 'json',
                success: function(data) {
                    if(data.estado === 'success'){
                        Swal.fire({
                            icon: 'success',
                            title: data.msg,
                            showConfirmButton: false, // No mostrar botón de confirmación
                            timer: 2000, // 5 segundos
                            timerProgressBar: true
                        });
                        ajaxListadoTipoEvento();
                    }else{
                        Swal.fire({
                            icon: 'error',
                            title: data.msg,
                            showConfirmButton: false, // No mostrar botón de confirmación
                            timer: 2000, // 5 segundos
                            timerProgressBar: true
                        });
                    }
                }
            });
        }

    </script>
@endsection


