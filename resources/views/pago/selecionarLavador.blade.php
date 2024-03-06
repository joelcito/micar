<div class="row mt-5">
    <div class="col-md-6 text-center"><h3>Lavador: <span class="text-info">{{ $lavador->name }}</span></h3></div>
    <div class="col-md-3 text-center"><h3>Fecha Ini: <span class="text-info">{{ date("d/m/Y", strtotime($fecha_ini)) }}</span></h3></div>
    <div class="col-md-3 text-center"><h3>Fecha Fin: <span class="text-info">{{ date("d/m/Y", strtotime($fecha_fin)) }}</span></h3></div>
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
        <form id="formulario_pagar_vendedor">
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
                    <select name="cliente_lavador" id="cliente_lavador" class="form-control" onchange="buscarCuentasPorCobrar()" required>
                        <option value="">Seleccione</option>
                        @foreach($clientesLavadores as $key => $lav)
                            <option value="{{ $lav->id }}">{{ $lav->nombres." ".$lav->ap_paterno." ".$lav->ap_materno }}</option>
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
                    <input type="number" step="0.01" readonly class="form-control form-control-solid" name="total_servicios_lavador" id="total_servicios_lavador" value="{{ $sumaTatalPagar }}" required min="1">
                </div>
                <div class="col-md-3">
                    <label class="required fw-semibold fs-6 mb-2">Cuentas por cobrar</label>
                    <input type="number" class="form-control form-control-solid" name="cuentas_por_cobrar_pagar" id="cuentas_por_cobrar_pagar" value="0" min="0.1" step="0.01" oninput="realizarCalculo()" required>
                </div>
                <div class="col-md-3">
                    <label class="required fw-semibold fs-6 mb-2">Liquido Pagable</label>
                    <input type="number" class="form-control form-control-solid" name="total_liquido_pagable" id="total_liquido_pagable" value="{{ $sumaTatalPagar }}" readonly required min="1">
                </div>
                <div class="col-md-3">
                    <button class="btn btn-success w-100 btn-sm mt-9" type="button" onclick="cancelarVendedor()">Pagar</button>
                </div>
            </div>
        </form>
    </div>
</div>
{{-- @section('js')

<script>
    console.log("hola che")
</script>
@endsection --}}
<script>
    $('#tabla_user').DataTable({
        ordering: false
    });

    function cancelarVendedor(){


        // // Obtén una referencia al elemento de formulario que deseas validar
        // var formulario = document.getElementById("formulario_pagar_vendedor");

        // console.log(formulario)

        // // Verifica si el formulario es nulo o indefinido antes de acceder a checkValidity
        // if (formulario && formulario.checkValidity) {
        //     // Realiza operaciones de validación aquí
        //     if (formulario.checkValidity()) {
        //         // El formulario es válido, realiza las acciones de cancelación
        //         // ...
        //         console.log("tas")
        //     } else {
        //         // El formulario no es válido, muestra un mensaje de error o realiza otra acción
        //         // ...
        //         console.log("aber")
        //     }
        // }else{
        //     console.log("no")
        //     // $("#formulario_pagar_vendedor")[0].reportValidity()
        // }


        if($("#formulario_pagar_vendedor")[0].checkValidity()){
            if($('#total_servicios_lavador').val() > 0 && $('#total_liquido_pagable').val() > 0){
                $.ajax({
                    url: "{{ url('pago/cancelarVendedor') }}",
                    type: 'POST',
                    data:{
                        total_servicios_lavador : $('#total_servicios_lavador').val(),
                        cuentas_por_cobrar_pagar: $('#cuentas_por_cobrar_pagar').val(),
                        total_liquido_pagable   : $('#total_liquido_pagable').val(),
                        lavador_cliente         : $('#cliente_lavador').val(),
                        fecha_ini               : "{{ $fecha_ini }}",
                        fecha_fin               : "{{ $fecha_fin }}",
                        lavador_usuario         : "{{ $lavador->id }}"
                    },
                    dataType: 'json',
                    success: function(data) {
                        if(data.estado === 'success'){
                            console.log(data)
                            let url = "{{ asset('pago/imprimeLiquidacionVendedor') }}/"+data.LiquidacionLavadorPago.id;
                            window.location.href = url;
                        }
                    }
                });
            }else{
                Swal.fire({
                    icon : 'error',
                    title: "ERRORA AL PROCESAR",
                    text : "El (Total Servicios) y el (Liquido Pagable) deben ser mayor a 0",
                })
            }
        }else{
            console.log("no")
            $("#formulario_pagar_vendedor")[0].reportValidity()
        }
    }
</script>
