<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Servicio;
use App\Models\LiquidacionLavador;
use App\Models\Movimiento;
use App\Models\Pago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServicioController extends Controller
{

    public function listado(Request $request){
        $categorias = Categoria::all();
        return view('servicio.listado')->with(compact('categorias'));
    }

    public function guarda(Request $request){
        if($request->ajax()){
            if(intval($request->input('servicio_id')) === 0){
                $servicio = new Servicio();
                $servicio->creador_id = Auth::user()->id;
            }else{
                $servicio = Servicio::find($request->input('servicio_id'));
                $servicio->modificador_id = Auth::user()->id;
            }

            $servicio->descripcion     = $request->input('descripcion');
            $servicio->unidad_venta    = $request->input('unidad_venta');
            $servicio->precio          = $request->input('precio');
            $servicio->categoria_id    = $request->input('categoria_id');
            $servicio->codigoActividad = $request->input('cod_actividad');
            $servicio->codigoProducto  = $request->input('cod_producto');
            $servicio->unidadMedida    = $request->input('uni_medida');

            $servicio->save();

            $data['estado'] = 'success';
            $data['listado'] = $this->listadoArray();
            return json_encode($data);
        }
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
        // $servicios = Servicio::all();
        $servicios = Servicio::where('estado','servicio')->get();
        return view('servicio.ajaxListado')->with(compact('servicios'))->render();
    }

    public function eliminar(Request $request){
        if($request->ajax()){
            if($request->has('id')){
                $servicio = Servicio::find($request->input('id'));
                $servicio->eliminador_id = Auth::user()->id;
                $servicio->save();
                Servicio::destroy($request->input('id'));
                $data['estado'] = 'success';
                $data['listado'] = $this->listadoArray();
            }else{
                $data['estado'] = 'error';
            }
        }else{
            $data['estado'] = 'error';
        }
        return json_encode($data);
    }

    public function producto(Request $request){
        return view('servicio.producto');
    }

    public function ajaxListadoProducto(Request $request){
        if($request->ajax()){
            $data['estado'] = 'success';
            $data['listado'] = $this->listadoArrayProducto();
        }else{
            $data['estado'] = 'error';
        }
        return $data;
    }

    protected function listadoArrayProducto(){
        $productos = Servicio::where('estado','producto')
                                ->orderBy('id', 'desc')
                                ->get();
        return view('servicio.ajaxListadoProducto')->with(compact('productos'))->render();
    }
    public function  guardaProdcuto(Request $request){
        if($request->ajax()){
            // dd($request->all());
            // GUARDAMOS EN LA TABLA MOVIMIENTOS
            $servicio_id             = $request->input('servicio_id');
            $movimiento              = new Movimiento();
            $movimiento->creador_id  = Auth::user()->id;
            $movimiento->servicio_id = $servicio_id;

            if($request->input('ingreso')){
                $movimiento->ingreso     = $request->input('cantidad');
            }else{
                $movimiento->salida     = $request->input('cantidad');
            }

            $movimiento->fecha       = date('Y-m-d H:i:s');
            $movimiento->descripcion = $request->input('descripcion');
            $movimiento->save();

            // GUARDAMOS EN LA TABLA PAGOS
            // $pago              = new Pago();
            // $pago->creador_id  = Auth::user()->id;
            // $pago->servicio_id = $request->input('servicio_id');
            // $pago->cantidad    = $request->input('cantidad');
            // $pago->monto       = $request->input('total_pagar');
            // $pago->fecha       = date('Y-m-d H:i:s');
            // $pago->tipo_pago   = $request->input('tipo_pago');
            // $pago->estado      = "Salida";
            // $pago->descripcion = $request->input('descripcion');
            // $pago->save();

            $data['listado'] = $this->listadoArrayProducto();
            $data['estado'] = 'success';
        }else{
            $data['estado'] = 'error';
        }
        return $data;
    }

    public function cantidadAlmacen(Request $request){
        if($request->ajax()){
            $servicio_id = $request->input('servicio');
            $sumaIngreso = Movimiento::where('servicio_id', $servicio_id)->sum('ingreso');
            $sumaSalida  = Movimiento::where('servicio_id', $servicio_id)->sum('salida');
            $data['cantidaAlacen'] = ($sumaIngreso-$sumaSalida);
            $data['estado'] = 'success';
        }else{
            $data['estado'] = 'error';
        }
        return $data;
    }

    public function ajaxListadoAsignaciones(Request $request) {
        if($request->ajax()){
            $usuario      = $request->input('usuario');
            $asignaciones = LiquidacionLavador::where('lavador_id', $usuario)->get();
            $data['estado'] = 'success';
            $data['listado'] = view('servicio.ajaxListadoAsignaciones')->with(compact('asignaciones'))->render();
        }else{
            $data['estado'] = 'success';
        }
        return $data;
    }

    public function guardarAsignacion(Request $request){
        if($request->ajax()){

            $asignacion_id = $request->input('asignacion');

            if($asignacion_id == 0){
                $usuario  = $request->input('usuario');
                $servicio = $request->input('servicio');
                $datos = LiquidacionLavador::where('lavador_id',$usuario)
                                            ->where('servicio_id', $servicio)
                                            ->get();
                if(count($datos) == 0){
                    $liquidacionlavador                   = new LiquidacionLavador();
                    $liquidacionlavador->creador_id       = Auth::user()->id;
                    $liquidacionlavador->lavador_id       = $usuario;
                    $liquidacionlavador->servicio_id      = $servicio;
                    $liquidacionlavador->liquidacion      = $request->input('porcentaje');
                    $liquidacionlavador->tipo_liquidacion = 'porcentaje';
                    $liquidacionlavador->save();
                    $data['estado']  = 'success';
                }else{
                    $data['estado'] = 'error';
                    $data['msg']    = 'Ya existe la Asignacion';
                }
            }else{
                $liquidacion              = LiquidacionLavador::find($asignacion_id);
                $liquidacion->Liquidacion = $request->input('porcentaje');
                $liquidacion->save();
                $data['estado'] = 'success';
            }
        }else{
            $data['estado']  = 'error';
            $data['msg']    = 'Error en el Ajax!';
        }
        return $data;
    }

    public function agregarProdcuto(Request $request){
        if($request->ajax()){
            $producto_ud = $request->input('new_producto');
            $servicio               = $producto_ud == 0 ?  new Servicio() : Servicio::find($producto_ud);
            $servicio->descripcion  = $request->input('new_descripcion');
            $servicio->unidad_venta = 'UNIDAD';
            $servicio->precio       = $request->input('new_precio');
            $servicio->estado       = 'producto';
            $servicio->save();

            $data['estado'] = 'success';
        }else{
            $data['estado'] = 'error';
        }
        return $data;
    }

    function eliminarProduto(Request $request){
        if($request->ajax()){
            Servicio::destroy($request->input('id'));
            $data['estado'] = 'success';
        }else{
            $data['estado'] = 'error';
        }
        return $data;
    }

    public function eliminarAsignacion(Request $request) {
        if($request->ajax()){
            $asignacion_id = $request->input('asignacion');
            LiquidacionLavador::destroy($asignacion_id);
            $data['estado'] = 'success';
        }else{
            $data['estado'] = 'error';
        }
        return $data;
    }

}
