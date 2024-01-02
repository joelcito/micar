<!--begin::Table-->
<table class="table align-middle table-row-dashed fs-6 gy-5" id="tabla_user">
    <thead>
        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
            <th class="min-w-125px">Placa</th>
            <th class="min-w-125px">Color</th>
            <th class="min-w-125px">Marca</th>
            <th class="text-end min-w-100px">Actions</th>
        </tr>
    </thead>
    <tbody class="text-gray-600 fw-semibold">
        @forelse ( $vehiculos as  $v )
            <tr>
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
                    @if (Auth::user()->isEdit())
                        <button class="btn btn-warning btn-icon btn-sm" onclick="editarMovilidad('{{ $v->id }}','{{ $v->placa }}', '{{ $v->color }}','{{ $v->marca }}')" type="button"><i class="fa fa-edit"></i></button>
                    @endif

                    @if (Auth::user()->isDelete())
                        <button class="btn btn-danger btn-icon btn-sm" onclick="eliminarMovilidad('{{ $v->id }}', '{{ $v->placa }}')" type="button"><i class="fa fa-trash"></i></button>
                    @endif
                </td>
            </tr>
        @empty
            {{-- <button class="btn btn-info btn-sm w-100" onclick="registraNuevoVehiculo()">Registrar nuevo vehiculo</button> --}}
            {{--  <h4 class="text-danger text-center">Sin registros</h4>  --}}
        @endforelse
    </tbody>
</table>
<!--end::Table-->
<script>
    $('#tabla_user').DataTable({

    });
</script>
