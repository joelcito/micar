<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Pago;
use App\Models\Vehiculo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClienteController extends Controller
{

    public function listado(Request $request){
        return view('cliente.listado');
    }

    public function nuevo(Request $request){
        return view('cliente.nuevo');
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


    public function guarda(Request $request){
        if($request->ajax()){

            $cliente_id  = $request->input('cliente_id');
            if($cliente_id === "0"){
                $cliente                = new Cliente();
                $cliente->creador_id    = Auth::user()->id;
            }else{
                $cliente                    = Cliente::find($cliente_id);
                $cliente->modificador_id    = Auth::user()->id;
            }

            $cliente->nombres       = $request->input('nombres');
            $cliente->ap_paterno    = $request->input('ap_paterno');
            $cliente->ap_materno    = $request->input('ap_materno');
            $cliente->cedula        = $request->input('cedula');
            $cliente->complemento   = $request->input('complemento');
            $cliente->nit           = $request->input('nit');
            $cliente->razon_social  = $request->input('razon_social');
            $cliente->correo        = $request->input('correo');
            $cliente->celular       = $request->input('celular');
            $cliente->save();

            if($request->input('tipo_cliente') === 'lavador'){
                $vehiculo             = new Vehiculo();
                $vehiculo->creador_id = Auth::user()->id;
                $vehiculo->cliente_id = $cliente->id;
                $vehiculo->placa      = '0000-AAA';
                $vehiculo->save();
                $cliente->tipo_cliente = 'lavador';
            }else{
                $cliente->tipo_cliente = 'cliente';
            }
            $cliente->save();

            $data['estado']  = 'success';
            $data['listado'] = $this->listadoArray();
            $data['new']     = $cliente;

        }else{
            $data['estado'] = 'error';
        }
        return $data;
    }

    public function perfil(Request $request, $cliente_id){

        $cliente = Cliente::find($cliente_id);

        $vehiculos = Vehiculo::where('cliente_id', $cliente_id)
                                ->get();

        return view('cliente.perfil')->with(compact('cliente', 'vehiculos'));
    }

    public function ajaxListadoVehiculo(Request $request){
        if($request->ajax()){
            $cliente_id = $request->input('cliente');

            $data['listado'] = $this->listadoArrayVehiuclos($cliente_id);
            $data['estado'] = 'success';
        }else{
            $data['estado'] = 'error';
        }

        return $data;
    }

    public function listadoArrayVehiuclos($cliente){

        $vehiculos = Vehiculo::where('cliente_id', $cliente)->get();

        return view('cliente.ajaxListadoVehiculos')->with(compact('vehiculos'))->render();
    }

    public function  actualizarUsuario(Request $request){
        if($request->ajax()){

            $cliente_id              = $request->input('act_cliente_id');

            $cliente                 = Cliente::find($cliente_id);
            $cliente->modificador_id = Auth::user()->id;
            $cliente->nombres        = $request->input('act_nombres');
            $cliente->cedula         = $request->input('act_cedula');
            $cliente->complemento    = $request->input('act_complemento');
            $cliente->nit            = $request->input('act_nit');
            $cliente->razon_social   = $request->input('act_razon_social');
            $cliente->correo         = $request->input('act_correo');
            $cliente->save();

            $data['estado'] = 'success';
        }else{
            $data['estado'] = 'error';
        }
        return $data;
    }

    protected function listadoArray(){
        // $clientes = Cliente::all();
        $clientes = Cliente::orderBy('id', 'desc')->limit(100)->get();
        return view('cliente.ajaxListado')->with(compact('clientes'))->render();
    }

}
