<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use App\Models\Servicio;
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

        $fecha_ini = $request->input('fecha_ini');
        $fecha_fin = $request->input('fecha_fin');

        $facturas = Factura::where('estado_pago', 'Deuda')
                            ->whereNull('estado')
                            ->whereBetween(DB::raw('LEFT(`fecha`, 10)'), [$fecha_ini, $fecha_fin])
                            ->get();

        $pdf = PDF::loadView('reporte.reporteCuentaPorCobrar', compact('fecha_ini', 'fecha_fin', 'facturas'))->setPaper('letter');
        return $pdf->stream('reporteCuentaPorCobrar.pdf');
    }

    public function reporteInventario(Request $request){
        $fecha_ini = $request->input('fecha_ini');
        $fecha_fin = $request->input('fecha_fin');

        $servicios = Servicio::select('servicios.*', 'movimientos.descripcion as desMov')
                                ->join('movimientos','servicios.id', '=', 'movimientos.servicio_id')
                                ->where('servicios.estado','producto')
                                ->whereBetween(DB::raw('LEFT(movimientos.fecha, 10)'), [$fecha_ini, $fecha_fin])
                                ->get();

        $pdf = PDF::loadView('reporte.reporteInventario', compact('fecha_ini', 'fecha_fin', 'servicios'))->setPaper('letter');
        return $pdf->stream('reporteInventario.pdf');
    }

    public function  reporteInformeVenta(Request $request){
        $fecha_ini = $request->input('fecha_ini');
        $fecha_fin = $request->input('fecha_fin');

        $ventas = Factura::whereBetween(DB::raw('LEFT(`fecha`, 10)'), [$fecha_ini, $fecha_fin])
                        ->whereNull('estado')
                        ->get();

        $pdf = PDF::loadView('reporte.reporteInformeVenta', compact('fecha_ini', 'fecha_fin', 'ventas'))->setPaper('letter');
        return $pdf->stream('reporteInformeVenta.pdf');
    }
}
