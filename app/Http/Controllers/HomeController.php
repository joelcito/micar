<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Factura;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){

        $clientes  = Cliente::where('tipo_cliente', 'cliente')->count();
        $lavadores = Cliente::where('tipo_cliente', 'lavador')->count();

        $total     = $clientes + $lavadores;
        $porCenLav = number_format(($lavadores * 100) / $total, 2 );
        $porCenCli = number_format(($clientes * 100) / $total, 2 );

        $fechaHoy = date('Y-m-d');

        $cantidadVentas = Factura::whereBetween('fecha', [$fechaHoy." 00:00:00", $fechaHoy." 23:59:59"])->count();

        return view('home.inicio')->with(compact('clientes', 'lavadores', 'porCenLav', 'porCenCli', 'cantidadVentas'));
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
