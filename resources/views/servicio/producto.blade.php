@extends('layouts.app')
@section('css')
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('metadatos')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection
@section('content')

    <!--begin::Modal - Add task-->
    <div class="modal fade" id="modalEditarProduto" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" id="kt_modal_add_user_header">
                    <h2 class="fw-bold">FORMULARIO DE PRODUCTO</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary"data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                </div>
                <div class="modal-body scroll-y mx-5 my-7">
                    <form id="formularioNewProducto">
                        @csrf
                        <div class="row">
                            <div class="col-md-9">
                                <div class="fv-row mb-4">
                                    <label class="required fw-semibold fs-6 mb-2">Descripcion</label>
                                    <input type="text" class="form-control form-control-solid" required name="new_descripcion" id="new_descripcion">
                                    <input type="hidden" value="0" id="new_producto" name="new_producto">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Uni. venta</label>
                                    <input type="text" class="form-control form-control-solid" required name="new_uni_venta" id="new_uni_venta" value="UNIDAD" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="fv-row mb-4">
                                    <label class="required fw-semibold fs-6 mb-2">Precio</label>
                                    <input type="number" class="form-control form-control-solid" required name="new_precio" id="new_precio" min="0.1" step="0.01">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Cantidad</label>
                                    <input type="number" class="form-control form-control-solid" required name="new_cantidad" id="new_cantidad" min="1">
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="row">
                        <div class="col-md-12">
                            <button class="btn btn-success w-100" onclick="agregarProdcuto()">Guardar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--end::Modal - New Card-->
    <!--begin::Modal - Add task-->
    <div class="modal fade" id="modalAgregarProduto" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" id="kt_modal_add_user_header">
                    <h2 class="fw-bold"><span id="tipo_formulario"></span> <span class="text-info" id="texto_titulo"></span> </h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary"data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                </div>
                <div class="modal-body scroll-y mx-5 my-7">
                    <form id="formularioServicio">
                        @csrf
                        <div class="row">
                            <div class="col-md-3">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Cantidad</label>
                                    <input type="number" class="form-control form-control-solid" required name="cantidad" id="cantidad">
                                    <input type="hidden" name="servicio_id" id="servicio_id">
                                    <input type="hidden" name="tipo" id="tipo">
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="fv-row mb-4">
                                    <label class="required fw-semibold fs-6 mb-2">Descripcion</label>
                                    <input type="text" class="form-control form-control-solid" required name="descripcion" id="descripcion">
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="row">
                        <div class="col-md-12">
                            <button class="btn btn-success w-100" onclick="guardaProdcuto()">Guardar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!--begin::Card-->
    <div class="card">
        <div class="card-header border-0 pt-6 bg-light-primary">
            <div class="card-title">
                <h3>Listado de Productos</h3>
            </div>
            <div class="card-toolbar">
                <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                    <button type="button" class="btn btn-primary" onclick="modalNuevoProducto()">
                    <i class="ki-duotone ki-plus fs-2"></i>Nuevo Producto</button>
                </div>
            </div>
            <!--end::Card toolbar-->
        </div>
        <!--end::Card header-->
        <!--begin::Card body-->
        <div class="card-body py-4">
            <div id="table_prodcutos">

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
            $('#cantidad').on('input', function() {
                var valor = $(this).val();
                var valorPrecio = $('#precio_unitario').val();
                $('#total_pagar').val((valor*valorPrecio))
            });
        });

        function ajaxListado(){
            $.ajax({
                url: "{{ url('servicio/ajaxListadoProducto') }}",
                type: 'POST',
                dataType: 'json',
                success: function(data) {
                    if(data.estado === 'success'){
                        $('#table_prodcutos').html(data.listado);
                    }
                }
            });
        }

        function modalAgregarProducto(servicio,nombre, precio){
            $('#tipo').val('ingreso')
            $('#tipo_formulario').text("Formulario de ingreso de item de")
            $('#servicio_id').val(servicio)
            $('#precio_unitario').val(precio)
            $('#texto_titulo').text(nombre)
            $('#modalAgregarProduto').modal('show')
        }

        function modalQuitarProducto(servicio,nombre, precio){
            $('#tipo_formulario').text("Formulario de salida de item de")
            $('#tipo').val('salida')
            $('#servicio_id').val(servicio)
            $('#precio_unitario').val(precio)
            $('#texto_titulo').text(nombre)
            $('#modalAgregarProduto').modal('show')
        }

        function modalNuevoProducto(){
            $('#new_producto').val(0)
            $('#new_descripcion').val('')
            $('#new_precio').val(0)
            $('#new_cantidad').val(0)
            $("#new_cantidad").prop('disabled', false);
            $('#modalEditarProduto').modal('show');
        }

        function modalModificar(servicio,nombre, precio, cantidad){
            $('#new_producto').val(servicio)
            $('#new_descripcion').val(nombre)
            $('#new_precio').val(precio)
            $('#new_cantidad').val(cantidad)
            $("#new_cantidad").prop('disabled', true);
            $('#modalEditarProduto').modal('show');
        }

        function guardaProdcuto(){
            if($("#formularioServicio")[0].checkValidity()){
                datos = $("#formularioServicio").serializeArray()
                $.ajax({
                    url: "{{ url('servicio/guardaProdcuto') }}",
                    data:datos,
                    type: 'POST',
                    dataType: 'json',
                    success: function(data) {
                        if(data.estado === 'success'){
                            $('#table_prodcutos').html(data.listado);
                            Swal.fire({
                                icon: 'success',
                                title: 'Correcto!',
                                text: 'Se registro con exito!',
                                timer: 1000
                            })
                            $('#modalAgregarProduto').modal('hide');
                        }
                    }
                });
            }else{
    			$("#formularioServicio")[0].reportValidity()
            }
        }

        function agregarProdcuto(){
            if($("#formularioNewProducto")[0].checkValidity()){
                datos = $("#formularioNewProducto").serializeArray()
                $.ajax({
                    url: "{{ url('servicio/agregarProdcuto') }}",
                    data:datos,
                    type: 'POST',
                    dataType: 'json',
                    success: function(data) {
                        if(data.estado === 'success'){
                            Swal.fire({
                                icon: 'success',
                                title: 'Correcto!',
                                text: 'Se registro con exito!',
                                timer: 1000
                            })
                            ajaxListado();
                            $('#modalEditarProduto').modal('hide');
                        }
                    }
                });
            }else{
    			$("#formularioNewProducto")[0].reportValidity()
            }
        }

        function eliminarProduto(prodcuto, nombre){
            Swal.fire({
                title: "Estas seguro de eliminar el producto "+nombre+"?",
                text: "Ya no podras revertir eso!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Si, eliminar!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url     : "{{ url('servicio/eliminarProduto') }}",
                        data    : {id:prodcuto},
                        type    : 'POST',
                        dataType: 'json',
                        success : function(data) {
                            if(data.estado === 'success'){
                                Swal.fire({
                                    icon : 'success',
                                    title: 'Correcto!',
                                    text : 'Se elimino con exito!',
                                    timer: 1000
                                })
                                ajaxListado();
                            }
                        }
                    });
                }
            });
        }
    </script>
@endsection


