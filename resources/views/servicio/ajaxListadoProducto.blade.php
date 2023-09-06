<!--begin::Table-->
<table class="table align-middle table-row-dashed fs-6 gy-5" id="tabla_user">
    <thead>
        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
            <th class="min-w-125px">Descripcion</th>
            <th class="min-w-125px">Unidad Venta</th>
            <th class="min-w-125px">Precio</th>
            <th class="min-w-125px">Cantidad</th>
            <th class="text-end min-w-100px">Actions</th>
        </tr>
    </thead>
    <tbody class="text-gray-600 fw-semibold">
        @forelse ( $productos as  $p )
            <tr>
                <td>
                    <div class="d-flex flex-column">
                        <a class="text-gray-800 text-hover-primary mb-1">{{ $p->descripcion }}</a>
                    </div>
                </td>
                <td>
                    <div class="d-flex flex-column">
                        <a class="text-gray-800 text-hover-primary mb-1">{{ $p->unidad_venta }}</a>
                    </div>
                </td>
                <td>
                    <a class="text-gray-800 text-hover-primary mb-1">{{ $p->precio }}</a>
                </td>
                <td>
                    @php
                        $cantidadIngreso = $p->movimientos->sum('ingreso');
                        $cantidadSalida = $p->movimientos->sum('salida');
                        $cantidadEnAlmacen = $cantidadIngreso - $cantidadSalida;
                    @endphp
                    <a class="text-gray-800 text-hover-primary mb-1">{{ $cantidadEnAlmacen }}</a>
                </td>
                <td class="text-end">
                    <button class="btn btn-sm btn-icon btn-success" onclick="modalAgregarProducto('{{ $p->id }}','{{ $p->descripcion }}', '{{ $p->precio }}')"><i class="fas fa-plus-circle"></i></button>
                    <button class="btn btn-sm btn-icon btn-danger"><i class="fas fa-minus-circle"></i></button>
                </td>
            </tr>
        @empty
            <h4 class="text-danger text-center">Sin registros</h4>
        @endforelse
    </tbody>
</table>
<!--end::Table-->
<script>
    $('#tabla_user').DataTable({
        {{--  responsive: true,
        language: {
            url: '{{ asset('datatableEs.json') }}',
        },
        order: [[ 0, "desc" ]]  --}}
    });
</script>
