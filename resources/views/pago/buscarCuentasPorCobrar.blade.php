
{{-- <hr>
<div class="row">
    <div class="col-md-12">
        <h2 class="text-info text-center">Cuentas por Cobrar</h2>
    </div>
</div> --}}
<!--begin::Table-->
<table class="table align-middle table-row-dashed fs-6 gy-5" id="porCobrarTable">
    <thead>
        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
            <th>ID</th>
            <th>Ap Paterno</th>
            <th>Ap Materno</th>
            <th>Nombres</th>
            <th>Cedula</th>
            <th>Placa</th>
            <th>Fecha</th>
            <th>Rec/Fac</th>
            <th>Importe Total</th>
            <th>Importe Pagado</th>
            <th>Importe Saldo</th>
            {{-- <th class="min-w-125px">Cant Servicios</th> --}}
            {{-- <th>Actions</th> --}}
        </tr>
    </thead>
    <tbody class="text-gray-600 fw-semibold">
        @php
            $totalMontoCuentasPorCobrar = 0;
        @endphp
        @foreach ( $facturas as $f)
            @php
                $pagado = App\Models\Pago::where('factura_id', $f->id)->sum('monto');
                $totalMontoCuentasPorCobrar+=((float)$f->total - (float)$pagado);
            @endphp
            <tr>
                <td>{{ $f->id }}</td>
                <td>{{ $f->cliente->ap_paterno }}</td>
                <td>{{ $f->cliente->ap_materno }}</td>
                <td>{{ $f->cliente->nombres }}</td>
                <td>{{ $f->cliente->cedula }}</td>
                <td>{{ $f->vehiculo->placa }}</td>
                <td>{{ date('d/m/Y h:i a', strtotime($f->fecha)) }}</td>
                <td>
                    @if ($f->facturado == "Si")
                        <span class="text-success">N° Fac: </span>{{ $f->numero }}
                    @else
                        <span class="text-primary">N° Rec: </span>{{ $f->numero_recibo }}
                    @endif
                </td>
                <td>{{ number_format($f->total,2) }}</td>
                <td>{{ number_format($pagado, 2) }}</td>
                <td>{{ number_format(((int)$f->total - (int)$pagado), 2) }}</td>
                {{-- <td>
                    <button type="button" {{ ($vender==0)? 'disabled' : '' }} class="btn btn-sm btn-success btn-icon" onclick="abreModalPagar(
                                                                                '{{ $f->id }}',
                                                                                '{{ $f->cliente->ap_paterno.' '.$f->cliente->ap_materno.' '.$f->cliente->nombres }}',
                                                                                '{{ $f->vehiculo->placa }}',
                                                                                '{{ $f->total }}',
                                                                                '{{ $pagado }}'
                                                                                )"><i class="fa fa-dollar"></i></button>
                </td> --}}
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="9">TOTAL CUENTAS POR COBRAR</th>
            <th>
                <b>{{ number_format($totalMontoCuentasPorCobrar, 2) }}</b>
                <input type="hidden" value="{{ $totalMontoCuentasPorCobrar }}" id="cuenta_por_cobrar_total">
            </th>
        </tr>
    </tfoot>
</table>
<!--end::Table-->
<script>
    $('#porCobrarTable').DataTable({
        ordering: false
    });
</script>
