<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
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

    protected function listadoArray(){
        // $clientes = Cliente::all();
        $clientes = Cliente::orderBy('id', 'desc')->limit(100)->get();
        return view('cliente.ajaxListado')->with(compact('clientes'))->render();
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

            $data['estado'] = 'success';
            $data['listado'] = $this->listadoArray();
        }else{
            $data['estado'] = 'error';
        }
        return $data;
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
     * @param  \App\Models\Cliente  $cliente
     * @return \Illuminate\Http\Response
     */
    public function show(Cliente $cliente)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Cliente  $cliente
     * @return \Illuminate\Http\Response
     */
    public function edit(Cliente $cliente)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Cliente  $cliente
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Cliente $cliente)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Cliente  $cliente
     * @return \Illuminate\Http\Response
     */
    public function destroy(Cliente $cliente)
    {
        //
    }
}
