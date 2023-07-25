<!--begin::Table-->
    <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_users1">
        <thead>
            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                <th>ID</th>
                <th class="min-w-125px">Placa</th>
                <th class="min-w-125px">Cliente</th>
                <th class="min-w-50px">Monto</th>
                <th class="min-w-50px">Estado</th>
                <th class="min-w-50px">Estado Siat</th>
                <th class="min-w-50px">Emision</th>
                <th class="text-end min-w-100px">Actions</th>
            </tr>
        </thead>
        <tbody class="text-gray-600 fw-semibold">
            @forelse ( $pagos as  $p )
                <tr>
                    <td class=" align-items-center">
                        <span class="text-info">{{ $p->id }}</span>
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
                    <td>
                        @if ($p->estado === "Anulado")
                            <span class="badge badge-danger badge-sm">ANULADO</span>
                        @else
                            <span class="badge badge-success badge-sm">VIGENTE</span>
                        @endif
                    </td>
                    <td>
                        @php
                            if($p->codigo_descripcion == "VALIDADA"){
                                $text = "badge badge-success";
                            }elseif($p->codigo_descripcion == "PENDIENTE"){
                                $text = "badge badge-warning badge-sm";
                            }else{
                                $text = "badge badge-danger badge-sm";
                            }
                        @endphp
                        <span class="{{ $text }}" >{{ $p->codigo_descripcion }}</span>
                    </td>
                    <td>
                        @if ($p->tipo_factura === "online")
                            <span class="badge badge-success badge-sm" >Linea</span>
                        @elseif($p->tipo_factura === "offline")
                            <span class="badge badge-warning text-white badge-sm" >Fuera de Linea</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <a  class="btn btn-primary btn-icon btn-sm"href="{{ url('factura/generaPdfFacturaNew', [$p->id]) }}" target="_blank"><i class="fa fa-file-pdf"></i></a>
                        <a href="https://pilotosiat.impuestos.gob.bo/consulta/QR?nit=5427648016&cuf={{ $p->cuf }}&numero={{ $p->numero }}&t=2" target="_blank" class="btn btn-dark btn-icon btn-sm"><i class="fa fa-file"></i></a>
                        @if ($p->estado != 'Anulado')
                            @if ($p->tipo_factura === "online")
                                @if ($p->productos_xml != null)
                                    <button  class="btn btn-danger btn-icon btn-sm" type="button" onclick="modalAnular('{{ $p->id }}')"><i class="fa fa-trash"></i></button>
                                @else

                                @endif
                            @else
                                @if ($p->codigo_descripcion != 'VALIDADA' && $p->codigo_descripcion != 'PENDIENTE')
                                    <button class="btn btn-info btn-icon btn-sm" onclick="modalRecepcionFacuraContingenciaFueraLinea()"><i class="fa fa-upload" aria-hidden="true"></i></button>
                                @else
                                    <button  class="btn btn-danger btn-icon btn-sm" type="button" onclick="modalAnular('{{ $p->id }}')"><i class="fa fa-trash"></i></button>
                                @endif
                            @endif
                        @endif
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
            // Habilitar el ordenamiento de columnas
            ordering: false,
        });
    </script>
