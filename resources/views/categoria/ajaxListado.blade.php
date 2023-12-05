<!--begin::Table-->
    <table class="table align-middle table-row-dashed fs-6 gy-5" id="tabla_user">
        <thead>
            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                <th class="min-w-125px">Nombre</th>
                <th class="min-w-125px">Descripcion</th>
                <th class="text-end min-w-100px">Actions</th>
            </tr>
        </thead>
        <tbody class="text-gray-600 fw-semibold">
            @forelse ( $categorias as  $c )
                <tr>
                    <td class="d-flex align-items-center">
                        <div class="d-flex flex-column">
                            <a class="text-gray-800 text-hover-primary mb-1">{{ $c->nombre }}</a>
                        </div>
                    </td>
                    <td>
                        <a class="text-gray-800 text-hover-primary mb-1">{{ $c->descripcion }}</a>
                    </td>
                    <td class="text-end">
                        <button class="btn btn-warning btn-icon btn-sm" onclick="editarCategoria('{{ $c->id }}', '{{ $c->nombre }}', '{{ $c->descripcion }}')"><i class="fa fa-edit"></i></button>
                        <button class="btn btn-danger btn-icon btn-sm" onclick="eliminrCategoria('{{ $c->id}}')"><i class="fa fa-trash"></i></button>
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
            }
        });
    </script>
