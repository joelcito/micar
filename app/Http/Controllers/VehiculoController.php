<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\Cliente;
use App\Models\Servicio;
use App\Models\User;
use App\Models\Pago;
use App\Models\TipoDocumento;
use App\Models\TipoEvento;
use App\Models\Vehiculo;
use App\Models\Detalle;
use App\Models\Movimiento;
use App\Models\Venta;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use function GuzzleHttp\Promise\all;

class VehiculoController extends Controller
{

    public function listado(Request $request){
        $servicios = Servicio::all();

        $lavadores = User::where('rol_id', 3)->get();

        $clientes = Cliente::all();

        // para el siat LA CONECCION
        $siat = app(SiatController::class);
        $verificacionSiat = json_decode($siat->verificarComunicacion());

        // NUMOER DE FACTURA
        $fac = app(FacturaController::class);
        $numFac = $fac->numeroFactura()+1;

        $tipoDocumento = TipoDocumento::all();

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

        return view('vehiculo.listado')->with(compact('servicios', 'lavadores', 'clientes', 'verificacionSiat', 'numFac', 'tipoDocumento', 'vender'));
    }

    public function ajaxListado(Request $request){
        if($request->ajax()){
            $data['estado'] = 'success';
            $data['listado'] = $this->listadoArray();
        }else{
            $data['estado'] = 'error';
        }
        return json_encode($data);
    }

    protected function listadoArray(){
        $vehiculos = Vehiculo::orderBy('id', 'desc')->limit(50)->get();
        return view('vehiculo.ajaxListado')->with(compact('vehiculos', 'vender'))->render();
    }

    protected function listadoArrayVentas($pago_id){
        $ventas = Venta::where('pago_id', $pago_id)->get();

        return view('vehiculo.detalleVentaAjax')->with(compact('ventas'))->render();
    }


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

    public function ajaxRegistraVenta(Request $request){
        if($request->ajax()){

            // dd($request->all());

            $vehiculo_id = $request->input('vehiculo_id');

            $detalle                     = new Detalle();
            $detalle->creador_id         = Auth::user()->id;
            $detalle->vehiculo_id        = $vehiculo_id;
            $detalle->servicio_id        = $request->input('servicio_id');
            $detalle->lavador_id         = $request->input('lavador_id');
            $detalle->precio             = $request->input('precio');
            $detalle->cantidad           = $request->input('cantidad');
            $detalle->total              = $request->input('total');
            $detalle->descuento          = 0;
            $detalle->importe            = $request->input('total');
            $detalle->fecha              = date('Y-m-d');
            $detalle->estado_liquidacion = "Debe";
            $detalle->estado             = "Parapagar";
            $detalle->save();

            $cantidadProductos = Detalle::join('servicios', 'detalles.servicio_id','=', 'servicios.id')
                                        ->where('servicios.estado', 'producto')
                                        ->where('detalles.vehiculo_id', $vehiculo_id)
                                        ->count();

            $servicio = Servicio::find($request->input('servicio_id'));

            if($servicio->estado == 'producto'){
                $movimeinto              = new Movimiento();
                $movimeinto->creador_id  = Auth::user()->id;
                $movimeinto->servicio_id = $servicio->id;
                $movimeinto->detalle_id  = $detalle->id;
                $movimeinto->ingreso     = 0;
                $movimeinto->salida      = $request->input('cantidad');
                $movimeinto->fecha       = date('Y-m-d H:i:s');
                $movimeinto->descripcion = "VENTA";
                $movimeinto->save();
            }

            $data['estado'] = 'success';
            $data['cantida_productos'] = $cantidadProductos;
            $data['listado_ventas'] = $this->listadoArrayPagos($vehiculo_id);

        }else{
            $data['estado'] = 'error';
        }

        // return json_encode($data);
        return $data;

    }

    // public function eliminarVenta(Request $request){
    //     if($request->ajax()){
    //         Venta::destroy($request->input('id'));
    //         $data['estado'] = 'success';
    //         $pago_id = $request->input('pago');
    //         $data['pago_id'] = $pago_id;
    //         $data['listado_ventas'] = $this->listadoArrayVentas($pago_id);

    //     }else{
    //         $data['estado'] = 'error';
    //     }

    //     return $data;
    // }

    // public function imprimeNota(Request $request, $pago_id){

    //     $ventas = Venta::where('pago_id', $pago_id)->get();

    //     return view('vehiculo.imprimeNota')->with(compact('ventas'));
    // }

    public function buscarVehiculo(Request $request){
        if($request->ajax()){

            $query = Vehiculo::select('vehiculos.id  as idvehiculo','vehiculos.marca' ,'vehiculos.color','vehiculos.placa','clientes.ap_paterno','clientes.ap_materno','clientes.nombres','clientes.id as idcliente')
                                ->join('clientes','vehiculos.cliente_id', '=', 'clientes.id');

            if(!is_null($request->input('placa'))){
                $placa = $request->input('placa');
                $query->where('vehiculos.placa', 'LIKE',"%$placa%");
            }

            if(!is_null($request->input('cedula'))){
                $cedula = $request->input('cedula');
                $query->where('clientes.cedula', 'LIKE', "%$cedula%");
            }

            if(!is_null($request->input('paterno'))){
                $paterno = $request->input('paterno');
                $query->where('clientes.ap_paterno', 'LIKE', "%$paterno%");
            }

            if(!is_null($request->input('materno'))){
                $materno = $request->input('materno');
                $query->where('clientes.ap_materno', 'LIKE', "%$materno%");
            }

            if(!is_null($request->input('nombre'))){
                $nombre = $request->input('nombre');
                $query->where('clientes.nombres', 'LIKE', "%$nombre%");
            }

            if(
                !is_null($request->input('placa')) &&
                !is_null($request->input('cedula')) &&
                !is_null($request->input('paterno')) &&
                !is_null($request->input('materno')) &&
                !is_null($request->input('nombre'))
                ){
                    $vehiculos = $query->limit(50)->get();
            }else{
                $vehiculos = $query->orderBy('vehiculos.id', 'desc')->limit(100)->get();
            }



            // if(!is_null($request->input('placa'))){
            //     $placa = $request->input('placa');
            //     $vehiculos = Vehiculo::where('placa', 'LIKE',"%$placa%")
            //                         ->limit(100)
            //                         ->get();
            // }else{
            //     $vehiculos = Vehiculo::orderBy('id', 'desc')
            //                             ->limit(200)
            //                             ->get();
            // }

            // PARA LA APERTURA DE LA CAJA
            $ultimaCajaAperturada = Caja::where('estado', 'Abierto')
                                        ->latest()
                                        ->first();

            if($ultimaCajaAperturada){
                $fechaActual =  Carbon::now()->format('Y-m-d H:i:s');
                $fechaAperturaCaja = $ultimaCajaAperturada->fecha_apertura;
                $fecha1 = Carbon::parse($fechaActual);
                $fecha2 = Carbon::parse($fechaAperturaCaja);
                if ($fecha1->gt($fecha2)) {
                    $vender = true;
                } else {
                    $vender = false;
                }
            }else{
                $vender = false;
            }
            $data['estado'] = 'success';
            $data['listado'] = view('vehiculo.ajaxListado')->with(compact('vehiculos', 'vender'))->render();
        }else{
            $data['estado'] = 'error';
        }
        return json_encode($data);
    }

    public function obtenerNitRazonSocial(Request $request){
        if($request->ajax()){
            $cliente_id = $request->input('id');
            $cliente = Cliente::find($cliente_id);

            $data['nit']            = $cliente->nit;
            $data['razon_social']   = $cliente->razon_social;

            $data['estado']         = 'success';
        }else{
            $data['estado'] = 'error';
        }
        return $data;
    }

    public function consultaPagosPorCobrar(Request $request){
        if($request->ajax()){
            $vehiculo_id = $request->input('id');
            $data['listado_ventas'] = $this->listadoArrayPagos($vehiculo_id);
            $data['estado'] = "success";
        }else{
            $data['estado'] = "errror";
        }
        return $data;
    }

    public function guarda(Request $request){
        if($request->ajax()){

            $vehiculo_id = $request->input('vehiculo_id');
            $placa       = $request->input('placa');
            $color       = $request->input('color');
            $marca       = $request->input('marca');
            $cliente_id  = $request->input('cliente');

            if($vehiculo_id == 0){
                $vehiculo             = new Vehiculo();
                $vehiculo->creador_id = Auth::user()->id;
            }else{
                $vehiculo                 = Vehiculo::find($vehiculo_id);
                $vehiculo->modificador_id = Auth::user()->id;
            }

            $vehiculo->cliente_id   = $cliente_id;
            $vehiculo->placa        = $placa;
            $vehiculo->color        = $color;
            $vehiculo->marca        = $marca;
            $vehiculo->save();

            $clienteCLass = app(ClienteController::class);

            $data['listado'] = $clienteCLass->listadoArrayVehiuclos($cliente_id);
            $data['estado'] = 'success';
        }else{
            $data['estado'] = 'error';
        }

        return $data;
    }

    public function  eliminarMovilidad(Request $request){
        if($request->ajax()){
            $data['estado'] = 'success';
            Vehiculo::destroy($request->input('id'));
        }else{
            $data['estado'] = 'error';
        }
        return $data;
    }

}
