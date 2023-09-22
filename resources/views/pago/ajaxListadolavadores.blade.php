<!--begin::Table-->
<table class="table align-middle table-row-dashed fs-6 gy-5" id="tabla_user_1">
    <thead>
        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
            <th>ID</th>
            <th>Cedula</th>
            <th>Paterno</th>
            <th>Materno</th>
            <th>Nombres</th>
            <th>Fecha</th>
            <th></th>
        </tr>
    </thead>
    <tbody class="text-gray-600 fw-semibold">
        @foreach ( $lavadores as $l)
            <tr>
                <td>{{ $l->id }}</td>
                <td>{{ $l->cedula }}</td>
                <td>{{ $l->ap_paterno }}</td>
                <td>{{ $l->ap_materno }}</td>
                <td>{{ $l->nombres }}</td>
                <td>
                    <input type="date" class="form-control" id="fecha_lavador_{{ $l->id }}" value="{{ date('Y-m-d') }}">
                </td>
                <td>
                    <button class="btn btn-success btn-icon btn-sm" onclick="selecionarLavador('{{ $l->id }}')"><i class="fa fa-money-bill"></i></button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
<script>
    $('#tabla_user_1').DataTable({
        ordering: false
    });
</script>
