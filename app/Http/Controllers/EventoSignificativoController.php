<?php

namespace App\Http\Controllers;

use App\Models\TipoEvento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventoSignificativoController extends Controller
{
    public function listado(Request $request){
        // $siat = app(SiatController::class);
        // $respuesta = json_decode($siat->sincronizarParametricaEventosSignificativos());
        // if($respuesta->estado === "success"){
        //     if($respuesta->resultado->RespuestaListaParametricas->transaccion){
        //         $eventosparametricas = json_decode(json_encode($respuesta->resultado->RespuestaListaParametricas->listaCodigos), true);
        //     }else{
        //         $eventosparametricas = [];
        //     }
        // }else{
        //     $eventosparametricas = [];
        // }

        $tipoEventos = TipoEvento::all();

        return view('eventosignificativo.listado')->with(compact('tipoEventos'));
    }

    public function consultaEventos(Request $request){
        if($request->ajax()){
            $fechaEvento = $request->input('fecha');
            $siat = app(SiatController::class);
            $respuesta = json_decode($siat->consultaEventoSignificativo($fechaEvento));
            if($respuesta->estado === "success"){
                if($respuesta->resultado->RespuestaListaEventos->transaccion){
                    $eventos = json_decode(json_encode($respuesta->resultado->RespuestaListaEventos->listaCodigos), true);
                    $data['estado'] = 'success';
                    $data['listado'] = view('eventosignificativo.ajaxListado')->with(compact('eventos'))->render();
                }else{
                    //NO EXISTE REGISTRO DE EVENTO SIGNIFICATIVO EN LA BASE DE DATOS DEL SIN
                    $data['estado'] = 'error';
                    $data['msg'] = $respuesta->resultado->RespuestaListaEventos->mensajesList->descripcion;
                }
            }else{
                $data['estado'] = 'error';
                $data['msg'] = 'ERROR EN LA BASE DE DATOS';
            }

            return $data;
        }
    }

    public function registro(Request $request) {
        if($request->ajax()){

            // RECUPERAMOS EL ULTIMO CUFD QUE FUE VIGENTE
            $cufd   = app(CufdController::class);
            $datosCufdOffLine  = $cufd->sacarCufdVigenteFueraLinea();

            // ELIMINAMOS EL CUFD Y CREAMOS OTRO PARA EL REGISTRO DE UN NUEVO EVENTOS SIGNIFICACITO
            session()->forget(['scufd','scodigoControl','sdireccion','sfechaVigenciaCufd']);

            $codMotEvent    = $request->input('codigoEvento');
            $cufdEvent      = $datosCufdOffLine['scufd'];
            $desc           = $request->input('descripcion');
            // $fechaIni       = $request->input('fechainicio').":00";
            // $fechaFin       = $request->input('fechafin').":00";
            $fechaIni       = str_replace(' ', 'T', trim($request->input('fechainicio')));
            $fechaFin       = str_replace(' ', 'T', trim($request->input('fechafin')));

            $siat = app(SiatController::class);
            // AQUI CREAMOS EL NUEVO CUFD PARA EL NUEVO DIA
            $cufd = json_decode($siat->cufd());
            if($cufd->estado === "success"){
                if($cufd->resultado->RespuestaCufd->transaccion){
                    session(['scufd' => $cufd->resultado->RespuestaCufd->codigo]);
                    session(['scodigoControl' => $cufd->resultado->RespuestaCufd->codigoControl]);
                    session(['sdireccion' => $cufd->resultado->RespuestaCufd->direccion]);
                    session(['sfechaVigenciaCufd' => $cufd->resultado->RespuestaCufd->fechaVigencia]);
                    $cufdNew = app(CufdController::class);
                    $cufdNew->create(
                                    $cufd->resultado->RespuestaCufd->codigo,
                                    $cufd->resultado->RespuestaCufd->codigoControl,
                                    $cufd->resultado->RespuestaCufd->direccion,
                                    $cufd->resultado->RespuestaCufd->fechaVigencia,
                                    Auth::user()->codigo_punto_venta
                                );
                }
            }
            // END AQUI CREAMOS EL NUEVO CUFD PARA EL NUEVO DIA
            $respuesta = json_decode($siat->registroEventoSignificativo($codMotEvent, $cufdEvent, $desc, $fechaIni, $fechaFin));

            if($respuesta->estado === "success" && $respuesta->resultado->RespuestaListaEventos->transaccion){
                $data['estado']     = "success";
                $data['msg']        = $respuesta->resultado->RespuestaListaEventos->codigoRecepcionEventoSignificativo;
            }else{
                $data['estado']     = "error";
                $data['msg']        = $respuesta->resultado->RespuestaListaEventos->mensajesList->descripcion;
            }
        }else{
            $data['estado']     = "error";
            $data['msg']        = "Algo salio mal";
        }
        return $data;
    }

    public function buscarEventosSignificativos(Request $request){
        if($request->ajax()){
            $fechaEvento = $request->input('fecha_contingencia');
            $siat = app(SiatController::class);
            $respuesta = json_decode($siat->consultaEventoSignificativo($fechaEvento));
            // dd($respuesta);
            if($respuesta->estado === "success"){
                if($respuesta->resultado->RespuestaListaEventos->transaccion){
                    $eventos = json_decode(json_encode($respuesta->resultado->RespuestaListaEventos->listaCodigos), true);
                    $data['estado'] = 'success';
                    $data['eventos'] = $eventos;
                    // $data['listado'] = view('eventosignificativo.ajaxListado')->with(compact('eventos'))->render();
                }else{
                    //NO EXISTE REGISTRO DE EVENTO SIGNIFICATIVO EN LA BASE DE DATOS DEL SIN
                    // dd($respuesta->resultado->RespuestaListaEventos->mensajesList->descripcion);
                    $data['estado'] = 'error';
                    $data['msg'] = $respuesta->resultado->RespuestaListaEventos->mensajesList->descripcion;
                }
            }else{
                $data['estado'] = 'error';
                $data['msg'] = 'ERROR EN LA BASE DE DATOS O CONSULTA';
            }

            return $data;
        }
    }
}
