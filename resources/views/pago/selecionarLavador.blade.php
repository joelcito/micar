<table class="table align-middle table-row-dashed fs-6 gy-5" id="tabla_user">
    <thead>
        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
            <th>Cantidad</th>
            <th>Monto Lavado1</th>
            <th>Descripcion</th>
            <th>%</th>
            <th>Monto</th>
            <th>Observacion</th>
        </tr>
    </thead>
    <tbody class="text-gray-600 fw-semibold">
        @foreach ( $detalles as $d)
            <tr>
                <td>{{ $d->cantidad }}</td>
                <td></td>
                <td>{{ $d->descripcion }}</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        @endforeach
    </tbody>
</table>
<script>
    $('#tabla_user').DataTable({
        ordering: false
    });
</script>
