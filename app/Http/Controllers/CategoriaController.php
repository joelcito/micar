<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoriaController extends Controller
{

    public function listado(Request $request){
        return view('categoria.listado');
    }

    public function guarda(Request $request){
        if($request->ajax()){
            if(intval($request->input('categoria_id')) ===  0){
                $categoria = new Categoria();
                $categoria->creador_id  = Auth::user()->id;
            }else{
                $categoria = Categoria::find($request->input('categoria_id'));
                $categoria->modificador_id = Auth::user()->id;
            }

            $categoria->nombre      = $request->input('nombre');
            $categoria->descripcion = $request->input('descripcion');

            $categoria->save();

            $data['estado'] = 'success';
            $data['listado'] = $this->listadoArray();
            return json_encode($data);
        }
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
        $categorias = Categoria::all();
        return view('categoria.ajaxListado')->with(compact('categorias'))->render();
    }

    public function eliminar(Request $request){
        if($request->ajax()){
            if($request->has('id')){
                $categoria = Categoria::find($request->input('id'));
                $categoria->eliminador_id = Auth::user()->id;
                $categoria->save();

                Categoria::destroy($request->input('id'));

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
     * @param  \App\Models\Categoria  $categoria
     * @return \Illuminate\Http\Response
     */
    public function show(Categoria $categoria)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Categoria  $categoria
     * @return \Illuminate\Http\Response
     */
    public function edit(Categoria $categoria)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Categoria  $categoria
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Categoria $categoria)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Categoria  $categoria
     * @return \Illuminate\Http\Response
     */
    public function destroy(Categoria $categoria)
    {
        //
    }
}
