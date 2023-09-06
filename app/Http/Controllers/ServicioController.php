<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Servicio;
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

            $servicio->descripcion  = $request->input('descripcion');
            $servicio->unidad_venta = $request->input('unidad_venta');
            $servicio->precio       = $request->input('precio');
            $servicio->categoria_id = $request->input('categoria_id');

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
        $productos = Servicio::where('estado','producto')->get();
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
            $movimiento->ingreso     = $request->input('cantidad');
            $movimiento->fecha       = date('Y-m-d H:i:s');
            $movimiento->descripcion = $request->input('descripcion');
            $movimiento->save();

            // GUARDAMOS EN LA TABLA PAGOS
            $pago              = new Pago();
            $pago->creador_id  = Auth::user()->id;
            $pago->servicio_id = $request->input('servicio_id');
            $pago->cantidad    = $request->input('cantidad');
            $pago->monto       = $request->input('total_pagar');
            $pago->fecha       = date('Y-m-d H:i:s');
            $pago->tipo_pago   = $request->input('tipo_pago');
            $pago->estado      = "Salida";
            $pago->descripcion = $request->input('descripcion');
            $pago->save();

            $data['listado'] = $this->listadoArrayProducto();
            $data['estado'] = 'success';
        }else{
            $data['estado'] = 'error';
        }
        return $data;
    }

}
