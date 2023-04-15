<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use Illuminate\Http\Request;

class RolController extends Controller
{

    public function listado(Request $request){
        return view('rol.listado');
    }

    public function guarda(Request $request){

        $data = array();

        if($request->ajax()){

            $rol = new Rol();

            $rol->nombre = $request->input('nombre');
            $rol->descripcion = $request->input('descripcion');

            $rol->save();
            $data['estado'] = 'success';
            $data['listado'] = $this->listadoArray();

        }else{
            $data['estado'] = 'error';
        }

        return json_encode($data);
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
        $roles = Rol::all();
        return view('rol.ajaxListado')->with(compact('roles'))->render();
    }

    public function eliminar(Request $request){
        $data = array();
        if($request->ajax()){
            // $rol = Rol::find($request->input('id'));
            Rol::destroy($request->input('id'));

            $data['listado']=$this->listadoArray();
            $data['estado'] = 'success';
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
     * @param  \App\Models\Rol  $rol
     * @return \Illuminate\Http\Response
     */
    public function show(Rol $rol)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Rol  $rol
     * @return \Illuminate\Http\Response
     */
    public function edit(Rol $rol)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Rol  $rol
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Rol $rol)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Rol  $rol
     * @return \Illuminate\Http\Response
     */
    public function destroy(Rol $rol)
    {
        //
    }
}
