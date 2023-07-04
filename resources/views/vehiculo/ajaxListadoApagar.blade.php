
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
            <th>CANTIDAD</th>
            <th>TOTAL</th>
            <th width="150px">DESCUENTO</th>
            <th width="150px">ACCIONES</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($pagos as $key => $p)
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
            <td>{{ $p->cantidad }}</td>
            <td>{{ $p->total }}</td>
            <td>
                <input type="number" class="form-control">
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
            <th colspan="6">
                <b>
                    DESCUENTO ADICIONAL
                </b>
                <input type="number" class="form-control">
            </th>
            <th>
                <b>
                    MONTO TOTAL
                </b>
                <input type="number" class="form-control">
            </th>
        </tr>
    </tfoot>
</table>
<div class="row">
    <div class="col-md-6">
        <button class="btn btn-success w-100" onclick="imprimeNota()">RECIBO</button>
    </div>
    <div class="col-md-6">
        <button class="btn btn-danger w-100" onclick="muestraDatosFactura()">FACTURA</button>
    </div>
</div>
