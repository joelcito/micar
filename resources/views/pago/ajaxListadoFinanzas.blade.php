<!--begin::Table-->
<table class="table align-middle table-row-dashed fs-6 gy-5" id="tabla_user">
    <thead>
        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
            <th>ID</th>
            <th>Fecha de Transaccion</th>
            <th>Descripcion</th>
            <th>Tipo de Pago</th>
            <th>Transaccion</th>
            <th>Total Venta</th>
            <th>Credito (Efectivo)</th>
            <th>Credito (Trams / QR)</th>
            <th>Debito (Salida)</th>
            <th>Usuario</th>
        </tr>
    </thead>
    <tbody class="text-gray-600 fw-semibold">
        @php
            $totalCredito       = 0;
            $totalCreditoTrmsQR = 0;
            $totalDebito        = 0;
            $totalVenta         = 0;
        @endphp
        @foreach ( $pagos as $p)
            @php

                //Credito (Efectivo)
                if($p->estado === 'Ingreso')
                    $totalCredito+=$p->monto;

                //if($p->factura_id != null || $p->caja_id != null)
                if(($p->factura_id != null || $p->caja_id != null) && $p->estado == 'Ingreso')
                    $totalVenta+=$p->monto;

                //Credito (Trams / QR)
                if($p->tipo_pago == 'tramsferencia' || $p->tipo_pago == 'qr')
                    $totalCreditoTrmsQR+=$p->monto;

                //Debito (Salida)
                if($p->estado == 'Salida' && $p->tipo_pago != 'tramsferencia' && $p->tipo_pago != 'qr')
                    $totalDebito+=$p->monto;

            @endphp
            <tr>
                <td>{{ $p->id }}</td>
                <td>{{ date('d/m/Y h:i a', strtotime($p->fecha)) }}</td>
                <td>{{ $p->descripcion }}</td>
                <td>{{ $p->tipo_pago }}</td>
                <td>
                    <span class="badge badge-{{ ($p->estado == 'Ingreso')? 'success' : 'danger' }}">{{ $p->estado }}</span>
                </td>
                <td>
                    {{--  Total Venta  --}}
                    {{--  @if (($p->factura_id != null || $p->caja_id != null) && $p->estado == 'Ingreso')  --}}
                    @if ($p->factura_id != null || $p->caja_id != null)
                        {{ number_format($p->monto, 2) }}
                    @else
                        {{ number_format(0, 2) }}
                    @endif
                </td>
                <td>
                    {{--  Credito (Efectivo)  --}}
                    @if ($p->estado == 'Ingreso')
                        {{ number_format($p->monto, 2) }}
                    @else
                        {{ number_format(0, 2) }}
                    @endif
                </td>
                <td>
                    {{--  Credito (Trams / QR)  --}}
                    @if ($p->tipo_pago == 'tramsferencia' || $p->tipo_pago == 'qr')
                        {{ number_format($p->monto, 2) }}
                    @else
                        {{ number_format(0, 2) }}
                    @endif
                </td>
                <td>
                    {{--  Debito (Salida)  --}}
                    @if ($p->estado == 'Salida' && $p->tipo_pago != 'tramsferencia' && $p->tipo_pago != 'qr')
                        {{ number_format($p->monto, 2) }}
                    @else
                        {{ number_format(0, 2) }}
                    @endif
                </td>
                <td>
                    {{ $p->usuario->name }}
                </td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="5"><b>TOTAL</b></th>
            <th><b>{{ number_format($totalVenta, 2) }} Bs.</b></th>
            <th><b>{{ number_format($totalCredito, 2) }} Bs.</b></th>
            <th><b>{{ number_format($totalCreditoTrmsQR, 2) }} Bs.</b></th>
            <th><b>{{ number_format($totalDebito, 2) }} Bs.</b></th>
            <th></th>
        </tr>
        <tr class="bg-light-primary">
            <th colspan="7" class="text-center"><b>TOTAL EFECTIVO CAJA</b></th>
            <th><b>{{ number_format(($totalCredito - $totalDebito), 2) }} Bs.</b></th>
            <th></th>
            <th></th>
        </tr>
    </tfoot>
</table>
<!--end::Table-->
<script>
    $('#tabla_user').DataTable({
        ordering: false
    });
</script>
