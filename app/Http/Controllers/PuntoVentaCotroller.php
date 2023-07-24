<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PuntoVentaCotroller extends Controller
{

    public function listado(Request $request)  {
        return view('puntoVenta.listado');
    }

    public function ajaxListado(Request $request){
        if($request->ajax()){

            $cufdController             = app(CufdController::class);
            $datosCufdOffLine           = $cufdController->sacarCufdVigenteFueraLinea();
            if($datosCufdOffLine['estado'] === "success"){
                // $scufd                  = $datosCufdOffLine['scufd'];
                // $scodigoControl         = $datosCufdOffLine['scodigoControl'];
                // $sdireccion             = $datosCufdOffLine['sdireccion'];
                // $sfechaVigenciaCufd     = $datosCufdOffLine['sfechaVigenciaCufd'];

                session(['scufd'                => $datosCufdOffLine['scufd'] ]);
                session(['scodigoControl'       => $datosCufdOffLine['scodigoControl'] ]);
                session(['sdireccion'           => $datosCufdOffLine['sdireccion'] ]);
                session(['sfechaVigenciaCufd'   => $datosCufdOffLine['sfechaVigenciaCufd'] ]);

            }else{

            }

            $siat = app(SiatController::class);
            $respuesta = json_decode($siat->consultaPuntoVenta());
            // dd($respuesta);
            if($respuesta->estado === "success"){
                $puntos = json_decode(json_encode($respuesta->resultado->RespuestaConsultaPuntoVenta->listaPuntosVentas), true);
                $data['listado'] = view('puntoventa.ajaxListado')->with(compact('puntos'))->render();
                $data['estado'] = 'success';
            }else{
                $data['estado'] = 'error';
            }
            return $data;
        }
    }

    public function guarda(Request $request){
        if($request->ajax()){
            $nombre         = $request->input('nombre');
            $descripcion    = $request->input('descripcion');
            $siat = app(SiatController::class);
            $res = json_decode($siat->registroPuntoVenta($descripcion,$nombre), true);
            $data['estado'] = $res['estado'];
        }else{

        }
        return $data;
    }

    public function eliminaPuntoVenta(Request $request){
        if($request->ajax()){
            $cod         = $request->input('cod');
            $siat = app(SiatController::class);
            $res = json_decode($siat->cierrePuntoVenta($cod), true);
            $data['estado'] = $res['estado'];
            return $data;
        }
    }
}
