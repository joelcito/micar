<div class="row mt-5">
    <div class="col-md-6 text-center"><h3>Lavador: <span class="text-info">{{ $lavador->name }}</span></h3></div>
    <div class="col-md-6 text-center"><h3>Fecha: <span class="text-info">{{ $fecha }}</span></h3></div>
</div>
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
<div class="row">
    <div class="col-md-12">
        <form action="">
            <div class="row">
                <div class="col-md-10">
                    SUMATORIA TOTAL:
                </div>
                <div class="col-md-2">
                    <input type="number" step="0.01" class="form-control" value="{{ $sumaTatalPagar }}" readonly>
                </div>
            </div>
            <div class="row">
                <div class="col-md-5">
                    CUENTAS POR COBRAR:
                </div>
                <div class="col-md-5">
                    <select name="" id="" class="form-control">
                        <option value=""></option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="number" step="0.01" class="form-control" value="0" readonly>
                </div>
            </div>
            <div class="row">
                <div class="col-md-10">
                    LIQUIDO PAGABLE:
                </div>
                <div class="col-md-2">
                    <input type="number" step="0.01" class="form-control" value="{{ $sumaTatalPagar }}">
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <button class="btn btn-sm w-100 btn-success">PAGAR</button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
    $('#tabla_user').DataTable({
        ordering: false
    });
</script>
