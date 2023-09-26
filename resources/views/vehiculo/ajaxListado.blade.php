<div class="row">
    <div class="col-md-12">
        <h3 class="text-primary text-center">LISTADO DE VEHICULOS</h3>
    </div>
</div>
<!--begin::Table-->
    <table class="table align-middle table-row-dashed fs-6 gy-5" id="tabla_user">
        <thead>
            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                <th>Cliente</th>
                <th>Placa</th>
                <th>Color</th>
                <th>Marca</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody class="text-gray-600 fw-semibold">
            @forelse ( $vehiculos as  $v )
                <tr>
                  <td>{{ $v->ap_paterno." ".$v->ap_materno." ".$v->nombres }}</td>
                  <td>{{ $v->placa }}</td>
                  <td>{{ $v->color }}</td>
                  <td>{{ $v->marca }}</td>
                  <td>
                    <button class="btn btn-success btn-icon btn-sm" {{ (!$vender)? 'disabled' : '' }} onclick="agregarServicio('{{ $v->placa }}', '{{ $v->marca }}', '{{ $v->ap_paterno }}', '{{ $v->ap_materno }}', '{{ $v->nombres }}', '{{ $v->idvehiculo }}', '{{ $v->idcliente }}', '{{ $v->complemento }}')"><i class="fa fa-donate"></i></button>
                    <button class="btn btn-danger btn-icon btn-sm" onclick="eliminrCategoria('{{ $v->idvehiculo }}')"><i class="fa fa-trash"></i></button>
                  </td>  
                </tr>
            @empty
                <button class="btn btn-info btn-sm w-100" onclick="registraNuevoVehiculo()">Registrar nuevo vehiculo</button>
                {{--  <h4 class="text-danger text-center">Sin registros</h4>  --}}
            @endforelse
        </tbody>
    </table>
<!--end::Table-->
    <script>
        $('#tabla_user').DataTable({
            // responsive: true,
            // language: {
            //     url: '{{ asset('datatableEs.json') }}',
            // },
            // order: [[ 0, "desc" ]]
        });
    </script>
