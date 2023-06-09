<!--begin::Table-->
    <table class="table align-middle table-row-dashed fs-6 gy-5" id="tabla_user">
        <thead>
            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                <th class="min-w-125px">Descripcion</th>
                <th class="min-w-125px">Categoria</th>
                <th class="min-w-125px">Unidad Venta</th>
                <th class="min-w-125px">Precio</th>
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
                    <td>
                        <div class="d-flex flex-column">
                            <a class="text-gray-800 text-hover-primary mb-1">{{ $s->unidad_venta }}</a>
                        </div>
                    </td>
                    <td>
                        <a class="text-gray-800 text-hover-primary mb-1">{{ $s->precio }}</a>
                    </td>
                    <td class="text-end">
                        <button class="btn btn-warning btn-icon btn-sm" onclick="editarServicio('{{ $s->id }}', '{{ $s->descripcion }}', '{{ $s->categoria_id }}', '{{ $s->unidad_venta }}', '{{ $s->precio }}')"><i class="fa fa-edit"></i></button>
                        <button class="btn btn-danger btn-icon btn-sm" onclick="eliminaServicio('{{ $s->id }}')"><i class="fa fa-trash"></i></button>
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
