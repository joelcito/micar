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
            <th>Importe Total</th>
            <th>Importe Pagado</th>
            <th>Importe Saldo</th>
            {{-- <th class="min-w-125px">Cant Servicios</th> --}}
            <th>Actions</th>
        </tr>
    </thead>
    <tbody class="text-gray-600 fw-semibold">
        @foreach ( $facturas as $f)
            @php
                $pagado = App\Models\Pago::where('factura_id', $f->id)->sum('monto');
            @endphp
            <tr>
                <td>{{ $f->id }}</td>
                <td>{{ $f->cliente->ap_paterno }}</td>
                <td>{{ $f->cliente->ap_materno }}</td>
                <td>{{ $f->cliente->nombres }}</td>
                <td>{{ $f->cliente->cedula }}</td>
                <td>{{ $f->vehiculo->placa }}</td>
                <td>{{ date('d/m/Y h:i a', strtotime($f->fecha)) }}</td>
                <td>{{ number_format($f->total,2) }}</td>
                <td>{{ number_format($pagado, 2) }}</td>
                <td>{{ number_format(((int)$f->total - (int)$pagado), 2) }}</td>
                <td>
                    <button type="button" {{ ($vender==0)? 'disabled' : '' }} class="btn btn-sm btn-success btn-icon" onclick="abreModalPagar(
                                                                                '{{ $f->id }}',
                                                                                '{{ $f->cliente->ap_paterno.' '.$f->cliente->ap_materno.' '.$f->cliente->nombres }}',
                                                                                '{{ $f->vehiculo->placa }}',
                                                                                '{{ $f->total }}',
                                                                                '{{ $pagado }}'
                                                                                )"><i class="fa fa-dollar"></i></button>
                </td>
            </tr>
        @endforeach
        {{-- @forelse ( $pagosPorCobrar as  $ppc )
            <tr>
                <td class="align-items-center">
                    <a class="text-gray-800 text-hover-primary">{{ $ppc->nombres }}</a>
                </td>
                <td class="align-items-center">
                    <a class="text-gray-800 text-hover-primary">{{ $ppc->ap_paterno }}</a>
                </td>
                <td class="align-items-center">
                    <a class="text-gray-800 text-hover-primary">{{ $ppc->ap_materno }}</a>
                </td>
                <td>
                    <a class="text-gray-800 text-hover-primary">{{ $ppc->cedula }}</a>
                </td>
                <td>
                    <a class="text-gray-800 text-hover-primary">{{ $ppc->placa }}</a>
                </td>
                <td>
                    <a class="text-gray-800 text-hover-primary">{{ $ppc->cantidad }}</a>
                </td>
                <td class="text-end">
                    <button class="btn btn-success btn-icon btn-sm" onclick="enviarModalPorPagar('{{ $ppc->vehiculo_id }}')"><i class="fa fa-dollar"></i></button>
                </td>
            </tr>
        @empty
            <h4 class="text-danger text-center">Sin registros</h4>
        @endforelse --}}
    </tbody>
</table>
<!--end::Table-->
<script>
    $('#porCobrarTable').DataTable({
        ordering: false
    });
</script>
