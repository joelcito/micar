<div class="row">
    <div class="col-md-12">
        <h3 class="text-primary text-center">LISTADO DE VEHICULOS</h3>
    </div>
</div>
<!--begin::Table-->
    <table class="table align-middle table-row-dashed fs-6 gy-5" id="tabla_user">
        <thead>
            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                <th class="min-w-125px">Cliente</th>
                <th class="min-w-125px">Placa</th>
                <th class="min-w-125px">Color</th>
                <th class="min-w-125px">Marca</th>
                <th class="text-end min-w-100px">Actions</th>
            </tr>
        </thead>
        <tbody class="text-gray-600 fw-semibold">
            @forelse ( $vehiculos as  $v )
                <tr>
                    <td class="d-flex align-items-center">
                        <div class="d-flex flex-column">
                            <a class="text-gray-800 text-hover-primary mb-1">{{ $v->cliente->ap_paterno." ".$v->cliente->ap_materno." ".$v->cliente->nombres }}</a>
                        </div>
                    </td>
                    <td>
                        <a class="text-gray-800 text-hover-primary mb-1">{{ $v->placa }}</a>
                    </td>

                    <td>
                        <a class="text-gray-800 text-hover-primary mb-1">{{ $v->color }}</a>
                    </td>

                    <td>
                        <a class="text-gray-800 text-hover-primary mb-1">{{ $v->marca }}</a>
                    </td>
                    <td class="text-end">
                        <button class="btn btn-success btn-icon btn-sm" {{ (!$vender)? 'disabled' : '' }} onclick="agregarServicio('{{ $v->placa }}', '{{ $v->marca }}', '{{ $v->cliente->ap_paterno }}', '{{ $v->cliente->ap_materno }}', '{{ $v->cliente->nombres }}', '{{ $v->id }}', '{{ $v->cliente->id }}', '{{ $v->cliente->complemento }}')"><i class="fa fa-donate"></i></button>
                        <button class="btn btn-danger btn-icon btn-sm" onclick="eliminrCategoria('{{ $v->id}}')"><i class="fa fa-trash"></i></button>
                    </td>
                </tr>
            @empty
                <button class="btn btn-info btn-sm w-100" onclick="registraNuevoVehiculo()">Registrar nuevo vehiculo</button>
                {{--  <h4 class="text-danger text-center">Sin registros</h4>  --}}
            @endforelse
        </tbody>
    </table>
<!--end::Table-->
    <script>
        $('#tabla_user').DataTable({
            {{--  responsive: true,
            language: {
                url: '{{ asset('datatableEs.json') }}',
            },
            order: [[ 0, "desc" ]]  --}}
        });
    </script>
