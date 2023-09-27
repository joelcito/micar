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
            <th><b>{{ number_format($sumaTatalPagar,2)  }}</b></th>
            <th></th>
        </tr>
    </tfoot>
</table>
<hr>
<div class="row">
    <div class="col-md-12">
        <h2 class="text-info text-center">Cuentas por Cobrar</h2>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <form action="">
            {{-- <div class="row">
                <div class="col-md-10">
                    SUMATORIA TOTAL:
                </div>
                <div class="col-md-2">
                    <input type="number" step="0.01" class="form-control" value="{{ $sumaTatalPagar }}" readonly>
                </div>
            </div> --}}
            <div class="row">
                <div class="col-md-6 mt-10">
                    <h5>CUENTAS POR COBRAR:</h5>
                </div>
                <div class="col-md-6">
                    <label class="required fw-semibold fs-6 mb-2">Lavador</label>
                    <select name="cliente_lavador" id="cliente_lavador" class="form-control" onchange="buscarCuentasPorCobrar()">
                        <option value="">Seleccione</option>
                        @foreach($clientesLavadores as $key => $lavador)
                            <option value="{{ $lavador->id }}">{{ $lavador->nombres." ".$lavador->ap_paterno." ".$lavador->ap_materno }}</option>
                        @endforeach
                    </select>
                </div>
                {{-- <div class="col-md-2">
                    <input type="number" step="0.01" class="form-control" value="0" readonly>
                </div> --}}
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div id="facturas_pendientes" style="display: none">
                        
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <label class="required fw-semibold fs-6 mb-2">Total Servicios</label>
                    <input type="number" step="0.01" readonly class="form-control form-control-solid" required name="total_servicios_lavador" id="total_servicios_lavador" value="{{ $sumaTatalPagar }}">
                </div>
                <div class="col-md-3">
                    <label class="required fw-semibold fs-6 mb-2">Cuentas por cobrar</label>
                    <input type="number" class="form-control form-control-solid" required name="cuentas_por_cobrar_pagar" id="cuentas_por_cobrar_pagar" value="0" max="{{ $sumaTatalPagar }}"  oninput="realizarCalculo()">
                </div>
                <div class="col-md-3">
                    <label class="required fw-semibold fs-6 mb-2">Liquido Pagable</label>
                    <input type="number" class="form-control form-control-solid" required name="total_liquido_pagable" id="total_liquido_pagable" value="{{ $sumaTatalPagar }}" readonly>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-success w-100 btn-sm mt-9" onclick="cancelarVendedor()">Pagar</button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
    $('#tabla_user').DataTable({
        ordering: false
    });

    $( document ).ready(function() {

        // $('#cuentas_por_cobrar_pagar').on("input", function() {
        //     console.log("haber")
        //     // let total_servico           = $("#total_servicios_lavador").val();
        //     // let total_acuenta_porcobrar = $(this).val();
        //     // console.log(total_servico, total_acuenta_porcobrar, (total_servico - total_acuenta_porcobrar))
        // });
    });
</script>
