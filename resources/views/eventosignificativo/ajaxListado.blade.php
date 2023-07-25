<div class="table-responsive m-t-40">
    <table id="tabla-usuarios" class="table table-striped table-bordered no-wrap table-hover">
        <thead>
            <tr>
                <th>CODIGO DE EVENTO</th>
                <th>CODIGO RECEPCION EVENTO SIGNIFICATIVO</th>
                <th>DESCRIPCION</th>
                <th>FECHA INICIO</th>
                <th>FECHA FIN</th>
            </tr>
        </thead>
        <tbody>
            @foreach ( $eventos as $e)
                @if (is_array($e))
                    <tr>
                        <td>{{ $e['codigoEvento'] }}</td>
                        <td>{{ $e['codigoRecepcionEventoSignificativo'] }}</td>
                        <td>{{ $e['descripcion'] }}</td>
                        <td>{{ $e['fechaInicio'] }}</td>
                        <td>{{ $e['fechaFin'] }}</td>
                    </tr>
                @else
                    <tr>
                        <td>{{ $eventos['codigoEvento'] }}</td>
                        <td>{{ $eventos['codigoRecepcionEventoSignificativo'] }}</td>
                        <td>{{ $eventos['descripcion'] }}</td>
                        <td>{{ $eventos['fechaInicio'] }}</td>
                        <td>{{ $eventos['fechaFin'] }}</td>
                    </tr>
                    @break
                @endif
            @endforeach
        </tbody>
    </table>
</div>
