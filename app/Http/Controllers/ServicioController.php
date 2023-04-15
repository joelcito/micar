<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Servicio;
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
        $servicios = Servicio::all();
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
     * @param  \App\Models\Servicio  $servicio
     * @return \Illuminate\Http\Response
     */
    public function show(Servicio $servicio)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Servicio  $servicio
     * @return \Illuminate\Http\Response
     */
    public function edit(Servicio $servicio)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Servicio  $servicio
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Servicio $servicio)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Servicio  $servicio
     * @return \Illuminate\Http\Response
     */
    public function destroy(Servicio $servicio)
    {
        //
    }
}
