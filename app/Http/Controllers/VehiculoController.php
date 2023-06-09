<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Servicio;
use App\Models\User;
use App\Models\Pago;
use App\Models\Vehiculo;
use App\Models\Venta;
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

        return view('vehiculo.listado')->with(compact('servicios', 'lavadores', 'clientes', 'verificacionSiat'));
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
        // $vehiculos = Vehiculo::all()->limit(100);
        $vehiculos = Vehiculo::orderBy('id', 'desc')->limit(100)->get();
        return view('vehiculo.ajaxListado')->with(compact('vehiculos'))->render();
    }

    protected function listadoArrayVentas($pago_id){
        $ventas = Venta::where('pago_id', $pago_id)->get();

        return view('vehiculo.detalleVentaAjax')->with(compact('ventas'))->render();
    }


    protected function listadoArrayPagos($vehiculo_id){

        $pagos = Pago::where('vehiculo_id', $vehiculo_id)
                        ->where('estado', "Parapagar")
                        ->get();

        return view('vehiculo.ajaxListadoApagar')->with(compact('pagos'))->render();
    }

    public function ajaxRegistraVenta(Request $request){
        if($request->ajax()){

            // dd($request->all());

            $vehiculo_id = $request->input('vehiculo_id');

            $pago = new Pago();

            $pago->creador_id       = Auth::user()->id;
            $pago->vehiculo_id      = $vehiculo_id;
            $pago->servicio_id      = $request->input('servicio_id');
            $pago->lavador_id       = $request->input('lavador_id');
            $pago->precio           = $request->input('precio');
            $pago->cantidad         = $request->input('cantidad');
            $pago->total            = $request->input('total');
            $pago->descuento        = 0;
            $pago->importe          = $request->input('total');
            $pago->fecha            = date('Y-m-d');
            $pago->estado           = "Parapagar";
            $pago->save();



            // if($request->input('pago_id') == 0){
            //     $pago = new Pago();

            //     $pago->creador_id = Auth::user()->id;
            //     $pago->vehiculo_id = $request->input('vehiculo_id');

            //     $pago->save();

            //     $pago_id = $pago->id;

            // }else{
            //     $pago_id = $request->input('pago_id');
            // }

            // $venta = new Venta();

            // $venta->creador_id  = Auth::user()->id;
            // $venta->lavador_id  = $request->input('lavador_id');
            // $venta->vehiculo_id = $request->input('vehiculo_id');
            // $venta->servicio_id = $request->input('servicio_id');
            // $venta->precio      = $request->input('precio');
            // $venta->cantidad    = $request->input('cantidad');
            // $venta->total       = $request->input('total');
            // $venta->fecha       = date('Y-m-d');
            // $venta->pago_id     = $pago_id;

            // $venta->save();

            $data['estado'] = 'success';
            // $data['pago_id'] = $pago_id;
            $data['listado_ventas'] = $this->listadoArrayPagos($vehiculo_id);

        }else{
            $data['estado'] = 'error';
        }

        // return json_encode($data);
        return $data;

    }

    public function eliminarVenta(Request $request){
        if($request->ajax()){
            Venta::destroy($request->input('id'));
            $data['estado'] = 'success';
            $pago_id = $request->input('pago');
            $data['pago_id'] = $pago_id;
            $data['listado_ventas'] = $this->listadoArrayVentas($pago_id);

        }else{
            $data['estado'] = 'error';
        }

        return $data;
    }

    public function imprimeNota(Request $request, $pago_id){

        $ventas = Venta::where('pago_id', $pago_id)->get();

        return view('vehiculo.imprimeNota')->with(compact('ventas'));
    }

    public function buscarVehiculo(Request $request){
        if($request->ajax()){
            if(!is_null($request->input('placa'))){
                $placa = $request->input('placa');
                $vehiculos = Vehiculo::where('placa', 'LIKE',"%$placa%")
                                    ->limit(100)
                                    ->get();
            }else{
                $vehiculos = Vehiculo::orderBy('id', 'desc')
                                        ->limit(200)
                                        ->get();
            }
            $data['estado'] = 'success';
            $data['listado'] = view('vehiculo.ajaxListado')->with(compact('vehiculos'))->render();
        }else{
            $data['estado'] = 'error';
        }
        return json_encode($data);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Vehiculo  $vehiculo
     * @return \Illuminate\Http\Response
     */
    public function show(Vehiculo $vehiculo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Vehiculo  $vehiculo
     * @return \Illuminate\Http\Response
     */
    public function edit(Vehiculo $vehiculo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Vehiculo  $vehiculo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Vehiculo $vehiculo)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Vehiculo  $vehiculo
     * @return \Illuminate\Http\Response
     */
    public function destroy(Vehiculo $vehiculo)
    {
        //
    }
}
