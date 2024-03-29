<!--begin::Table-->
    {{--  <table class="table align-middle table-row-dashed fs-6 gy-5" id="tabla_user">  --}}
    <table class="table align-middle table-row-dashed fs-6 gy-5" id="tabla_user">
        <thead>
            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                <th class="min-w-125px">Descripcion</th>
                <th class="min-w-125px">Categoria</th>
                {{-- <th class="min-w-125px">Unidad Venta</th> --}}
                <th class="min-w-125px">Precio</th>
                <th class="min-w-125px">Liquidacion</th>
                <th class="min-w-125px">Tipo Liquidacion</th>
                <th class="min-w-125px">Cod. Actividad</th>
                <th class="min-w-125px">Cod. Producto</th>
                <th class="min-w-125px">Uni Medida</th>
                <th class="text-end min-w-100px">Actions</th>
            </tr>
        </thead>
        <tbody class="text-gray-600 fw-semibold">
            @forelse ( $servicios as  $s )
                <tr>
                    <td>
                        <div class="d-flex flex-column">
                            <a class="text-gray-800 text-hover-primary mb-1">{{ $s->descripcion }}</a>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex flex-column">
                            @if ($s->categoria)
                            <a class="text-gray-800 text-hover-primary mb-1">{{ $s->categoria->descripcion }}</a>
                            @endif
                        </div>
                    </td>
                    {{-- <td>
                        <div class="d-flex flex-column">
                            <a class="text-gray-800 text-hover-primary mb-1">{{ $s->unidad_venta }}</a>
                        </div>
                    </td> --}}
                    <td>
                        <a class="text-gray-800 text-hover-primary mb-1">{{ $s->precio }}</a>
                    </td>
                    <td>
                        <a class="text-gray-800 text-hover-primary mb-1">{{ $s->liquidacion }}</a>
                    </td>
                    <td>
                        @php
                            $color = "danger";
                            if($s->tipo_liquidacion == 'depende')
                                $color  = "primary";
                            else if($s->tipo_liquidacion == 'fijo')
                                $color  = "info";
                            else if($s->tipo_liquidacion == 'porcentaje')
                                $color  = "warning";

                        @endphp
                        <span class="badge badge-{{ $color }} fw-bold">{{ $s->tipo_liquidacion }}</span>
                    </td>
                    <td>
                        <a class="text-gray-800 text-hover-primary mb-1">{{ $s->codigoActividad }}</a>
                    </td>
                    <td>
                        <a class="text-gray-800 text-hover-primary mb-1">{{ $s->codigoProducto }}</a>
                    </td>
                    <td>
                        <a class="text-gray-800 text-hover-primary mb-1">{{ $s->unidadMedida }}</a>
                    </td>
                    <td class="text-end">
                        @if (Auth::user()->isEdit())
                        <button class="btn btn-warning btn-icon btn-sm" onclick="editarServicio('{{ $s->id }}', '{{ $s->descripcion }}', '{{ $s->categoria_id }}', '{{ $s->unidad_venta }}', '{{ $s->precio }}', '{{ $s->codigoActividad }}', '{{ $s->codigoProducto }}', '{{ $s->unidadMedida }}', '{{ $s->liquidacion }}', '{{ $s->tipo_liquidacion }}')"><i class="fa fa-edit"></i></button>
                        @endif
                        @if (Auth::user()->isDelete())
                        <button class="btn btn-danger btn-icon btn-sm" onclick="eliminaServicio('{{ $s->id }}')"><i class="fa fa-trash"></i></button>
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
                    first: 'Primero',
                    last: 'Último',
                    next: 'Siguiente',
                    previous: 'Anterior'
                },
                search: 'Buscar:',
                lengthMenu: 'Mostrar _MENU_ registros por página',
                info: 'Mostrando _START_ a _END_ de _TOTAL_ registros',
                emptyTable: 'No hay datos disponibles'
            },
            order: [],
            responsive:true
        });
    </script>
