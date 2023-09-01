<!--begin::Table-->
<table class="table align-middle table-row-dashed fs-6 gy-5" id="tabla_user">
    <thead>
        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
            <th>ID</th>
            <th>Fecha de Transaccion</th>
            <th>Tipo de Pago</th>
            <th>Transaccion</th>
            <th>Credito</th>
            <th>Debito</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody class="text-gray-600 fw-semibold">
        @php
            $totalCredito = 0;
            $totalDebito = 0;
        @endphp
        @foreach ( $pagos as $p)
            @php
                if ($p->tipo_pago == 'efectivo')
                    $totalCredito+=$p->monto;

                if ($p->tipo_pago != 'efectivo')
                    $totalDebito+=$p->monto;
            @endphp
            <tr>
                <td>{{ $p->id }}</td>
                <td>{{ date('d/m/Y h:i a', strtotime($p->fecha)) }}</td>
                <td>{{ $p->tipo_pago }}</td>
                <td>
                    @if ($p->tipo_pago == 'efectivo')
                        Ingreso
                    @else
                        Salida
                    @endif
                </td>
                <td>
                    @if ($p->tipo_pago == 'efectivo')
                        {{ number_format($p->monto, 2) }}
                    @else
                        {{ number_format(0, 2) }}
                    @endif
                </td>
                <td>
                    @if ($p->tipo_pago != 'efectivo')
                        {{ number_format($p->monto, 2) }}
                    @else
                        {{ number_format(0, 2) }}
                    @endif
                </td>
                <td></td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <th>
            <td colspan="3"><b>TOTAL</b></td>
            <td><b>{{ number_format($totalCredito, 2) }}</b></td>
            <td><b>{{ number_format($totalDebito, 2) }}</b></td>
        </th>
    </tfoot>
</table>
<!--end::Table-->
<script>
    $('#tabla_user').DataTable({
        ordering: false
    });
</script>
