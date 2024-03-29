<!--begin::Table-->
<table class="table align-middle table-row-dashed fs-6 gy-5" id="tabla_user_asignaciones">
    <thead>
        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
            <th >ID</th>
            <th >Descripcion</th>
            <th >Porcentaje</th>
            <th ></th>
        </tr>
    </thead>
    <tbody class="text-gray-600 fw-semibold">
        @forelse ( $asignaciones as  $a )
            <tr>
                <td>{{ $a->id }}</td>
                <td>{{ $a->servicio->descripcion }}</td>
                <td>{{ $a->liquidacion }} %</td>
                <td>
                    @if (Auth::user()->isEdit())
                        <button class="btn btn-warning btn-icon btn-sm" onclick="modificaAsiganacion('{{ $a->id }}','{{ $a->servicio_id }}','{{ $a->liquidacion }}')"><i class="fa fa-edit"></i></button>
                    @endif
                    @if (Auth::user()->isDelete())
                        <button class="btn btn-danger btn-icon btn-sm" onclick="eliminaAsiganacion('{{ $a->id }}', '{{ $a->servicio->descripcion }}')"><i class="fa fa-trash"></i></button>
                    @endif
                </td>
            </tr>
        @empty
            <h4 class="text-danger text-center">Sin registros</h4>
        @endforelse
    </tbody>
</table>
<!--end::Table-->
<script>
    $('#tabla_user_asignaciones').DataTable({

    });
</script>
