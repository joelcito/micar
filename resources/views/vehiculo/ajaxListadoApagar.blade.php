
<div class="row">
    <div class="col-md-12">
        <hr>
        <h3 class="text-center text-primary">DETALLE DE VENTAS</h3>
    </div>
</div>
<form id="formularioDescuentos">
    <table class="table align-middle table-row-dashed fs-6 gy-5">
        <thead>
            <tr>
                <th>N°</th>
                <th>SERVICIO</th>
                <th>LAVADOR</th>
                <th>PREC. UNI.</th>
                <th>CANTIDAD</th>
                <th>TOTAL</th>
                <th width="100px">DESCUENTO</th>
                <th width="100px">SUB TOTAL</th>
                <th width="50px">ACCIONES</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total = 0;
            @endphp
            @foreach ($detalles as $key => $d)
            @php
                $total+=$d->importe;
            @endphp
            <tr>
                <td>{{ $key+1 }}</td>
                <td>
                    @if ($d->servicio)
                        {{ $d->servicio->descripcion }}
                    @endif
                </td>
                <td>
                    @if ($d->lavador)
                        {{ $d->lavador->name }}
                    @endif
                </td>
                <td>{{ $d->precio }}</td>
                <td>{{ $d->cantidad }}</td>
                <td>{{ $d->total }}</td>
                <td>
                    @php
                        if($d->descuento > 0)
                            $valoInput = $d->descuento;
                        else
                            $valoInput = 0;
                    @endphp
                    <input type="number"
                            class="form-control"
                            id="pago_listado_{{ $d->id }}"
                            value="{{ $valoInput }}"
                            min="0"
                            max="{{ $d->total }}"
                            step="0.1"
                            onchange="funcionNueva(this,{{ $d->id }}, {{ $d->total }})"
                            onfocus="guardarValorInicial(this)"
                            >
                </td>
                <td>
                    <span id="subTotalCalculdo_{{ $d->id }}">{{ $d->importe }}</span>
                </td>
                <td>
                    <center>
                        <button class="btn btn-danger btn-icon btn-sm" type="button" onclick="eliminarPago('{{ $d->id }}')"><i class="fa fa-trash"></i></button>
                    </center>
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="7">
                    <b>
                        DESCUENTO ADICIONAL
                    </b>
                    <input type="number" class="form-control" id="descuento_adicional" value="0" onchange="caluculaTotal(event)" />
                </th>
                <th colspan="2">
                    <b>
                        MONTO TOTAL
                    </b>
                    <input type="number" class="form-control" readonly id="motoTotalFac" value="{{ $total }}">
                </th>
            </tr>
        </tfoot>
    </table>
    <hr>
</form>
<div class="row">
    <div class="col-md-4">
        <button class="btn btn-info w-100 btn-sm" onclick="emitirTicket()">IMPRIME TICKET</button>
    </div>
    {{-- <div class="col-md-3">
        <button class="btn btn-primary w-100 btn-sm" onclick="emitirPorCobrar()">POR COBRAR</button>
    </div> --}}
    <div class="col-md-4">
        {{-- <button class="btn btn-success w-100 btn-sm" onclick="emitirRecibo()">RECIBO</button> --}}
        <button class="btn btn-success w-100 btn-sm" onclick="muestraDatosTipoPago()">RECIBO</button>
    </div>
    <div class="col-md-4">
        <button class="btn btn-dark w-100 btn-sm" id="boton_facturar" {{ ($cantidadProductos == 0)? '' : 'disabled' }} onclick="muestraDatosFactura()">FACTURA</button>
    </div>
    {{--  <div class="col-md-12">
        <button class="btn btn-dark w-100 btn-sm" onclick="muestraDatosFactura()">FACTURA</button>
    </div>  --}}
</div>
