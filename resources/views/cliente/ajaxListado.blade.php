<!--begin::Table-->
    <table class="table align-middle table-row-dashed fs-6 gy-5" id="tabla_user">
        <thead>
            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                <th class="min-w-125px">Cedula</th>
                <th class="min-w-125px">Ap Paterno</th>
                <th class="min-w-125px">Ap Materno</th>
                <th class="min-w-125px">Nombres</th>
                <th class="text-end min-w-100px">Actions</th>
            </tr>
        </thead>
        <tbody class="text-gray-600 fw-semibold">
            @forelse ( $clientes as  $c )
                <tr>
                    <td class="align-items-center">
                        <a class="text-gray-800 text-hover-primary">{{ $c->cedula }}</a>
                    </td>
                    <td class="align-items-center">
                        <a class="text-gray-800 text-hover-primary">{{ $c->ap_paterno }}</a>
                    </td>
                    <td class="align-items-center">
                        <a class="text-gray-800 text-hover-primary">{{ $c->ap_materno }}</a>
                    </td>
                    <td>
                        <a class="text-gray-800 text-hover-primary">{{ $c->nombres }}</a>
                    </td>
                    <td class="text-end">
                        <button class="btn btn-warning btn-icon btn-sm" onclick="editarCliente('{{ $c->id }}', '{{ $c->nombres }}', '{{ $c->ap_paterno }}', '{{ $c->ap_materno }}', '{{ $c->cedula }}', '{{ $c->complemento }}', '{{ $c->nit }}', '{{ $c->razon_social }}', '{{ $c->correo }}', '{{ $c->celular }}')"><i class="fa fa-edit"></i></button>
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
            ordering: false
        });
    </script>
