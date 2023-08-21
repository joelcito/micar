<div class="row">
    <div class="col-md-12">
        <hr>
        <h3 class="text-center text-primary">DETALLE DE VENTAS</h3>
        <input type="text" value="{{ $vehiculo_id }}" id="vehiculo_id">
        <input type="text" id="complemento" value="{{ $cliente->complemento }}">
    </div>
</div>
<form id="formularioDescuentos">
    <table class="table align-middle table-row-dashed fs-6 gy-5">
        <thead>
            <tr>
                <th>N°</th>
                <th>SERVICIO</th>
                <th>LAVADOR</th>
                <th>PREC. UNI.</th>
                <th>CANTIDAD</th>
                <th>TOTAL</th>
                <th width="100px">DESCUENTO</th>
                <th width="100px">SUB TOTAL</th>
                <th width="50px">ACCIONES</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total = 0;
            @endphp
            @foreach ($pagos as $key => $p)
            @php
                $total+=$p->importe;
            @endphp
            <tr>
                <td>{{ $key+1 }}</td>
                <td>
                    @if ($p->servicio)
                        {{ $p->servicio->descripcion }}
                    @endif
                </td>
                <td>
                    @if ($p->lavador)
                        {{ $p->lavador->name }}
                    @endif
                </td>
                <td>{{ $p->precio }}</td>
                <td>{{ $p->cantidad }}</td>
                <td>{{ $p->total }}</td>
                <td>
                    @php
                        if($p->descuento > 0)
                            $valoInput = $p->descuento;
                        else
                            $valoInput = 0;
                    @endphp
                    {{-- <input type="number" class="form-control" id="pago_listado_{{ $p->id }}" onchange="funcionNueva(this,{{ $p->id }}, {{ $p->total }})" value="{{ $valoInput }}" min="0" max="{{ $p->total }}" onfocus="guardarValorInicial(this)"> --}}
                    {{-- <input type="number" class="form-control" id="pago_listado_{{ $p->id }}" oninput="funcionNueva(this,{{ $p->id }}, {{ $p->total }})" value="{{ $valoInput }}" min="0" max="{{ $p->total }}" onfocus="guardarValorInicial(this)"> --}}
                    <input type="number"
                            class="form-control"
                            id="pago_listado_{{ $p->id }}"
                            value="{{ $valoInput }}"
                            min="0"
                            max="{{ $p->total }}"
                            step="0.1"
                            onchange="funcionNueva(this,{{ $p->id }}, {{ $p->total }})"
                            onfocus="guardarValorInicial(this)"
                            >
                </td>
                <td>
                    <span id="subTotalCalculdo_{{ $p->id }}">{{ $p->importe }}</span>
                </td>
                <td>
                    <center>
                        <button class="btn btn-danger btn-icon btn-sm" type="button" onclick="eliminarPago('{{ $p->id }}', '{{ $p->pago_id }}')"><i class="fa fa-trash"></i></button>
                    </center>
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="7">
                    <b>
                        DESCUENTO ADICIONAL
                    </b>
                    <input type="number" class="form-control" id="descuento_adicional" value="0" onchange="caluculaTotal(event)" />
                </th>
                <th colspan="2">
                    <b>
                        MONTO TOTAL
                    </b>
                    <input type="number" class="form-control" readonly id="motoTotalFac" value="{{ $total }}">
                </th>
            </tr>
        </tfoot>
    </table>
    <hr>
    <h3 class="text-center text-info">PAGO</h3>
    <div class="row">
        <div class="col-md-4">
            <label for="">Tipo de Pago</label>
            <select name="tipo_pago" id="tipo_pago" class="form-control">
                <option value="">Seleccionar</option>
                <option value="efectivo">Efectivo</option>
                <option value="tramsferencia">Tramsferencia</option>
                <option value="qr">Pago Qr</option>
            </select>
        </div>
        <div class="col-md-4">
            <label for="monto_pagado">Monto</label>
            <input type="text" class="form-control" id="miInput">
        </div>
        <div class="col-md-4">
            <label for="cambio_devuelto">Cambio</label>
            <input type="text" class="form-control">
        </div>
    </div>
    <hr>
</form>
<div class="row">
    {{-- <div class="col-md-3">
        <button class="btn btn-info w-100 btn-sm" onclick="emitirTicket()">IMPRIME TICKET</button>
    </div>
    <div class="col-md-3">
        <button class="btn btn-primary w-100 btn-sm" onclick="emitirPorCobrar()">POR COBRAR</button>
    </div>
    <div class="col-md-3">
        <button class="btn btn-success w-100 btn-sm" onclick="emitirRecibo()">RECIBO</button>
    </div>
    <div class="col-md-3">
        <button class="btn btn-dark w-100 btn-sm" onclick="muestraDatosFactura()">FACTURA</button>
    </div> --}}
     <div class="col-md-12">
        <button class="btn btn-dark w-100 btn-sm" onclick="muestraDatosFactura()">FACTURA</button>
    </div>
    <hr>
        <div id="bloqueDatosFactura" style="display: none">
            <form id="formularioGeneraFactura">
                <div class="row">
                    <div class="col-md-1">
                        <label for="">N Factura</label>
                        <input type="number" class="form-control" id="numero_factura" value="{{ $numFac }}" readonly>
                    </div>
                    <div class="col-md-3">
                        <label for="">Tipo Docuemnto</label>
                        <select name="tipo_documento" id="tipo_documento" class="form-control" onchange="verificaNit()" required>
                            <option value="">SELECCIONE</option>
                            @foreach ($tipoDocumento as $te)
                                <option value="{{ $te->codigo_sin }}">{{ $te->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="">Nit/Cedula</label>
                        <input type="number" class="form-control" id="nit_factura" name="nit_factura" onchange="verificaNit()"  value="{{ $cliente->nit }}">
                        <small style="display: none;" class="text-danger" id="nitnoexiste">NIT INVALIDO</small>
                        <small style="display: none;" class="text-success" id="nitsiexiste">NIT VALIDO</small>
                    </div>
                    <div class="col-md-2">
                        <label for="">Razon Social</label>
                        <input type="text" class="form-control" id="razon_factura" name="razon_factura" value="{{ $cliente->razon_social }}">
                    </div>
                    <div class="col-md-2">
                        <label for="">Tipo Factura</label>
                        <select name="tipo_facturacion" id="tipo_facturacion" class="form-control" onchange="bloqueCAFC()">
                            <option value="online">En Linea</option>
                            <option value="offline">Fuera de Linea</option>
                        </select>
                    </div>
                    <div class="col-md-2" style="display: none;" id="bloque_cafc">
                        <label for="">Uso del CAFC?</label>
                        <div class="row mt-5">
                            <div class="col-md-6">
                                <label for="radioNo">No</label>
                                <input type="radio" name="uso_cafc" id="radioNo" value="No" checked>
                            </div>
                            <div class="col-md-6">
                                <label for="radioSi">Si</label>
                                <input type="radio" name="uso_cafc" id="radioSi" value="Si">
                                <input type="hidden" id="codigo_cafc_contingencia" name="codigo_cafc_contingencia">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row" style="display: none" id="bloque_exepcion">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label class="control-label">Enviar con execpcion?</label>
                            <input type="checkbox" name="execpcion" id="execpcion" required readonly>
                        </div>
                    </div>
                </div>
            </form>
            <div class="row mt-2">
                <div class="col-md-12">
                    <button class="btn btn-sm w-100 btn-success" onclick="emitirFactura()">Enviar</button>
                </div>
            </div>
        </div>
</div>
