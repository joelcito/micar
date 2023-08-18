<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use App\Models\MotivoAnulacion;
use App\Models\Pago;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            $pago_id = $request->input('id') ;
            Pago::destroy($pago_id);
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
        return view('pago.porcobrar');
    }
    // ============================= PARA LA GENERACION DE CUENTAS POR COBRAR ==================================================


    protected function listadoArrayPagos($vehiculo_id){

        $pagos = Pago::where('vehiculo_id', $vehiculo_id)
                        ->where('estado', "Parapagar")
                        ->get();

        return view('vehiculo.ajaxListadoApagar')->with(compact('pagos'))->render();
    }



}
