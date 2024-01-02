@extends('layouts.app')
@section('css')
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('metadatos')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
@endsection
@section('content')

    <!--begin::Modal - Add task-->
    {{--  <div class="modal fade" id="kt_modal_add_user" tabindex="-1" aria-hidden="true">
        <!--begin::Modal dialog-->
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <!--begin::Modal content-->
            <div class="modal-content">
                <!--begin::Modal header-->
                <div class="modal-header" id="kt_modal_add_user_header">
                    <!--begin::Modal title-->
                    <h2 class="fw-bold">Formulario de rol</h2>
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

                    <form id="formularioRol">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Nombre</label>
                                    <input type="text" id="nombre" name="nombre" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="Administrador">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="fv-row mb-7">
                                    <label class="required fw-semibold fs-6 mb-2">Descripcion</label>
                                    <input type="text" id="descripcion" name="descripcion" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="Administrador del sistema">
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="row">
                        <div class="col-md-12">
                            <button class="btn btn-success w-100" onclick="guardarVenta()">Guardar</button>
                        </div>
                    </div>
                </div>
                <!--end::Modal body-->
            </div>
            <!--end::Modal content-->
        </div>
        <!--end::Modal dialog-->
    </div>  --}}
    <!--end::Modal - Add task-->


    <!--begin::Card-->
    <div class="card">
        <!--begin::Card body-->
        <div class="card-body py-4">
            <div class="row">
                <div class="col-md-4">
                    <label class="required fw-semibold fs-6 mb-2">Nombre</label>
                    <input type="text" class="form-control" id="" value="{{ $ventas[0]->vehiculo->cliente->nombres }}" readonly>
                </div>
                <div class="col-md-4">
                    <label class="required fw-semibold fs-6 mb-2">Paterno</label>
                    <input type="text" class="form-control" id="" value="{{ $ventas[0]->vehiculo->cliente->ap_paterno }}" readonly>
                </div>
                <div class="col-md-4">
                    <label class="required fw-semibold fs-6 mb-2">Materno</label>
                    <input type="text" class="form-control" id="" value="{{ $ventas[0]->vehiculo->cliente->ap_materno }}" readonly>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <label class="required fw-semibold fs-6 mb-2">Marca</label>
                    <input type="text" class="form-control" id="" value="{{ $ventas[0]->vehiculo->marca }}" readonly>
                    <input type="hidden" id="pago_id" value="{{ $ventas[0]->pago_id}}">
                </div>
                <div class="col-md-6">
                    <label class="required fw-semibold fs-6 mb-2">Placa</label>
                    <input type="text" class="form-control" id="" value="{{ $ventas[0]->vehiculo->placa }}" readonly>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <hr>
                    <h3 class="text-center text-primary">DETALLE DE VENTAS</h3>
                </div>
            </div>
            <table class="table align-middle table-row-dashed fs-6 gy-5">
                <thead>
                    <tr>
                        <th>N°</th>
                        <th>SERVICIO</th>
                        <th>LAVADOR</th>
                        <th>CANTIDAD</th>
                        <th>TOTAL</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $total = 0;
                    @endphp
                    @foreach ($ventas as $key => $v)
                    @php
                        $total += $v->total;
                    @endphp
                    <tr>
                        <td>{{ $key+1 }}</td>
                        <td>
                            @if ($v->servicio)
                                {{ $v->servicio->descripcion }}
                            @endif
                        </td>
                        <td>
                            @if ($v->lavador)
                                {{ $v->lavador->name }}
                            @endif
                        </td>
                        <td>{{ $v->cantidad }}</td>
                        <td>{{ $v->total }}</td>
                        <td>
                            {{--  <button class="btn btn-danger btn-icon btn-sm" onclick="eliminarVenta('{{ $v->id }}', '{{ $v->pago_id }}')"><i class="fa fa-trash"></i></button>  --}}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-200">
                    <tr>
                        <th colspan="3"><b>TOTAL</b></th>
                        <th></th>
                        <th></th>
                        <th>{{ $total."Bs" }}</th>
                    </tr>
                </tfoot>
            </table>
            <div class="row">
                <div class="col-md-6">
                    <button class="btn btn-dark w-100" onclick="imprimeNota()">Recibo</button>
                </div>
                <div class="col-md-6">
                    <button class="btn btn-primary w-100" onclick="imprimeNota()">Factura</button>
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
            {{--  ajaxListado();  --}}
        });

       function guardarVenta(){
            if($("#formularioRol")[0].checkValidity()){
                datos = $("#formularioRol").serializeArray()
                $.ajax({
                    url: "{{ url('rol/guarda') }}",
                    data:datos,
                    type: 'POST',
                    dataType: 'json',
                    success: function(data) {
                        if(data.estado === 'success')
                            $('#table_roles').html(data.listado);
                    }
                });
            }else{
    			$("#formularioRol")[0].reportValidity()
            }
        }

        function ajaxListado(){
            $.ajax({
                url: "{{ url('rol/ajaxListado') }}",
                type: 'POST',
                dataType: 'json',
                success: function(data) {
                    if(data.estado === 'success')
                        $('#table_roles').html(data.listado);
                }
            });
        }

        function eliminar(rol){
            $.ajax({
                url: "{{ url('rol/eliminar') }}",
                type: 'POST',
                data:{id:rol},
                dataType: 'json',
                success: function(data) {
                    if(data.estado === 'success')
                        $('#table_roles').html(data.listado);
                }
            });
        }

        function imprimeNota(){
            let pago = $('#pago_id').val();
            let url = "{{ asset('vehiculo/imprimeNota') }}/"+pago;
            window.location.href = url;
        }
    </script>
@endsection


