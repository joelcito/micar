<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\Cliente;
use App\Models\Factura;
use App\Models\MotivoAnulacion;
use App\Models\Pago;
use App\Models\TipoDocumento;
use App\Models\Venta;
use App\Models\Detalle;
use App\Models\LiquidacionLavadorPago;
use App\Models\Movimiento;
use App\Models\User;
use App\Models\Vehiculo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class PagoController extends Controller
{
    public function listado(Request $request){
        $motivoAnulacion = MotivoAnulacion::all();
        return view('pago.listado')->with(compact('motivoAnulacion'));
    }

    public function ajaxListado(Request $request){

        // dd($request->all());

        $datos = $request->all();

        $data = array();

        if($request->ajax()){
            $data['listado'] = $this->listadoArray($datos);
            $data['estado']  = 'success';
        }else{
            $data['estado'] = 'error';
        }

        return json_encode($data);
    }

    protected function listadoArray($datos){

        // $query = Factura::select("*")
        $query = Factura::select("*", "facturas.id as factura_id", "facturas.estado as estado_factura", "facturas.nit as nitFactura")
                        ->join('clientes', 'clientes.id', '=', 'facturas.cliente_id')
                        ->join('vehiculos', 'vehiculos.id', '=', 'facturas.vehiculo_id');

        if(!is_null($datos['buscar_placa'])){
            $placa = $datos['buscar_placa'];
            $query->where('vehiculos.placa',  'LIKE',  "%$placa%");
        }
        if(!is_null($datos['buscar_ap_paterno'])){
            $paterno = $datos['buscar_ap_paterno'];
            $query->where('clientes.ap_paterno',  'LIKE',  "%$paterno%");
        }
        if(!is_null($datos['buscar_ap_materno'])){
            $materno = $datos['buscar_ap_paterno'];
            $query->where('clientes.ap_materno',  'LIKE',  "%$materno%");
        }
        if(!is_null($datos['buscar_nombre'])){
            $nombres = $datos['buscar_nombre'];
            $query->where('clientes.nombres',  'LIKE',  "%$nombres%");
        }
        if(!is_null($datos['buscar_nit'])){
            $nit = $datos['buscar_nit'];
            // $query->where('clientes.nit', "$nit");
            $query->where('facturas.nit', "$nit");
        }
        if(!is_null($datos['buscar_fecha_ini']) && !is_null($datos['buscar_fecha_fin'])){
            $fecha_ini = $datos['buscar_fecha_ini'];
            $fecha_fin = $datos['buscar_fecha_fin'];
            $query->whereBetween('facturas.fecha', [$fecha_ini." 00:00:00", $fecha_fin." 23:59:59"]);
        }

        if(!is_null($datos['tipo_emision'])){
            $tipo_emision = $datos['tipo_emision'];
            $query->where('facturas.facturado', $tipo_emision);
        }

        if(
            !is_null($datos['buscar_placa']) &&
            !is_null($datos['buscar_ap_paterno']) &&
            !is_null($datos['buscar_ap_materno']) &&
            !is_null($datos['buscar_nombre']) &&
            !is_null($datos['buscar_nit']) &&
            !is_null($datos['buscar_fecha_ini']) &&
            !is_null($datos['buscar_fecha_fin']) &&
            !is_null($datos['tipo_emision'])
        ){
            $pagos = $query->limit(50)->get();
        }else{
            $pagos = $query->orderBy('facturas.id', 'desc')->limit(500)->get();
            // $pagos = $query->orderBy('facturas.id', 'desc')->limit(100)->toSql();
            // dd(
            //     $pagos,
            //     $datos['buscar_placa'],
            //     $datos['buscar_ap_paterno'],
            //     $datos['buscar_ap_materno'],
            //     $datos['buscar_nombre'],
            //     $datos['buscar_nit'],
            //     $datos['buscar_fecha_ini'],
            //     $datos['buscar_fecha_fin'],
            //     $datos['tipo_emision']
            // );

        }

        // $pagos = Factura::orderBy('id', 'desc')
        //                 // ->take(200)
        //                 ->get();

        return view('pago.ajaxListado')->with(compact('pagos'))->render();
    }


    public function eliminarPago(Request $request) {
        if($request->ajax()){
            $vehiculo_id = $request->input('vehiculo');
            $detalle_id  = $request->input('id') ;
            Detalle::destroy($detalle_id);
            Movimiento::where('detalle_id',$detalle_id)->delete();
            $data['listado']           = $this->listadoArrayPagos($vehiculo_id);
            // $data['cantida_productos'] = $cantidadProductos;
            $data['estado']            = 'success';
        }else{
            $data['estado'] = 'error';
        }

        return $data;
    }

    // ============================= PARA LA GENERACION DE CUENTAS POR COBRAR ==================================================
    public function  emitirPorCobrar(Request $request){
        if($request->ajax()){
            $vehiculo_id = $request->input('vehiculo');
            Pago::where('estado', 'Parapagar')->update(['estado' => 'Porcobrar']);
            $data['estado'] = "success";
        }else{
            $data['estado'] = "error";
        }
        return $data;
    }

    public function  porcobrar(Request $request) {


        // $pagosPorCobrar = Pago::where('estado', 'Porcobrar')
        //                     ->select('vehiculo_id', \DB::raw('COUNT(*) as cantidad'))
        //                     ->groupBy('vehiculo_id')
        //                     ->get();

        // $pagosPorCobrar = Pago::join('vehiculos as v', 'pagos.vehiculo_id', '=', 'v.id')
        //                     ->join('clientes as c', 'v.cliente_id', '=', 'c.id')
        //                     ->where('pagos.estado', 'Porcobrar')
        //                     ->select('pagos.vehiculo_id', \DB::raw('COUNT(*) as cantidad'), 'v.*', 'c.*')
        //                     ->groupBy('pagos.vehiculo_id')
        //                     ->get();


        // dd($pagosPorCobrar);


        // return view('pago.porcobrar')->with(compact('pagosPorCobrar'));
        return view('pago.porcobrar');
    }

    public function ajaxBuscarPorCobrar(Request $request){
        if($request->ajax()){

            // dd($request->all());

            // $query = Factura::orderBy('id', 'desc');
            $query = Factura::select('facturas.*')
                            ->join('clientes', 'clientes.id', '=', 'facturas.cliente_id')
                            ->join('vehiculos', 'vehiculos.id', '=', 'facturas.vehiculo_id');

            if(!is_null($request->input('nombre'))){
                $nombre = $request->input('nombre');
                $query->where('clientes.nombres', 'like', "%$nombre%");
            }

            if(!is_null($request->input('appaterno'))){
                $appaterno = $request->input('appaterno');
                $query->where('clientes.ap_paterno', $appaterno);
            }

            if(!is_null($request->input('apmaterno'))){
                $apmaterno = $request->input('apmaterno');
                $query->where('clientes.ap_materno', $apmaterno);
            }

            if(!is_null($request->input('cedula'))){
                $cedula = $request->input('cedula');
                $query->where('clientes.cedula', $cedula);
            }

            if(!is_null($request->input('placa'))){
                $placa = $request->input('placa');
                $query->where('vehiculos.placa', $placa);
            }

            if(!is_null($request->input('fecha_ini')) && !is_null($request->input('fecha_fin'))){
                $fecha_ini = $request->input('fecha_ini');
                $fecha_fin = $request->input('fecha_fin');
                $query->whereBetween(DB::raw('LEFT(facturas.fecha, 10)'), [$fecha_ini, $fecha_fin]);
            }

            $facturas = $query->where('facturas.estado_pago', 'Deuda')
                                ->whereNull('facturas.estado')
                                ->get();
                                // ->toSql();
            // dd($facturas);

            // PARA VER SI HAY CAJA O NO
            $ultimaCajaAperturada = Caja::where('estado', 'Abierto')
                                        ->latest()
                                        ->first();

            if($ultimaCajaAperturada){
                // $fechaActual =  Carbon::now()->format('Y-m-d H:i:s');
                // $fechaAperturaCaja = $ultimaCajaAperturada->fecha_apertura;
                // $fecha1 = Carbon::parse($fechaActual);
                // $fecha2 = Carbon::parse($fechaAperturaCaja);
                // if ($fecha1->gt($fecha2)) {
                //     $vender = $ultimaCajaAperturada->id;
                // } else {
                //     $vender = 0;
                // }

                $vender = $ultimaCajaAperturada->id;
            }else{
                $vender = 0;
            }

            $data['estado'] = 'success';
            $data['listado'] = view('pago.ajaxBuscarPorCobrar')->with(compact('facturas', 'vender', 'ultimaCajaAperturada'))->render();

        }else{
            $data['estado'] = 'error';
        }

        return $data;
    }

    public function pagarCuenta(Request $request){
        if($request->ajax()){

            $factura_id = $request->input('factura_id');

            $ultimaCajaAperturada = Caja::where('estado', 'Abierto')
            ->latest()
            ->first();

            $pago                = new Pago();
            $pago->creador_id    = Auth::user()->id;
            $pago->factura_id    = $request->input('factura_id');
            $pago->caja_id       = $ultimaCajaAperturada->id;
            $pago->monto         = $request->input('importe_pagar');
            $pago->fecha         = date('Y-m-d H:i:s');
            $pago->apertura_caja = "No";
            $pago->descripcion   = "PAGO CUENTA POR COBRAR";
            $pago->tipo_pago     = $request->input('tipo_pago');
            $pago->estado        = ($pago->tipo_pago === 'efectivo' )? 'Ingreso' : 'Salida';
            $pago->save();

            // VERIFICAR SI LA FACTURA FUE PAGADA EN TOTALIDAD
            $factura           = Factura::find($factura_id);
            $pagadosPlazos     = (double) Pago::where('factura_id', $factura_id)->sum('monto');
            $montoTotalFActura = (double) $factura->total;

            if($montoTotalFActura === $pagadosPlazos){
                $factura->estado_pago = 'Pagado';
                $factura->save();
            }

            $data['estado'] = 'success';
        }else{
            $data['estado'] = 'error';
        }
        return $data;
    }

    public function finanza(REquest $request){

        // PARA VER SI HAY CAJA O NO
        $ultimaCajaAperturada = Caja::where('estado', 'Abierto')
                                    ->latest()
                                    ->first();

        if($ultimaCajaAperturada){
            $fechaActual =  Carbon::now()->format('Y-m-d H:i:s');
            $fechaAperturaCaja = $ultimaCajaAperturada->fecha_apertura;
            $fecha1 = Carbon::parse($fechaActual);
            $fecha2 = Carbon::parse($fechaAperturaCaja);
            if ($fecha1->gt($fecha2)) {
                $vender = $ultimaCajaAperturada->id;
            } else {
                $vender = 0;
            }
        }else{
            $vender = 0;
        }

        $cajeros = User::whereIn('rol_id',[4,1])->get();

        return view('pago.finanza')->with(compact('vender', 'cajeros'));
    }

    public function  ajaxListadoFinanzas(Request $request) {
        if($request->ajax()){

            // dd($request->all());

            $query = Pago::orderBy('id', 'desc');

            if(!is_null($request->input('fechaIni')) && !is_null($request->input('fechaFin'))){
                $fechaIni = $request->input('fechaIni');
                $fechaFin = $request->input('fechaFin');
                $query->whereBetween('fecha',[$fechaIni.' 00:00:00', $fechaFin.' 23:59:59']);
            }

            if(!is_null($request->input('tipo_pago'))){
                $tipoPago = $request->input('tipo_pago');
                $query->where('tipo_pago',$tipoPago);
            }

            if(!is_null($request->input('cajero_id'))){
                $cajero_id = $request->input('cajero_id');
                $query->where('creador_id', $cajero_id);
            }

            $pagos = $query->get();
            // $pagos = $query->toSql();
            // dd($pagos, $fechaIni, $fechaFin);

            $data['listado'] = view('pago.ajaxListadoFinanzas')->with(compact('pagos'))->render();
            $data['estado'] = 'success';
        }else{
            $data['estado'] = 'error';
        }
        return $data;
    }

    public function guardarTipoIngresoSalida(Request $request){
        if($request->ajax()){
            // dd($request->all());
            $pago                = new Pago();
            $pago->creador_id    = Auth::user()->id;
            $pago->caja_id       = $request->input('caja_abierto_ingre_cerra');
            $pago->apertura_caja = "No";
            $pago->monto         = $request->input('monto');
            $pago->fecha         = date('Y-m-d H:i:s');
            $pago->tipo_pago     = 'efectivo';
            $pago->descripcion   = $request->input('descripcion');
            $pago->estado        = $request->input('tipo');
            $pago->save();
            $data['estado'] = 'success';
        }else{
            $data['estado'] = 'error';
        }
        return $data;
    }

    public function aperturaCaja(Request $request){
        if($request->ajax()){

            $caja                 = new Caja();
            $caja->creador_id     = Auth::user()->id;
            $caja->monto_apertura = $request->input('monto_ape_caja');
            $caja->fecha_apertura = date('Y-m-d H:i:s');
            $caja->descripcion    = $request->input('descripcion_ape_caja');
            $caja->estado         = "Abierto";
            $caja->save();

            $pago                = new Pago();
            $pago->creador_id    = Auth::user()->id;
            $pago->caja_id       = $caja->id;
            $pago->fecha         = date('Y-m-d H:i:s');
            $pago->monto         = $request->input('monto_ape_caja');
            $pago->descripcion   = $request->input('descripcion_ape_caja');
            $pago->apertura_caja = "Si";
            $pago->tipo_pago     = 'efectivo';
            $pago->estado        = 'Ingreso';
            $pago->save();

            $data['estado'] = "success";
            $data['caja']   = $caja->id;
        }else{
            $data['estado'] =  "error";
        }
        return $data;
    }

    public function infomearqueo(Request $request){
        return view('pago.infomearqueo');
    }

    public function ajaxListadoCajas(REquest $request){
        if($request->ajax()){
            // dd($request->all());
            $fechaIni = $request->input('fechaIni');
            $fechaFin = $request->input('fechaFin');
            $data['estado'] = 'success';
            $data['listado']=$this->listadoArrayCajas($fechaIni, $fechaFin);
        }else{
            $data['estado'] = 'error';
        }
        return $data;
    }

    private function listadoArrayCajas($fechaIni, $fechaFin){
        $query = Caja::orderBy('id', 'asc');
        if(!is_null($fechaIni) && !is_null($fechaFin)){
            $query->whereBetween('fecha_apertura',[$fechaIni.' 00:00:00', $fechaFin.' 23:59:59']);
            $query->limit(20);
        }else{
            $query->limit(10);
        }
        $cajas = $query->get();
        return view('pago.ajaxListadoCajas')->with(compact('cajas'))->render();
    }

    public function cierreCaja(Request $request){
        if($request->ajax()){
            // dd("entras al menos");
            // $ultimaCajaAperturada = Caja::where('estado', 'Abierto')
            //                             ->latest()
            //                             ->first();

            $caja_id = $request->input('caja_abierto_cierre');

            $ultimaCajaAperturada = Caja::find($caja_id);

            if($ultimaCajaAperturada){

                // PARA EL TOTAL VENTA
                $total_venta = Pago::where(function ($query) use ($caja_id) {
                        $query->whereIn('id', function ($subquery) use ($caja_id) {
                            $subquery->select('id')
                                ->from('pagos')
                                ->where('caja_id', $caja_id);
                        })->where('estado', 'Ingreso');
                    })
                    ->orWhere(function ($query) use ($caja_id) {
                        $query->whereIn('tipo_pago', ['qr', 'tramsferencia'])
                            ->where('caja_id', $caja_id);
                    // })->selectRaw('SUM(monto) as total')->toSql();
                    })->selectRaw('SUM(monto) as total')->first();

                // $query = Pago::where('caja_id', $caja_id)
                //             ->where('estado', 'Ingreso')
                //             ->orWhere(function ($query) {
                //                 $query->where('tipo_pago', 'qr')
                //                     ->orWhere('tipo_pago', 'tramsferencia');
                //             })
                //             ->selectRaw('SUM(monto) as total');
                //             $total_venta = $query->first();
                            // $total_venta = $query->toSql();
                // dd($total_venta);


                // PARA EL TOTAL VENTA EFECTIVO
                $query = Pago::where('caja_id', $caja_id)
                            ->where('estado', 'Ingreso')
                            ->selectRaw('SUM(monto) as total');
                        $venta_contado = $query->first();


                // PARA OTROS INGRESOS
                $query = Pago::where('caja_id', $caja_id)
                            ->where('estado', 'Ingreso')
                            ->whereNull('factura_id')
                            ->selectRaw('SUM(monto) as total');
                        $otros_ingresos = $query->first();

                // PARA TOTAL QR Y TRAMFERENCIA
                $query = Pago::where('caja_id', $caja_id)
                                ->whereIn('tipo_pago', ['qr', 'tramsferencia'])
                                ->selectRaw('SUM(monto) as total');
                        $total_qrtramsferencia = $query->first();

                // PARA TOTAL SALIDAS GASTOS
                $query = Pago::where('caja_id', $caja_id)
                            ->where('estado', 'Salida')
                            ->where('tipo_pago','!=' ,'qr')
                            ->where('tipo_pago','!=' ,'tramsferencia')
                            ->selectRaw('SUM(monto) as total');
                        $total_salidas_gasto = $query->first();

                $ultimaCajaAperturada->total_venta           = $total_venta->total;
                $ultimaCajaAperturada->otros_ingresos        = $otros_ingresos->total;
                $ultimaCajaAperturada->total_ingresos        = ((float)$venta_contado->total + (float)$otros_ingresos->total) - (float)$total_salidas_gasto->total;
                $ultimaCajaAperturada->total_qrtramsferencia = $total_qrtramsferencia->total;
                $ultimaCajaAperturada->total_salidas_gasto   = $total_salidas_gasto->total;
                $ultimaCajaAperturada->venta_contado         = (float)$venta_contado->total - (float)$total_salidas_gasto->total;
                $ultimaCajaAperturada->total_salidas         = (float)$total_qrtramsferencia->total + (float)$total_salidas_gasto->total;
                $ultimaCajaAperturada->saldo                 = (float)$ultimaCajaAperturada->total_ingresos - (float)$ultimaCajaAperturada->total_salidas;
                $ultimaCajaAperturada->monto_cierre          = $request->input('monto_cie_caja');
                $ultimaCajaAperturada->fecha_cierre          = date('Y-m-d H:i:s');
                $ultimaCajaAperturada->estado                = 'Cerrado';
                $ultimaCajaAperturada->save();

                $data['estado'] = 'success';
            }else{
                $data['msg'] = 'No hay caja aperturada';
                $data['estado'] = 'error';
                dd("dd");
            }
        }else{
            $data['estado'] = 'error';
        }
        return $data;
    }

    public function liquidacionNew(Request $request){
        return view('pago.liquidacionNew');
    }

    // public function  liquidacionList(Request $request){
    //     return view('pago.liquidacionList');
    // }

    public function buscarServicios(Request $request){
        if($request->ajax()){

            $query = User::where('rol_id',3);

            if(!is_null($request->input('cedula'))){
                $cedula = $request->input('cedula');
                $query->where('cedula', $cedula);
            }

            if(!is_null($request->input('paterno'))){
                $paterno = $request->input('paterno');
                $query->where('ap_paterno','LIKE', "%$paterno%");
            }

            if(!is_null($request->input('materno'))){
                $materno = $request->input('materno');
                $query->where('ap_materno','LIKE', "%$materno%");
            }

            if(!is_null($request->input('nombre'))){
                $nombre = $request->input('nombre');
                $query->where('nombres','LIKE', "%$nombre%");
            }

            $lavadores = $query->get();

            $data['estado'] = 'success';
            $data['listado'] = view('pago.ajaxListadolavadores')->with(compact('lavadores'))->render();
        }else{
            $data['estado'] = 'error';
        }
        return $data;
    }

    public function selecionarLavador(Request $request){
        if($request->ajax()){

            $lavador_id = $request->input('lavador');
            $fecha_ini  = $request->input('fecha_ini');
            $fecha_fin  = $request->input('fecha_fin');

            $lavador = User::find($lavador_id);

            // $detalles = Detalle::select(
            //                         'detalles.servicio_id',
            //                         DB::raw('SUM(detalles.cantidad) as cantidad'),
            //                         'servicios.precio',
            //                         'servicios.descripcion',
            //                         'servicios.liquidacion as liquidacionServicio',
            //                         'servicios.tipo_liquidacion as tipoLiquidacionServicio',
            //                         'liquidacion_lavadores.tipo_liquidacion as tipoLiquidacionLl',
            //                         'liquidacion_lavadores.liquidacion as liquidacionLl'
            //                     )
            //                     ->join('servicios', 'detalles.servicio_id', '=', 'servicios.id')
            //                     ->leftJoin('liquidacion_lavadores', 'detalles.servicio_id', '=', 'liquidacion_lavadores.servicio_id')
            //                     ->where('detalles.lavador_id', $lavador_id)
            //                     ->whereBetween('detalles.fecha', [$fecha, $fecha])
            //                     ->groupBy('detalles.servicio_id', 'liquidacion_lavadores.tipo_liquidacion', 'liquidacion_lavadores.liquidacion')
            //                     ->get();

            $detalles = Detalle::detallesLavadorFecha($lavador_id, $fecha_ini, $fecha_fin);

            $clientesLavadores = Cliente::where('tipo_cliente','lavador')->get();

            $data['estado']  = 'success';
            $data['listado']  = view('pago.selecionarLavador')->with(compact('detalles', 'lavador', 'fecha_ini', 'fecha_fin', 'clientesLavadores'))->render();
        }else{
            $data['estado']  = 'error';
        }
        return $data;
    }

    public function buscarCuentasPorCobrar(Request $request){
        if($request->ajax()){
            $clienteLvador = $request->input('lavador');
            $vehiculo  = Vehiculo::where('cliente_id', $clienteLvador)->first();

            if($vehiculo){
                // $facturas = Factura::where('estado_pago', 'Deuda')
                //                     ->where('vehiculo_id',$vehiculo->id)
                //                     ->where('cliente_id',$clienteLvador)
                //                     ->get();

                $facturas = Factura::facturasDeudoras($vehiculo->id, $clienteLvador);

                // PARA VER SI HAY CAJA O NO
                $ultimaCajaAperturada = Caja::where('estado', 'Abierto')
                                            ->latest()
                                            ->first();

                if($ultimaCajaAperturada)
                    $vender = $ultimaCajaAperturada->id;
                else
                    $vender = 0;

                $data['listado'] = view('pago.buscarCuentasPorCobrar')->with(compact('facturas', 'vender'))->render();
                // $data['listado'] = view('pago.ajaxBuscarPorCobrar')->with(compact('facturas', 'vender'))->render();
                $data['estado'] = 'success';
            }else{
                $data['estado'] = 'error';
            }
        }else{
            $data['estado'] = 'error';
        }
        return $data;
    }

    public function cancelarVendedor(Request $request){
        if($request->ajax()){

            $lavador_usuario        = $request->input('lavador_usuario');
            $lavador_cliente        = $request->input('lavador_cliente');
            $vehiculo               = Vehiculo::where('cliente_id',$lavador_cliente)->first();
            // $fecha_pago              = $request->input('fecha');
            $fecha_pago_ini          = $request->input('fecha_ini');
            $fecha_pago_fin          = $request->input('fecha_fin');
            $cuenta_por_pagar        = $request->input('cuentas_por_cobrar_pagar');
            $cuenta_por_pagarLol     = $request->input('cuentas_por_cobrar_pagar');
            $total_servicios_lavador = $request->input('total_servicios_lavador');
            $liquido_pagable         = $request->input('total_liquido_pagable');

            //PARA AGREGAR EL PAGOS SALIENTE DEL VENDEDOR
            $ultimaCajaAperturada = Caja::where('estado', 'Abierto')
                                        ->latest()
                                        ->first();

            $pago                = new Pago();
            $pago->creador_id    = Auth::user()->id;
            $pago->caja_id       = $ultimaCajaAperturada->id;
            $pago->apertura_caja = "No";
            $pago->monto         = $request->input('total_servicios_lavador');
            $pago->fecha         = date('Y-m-d H:i:s');
            $pago->tipo_pago     = 'efectivo';
            $pago->descripcion   = "PAGO LIQUIDACION LAVADOR";
            $pago->estado        = "Salida";
            $pago->save();

            //PARA PAGAR CUENTAS POR COBRAR SI ES QUE TIENE
            if((int)$cuenta_por_pagar > 0){

                $conu                 = 0;
                $facturasDeudroas     = Factura::facturasDeudoras($vehiculo->id, $vehiculo->cliente_id);

                while($cuenta_por_pagar > 0){
                    $pagado           = Pago::where('factura_id', $facturasDeudroas[$conu]->id)->sum('monto');
                    $deudaFactura     = ((float)$facturasDeudroas[$conu]->total - (float)$pagado);

                    if($cuenta_por_pagar >= $deudaFactura){
                        $pagarFactura                           = $deudaFactura;
                        $facturasDeudroas[$conu]->estado_pago   = 'Pagado';
                        $facturasDeudroas[$conu]->save();
                    }else{
                        $pagarFactura = $cuenta_por_pagar;
                    }

                    $pago                = new Pago();
                    $pago->creador_id    = Auth::user()->id;
                    $pago->factura_id    = $facturasDeudroas[$conu]->id;
                    $pago->caja_id       = $ultimaCajaAperturada->id;
                    $pago->monto         = $pagarFactura;
                    $pago->fecha         = date('Y-m-d H:i:s');
                    $pago->apertura_caja = "No";
                    $pago->descripcion   = "PAGO CUENTA POR COBRAR";
                    $pago->tipo_pago     = "efectivo";
                    $pago->estado        = 'Ingreso';
                    $pago->save();

                    $conu++;
                    $cuenta_por_pagar = $cuenta_por_pagar - $deudaFactura;

                }
            }

            $detalles_ids = Detalle::select('id')
                                ->where('lavador_id', $lavador_usuario)
                                // ->where('fecha', $fecha_pago)
                                ->whereBetween('fecha', [$fecha_pago_ini, $fecha_pago_fin])
                                ->where('estado_liquidacion', "Debe")
                                ->pluck('id');

            Detalle::whereIn('id', $detalles_ids)
                    ->update(['estado_liquidacion'  => 'Pagado']);

            $LiquidacionLavadorPago                     = new  LiquidacionLavadorPago();
            $LiquidacionLavadorPago->creador_id         = Auth::user()->id;
            $LiquidacionLavadorPago->lavador_id_user    = $lavador_usuario;
            $LiquidacionLavadorPago->lavador_id_cliente = $lavador_cliente;
            // $LiquidacionLavadorPago->fecha_pagado       = $fecha_pago;
            $LiquidacionLavadorPago->fecha_pagado_ini = $fecha_pago_ini;
            $LiquidacionLavadorPago->fecha_pagado_fin = $fecha_pago_fin;
            $LiquidacionLavadorPago->total_servicios  = $total_servicios_lavador;
            $LiquidacionLavadorPago->cuenta_por_pagar = $cuenta_por_pagarLol;
            $LiquidacionLavadorPago->liquido_pagable  = $liquido_pagable;
            $LiquidacionLavadorPago->detalles_id      = $detalles_ids;
            $LiquidacionLavadorPago->save();

            $data['estado'] = 'success';
            $data['LiquidacionLavadorPago'] = $LiquidacionLavadorPago;
        }else{
            $data['estado'] = 'error';
        }
        return $data;
    }

    public function imprimeLiquidacionVendedor(Request $request, $liquidacion_vendedor_pago_id){

        $liquidacion_vendedor_pago = LiquidacionLavadorPago::find($liquidacion_vendedor_pago_id);

        $detalles = Detalle::select(
                                'detalles.servicio_id',
                                DB::raw('SUM(detalles.cantidad) as cantidad'),
                                'servicios.precio',
                                'servicios.descripcion',
                                'servicios.liquidacion as liquidacionServicio',
                                'servicios.tipo_liquidacion as tipoLiquidacionServicio',
                                'liquidacion_lavadores.tipo_liquidacion as tipoLiquidacionLl',
                                'liquidacion_lavadores.liquidacion as liquidacionLl'
                                )
                        ->join('servicios', 'detalles.servicio_id', '=', 'servicios.id')
                        ->leftjoin('liquidacion_lavadores', function($join){
                            $join->on('detalles.servicio_id', '=', 'liquidacion_lavadores.servicio_id')
                            ->on('detalles.lavador_id', '=', 'liquidacion_lavadores.lavador_id');
                        })
                        // ->leftJoin('liquidacion_lavadores', 'detalles.servicio_id', '=', 'liquidacion_lavadores.servicio_id')
                        ->whereIn('detalles.id', json_decode($liquidacion_vendedor_pago->detalles_id))
                        ->groupBy('detalles.servicio_id', 'liquidacion_lavadores.tipo_liquidacion', 'liquidacion_lavadores.liquidacion')
                        ->get();

        // dd($pagos);

        return view('pago.imprimeLiquidacionVendedor')->with(compact('liquidacion_vendedor_pago', 'detalles'));
    }

    public function imprimeTicked(Request $request, $factura_id, $vehiculo_id){

        // dd($factura_id, $vehiculo_id);

        
        $pagos = Detalle::where('vehiculo_id',$vehiculo_id)
                        // ->where('estado','Parapagar')
                        ->where('factura_id',$factura_id)
                        ->get();

        // dd($pagos);

        return view('pago.imprimeTicked')->with(compact('pagos'));

    }

    public function verQueDebe(Request $request){

        if($request->ajax()){

            dd($request->all());
            
        }else{

        }

        // return $data;
        
    } 



    // public function ajaxServiciosMasa(Request $request){
    //     if($request->ajax()){
    //         $vehiculo_id = $request->input('vehiculo');
    //         $pagos = Pago::where('vehiculo_id', $vehiculo_id)
    //                         ->where('estado','Porcobrar')
    //                         ->get();

    //         $cliente = $pagos[0]->vehiculo->cliente;

    //         $tipoDocumento = TipoDocumento::all();

    //         // NUMOER DE FACTURA
    //         $fac = app(FacturaController::class);
    //         // dd($fac->numeroFactura());
    //         $numFac = $fac->numeroFactura()+1;

    //         $data['estado'] = 'success';
    //         $data['listado'] = view('pago.ajaxServiciosMasa')->with(compact('pagos', 'vehiculo_id','tipoDocumento', 'numFac', 'cliente'))->render();
    //     }else{
    //         $data['estado'] = 'error';
    //     }
    //     return $data;
    // }

    // public function arrayCuotasPorCobrar(Request $request){
    //     if($request->ajax()){

    //         $vehiculo_d = $request->input('vehiculo');

    //         $servicios = Pago::select('pagos.*','servicios.codigoActividad', 'servicios.codigoProducto', 'servicios.unidadMedida', 'servicios.descripcion')
    //                             ->join('servicios', 'pagos.servicio_id','=', 'servicios.id')
    //                             ->where('pagos.estado',"Porcobrar")
    //                             ->where('pagos.vehiculo_id',$vehiculo_d)
    //                             ->get();

    //         $pagos =    $servicios->pluck('id');

    //         $data['lista']  = json_encode($servicios);
    //         $data['pagos']  = $pagos;
    //         $data['estado'] = 'success';
    //     }else{
    //         $data['estado'] = 'error';
    //     }
    //     return $data;
    // }
    // ============================= PARA LA GENERACION DE CUENTAS POR COBRAR ==================================================

    protected function listadoArrayPagos($vehiculo_id){

        $detalles = Detalle::where('vehiculo_id', $vehiculo_id)
                        ->where('estado', "Parapagar")
                        ->get();

        $cantidadProductos = Detalle::join('servicios', 'detalles.servicio_id','=', 'servicios.id')
                        ->where('servicios.estado', 'producto')
                        ->where('detalles.estado', "Parapagar")
                        ->where('detalles.vehiculo_id', $vehiculo_id)
                        ->count();

        return view('vehiculo.ajaxListadoApagar')->with(compact('detalles', 'cantidadProductos'))->render();
    }

}
