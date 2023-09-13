<!--begin::Table-->
<table class="table align-middle table-row-dashed fs-6 gy-5" id="tabla_user">
    <thead>
        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
            <th>ID</th>
            <th>Ap Paterno</th>
            <th>Ap Materno</th>
            <th>Nombres</th>
            <th>Cedula</th>
            <th>Placa</th>
            <th>Fecha</th>
            <th>Importe Total</th>
            <th>Importe Pagado</th>
            <th>Importe Saldo</th>
            {{-- <th class="min-w-125px">Cant Servicios</th> --}}
            <th>Actions</th>
        </tr>
    </thead>
    <tbody class="text-gray-600 fw-semibold">
        @foreach ( $cajas as $c)
            @php
                $pagado = App\Models\Pago::where('factura_id', $c->id)->sum('monto');
                // dd($pagado);
            @endphp
            <tr>
                <td>{{ $c->id }}</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
<script>
    $('#tabla_user').DataTable({
        ordering: false
    });
</script>
