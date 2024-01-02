@extends('layouts.app')
@section('css')
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('metadatos')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection
@section('content')

    <!--end::Modal - New Card-->
    <!--begin::Modal - Add task-->
    <div class="modal fade" id="kt_modal_add_servicio" tabindex="-1" aria-hidden="true">
        <!--begin::Modal dialog-->
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <!--begin::Modal content-->
            <div class="modal-content">
                <!--begin::Modal header-->
                <div class="modal-header" id="kt_modal_add_user_header">
                    <!--begin::Modal title-->
                    <h2 class="fw-bold">Formulario de servicio</h2>
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
                <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">

                    <form id="formularioServicio">
                        @csrf
                        <div class="row">
                            <div class="col-md-7">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Descripcion</label>
                                    <input type="text" class="form-control form-control-solid" required name="descripcion" id="descripcion" placeholder="LAVADO BASICO DE AUTO">
                                    <input type="hidden" name="servicio_id" id="servicio_id">
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="fv-row mb-4">
                                    <label class="required fw-semibold fs-6 mb-2">Categoria</label>
                                    <select name="categoria_id" id="categoria_id" class="form-control form-control-solid">
                                        <option value="">Seleccione</option>
                                        @foreach ($categorias as $c)
                                        <option value="{{ $c->id }}">{{ $c->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Unidad de venta</label>
                                    <input type="text" name="unidad_venta" id="unidad_venta" class="form-control form-control-solid mb-3 mb-lg-0" required/>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Precio</label>
                                    <input type="number" name="precio" id="precio" class="form-control form-control-solid mb-3 mb-lg-0" required/>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Cod. Actividad</label>
                                    <input type="number" name="cod_actividad" id="cod_actividad" class="form-control form-control-solid mb-3 mb-lg-0" required/>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Cod. Producto</label>
                                    <input type="number" name="cod_producto" id="cod_producto" class="form-control form-control-solid mb-3 mb-lg-0" required/>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Unidad de medida</label>
                                    <input type="number" name="uni_medida" id="uni_medida" class="form-control form-control-solid mb-3 mb-lg-0" required/>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="row">
                        <div class="col-md-12">
                            <button class="btn btn-success w-100" onclick="guardarServicio()">Guardar</button>
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
        <!--begin::Card header-->
        <div class="card-header border-0 pt-6 bg-light-primary">
            <div class="card-title">
                <h3>Listado de Servicios</h3>
            </div>
            <div class="card-toolbar">
                <!--begin::Toolbar-->
                <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                    <button type="button" class="btn btn-primary" onclick="nuevoServicio()">
                    <i class="ki-duotone ki-plus fs-2"></i>Nuevo Servicio</button>
                    <!--end::Add user-->
                </div>
            </div>
            <!--end::Card toolbar-->
        </div>
        <!--end::Card header-->
        <!--begin::Card body-->
        <div class="card-body py-4">
            <div id="table_servicios">

            </div>
        </div>
        <!--end::Card body-->
    </div>
    <!--end::Card-->
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

        function ajaxListado(){
            $.ajax({
                url: "{{ url('servicio/ajaxListado') }}",
                type: 'POST',
                dataType: 'json',
                success: function(data) {
                    if(data.estado === 'success'){
                        $('#table_servicios').html(data.listado);
                    }
                }
            });
        }

       function guardarServicio(){
            if($("#formularioServicio")[0].checkValidity()){
                datos = $("#formularioServicio").serializeArray()
                $.ajax({
                    url: "{{ url('servicio/guarda') }}",
                    data:datos,
                    type: 'POST',
                    dataType: 'json',
                    success: function(data) {
                        if(data.estado === 'success'){
                            $('#table_servicios').html(data.listado);
                            Swal.fire({
                                icon: 'success',
                                title: 'Correcto!',
                                text: 'Se cambio con exito!',
                                timer: 1000
                            })
                            $('#kt_modal_add_servicio').modal('hide');
                        }
                    }
                });
            }else{
    			$("#formularioServicio")[0].reportValidity()
            }
        }

        function nuevoServicio(){
            $('#servicio_id').val(0)
            $('#descripcion').val('')
            $('#precio').val(0)
            $('#unidad_venta').val('UNIDAD')
            $('#categoria_id').val('')
            $('#cod_actividad').val('')
            $('#cod_producto').val('')
            $('#uni_medida').val('')
            $('#kt_modal_add_servicio').modal('show');
        }

        function editarServicio(servicio, descripcion, categoria, unidad, precio, cod_actividad, cod_producto, uni_medida){
            $('#servicio_id').val(servicio)
            $('#descripcion').val(descripcion)
            $('#categoria_id').val(categoria)
            $('#unidad_venta').val(unidad)
            $('#precio').val(precio)
            $('#cod_actividad').val(cod_actividad)
            $('#cod_producto').val(cod_producto)
            $('#uni_medida').val(uni_medida)

            $('#kt_modal_add_servicio').modal('show');
        }

        function eliminaServicio(servicio){
            Swal.fire({
                title: 'Estas seguro de eliminar el servicio ?',
                text: "No podrás revertir esto.!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si, eliminar!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('servicio/eliminar') }}",
                        type: 'POST',
                        data:{id:servicio},
                        dataType: 'json',
                        success: function(data) {
                            if(data.estado === 'success'){
                                $('#table_servicios').html(data.listado);
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Eliminado!',
                                    text: 'El sericio se elimino!',
                                    timer: 1000
                                })
                            }
                        }
                    });
                }
            })
        }

        function bloqueCAFC(){
            console.log()
        }

        // Agregar un evento para verificar el radio seleccionado al cambiar
        $('input[name="uso_cafc"]').on('change', function() {
            verificarRadioSeleccionado();
        });

        function verificarRadioSeleccionado() {
            // Obtener el valor del radio seleccionado
            var valorSeleccionado = $('input[name="uso_cafc"]:checked').val();

            // Hacer algo con el valor seleccionado
            if (valorSeleccionado === 'No') {
                console.log('El radio seleccionado es "No"');
            } else if (valorSeleccionado === 'Si') {
                console.log('El radio seleccionado es "Si"');
            }
        }
    </script>
@endsection


