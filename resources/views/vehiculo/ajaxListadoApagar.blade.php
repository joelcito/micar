
<div class="row">
    <div class="col-md-12">
        <hr>
        <h3 class="text-center text-primary">DETALLE DE VENTAS</h3>
    </div>
</div>
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
        @foreach ($pagos as $key => $p)
        @php
            $total+=$p->importe;
        @endphp
        <tr>
            <td>{{ $key+1 }}</td>
            <td>
                @if ($p->servicio)
                    {{ $p->servicio->descripcion }}
                @endif
            </td>
            <td>
                @if ($p->lavador)
                    {{ $p->lavador->name }}
                @endif
            </td>
            <td>{{ $p->precio }}</td>
            <td>{{ $p->cantidad }}</td>
            <td>{{ $p->total }}</td>
            <td>
                @php
                    if($p->descuento > 0)
                        $valoInput = $p->descuento;
                    else
                        $valoInput = 0;
                @endphp
                <input type="number" class="form-control" id="pago_listado_{{ $p->id }}" onchange="funcionNueva(this,{{ $p->id }}, {{ $p->total }})" value="{{ $valoInput }}">
            </td>
            <td>
                <span id="subTotalCalculdo_{{ $p->id }}">{{ $p->importe }}</span>
            </td>
            <td>
                <center>
                    <button class="btn btn-danger btn-icon btn-sm" onclick="eliminarPago('{{ $p->id }}', '{{ $p->pago_id }}')"><i class="fa fa-trash"></i></button>
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
<div class="row">
    <div class="col-md-6">
        <button class="btn btn-success w-100 btn-sm" onclick="imprimeNota()">RECIBO</button>
    </div>
    <div class="col-md-6">
        <button class="btn btn-dark w-100 btn-sm" onclick="muestraDatosFactura()">FACTURA</button>
    </div>
</div>
