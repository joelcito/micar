<table class="table align-middle table-row-dashed fs-6 gy-5" id="tabla_user">
    <thead>
        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
            <th>Precio</th>
            <th>Cantidad</th>
            <th>Monto Lavado</th>
            <th>Descripcion</th>
            <th>%</th>
            <th>Monto</th>
            <th>Observacion</th>
        </tr>
    </thead>
    <tbody class="text-gray-600 fw-semibold">
        @php
            $sumaTatalPagar = 0;
        @endphp
        @foreach ( $detalles as $d)
            <tr>
                @php

                    $sw = true;

                    // MONTO DEL LAVADO
                    $montoLavado = $d->precio * $d->cantidad;

                    // PARA PORCENTAJE Y PRECIO FINAL
                    if ($d->tipoLiquidacionServicio == 'porcentaje'){
                        $montoporcentaje = $d->liquidacionServicio;
                        $montoFinal =   (float)($montoLavado * $montoporcentaje) / 100;
                    }
                    else if($d->tipoLiquidacionServicio == 'depende'){
                        $montoporcentaje = $d->liquidacionLl;
                        $montoFinal =   (float)($montoLavado * $montoporcentaje) / 100;
                    }
                    else{
                        $montoporcentaje = $d->liquidacionServicio;
                        $montoFinal =   (float)($d->cantidad * $d->liquidacionServicio);
                        $sw = false;
                    }

                    // PARA EL TOTAL PAGAR
                    $sumaTatalPagar += $montoFinal;
                @endphp
                <td>{{ $d->precio }}</td>
                <td>{{ $d->cantidad }}</td>
                <td>{{ number_format($montoLavado, 2) }}</td>
                <td>{{ $d->descripcion }}</td>
                <td>
                    {{ $montoporcentaje }} {{ ($sw)? '%' : '' }}
                </td>
                <td>
                    {{ number_format($montoFinal,2)  }}
                </td>
                <td></td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="5">TOTAL</th>
            <th>{{ number_format($sumaTatalPagar,2)  }}</th>
            <th></th>
        </tr>
        <tr>
            <th colspan="5">CUENTAS POR COBRAR</th>
            <th>{{ number_format($sumaTatalPagar,2)  }}</th>
            <th></th>
        </tr>
        <tr>
            <th colspan="5">LIQUIDO PAGABLE</th>
            <th>{{ number_format($sumaTatalPagar,2)  }}</th>
            <th></th>
        </tr>
    </tfoot>
</table>
<script>
    $('#tabla_user').DataTable({
        ordering: false
    });
</script>
