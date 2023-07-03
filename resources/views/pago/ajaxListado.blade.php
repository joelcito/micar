<!--begin::Table-->
    <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_users1">
        <thead>
            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                <th class="min-w-125px">Marca</th>
                <th class="min-w-125px">Placa</th>
                <th class="min-w-125px">Cliente</th>
                <th class="min-w-50px">Monto</th>
                <th class="text-end min-w-100px">Actions</th>
            </tr>
        </thead>
        <tbody class="text-gray-600 fw-semibold">
            @forelse ( $pagos as  $p )
                <tr>
                    <td class="d-flex align-items-center">
                        <div class="d-flex flex-column">
                            @if ($p->vehiculo)
                            <a class="text-gray-800 text-hover-primary mb-1">{{ $p->vehiculo->marca }}</a>
                            @endif
                        </div>
                    </td>
                    <td>
                        @if ($p->vehiculo)
                        <a class="text-gray-800 text-hover-primary mb-1">{{ $p->vehiculo->placa }}</a>
                        @endif
                    </td>
                    <td>
                        @if ($p->vehiculo)
                        <a class="text-gray-800 text-hover-primary mb-1">{{ $p->vehiculo->cliente->nombres." ".$p->vehiculo->cliente->ap_paterno." ".$p->vehiculo->cliente->ap_materno }}</a>
                        @endif
                    </td>
                    <td>
                        <a class="text-gray-800 text-hover-primary mb-1">{{ $p->total }}</a>
                    </td>
                    <td class="text-end">
                        <a  class="btn btn-info btn-icon btn-sm" href="{{ url('/pago/detalle',[$p->id]) }}"><i class="fa fa-eye"></i></a>
                        {{--  <button class="btn btn-warning btn-icon btn-sm"><i class="fa fa-edit"></i></button>  --}}
                        {{--  <button class="btn btn-danger btn-icon btn-sm" onclick="eliminar('{{ $p->id }}')"><i class="fa fa-trash"></i></button>  --}}
                    </td>
                </tr>
            @empty
                <h4 class="text-danger text-center">Sin registros</h4>
            @endforelse
        </tbody>
    </table>
<!--end::Table-->
    <script>
        $('#kt_table_users1').DataTable({
            {{--  responsive: true,
            language: {
                url: '{{ asset('datatableEs.json') }}',
            },
            order: [[ 0, "desc" ]]  --}}
        });
    </script>
