<form action="">
    <div class="row">
        <div class="col-md-3">
            <div class="fv-row mb-7">
                <label class="required fw-semibold fs-6 mb-2">Razon Social</label>
                <input type="text" class="form-control" id="fecha_contingencia" name="fecha_contingencia" required value="{{ $factura->razon_social }}" readonly>
            </div>
        </div>
        <div class="col-md-3">
            <div class="fv-row mb-7">
                <label class="required fw-semibold fs-6 mb-2">Nit/Cedula</label>
                <input type="number" class="form-control" id="fecha_contingencia" name="fecha_contingencia" required value="{{ $factura->nit }}" readonly>
            </div>
        </div>
        <div class="col-md-2">
            <div class="fv-row mb-7">
                <label class="required fw-semibold fs-6 mb-2">Fecha</label>
                <input type="date" class="form-control" id="fecha_contingencia" name="fecha_contingencia" required value="{{ date('Y-m-d', strtotime($factura->fecha)) }}" readonly>
            </div>
        </div>
        <div class="col-md-2">
            <div class="fv-row mb-7">
                <label class="required fw-semibold fs-6 mb-2">Importe</label>
                <input type="number" class="form-control" id="fecha_contingencia" name="fecha_contingencia" required value="{{ $factura->total }}" readonly>
            </div>
        </div>
        <div class="col-md-2">
            <div class="fv-row mb-7">
                <label class="required fw-semibold fs-6 mb-2">Estado</label>
                <input type="text" class="form-control" id="fecha_contingencia" name="fecha_contingencia" required value="{{ $factura->estado_pago }}" readonly>
            </div>
        </div>
    </div>
    <hr>
    <h3 class="text-center text-info">DETALLES</h3>
    <table class="table align-middle table-row-dashed fs-6 gy-5">
        <thead class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
            <tr>
                <th>Servicio</th>
                <th>Lavador</th>
                <th>Precio</th>
                <th>Cantidad</th>
                <th>Total</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody class="text-gray-600 fw-semibold">
            @foreach ( $detalles as $det)
                <tr>
                    <td>{{ $det->servicio->descripcion }}</td>
                    <td>{{ $det->lavador->name }}</td>
                    <td>{{ $det->precio }}</td>
                    <td>{{ $det->cantidad }}</td>
                    <td>{{ $det->total }}</td>
                    <td>{{ $det->fecha }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <hr>
    <div class="row">
        <div class="col-md-4">
            <div class="fv-row mb-7">
                <label class="required fw-semibold fs-6 mb-2">RAZON SOCIAL</label>
                <input type="text" class="form-control" id="fecha_contingencia" name="fecha_contingencia" required value="{{ $factura->razon_social }}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="fv-row mb-7">
                <label class="required fw-semibold fs-6 mb-2">NIT / CEDULA</label>
                <input type="number" class="form-control" id="fecha_contingencia" name="fecha_contingencia" required value="{{ $factura->nit }}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="fv-row mb-7 mt-10">
                <button class="btn btn-success btn-sm w-100">FACTURAR</button>
            </div>
        </div>
    </div>

</form>