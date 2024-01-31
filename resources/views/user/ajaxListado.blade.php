<!--begin::Table-->
    <table class="table align-middle table-row-dashed fs-6 gy-5" id="tabla_user">
        <thead>
            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                <th class="min-w-125px">Nombre</th>
                <th class="min-w-125px">Ape. Paterno</th>
                <th class="min-w-125px">Ape. Materno</th>
                <th class="min-w-125px">Cedula</th>
                <th class="min-w-125px">Correo</th>
                <th class="min-w-125px">Rol</th>
                <th class="text-end min-w-100px">Actions</th>
            </tr>
        </thead>
        <tbody class="text-gray-600 fw-semibold">
            @forelse ( $usuarios as  $u )
                <tr>
                    <td>
                        <a class="text-gray-800 text-hover-primary mb-1">{{ $u->nombres }}</a>
                    </td>
                    <td>
                        <a class="text-gray-800 text-hover-primary mb-1">{{ $u->ap_paterno }}</a>
                    </td>
                    <td>
                        <a class="text-gray-800 text-hover-primary mb-1">{{ $u->ap_materno }}</a>
                    </td>
                    <td>
                        <a class="text-gray-800 text-hover-primary mb-1">{{ $u->cedula }}</a>
                    </td>
                    <td>
                        <span class="badge badge-light-success fw-bold">{{ $u->email }}</span>
                    </td>
                    <td>
                        @php
                            $color = "danger";
                            if($u->rol_id == 1)
                                $color  = "primary";
                            else if($u->rol_id == 2)
                                $color  = "info";
                            else if($u->rol_id == 3)
                                $color  = "warning";
                            else if($u->rol_id == 4)
                                $color  = "dark";
                        @endphp
                        <span class="badge badge-{{ $color }} fw-bold">{{ $u->rol->nombre }}</span>
                    </td>
                    <td class="text-end">

                        @if($u->rol_id != 3)
                            <button onclick="darPermisos('{{ $u->id }}')" class="btn btn-icon btn-primary btn-sm"><i class="fa fa-list"></i></button>
                        @endif

                        <a href="{{ url('user/detalle',[$u->id]) }}" class="btn btn-icon btn-info btn-sm"><i class="fa fa-eye"></i></a>
                        @if (Auth::user()->isDelete())
                            <button class="btn btn-icon btn-danger btn-sm" onclick="eliminarUsuario('{{ $u->id }}', '{{ $u->name }}')"><i class="fa fa-trash"></i></button>
                        @endif
                    </td>
                </tr>
            @empty
                <h4 class="text-danger text-center">Sin registros</h4>
            @endforelse
        </tbody>
    </table>
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
