<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReporteController extends Controller
{
    public function pagos(Request $request) {
        return view('reporte.pagos');
    }
}
