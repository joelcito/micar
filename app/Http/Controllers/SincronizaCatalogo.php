<?php

namespace App\Http\Controllers;

use App\Models\MotivoAnulacion;
use App\Models\TipoDocumento;
use App\Models\TipoEvento;
use Illuminate\Http\Request;

class SincronizaCatalogo extends Controller
{
    public function listado(Request $request){
        return view('sincronizacionCatalogo.listado');
    }

    public function ajaxListadoTipoDocumento(REquest $request){
        if($request->ajax()){
            $data['estado']     = 'success';
            $data['listado']    = $this->listadoArrayTipoDocumento();
        }else{
            $data['estado']     = 'error';
        }
        return $data;
    }

    protected function listadoArrayTipoDocumento(){
        $tipoDocumentos = TipoDocumento::all();
        return view('sincronizacionCatalogo.ajaxListadoTipoDocumento')->with(compact('tipoDocumentos'))->render();
    }


    public function ajaxListadoMotivoAnulacion(REquest $request){
        if($request->ajax()){
            $data['estado']     = 'success';
            $data['listado']    = $this->listadoArrayMotivoAnulacion();
        }else{
            $data['estado']     = 'error';
        }
        return $data;
    }

    protected function listadoArrayMotivoAnulacion(){
        $tipoDocumentos = MotivoAnulacion::all();
        return view('sincronizacionCatalogo.ajaxListadoMotivoAnulacion')->with(compact('tipoDocumentos'))->render();
    }


    public function ajaxListadoTipoEvento(REquest $request){
        if($request->ajax()){
            $data['estado']     = 'success';
            $data['listado']    = $this->listadoArrayTipoEvento();
        }else{
            $data['estado']     = 'error';
        }
        return $data;
    }

    protected function listadoArrayTipoEvento(){
        $tipoDocumentos = TipoEvento::all();
        return view('sincronizacionCatalogo.ajaxListadoTipoEvento')->with(compact('tipoDocumentos'))->render();
    }

    public function sincronizarTipoDocumento(REquest $request){
        if($request->ajax()){
            $siat = app(SiatController::class);
            $sincronizarParametricaTipoDocumentoIdentidad   = json_decode($siat->sincronizarParametricaTipoDocumentoIdentidad());
            if($sincronizarParametricaTipoDocumentoIdentidad->resultado->RespuestaListaParametricas){
                $array = $sincronizarParametricaTipoDocumentoIdentidad->resultado->RespuestaListaParametricas->listaCodigos;
                foreach ($array as $key => $value) {
                    $tipoDocuemneto = TipoDocumento::where('codigo_sin', $value->codigoClasificador)->first();
                    if($tipoDocuemneto){
                        $tipoDocuemneto->nombre = $value->descripcion;
                    }else{
                        $tipoDocuemneto = new TipoDocumento();
                        $tipoDocuemneto->codigo_sin = $value->codigoClasificador;
                        $tipoDocuemneto->nombre     = $value->descripcion;
                    }
                    $tipoDocuemneto->save();
                }
                $data['estado'] = 'success';
                $data['msg']    = 'SINCRONIZACION EXITOSA!';
            }else{
                $data['estado'] = 'error';
                $data['msg'] = 'ERROR AL SINCRONIZAR!';
            }
        }else{
            $data['estado'] = 'error';
        }
        return $data;
    }

    public function sincronizarMotivoAnulacion(REquest $request){
        if($request->ajax()){
            $siat = app(SiatController::class);
            $sincronizarParametricaTipoDocumentoIdentidad   = json_decode($siat->sincronizarParametricaMotivoAnulacion());
            if($sincronizarParametricaTipoDocumentoIdentidad->resultado->RespuestaListaParametricas){
                $array = $sincronizarParametricaTipoDocumentoIdentidad->resultado->RespuestaListaParametricas->listaCodigos;
                foreach ($array as $key => $value) {
                    $motivoAnulacion = MotivoAnulacion::where('codigo_sin', $value->codigoClasificador)->first();
                    if($motivoAnulacion){
                        $motivoAnulacion->nombre = $value->descripcion;
                    }else{
                        $motivoAnulacion = new MotivoAnulacion();
                        $motivoAnulacion->codigo_sin = $value->codigoClasificador;
                        $motivoAnulacion->nombre     = $value->descripcion;
                    }
                    $motivoAnulacion->save();
                }
                $data['estado'] = 'success';
                $data['msg']    = 'SINCRONIZACION EXITOSA!';
            }else{
                $data['estado'] = 'error';
                $data['msg'] = 'ERROR AL SINCRONIZAR!';
            }
        }else{
            $data['estado'] = 'error';
        }
        return $data;
    }

    public function sincronizarTipoEvento(REquest $request){
        if($request->ajax()){
            $siat = app(SiatController::class);
            $sincronizarParametricaTipoDocumentoIdentidad   = json_decode($siat->sincronizarParametricaEventosSignificativos());
            if($sincronizarParametricaTipoDocumentoIdentidad->resultado->RespuestaListaParametricas){
                $array = $sincronizarParametricaTipoDocumentoIdentidad->resultado->RespuestaListaParametricas->listaCodigos;
                foreach ($array as $key => $value) {
                    $tipoEvento = TipoEvento::where('codigo_sin', $value->codigoClasificador)->first();
                    if($tipoEvento){
                        $tipoEvento->nombre = $value->descripcion;
                    }else{
                        $tipoEvento = new TipoEvento();
                        $tipoEvento->codigo_sin = $value->codigoClasificador;
                        $tipoEvento->nombre     = $value->descripcion;
                    }
                    $tipoEvento->save();
                }
                $data['estado'] = 'success';
                $data['msg']    = 'SINCRONIZACION EXITOSA!';
            }else{
                $data['estado'] = 'error';
                $data['msg'] = 'ERROR AL SINCRONIZAR!';
            }
        }else{
            $data['estado'] = 'error';
        }
        return $data;
    }

}
