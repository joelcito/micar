<div class="table-responsive m-t-40">
    <table id="tabla-usuarios" class="table table-striped table-bordered no-wrap table-hover">
        <thead>
            <tr>
                <th>CODIGO DE PUNTO DE VENTA</th>
                <th>NOMBRE DEL PUNTO DE VENTA</th>
                <th>TIPO PUNTO DE VENTA</th>
                <th>Accion</th>
            </tr>
        </thead>
        <tbody>
            @foreach ( $puntos as $p)
                @if (is_array($p))
                    <tr>
                        <td>{{ $p['codigoPuntoVenta'] }}</td>
                        <td>{{ $p['nombrePuntoVenta'] }}</td>
                        <td>{{ $p['tipoPuntoVenta'] }}</td>
                        <td>
                            @if(Auth::user()->isDelete())
                                <button class="btn btn-danger btn-icon btn-sm" onclick="eliminaPuntoVenta('{{ $p['codigoPuntoVenta'] }}')"><i class="fa fa-trash"></i></button>
                            @endif
                        </td>
                    </tr>
                @else
                    <tr>
                        <td>{{ $puntos['codigoPuntoVenta'] }}</td>
                        <td>{{ $puntos['nombrePuntoVenta'] }}</td>
                        <td>{{ $puntos['tipoPuntoVenta'] }}</td>
                        <td>
                            @if(Auth::user()->isDelete())
                                <button class="btn btn-danger btn-icon btn-sm" onclick="eliminaPuntoVenta('{{ $puntos['codigoPuntoVenta'] }}')"><i class="fa fa-trash"></i></button>
                            @endif
                        </td>
                    </tr>
                    @break
                @endif
            @endforeach
        </tbody>
    </table>
</div>
