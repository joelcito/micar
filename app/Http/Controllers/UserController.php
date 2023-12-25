<?php

namespace App\Http\Controllers;

use App\Models\Detalle;
use App\Models\LiquidacionLavadorPago;
use App\Models\Rol;
use App\Models\Servicio;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function listado(Request $request){
        $roles = Rol::all();
        return view("user.listado")->with(compact('roles'));
    }

    public function ajaxListado(Request $request){
        $data = array();
        if($request->ajax()){
            $data['estado'] = 'success';
            $data['listado'] = $this->listadoArray();
        }else{
            $data['estado'] = 'error';
        }

        return json_encode($data);
    }

    protected function listadoArray(){
        $usuarios = User::all();
        return view("user.ajaxListado")->with(compact('usuarios'))->render();
    }

    public function guarda(Request $request){
        $data = array();
        if($request->ajax()){

            $user = new User();

            $user->nombres      = $request->input('nombres');
            $user->ap_paterno   = $request->input('ap_paterno');
            $user->ap_materno   = $request->input('ap_materno');
            $user->name         = $request->input('nombres')." ".$request->input('ap_paterno')." ".$request->input('ap_materno');
            $user->cedula       = $request->input('cedula');
            $user->email        = $request->input('email');
            $user->rol_id       = $request->input('rol_id');
            $user->direccion    = $request->input('direccion');
            $user->password     = Hash::make($request->input('password'));

            $user->save();

            $data['estado'] = 'success';
            $data['listado'] = $this->listadoArray();
        }else{
            $data['estado'] = 'error';
        }

        return json_encode($data);
    }

    public function detalle(Request $request, $usuario_id){

        $servicios  = Servicio::where('tipo_liquidacion', 'depende')->get();
        $usuario    = User::find($usuario_id);

        $liquidaciones = LiquidacionLavadorPago::where('lavador_id_user',$usuario_id)->get();

        $serviciosRealizados = Detalle::where('lavador_id', $usuario_id)
                                        ->orderBy('id', 'desc')
                                        ->get();

        return view('user.detalle')->with(compact('usuario', 'servicios', 'liquidaciones', 'serviciosRealizados'));
    }

    public function cambioPass(Request $request){
        if($request->ajax()){
            $user           = User::find($request->input('user_id_new_pro'));
            $user->password = Hash::make($request->input('pass1'));
            $user->save();
            $data['estado'] = 'success';
        }else{
            $data['estado'] = 'error';
        }
        return $data;
    }
}
