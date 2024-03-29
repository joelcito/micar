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
    <div class="modal fade" id="kt_modal_add_user" tabindex="-1" aria-hidden="true">
        <!--begin::Modal dialog-->
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <!--begin::Modal content-->
            <div class="modal-content">
                <!--begin::Modal header-->
                <div class="modal-header" id="kt_modal_add_user_header">
                    <!--begin::Modal title-->
                    <h2 class="fw-bold">Formulario de usuario</h2>
                    <!--end::Modal title-->
                    <!--begin::Close-->
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
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

                    <form id="formularioUsuario">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Nombre</label>
                                    <input type="text" class="form-control form-control-solid" required name="nombres" id="nombres" placeholder="Joel Jonathan" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Ap. Paterno</label>
                                    <input type="text" class="form-control form-control-solid" name="ap_paterno" id="ap_paterno" placeholder="Flores">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Ap. Materno</label>
                                    <input type="text" class="form-control form-control-solid" name="ap_materno" id="ap_materno" placeholder="Quispe" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Cedula</label>
                                    <input type="number" name="cedula" id="cedula" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="8401524" required/>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Correo</label>
                                    <input type="email" name="email" id="email" class="form-control form-control-solid mb-3 mb-lg-0" required/>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Rol</label>
                                    <select name="rol_id" id="rol_id" class="form-control form-control-solid" required>
                                        <option value="">Seleccione</option>
                                        @foreach ($roles as $r)
                                        <option value="{{ $r->id }}">{{ $r->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Direccion</label>
                                    <input type="text" name="direccion" id="direccion" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="AV. Pabom"/>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Contraseña</label>
                                    <input type="password" name="password" id="password" class="form-control form-control-solid mb-3 mb-lg-0" required/>
                                </div>
                            </div>

                        </div>
                    </form>

                    <div class="row">
                        <div class="col-md-12">
                            <button class="btn btn-success w-100" onclick="guardarUsuario()">Guardar</button>
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

      <!--begin::Modal - Add task-->
    <div class="modal fade" id="modal_roles" tabindex="-1" aria-hidden="true">
        <!--begin::Modal dialog-->
        <div class="modal-dialog modal-dialog-centered">
            <!--begin::Modal content-->
            <div class="modal-content">
                <!--begin::Modal header-->
                <div class="modal-header" id="modal_roles">
                    <!--begin::Modal title-->
                    <h2 class="fw-bold">Listado de Permisos</h2>
                    <!--end::Modal title-->
                    <!--begin::Close-->
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                    <!--end::Close-->
                </div>
                <!--end::Modal header-->
                <!--begin::Modal body-->
                {{-- <div class="modal-body scroll-y mx-5 mx-xl-15 my-7"> --}}
                <div class="modal-body scroll-y">
                    <div id="listado_roles">

                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <button class="btn btn-success w-100" onclick="guardarMenusPermisso()">Guardar</button>
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
                <h3>Listado de Usuario</h3>
            </div>
            <div class="card-toolbar">
                <!--begin::Toolbar-->
                <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_add_user">
                    <i class="ki-duotone ki-plus fs-2"></i>Nuevo Usuario</button>
                    <!--end::Add user-->
                </div>
            </div>
            <!--end::Card toolbar-->
        </div>
        <!--end::Card header-->
        <!--begin::Card body-->
        <div class="card-body py-4">
            <div id="table_users">

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
                url: "{{ url('user/ajaxListado') }}",
                type: 'POST',
                dataType: 'json',
                success: function(data) {
                    if(data.estado === 'success')
                        $('#table_users').html(data.listado);
                }
            });
        }

        function vaciaFomulario(){
            $('#nombres').val('');
            $('#ap_paterno').val('');
            $('#ap_materno').val('');
            $('#cedula').val('');
            $('#email').val('');
            $('#rol_id').val('');
            $('#direccion').val('');
            $('#password').val('');
        }

       function guardarUsuario(){
            if($("#formularioUsuario")[0].checkValidity()){
                datos = $("#formularioUsuario").serializeArray()
                $.ajax({
                    url: "{{ url('user/guarda') }}",
                    data:datos,
                    type: 'POST',
                    dataType: 'json',
                    success: function(data) {
                        if(data.estado === 'success'){
                            Swal.fire({
                                icon:'success',
                                title: 'Exito!',
                                text:"Se registro con exito!",
                                timer:1500
                            })
                            $('#kt_modal_add_user').modal('hide');
                            $('#table_users').html(data.listado);
                            vaciaFomulario();
                        }else{
                            Swal.fire({
                                icon:'error',
                                title: 'Error!',
                                text:"Paso algo no se registro!",
                                timer:1500
                            })
                        }
                    }
                });
            }else{
    			$("#formularioUsuario")[0].reportValidity()
            }
        }

        function darPermisos(usuario){
            $.ajax({
                url: "{{ url('user/permisos') }}",
                type: 'POST',
                data:{id:usuario},
                dataType: 'json',
                success: function(data) {
                    if(data.estado === 'success'){
                        $('#listado_roles').html(data.listado);
                        $('#modal_roles').modal('show')
                    }
                }
            });
        }

        function guardarMenusPermisso(){
            datos = $("#formulario_roles_permisos").serializeArray()
                $.ajax({
                    url: "{{ url('user/guardarMenusPermisso') }}",
                    data:datos,
                    type: 'POST',
                    dataType: 'json',
                    success: function(data) {
                        if(data.estado === 'success'){
                            Swal.fire({
                                icon:'success',
                                title: 'Exito!',
                                text:"Se registro con exito!",
                                timer:1500
                            })
                            $('#modal_roles').modal('hide');
                            // $('#table_roles').html(data.listado);
                        }
                    }
                });
        }

        function eliminarUsuario(usuario, name){
            Swal.fire({
                title: "Estas seguro de eliminar al usuario "+name+"?",
                text: "No podras revertir eso!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Si, Eliminar!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                    url: "{{ url('user/eliminarUser') }}",
                    data:{
                        usuario:usuario
                    },
                    type: 'POST',
                    dataType: 'json',
                    success: function(data) {
                        if(data.estado === 'success'){
                            Swal.fire({
                                icon:'success',
                                title: 'Exito!',
                                text:"Se registro con exito!",
                                timer:1500
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


