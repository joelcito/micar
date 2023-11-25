<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;

class ReporteController extends Controller
{
    public function pagos(Request $request) {
        return view('reporte.pagos');
    }

    public function listado(Request $request) {
        return view('reporte.listado');
    }

    public function  reporteCuentaPorCobrar(Request $request){
        // return view('reporte.reporteCuentaPorCobrar');
        // $pdf = PDF::loadView('reporte.reporteCuentaPorCobrar', compact('factura', 'archivoXML','rutaImagenQR'))->setPaper('letter');

        $fecha_ini = $request->input('fecha_ini');
        $fecha_fin = $request->input('fecha_fin');

        $facturas = Factura::where('estado_pago', 'Deuda')
                            ->whereNull('estado')
                            ->whereBetween(DB::raw('LEFT(`fecha`, 10)'), [$fecha_ini, $fecha_fin])
                            ->get();
                            // ->toSql();
                            // dd($facturas, $fecha_ini,$fecha_fin);

        $pdf = PDF::loadView('reporte.reporteCuentaPorCobrar', compact('fecha_ini', 'fecha_fin', 'facturas'))->setPaper('letter');
        return $pdf->stream('reporteCuentaPorCobrar.pdf');
    }
}
