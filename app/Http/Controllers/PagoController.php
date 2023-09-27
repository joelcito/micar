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
        $data = array();

        if($request->ajax()){
            $data['listado']=$this->listadoArray();
            $data['estado'] = 'success';
        }else{
            $data['estado'] = 'error';
        }

        return json_encode($data);
    }

    protected function listadoArray(){
        // $pagos = Pago::select('ventas.pago_id', 'ventas.vehiculo_id','pagos.id', DB::raw('SUM(ventas.total) as total'))
        //                ->join('ventas', 'pagos.id','=','ventas.pago_id')
        //                ->groupBy('ventas.pago_id','ventas.vehiculo_id')
        //                ->orderBy('pagos.id', 'DESC')
        //                ->get();
        $pagos = Factura::orderBy('id', 'desc')

                        // ->whereNull('estado') // solopara las anuaciones rapidas
                        // ->where('codigo_descripcion','!=' , '')
                        // ->where('codigo_descripcion','!=' , 'OBSERVADA')

                        ->take(200)
                        ->get();
        return view('pago.ajaxListado')->with(compact('pagos'))->render();
    }

    public function detalle(Request $request, $pago_id){

        $ventas = Venta::where('pago_id', $pago_id)->get();

        return view('pago.detalle')->with(compact('ventas'));
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

            $facturas = Factura::where('estado_pago', 'Deuda')->get();

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
            $data['listado'] = view('pago.ajaxBuscarPorCobrar')->with(compact('facturas', 'vender'))->render();

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
            $pago->tipo_pago     = $request->input('tipo_pago');
            $pago->estado        = ($pago->tipo_pago === 'efectivo' )? 'Ingreso' : 'Salida';
            $pago->save();

            // VERIFICAR SI LA FACTURA FUE PAGADA EN TOTALIDAD
            $factura           = Factura::find($factura_id);
            $pagadosPlazos     = (int) Pago::where('factura_id', $factura_id)->sum('monto');
            $montoTotalFActura = (int) $factura->total;

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

    public function liquidacion(Request $request){
        return view('pago.liquidacion');
    }

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
            $fecha      = $request->input('fecha');

            $lavador = User::find($lavador_id);

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
                                ->leftJoin('liquidacion_lavadores', 'detalles.servicio_id', '=', 'liquidacion_lavadores.servicio_id')
                                ->where('detalles.lavador_id', $lavador_id)
                                ->whereBetween('detalles.fecha', [$fecha, $fecha])
                                ->groupBy('detalles.servicio_id', 'liquidacion_lavadores.tipo_liquidacion', 'liquidacion_lavadores.liquidacion')
                                ->get();

            $clientesLavadores = Cliente::where('tipo_cliente','lavador')->get();

            $data['estado']  = 'success';
            $data['listado']  = view('pago.selecionarLavador')->with(compact('detalles', 'lavador', 'fecha', 'clientesLavadores'))->render();
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
                $facturas = Factura::where('estado_pago', 'Deuda')
                                    ->where('vehiculo_id',$vehiculo->id)
                                    ->where('cliente_id',$clienteLvador)
                                    ->get();


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
