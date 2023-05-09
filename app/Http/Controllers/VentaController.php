<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Vehiculo;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use function GuzzleHttp\Promise\all;

class VentaController extends Controller
{


    public function listado(Request $request){

        return view('venta.listado');

        // $ventas = Venta::all();
        // $lavadores = User::all();
        // $vehiculos = Vehiculo::all();

        // return view('venta.listado')->with(compact('ventas', 'lavadores', 'vehiculos'));
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
        $ventas = Venta::all();
        return view('venta.ajaxListado')->with(compact('ventas'))->render();
    }

    public function guarda(Request $request){

        // if($request->ajax()){

        //     // $validated = $request->validate([
        //     //     'lavador_id' => 'required',
        //     //     'vehiculo_id' => 'required',
        //     //     'precio' => 'required',
        //     //     'fecha' => 'required',
        //     // ]);

        //     $venta = new Venta();

        //     $venta->creador_id = Auth::user()->id;
        //     $venta->lavador_id = $request->input('lavador_id');
        //     $venta->vehiculo_id = $request->input('vehiculo_id');
        //     $venta->precio = $request->input('precio');
        //     $venta->fecha = $request->input('fecha');

        //     $venta->save();

        // }else{

        // }

    }

    public function pagar(Request $request){

        // // dd($request->input('id'));

        // $venta = Venta::find($request->input('id'));

        // $venta->estado = "Pagado";

        // $venta->save();
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
     * @param  \App\Models\Venta  $venta
     * @return \Illuminate\Http\Response
     */
    public function show(Venta $venta)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Venta  $venta
     * @return \Illuminate\Http\Response
     */
    public function edit(Venta $venta)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Venta  $venta
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Venta $venta)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Venta  $venta
     * @return \Illuminate\Http\Response
     */
    public function destroy(Venta $venta)
    {
        //
    }
}
