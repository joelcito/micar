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
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($ventas as $key => $v)
        <tr>
            <td>{{ $key+1 }}</td>
            <td>
                @if ($v->servicio)
                    {{ $v->servicio->descripcion }}
                @endif
            </td>
            <td>
                @if ($v->lavador)
                    {{ $v->lavador->name }}
                @endif
            </td>
            <td>{{ $v->cantidad }}</td>
            <td>{{ $v->total }}</td>
            <td>
                <button class="btn btn-danger btn-icon btn-sm" onclick="eliminarVenta('{{ $v->id }}', '{{ $v->pago_id }}')"><i class="fa fa-trash"></i></button>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
<div class="row">
    <div class="col-md-12">
        <button class="btn btn-primary w-100" onclick="imprimeNota()">Imprimir Nota</button>
    </div>
</div>
