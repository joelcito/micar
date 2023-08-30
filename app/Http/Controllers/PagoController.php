<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use App\Models\MotivoAnulacion;
use App\Models\Pago;
use App\Models\TipoDocumento;
use App\Models\Venta;
use App\Models\Detalle;
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
            $vehiculo_id    = $request->input('vehiculo');
            $detalle_id     = $request->input('id') ;
            // Pago::destroy($detalle_id);
            Detalle::destroy($detalle_id);
            $data['listado'] = $this->listadoArrayPagos($vehiculo_id);
            $data['estado'] = 'success';
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

            // $facturas = Factura::select('facturas.*')
            //                     ->leftJoin('pagos', 'facturas.id', '=', 'pagos.factura_id')
            //                     ->where('facturas.estado_pago', 'Deuda')
            //                     ->get();

            $facturas = Factura::where('estado_pago', 'Deuda')->get();

            // dd($facturas);

            // $query = Pago::join('vehiculos as v', 'pagos.vehiculo_id', '=', 'v.id')
            //                 ->join('clientes as c', 'v.cliente_id', '=', 'c.id')
            //                 ->where('pagos.estado', 'Porcobrar')
            //                 ->select('pagos.vehiculo_id', \DB::raw('COUNT(*) as cantidad'), 'v.*', 'c.*')
            //                 ->groupBy('pagos.vehiculo_id');

            //                 if(!is_null($request->input('nombre'))) {
            //                     $nombre = $request->input('nombre');
            //                     $query->where('c.nombres', 'like', '%' . $nombre . '%');
            //                 }

            //                 if (!is_null($request->input('appaterno'))) {
            //                     $appaterno = $request->input('nombre');
            //                     $query->where('c.ap_paterno', 'like', '%' . $appaterno . '%');
            //                 }

            //                 if (!is_null($request->input('apmaterno'))) {
            //                     $apmaterno = $request->input('nombre');
            //                     $query->where('c.ap_materno', 'like', '%' . $apmaterno . '%');
            //                 }

            //                 if (!is_null($request->input('cedula'))) {
            //                     $cedula = $request->input('nombre');
            //                     $query->where('c.cedula', 'like', '%' . $cedula . '%');
            //                 }

            //                 if (!is_null($request->input('placa'))) {
            //                     $placa = $request->input('placa');
            //                     $query->where('v.placa', 'like', '%' . $placa . '%');
            //                 }

            // $pagosPorCobrar = $pagosPorCobrar = $query->get();

            $data['estado'] = 'success';
            $data['listado'] = view('pago.ajaxBuscarPorCobrar')->with(compact('facturas'))->render();

        }else{
            $data['estado'] = 'error';
        }

        return $data;
    }

    function pagarCuenta(Request $request){
        if($request->ajax()){

            $factura_id = $request->input('factura_id');

            $pago             = new Pago();
            $pago->creador_id = Auth::user()->id;
            $pago->factura_id = $request->input('factura_id');
            $pago->monto      = $request->input('importe_pagar');
            $pago->fecha      = date('Y-m-d H:i:s');
            $pago->tipo_pago  = $request->input('tipo_pago');
            $pago->save();

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

        // $pagos = Pago::where('vehiculo_id', $vehiculo_id)
        //                 ->where('estado', "Parapagar")
        //                 ->get();
        $detalles = Detalle::where('vehiculo_id', $vehiculo_id)
                        ->where('estado', "Parapagar")
                        ->get();

        return view('vehiculo.ajaxListadoApagar')->with(compact('detalles'))->render();
    }

}
