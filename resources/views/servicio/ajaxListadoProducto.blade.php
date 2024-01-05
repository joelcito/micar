
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
                        // echo "<hr>".$cantidadIngreso."<br>";
                        // echo $cantidadSalida;
                    @endphp
                    <a class="text-gray-800 text-hover-primary mb-1">{{ $cantidadEnAlmacen }}</a>
                </td>
                <td class="text-end">
                    <button class="btn btn-sm btn-icon btn-success" onclick="modalAgregarProducto('{{ $p->id }}','{{ $p->descripcion }}', '{{ $p->precio }}')"><i class="fas fa-plus-circle"></i></button>
                    <button class="btn btn-sm btn-icon btn-dark" onclick="modalQuitarProducto('{{ $p->id }}','{{ $p->descripcion }}', '{{ $p->precio }}')"><i class="fas fa-minus-circle"></i></button>
                    <button class="btn btn-warning btn-icon btn-sm" onclick="modalModificar('{{ $p->id }}','{{ $p->descripcion }}', '{{ $p->precio }}', '{{ $cantidadEnAlmacen }}')"><i class="fa fa-edit"></i></button>
                    @if($p->movimientos->sum('salida') == 0)
                        <button class="btn btn-danger btn-icon btn-sm" onclick="eliminarProduto('{{ $p->id }}',  '{{ $p->descripcion }}')"><i class="fa fa-trash"></i></button>
                    @endif
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
        dom: 'lfrtip',
        lengthMenu: [10, 25, 50, 100], // Opciones de longitud de página
        language: {
            // Personalizar textos y mensajes
            paginate: {
                first   : 'Primero',
                last    : 'Último',
                next    : 'Siguiente',
                previous: 'Anterior'
            },
            search    : 'Buscar:',
            lengthMenu: 'Mostrar _MENU_ registros por página',
            info      : 'Mostrando _START_ a _END_ de _TOTAL_ registros',
            emptyTable: 'No hay datos disponibles'
        },
        order: []
    });
</script>
