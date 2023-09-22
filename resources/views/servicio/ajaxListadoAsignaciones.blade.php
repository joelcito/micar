<!--begin::Table-->
<table class="table align-middle table-row-dashed fs-6 gy-5" id="tabla_user_asignaciones">
    <thead>
        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
            <th >ID</th>
            <th >Descripcion</th>
            <th >Porcentaje</th>
        </tr>
    </thead>
    <tbody class="text-gray-600 fw-semibold">
        @forelse ( $asignaciones as  $a )
            <tr>
                <td>{{ $a->id }}</td>
                <td>{{ $a->servicio->descripcion }}</td>
                <td>{{ $a->liquidacion }} %</td>
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
