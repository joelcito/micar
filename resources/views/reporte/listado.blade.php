@extends('layouts.app')
@section('css')
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('metadatos')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection
@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header border-0 pt-6">
                    <div class="card-title">
                        <h3>REPORTE ESTADO DE CUENTAS POR COBRAR</h3>
                    </div>
                </div>
                <div class="card-body py-4">
                    <form action="{{ url('reporte/reporteCuentaPorCobrar') }}" method="POST" target="_blank">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card-body py-4">
                                    <label class="required fw-semibold fs-6 mb-2">Fecha Ini</label>
                                    <input type="date" name="fecha_ini" class="form-control" value="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card-body py-4">
                                    <label class="required fw-semibold fs-6 mb-2">Fecha Fin</label>
                                    <input type="date" name="fecha_fin" class="form-control" value="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card-body py-4">
                                    <button class="btn btn-block btn-success btn-sm w-100" type="submit">GENERAR</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header border-0 pt-6">
                    <div class="card-title">
                        <h3>REPORTE DE INVENTARIO INGRESOS Y SALIDAS</h3>
                    </div>
                </div>
                <div class="card-body py-4">
                    <form action="{{ url('reporte/reporteInventario') }}" method="POST" target="_blank">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card-body py-4">
                                    <label class="required fw-semibold fs-6 mb-2">Fecha Ini</label>
                                    <input type="date" name="fecha_ini" class="form-control" value="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card-body py-4">
                                    <label class="required fw-semibold fs-6 mb-2">Fecha Fin</label>
                                    <input type="date" name="fecha_fin" class="form-control" value="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card-body py-4">
                                    <button class="btn btn-block btn-success btn-sm w-100" type="submit">GENERAR</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header border-0 pt-6">
                    <div class="card-title">
                        <h3>REPORTE INFORME DE VENTAS</h3>
                    </div>
                </div>
                <div class="card-body py-4">
                    <form action="{{ url('reporte/reporteInformeVenta') }}" method="POST" target="_blank">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card-body py-4">
                                    <label class="required fw-semibold fs-6 mb-2">Fecha Ini</label>
                                    <input type="date" name="fecha_ini" class="form-control" value="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card-body py-4">
                                    <label class="required fw-semibold fs-6 mb-2">Fecha Fin</label>
                                    <input type="date" name="fecha_fin" class="form-control" value="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card-body py-4 mt-9">
                                    <button class="btn btn-block btn-success btn-sm w-100" type="submit">GENERAR</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header border-0 pt-6">
                    <div class="card-title">
                        <h3>REPORTE LIBRO DE VENTAS</h3>
                    </div>
                </div>
                <div class="card-body py-4">
                    <form action="{{ url('reporte/reporteLibroVenta') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card-body py-4">
                                    <label class="required fw-semibold fs-6 mb-2">Fecha Ini</label>
                                    <input type="date" name="fecha_ini" class="form-control" value="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card-body py-4">
                                    <label class="required fw-semibold fs-6 mb-2">Fecha Fin</label>
                                    <input type="date" name="fecha_fin" class="form-control" value="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card-body py-4 mt-9">
                                    <button class="btn btn-block btn-success btn-sm w-100" type="submit">GENERAR <i class="fa fa-file-excel"></i></button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
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

        function generarRepoCuentasCobrar(){

        }

        // function ajaxListado(){
        //     $.ajax({
        //         url: "{{ url('categoria/ajaxListado') }}",
        //         type: 'POST',
        //         dataType: 'json',
        //         success: function(data) {
        //             if(data.estado === 'success')
        //                 $('#table_categoria').html(data.listado);
        //         }
        //     });
        // }

        // function guardarCategoria(){
        //     if($("#formularioCategoria")[0].checkValidity()){
        //         datos = $("#formularioCategoria").serializeArray()
        //         $.ajax({
        //             url: "{{ url('categoria/guarda') }}",
        //             data:datos,
        //             type: 'POST',
        //             dataType: 'json',
        //             success: function(data) {
        //                 if(data.estado === 'success'){
        //                     $('#table_categoria').html(data.listado);
        //                     Swal.fire({
        //                         icon: 'success',
        //                         title: 'Correcto!',
        //                         text: 'Se cambio  con exito!',
        //                         timer: 1500
        //                     })
        //                     $('#kt_modal_add_categoria').modal('hide');
        //                 }
        //             }
        //         });
        //     }else{
    	// 		$("#formularioCategoria")[0].reportValidity()
        //     }
        // }

        // function editarCategoria(categoria, nombre, descripcion){
        //     $('#nombre').val(nombre);
        //     $('#descripcion').val(descripcion);
        //     $('#categoria_id').val(categoria)
        //     $('#kt_modal_add_categoria').modal('show');
        // }

        // function nuevoCategoria(){
        //     $('#nombre').val('');
        //     $('#descripcion').val('');
        //     $('#categoria_id').val(0)
        //     $('#kt_modal_add_categoria').modal('show');
        // }

        // function eliminrCategoria(categoria){
        //     Swal.fire({
        //         title: 'Estas seguro de eliminar la categoria ?',
        //         text: "No podrás revertir esto.!",
        //         icon: 'warning',
        //         showCancelButton: true,
        //         confirmButtonColor: '#3085d6',
        //         cancelButtonColor: '#d33',
        //         confirmButtonText: 'Si, eliminar!'
        //     }).then((result) => {
        //         if (result.isConfirmed) {
        //             $.ajax({
        //                 url: "{{ url('categoria/eliminar') }}",
        //                 type: 'POST',
        //                 data:{id:categoria},
        //                 dataType: 'json',
        //                 success: function(data) {
        //                     if(data.estado === 'success'){
        //                         $('#table_categoria').html(data.listado);
        //                         Swal.fire({
        //                             icon: 'success',
        //                             title: 'Eliminado!',
        //                             text: 'La categoria se elimino!',
        //                             timer: 1000
        //                         })
        //                     }
        //                 }
        //             });
        //         }
        //     })
        // }
    </script>
@endsection


