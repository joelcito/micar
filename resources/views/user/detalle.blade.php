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
    <div class="modal fade" id="modalAsignacion" tabindex="-1" aria-hidden="true">
        <!--begin::Modal dialog-->
        <div class="modal-dialog modal-dialog-centered">
            <!--begin::Modal content-->
            <div class="modal-content">
                <!--begin::Modal header-->
                <div class="modal-header" id="kt_modal_add_user_header">
                    <!--begin::Modal title-->
                    <h2 class="fw-bold">Formulario de Asignacion</h2>
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
                <div class="modal-body scroll-y">
                    <form id="formularioAsignacion">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">SERVICIO</label>
                                    <select name="servicio_id" id="servicio_id" class="form-control" required>
                                        @foreach ($servicios as $s)
                                            <option value="{{ $s->id }}">{{ $s->descripcion }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="fv-row mb-7">
                                    <label class=" required fw-semibold fs-6 mb-2">PORCENTAJE %</label>
                                    <input type="number" class="form-control"  max="100" min="1" name="porcentaje" id="porcentaje" required>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="row">
                        <div class="col-md-12">
                            <button class="btn btn-success w-100" onclick="guardarAsignacion()">Guardar</button>
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
                <h1>Perfil de Usuario</h1>
                <!--begin::Search-->
                {{-- <div class="d-flex align-items-center position-relative my-1">
                    <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-5">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <input type="text" data-kt-user-table-filter="search" class="form-control form-control-solid w-250px ps-13" placeholder="Buscar Venta" />
                </div> --}}
                <!--end::Search-->
            </div>
            <!--begin::Card title-->
            <!--begin::Card toolbar-->
            <div class="card-toolbar">
                <!--begin::Toolbar-->
                {{-- <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                    <button type="button" class="btn btn-primary" onclick="nuevoCliente()">
                        <i class="ki-duotone ki-plus fs-2"></i>Nueva Cliente
                    </button>
                </div> --}}
                <!--end::Group actions-->
                <!--begin::Modal - Adjust Balance-->
                {{-- <div class="modal fade" id="kt_modal_export_users" tabindex="-1" aria-hidden="true">
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
                </div> --}}
            </div>
            <!--end::Card toolbar-->
        </div>
        <!--end::Card header-->
        <!--begin::Card body-->
        <div class="card-body py-4">
            {{-- <div id="table_categoria">

            </div> --}}
            <div class="row">
                <div class="col-md-3">
                    <div class="fv-row mb-7">
                        <label class="required fw-semibold fs-6 mb-2">Cedula</label>
                        <input type="text" class="form-control" value="{{ $usuario->cedula }}">
                        <input type="text" id="usuario_id" value="{{ $usuario->id  }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="fv-row mb-7">
                        <label class="required fw-semibold fs-6 mb-2">Ap Paterno</label>
                        <input type="text" class="form-control" value="{{ $usuario->ap_paterno }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="fv-row mb-7">
                        <label class="required fw-semibold fs-6 mb-2">Ap Materno</label>
                        <input type="text" class="form-control" value="{{ $usuario->ap_materno }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="fv-row mb-7">
                        <label class="required fw-semibold fs-6 mb-2">Nombres</label>
                        <input type="text" class="form-control" value="{{ $usuario->nombres }}">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="fv-row mb-7">
                        <label class="required fw-semibold fs-6 mb-2">Correo</label>
                        <input type="text" class="form-control" value="{{ $usuario->email }}">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <button class="btn btn-success w-100 btn-sm">ACTUALIZAR</button>
                </div>
            </div>
        </div>
        <!--end::Card body-->
    </div>
    <!--end::Card-->

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid mt-5">
        <!--begin::Content container-->
        <div id="kt_app_content_container" class="app-container container-xxl">
            <!--begin::Row-->
            <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
                <!--begin::Col-->
                <div class="col-xl-12">
                    <!--begin::Table widget 8-->
                    <div class="card h-xl-100">
                        <!--begin::Header-->
                        <div class="card-header position-relative py-0 border-bottom-2">
                            <!--begin::Nav-->
                            <ul class="nav nav-stretch nav-pills nav-pills-custom d-flex mt-3">
                                <!--begin::Nav item-->
                                <li class="nav-item p-0 ms-0 me-8">
                                    <!--begin::Nav link-->
                                    <a class="nav-link btn btn-color-muted px-0 show active" data-bs-toggle="tab" href="#kt_table_widget_7_tab_content_1">
                                        <!--begin::Title-->
                                        <span class="nav-text fw-semibold fs-4 mb-3">ASIGNACIONES</span>
                                        <!--end::Title-->
                                        <!--begin::Bullet-->
                                        <span class="bullet-custom position-absolute z-index-2 w-100 h-2px top-100 bottom-n100 bg-primary rounded"></span>
                                        <!--end::Bullet-->
                                    </a>
                                    <!--end::Nav link-->
                                </li>
                                <!--end::Nav item-->
                                
                                <li class="nav-item p-0 ms-0 me-8">
                                    <a class="nav-link btn btn-color-muted px-0" data-bs-toggle="tab" href="#kt_table_widget_7_tab_content_servicios">
                                        <span class="nav-text fw-semibold fs-4 mb-3">SERVICIOS</span>
                                        <span class="bullet-custom position-absolute z-index-2 w-100 h-2px top-100 bottom-n100 bg-primary rounded"></span>
                                    </a>
                                </li>
                                <li class="nav-item p-0 ms-0 me-8">
                                    <a class="nav-link btn btn-color-muted px-0" data-bs-toggle="tab" href="#kt_table_widget_7_tab_content_2">
                                        <span class="nav-text fw-semibold fs-4 mb-3">LIQUIDACION</span>
                                        <span class="bullet-custom position-absolute z-index-2 w-100 h-2px top-100 bottom-n100 bg-primary rounded"></span>
                                    </a>
                                </li>
                            </ul>
                            <!--end::Nav-->
                        </div>
                        <!--end::Header-->
                        <!--begin::Body-->
                        <div class="card-body">
                            <!--begin::Tab Content (ishlamayabdi)-->
                            <div class="tab-content mb-2">
                                <!--begin::Tap pane-->
                                <div class="tab-pane fade show active" id="kt_table_widget_7_tab_content_1">

                                    <!--begin::Card-->
                                    <div class="card">
                                        <div class="card-header border-0">
                                            <div class="card-title">
                                                <h1>Listado de Asignacion de Servicios</h1>
                                            </div>
                                            <div class="card-toolbar">
                                                <button class="btn btn-sm btn-primary" type="button" onclick="nuevoAsignacion()">Nuevo Asignacion <i class="fa fa-plus"></i></button>
                                            </div>
                                        </div>
                                        <div class="card-body py-4">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div id="tabla_vehiuclos">

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end::Card body-->
                                    </div>
                                    <!--end::Card-->

                                </div>
                                <!--end::Tap pane-->
                                

                                <div class="tab-pane fade" id="kt_table_widget_7_tab_content_servicios">

                                    <!--begin::Card-->
                                    <div class="card">
                                        <div class="card-header border-0">
                                            <div class="card-title">
                                                <h1>Servicios Realizados</h1>
                                            </div>
                                        </div>
                                        <div class="card-body py-4">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="table-responsive">
                                                        <!--begin::Table-->
                                                            <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_users1">
                                                                <thead>
                                                                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                                                        <th>Servicio</th>
                                                                        <th>Precio</th>
                                                                        <th>Cantidad</th>
                                                                        <th>Descuento</th>
                                                                        <th>Total</th>
                                                                        <th>Fecha</th>
                                                                        <th>Estado</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody class="text-gray-600 fw-semibold">
                                                                    @forelse ( $servicios as  $ser )
                                                                        <tr>
                                                                            <td>{{ $ser->servicio->descripcion }}</td>
                                                                            <td>{{ $ser->precio }}</td>
                                                                            <td>{{ $ser->cantidad }}</td>
                                                                            <td>{{ $ser->descuento }}</td>
                                                                            <td>{{ $ser->total }}</td>
                                                                            <td>{{ $ser->fecha }}</td>
                                                                            <td>
                                                                                @if($ser->estado_liquidacion === 'Debe')
                                                                                    <small class="badge badge-danger">Debe</small>
                                                                                @else
                                                                                   <small class="badge badge-success">Pagado</small>
                                                                                @endif
                                                                        </tr>
                                                                    @empty
                                                                        <h4 class="text-danger text-center">Sin registros</h4>
                                                                    @endforelse
                                                                </tbody>
                                                            </table>
                                                        <!--end::Table-->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end::Card body-->
                                    </div>
                                    <!--end::Card-->

                                </div>
                                <!--begin::Tap pane-->
                                <div class="tab-pane fade" id="kt_table_widget_7_tab_content_2">

                                    <!--begin::Card-->
                                    <div class="card">
                                        <div class="card-header border-0">
                                            <div class="card-title">
                                                <h1>Listado de Liquidaciones</h1>
                                            </div>
                                        </div>
                                        <div class="card-body py-4">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="table-responsive">
                                                        <!--begin::Table-->
                                                            <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_users1">
                                                                <thead>
                                                                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                                                        <th class="text-center">Fecha Pagado Ini</th>
                                                                        <th class="text-center">Fecha Pagado Fin</th>
                                                                        <th class="text-center">Total Servicio</th>
                                                                        <th class="text-center">Cuenta por Cobrar</th>
                                                                        <th class="text-center">Liquido Pagado</th>
                                                                        <th class="text-center"></th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody class="text-gray-600 fw-semibold">
                                                                    @forelse ( $liquidaciones as  $liq )
                                                                        <tr>
                                                                            <td class="text-center">{{ $liq->fecha_pagado_ini }}</td>
                                                                            <td class="text-center">{{ $liq->fecha_pagado_fin }}</td>
                                                                            <td class="text-center">{{ number_format($liq->total_servicios,2) }}</td>
                                                                            <td class="text-center">{{ number_format($liq->cuenta_por_pagar,2) }}</td>
                                                                            <td class="text-center">{{ number_format($liq->liquido_pagable,2) }}</td>
                                                                            <td class="text-center">
                                                                                <a href="{{ url('pago/imprimeLiquidacionVendedor', [$liq->id]) }}"class="btn btn-info btn-icon btn-sm"><i class="fa fa-eye"></i></a>
                                                                            </td>
                                                                        </tr>
                                                                    @empty
                                                                        <h4 class="text-danger text-center">Sin registros</h4>
                                                                    @endforelse
                                                                </tbody>
                                                            </table>
                                                        <!--end::Table-->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end::Card body-->
                                    </div>
                                    <!--end::Card-->

                                </div>
                            </div>
                            <!--end::Tab Content-->
                        </div>
                        <!--end: Card Body-->
                    </div>
                    <!--end::Table widget 8-->
                </div>
                <!--end::Col-->
            </div>
            <!--end::Row-->
        </div>
        <!--end::Content container-->
    </div>
    <!--end::Content-->



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

        // $(function () {
        //     $('#myTab li:last-child a').tab('show')
        //     console.log($('#myTab li:last-child a'))
        // })

        $( document ).ready(function() {
            ajaxListado();
        });

        function ajaxListado(){
            $.ajax({
                url: "{{ url('servicio/ajaxListadoAsignaciones') }}",
                type: 'POST',
                data:{
                    usuario:$('#usuario_id').val()
                },
                dataType: 'json',
                success: function(data) {
                    if(data.estado === 'success')
                        $('#tabla_vehiuclos').html(data.listado);
                }
            });
        }

        function guardarAsignacion(){
            if($("#formularioAsignacion")[0].checkValidity()){
                $.ajax({
                    url: "{{ url('servicio/guardarAsignacion') }}",
                    data:{
                        servicio  : $('#servicio_id').val(),
                        porcentaje: $('#porcentaje').val(),
                        usuario   : $('#usuario_id').val()
                    },
                    type: 'POST',
                    dataType: 'json',
                    success: function(data) {
                        if(data.estado === 'success'){
                            ajaxListado();
                            Swal.fire({
                                icon: 'success',
                                title: 'Correcto!',
                                text: 'Se registro con exito!',
                                timer: 1500
                            })
                            $('#modalAsignacion').modal('hide');
                        }else{
                            Swal.fire({
                                icon: 'error',
                                title: 'Error al crear!',
                                text: data.msg,
                                timer: 5000
                            })
                        }
                    }
                });
            }else{
    			$("#formularioAsignacion")[0].reportValidity()
            }
        }

        function nuevoAsignacion(){
            $('#modalAsignacion').modal('show')
        }
    </script>
@endsection


