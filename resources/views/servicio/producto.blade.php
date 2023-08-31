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
    <div class="modal fade" id="modalAgregarProduto" tabindex="-1" aria-hidden="true">
        <!--begin::Modal dialog-->
        <div class="modal-dialog modal-dialog-centered">
            <!--begin::Modal content-->
            <div class="modal-content">
                <!--begin::Modal header-->
                <div class="modal-header" id="kt_modal_add_user_header">
                    <!--begin::Modal title-->
                    <h2 class="fw-bold">Formulario de agregacion de item de <span class="text-info" id="texto_titulo"></span> </h2>
                    <!--end::Modal title-->
                    <!--begin::Close-->
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-users-modal-action="close">
                        <i class="ki-duotone ki-cross fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                    <!--end::Close-->
                </div>
                <!--end::Modal header-->
                <!--begin::Modal body-->
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
        <div class="card-header border-0 pt-6">
            <!--begin::Card title-->
            <div class="card-title">
                <!--begin::Search-->
                <div class="d-flex align-items-center position-relative my-1">
                    <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-5">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <input type="text" data-kt-user-table-filter="search" class="form-control form-control-solid w-250px ps-13" placeholder="Buscar Venta" />
                </div>
                <!--end::Search-->
            </div>
            <!--begin::Card title-->
            <!--begin::Card toolbar-->
            <div class="card-toolbar">
                <!--begin::Toolbar-->
                <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                    <button type="button" class="btn btn-primary" onclick="nuevoServicio()">
                    <i class="ki-duotone ki-plus fs-2"></i>Nuevo Producto</button>
                    <!--end::Add user-->
                </div>
                <!--end::Toolbar-->
                <!--begin::Group actions-->
                <div class="d-flex justify-content-end align-items-center d-none" data-kt-user-table-toolbar="selected">
                    <div class="fw-bold me-5">
                    <span class="me-2" data-kt-user-table-select="selected_count"></span>Selected</div>
                    <button type="button" class="btn btn-danger" data-kt-user-table-select="delete_selected">Delete Selected</button>
                </div>
                <!--end::Group actions-->
                <!--begin::Modal - Adjust Balance-->
                <div class="modal fade" id="kt_modal_export_users" tabindex="-1" aria-hidden="true">
                    <!--begin::Modal dialog-->
                    <div class="modal-dialog modal-dialog-centered mw-650px">
                        <!--begin::Modal content-->
                        <div class="modal-content">
                            <!--begin::Modal header-->
                            <div class="modal-header">
                                <!--begin::Modal title-->
                                <h2 class="fw-bold">Export Users</h2>
                                <!--end::Modal title-->
                                <!--begin::Close-->
                                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-users-modal-action="close">
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
                                <!--begin::Form-->
                                <form id="kt_modal_export_users_form" class="form" action="#">
                                    <!--begin::Input group-->
                                    <div class="fv-row mb-10">
                                        <!--begin::Label-->
                                        <label class="fs-6 fw-semibold form-label mb-2">Select Roles:</label>
                                        <!--end::Label-->
                                        <!--begin::Input-->
                                        <select name="role" data-control="select2" data-placeholder="Select a role" data-hide-search="true" class="form-select form-select-solid fw-bold">
                                            <option></option>
                                            <option value="Administrator">Administrator</option>
                                            <option value="Analyst">Analyst</option>
                                            <option value="Developer">Developer</option>
                                            <option value="Support">Support</option>
                                            <option value="Trial">Trial</option>
                                        </select>
                                        <!--end::Input-->
                                    </div>
                                    <!--end::Input group-->
                                    <!--begin::Input group-->
                                    <div class="fv-row mb-10">
                                        <!--begin::Label-->
                                        <label class="required fs-6 fw-semibold form-label mb-2">Select Export Format:</label>
                                        <!--end::Label-->
                                        <!--begin::Input-->
                                        <select name="format" data-control="select2" data-placeholder="Select a format" data-hide-search="true" class="form-select form-select-solid fw-bold">
                                            <option></option>
                                            <option value="excel">Excel</option>
                                            <option value="pdf">PDF</option>
                                            <option value="cvs">CVS</option>
                                            <option value="zip">ZIP</option>
                                        </select>
                                        <!--end::Input-->
                                    </div>
                                    <!--end::Input group-->
                                    <!--begin::Actions-->
                                    <div class="text-center">
                                        <button type="reset" class="btn btn-light me-3" data-kt-users-modal-action="cancel">Discard</button>
                                        <button type="submit" class="btn btn-primary" data-kt-users-modal-action="submit">
                                            <span class="indicator-label">Submit</span>
                                            <span class="indicator-progress">Please wait...
                                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                        </button>
                                    </div>
                                    <!--end::Actions-->
                                </form>
                                <!--end::Form-->
                            </div>
                            <!--end::Modal body-->
                        </div>
                        <!--end::Modal content-->
                    </div>
                    <!--end::Modal dialog-->
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

        function modalAgregarProducto(servicio,nombre){
            $('#tipo').val('ingreso')
            $('#servicio_id').val(servicio)
            $('#texto_titulo').text(nombre)
            $('#modalAgregarProduto').modal('show')
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
                                text: 'Se agrego con exito!',
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

    //    function guardarServicio(){
    //         if($("#formularioServicio")[0].checkValidity()){
    //             datos = $("#formularioServicio").serializeArray()
    //             $.ajax({
    //                 url: "{{ url('servicio/guarda') }}",
    //                 data:datos,
    //                 type: 'POST',
    //                 dataType: 'json',
    //                 success: function(data) {
    //                     if(data.estado === 'success'){
    //                         $('#table_servicios').html(data.listado);
    //                         Swal.fire({
    //                             icon: 'success',
    //                             title: 'Correcto!',
    //                             text: 'Se cambio con exito!',
    //                             timer: 1000
    //                         })
    //                         $('#kt_modal_add_servicio').modal('hide');
    //                     }
    //                 }
    //             });
    //         }else{
    // 			$("#formularioServicio")[0].reportValidity()
    //         }
    //     }

    //     function nuevoServicio(){
    //         $('#servicio_id').val(0)
    //         $('#descripcion').val('')
    //         $('#categoria_id').val('')
    //         $('#kt_modal_add_servicio').modal('show');
    //     }

    //     function editarServicio(servicio, descripcion, categoria, unidad, precio){
    //         $('#servicio_id').val(servicio)
    //         $('#descripcion').val(descripcion)
    //         $('#categoria_id').val(categoria)
    //         $('#unidad_venta').val(unidad)
    //         $('#precio').val(precio)

    //         $('#kt_modal_add_servicio').modal('show');
    //     }

    //     function eliminaServicio(servicio){
    //         Swal.fire({
    //             title: 'Estas seguro de eliminar el servicio ?',
    //             text: "No podrás revertir esto.!",
    //             icon: 'warning',
    //             showCancelButton: true,
    //             confirmButtonColor: '#3085d6',
    //             cancelButtonColor: '#d33',
    //             confirmButtonText: 'Si, eliminar!'
    //         }).then((result) => {
    //             if (result.isConfirmed) {
    //                 $.ajax({
    //                     url: "{{ url('servicio/eliminar') }}",
    //                     type: 'POST',
    //                     data:{id:servicio},
    //                     dataType: 'json',
    //                     success: function(data) {
    //                         if(data.estado === 'success'){
    //                             $('#table_servicios').html(data.listado);
    //                             Swal.fire({
    //                                 icon: 'success',
    //                                 title: 'Eliminado!',
    //                                 text: 'El sericio se elimino!',
    //                                 timer: 1000
    //                             })
    //                         }
    //                     }
    //                 });
    //             }
    //         })
    //     }

    //     function bloqueCAFC(){
    //         console.log()
    //     }

    //     // Agregar un evento para verificar el radio seleccionado al cambiar
    //     $('input[name="uso_cafc"]').on('change', function() {
    //         verificarRadioSeleccionado();
    //     });

    //     function verificarRadioSeleccionado() {
    //         // Obtener el valor del radio seleccionado
    //         var valorSeleccionado = $('input[name="uso_cafc"]:checked').val();

    //         // Hacer algo con el valor seleccionado
    //         if (valorSeleccionado === 'No') {
    //             console.log('El radio seleccionado es "No"');
    //         } else if (valorSeleccionado === 'Si') {
    //             console.log('El radio seleccionado es "Si"');
    //         }
    //     }
    </script>
@endsection


