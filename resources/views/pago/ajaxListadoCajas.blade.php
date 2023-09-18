<!--begin::Table-->
<table class="table align-middle table-row-dashed fs-6 gy-5" id="tabla_user">
    <thead>
        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
            <th>ID</th>
            <th>F Registro</th>
            <th>Usuario</th>
            <th>T Venta</th>
            <th>T Contado</th>
            <th>V Credito</th>
            <th>O Ingreso</th>
            <th>T Ingresos</th>
            <th>T Saldos</th>
            <th>Saldo</th>
            <th>T Declarado</th>
            <th>T Diferencia</th>
            <th>Estado</th>
            {{--  <th>Actions</th>  --}}
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
                <td>{{ date('d/m/Y h:i a', strtotime($c->created_at)) }}</td>
                <td>{{ Auth::user()->name }}</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
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
