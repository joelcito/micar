<!--begin::Table-->
    <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_users1">
        <thead>
            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                <th class="min-w-125px">Codigo</th>
                <th class="min-w-125px">Fecha Vigencia</th>
                <th class="min-w-125px">Estado</th>
                <th class="text-end min-w-100px">Actions</th>
            </tr>
        </thead>
        <tbody class="text-gray-600 fw-semibold">
            @forelse ( $cufds as  $cufd )
                <tr>
                    <td class="d-flex align-items-center">
                        <div class="d-flex flex-column">
                            <a class="text-gray-800 text-hover-primary mb-1">{{ $cufd->codigo }}</a>
                        </div>
                    </td>
                    <td>
                        <a class="text-gray-800 text-hover-primary mb-1">{{ $cufd->fechaVigencia }}</a>
                    </td>
                    <td>
                        @php
                            $ahora = \Carbon\Carbon::now();
                            $vigencia = \Carbon\Carbon::parse($cufd->fechaVigencia);
                            $vigente = $vigencia->greaterThan($ahora);
                        @endphp
                        @if($vigente)
                            <span class="badge badge-success">VIGENTE</span>
                        @else
                            <span class="badge badge-danger">NO VIGENTE</span>
                        @endif
                    </td>
                    <td class="text-end">
                        @if ($vigente)
                            <button class="btn btn-danger btn-icon btn-sm" onclick="eliminarCufd({{ $cufd->id }})"><i class="fa fa-trash"></i></button>
                        @endif
                    </td>
                </tr>
            @empty
                <h4 class="text-danger text-center">Sin registros</h4>
            @endforelse
        </tbody>
    </table>
    <script>
        $('#kt_table_users1').DataTable({
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
            order:[],
            responsive:true
        });
    </script>
