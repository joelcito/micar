<?php

namespace App\Http\Controllers;

use App\Models\Cufd;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CufdController extends Controller
{
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
    public function create($codigo,$codigoControl,$direccion,$fechaVigencia){
        $cufd = new Cufd();
        $cufd->codigo               = $codigo;
        $cufd->codigoControl        = $codigoControl;
        $cufd->direccion            = $direccion;
        // Convierte el valor a un objeto Carbon y aplícale el formato deseado
        $fechaVigenciaFormateada    = Carbon::parse($fechaVigencia)->format('Y-m-d H:i:s');
        $cufd->fechaVigencia        = $fechaVigenciaFormateada;
        $cufd->save();
    }

    public function sacarCufdVigenteFueraLinea(){
        $cufd           = Cufd::orderBy('id','desc')->take(3)->get();
        $sw             = true;
        $tam            = 0;
        $fechaActual    = Carbon::now();

        while($sw && $tam <= 2){
            $fechaVigencia      = Carbon::parse($cufd[$tam]->fechaVigencia);
            $fechaLimite        = $fechaVigencia->addHours(72);
            $fechaVerificar     = $fechaActual;
            if($fechaVerificar->lte($fechaLimite)){
                $data['estado']             = "success";
                $data['scufd']              = $cufd[$tam]->codigo;
                $data['scodigoControl']     = $cufd[$tam]->codigoControl;
                $data['sdireccion']         = $cufd[$tam]->direccion;
                $data['sfechaVigenciaCufd'] = $cufd[$tam]->fechaVigencia;
                $sw                         = false;
            }else{
                echo "<->".$tam."<->";
            }
            $tam++;
        }
        if($sw){
            $data['estado']         = "error" ;
            $data['msg']            = "la fecha esta fuera de las 72 horas " ;
        }
        return $data;
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
     * @param  \App\Cufd  $cufd
     * @return \Illuminate\Http\Response
     */
    public function show(Cufd $cufd)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Cufd  $cufd
     * @return \Illuminate\Http\Response
     */
    public function edit(Cufd $cufd)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Cufd  $cufd
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Cufd $cufd)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Cufd  $cufd
     * @return \Illuminate\Http\Response
     */
    public function destroy(Cufd $cufd)
    {
        //
    }
}
