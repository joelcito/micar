<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use App\Models\Servicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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


        // $servicios = Servicio::select('servicios.*', 'movimientos.descripcion as desMov')
        //                         ->join('movimientos','servicios.id', '=', 'movimientos.servicio_id')
        //                         ->where('servicios.estado','producto')
        //                         // ->whereBetween(DB::raw('LEFT(movimientos.fecha, 10)'), [$fecha_ini, $fecha_fin])
        //                         // ->whereBetween('movimientos.fecha', [$fecha_ini." 00:00:00", $fecha_fin." 23:59:59"])
        //                         ->get();

        $servicios = Servicio::select('servicios.*')
                                // ->join('movimientos','servicios.id', '=', 'movimientos.servicio_id')
                                ->where('servicios.estado','producto')
                                // ->whereBetween(DB::raw('LEFT(movimientos.fecha, 10)'), [$fecha_ini, $fecha_fin])
                                // ->whereBetween('movimientos.fecha', [$fecha_ini." 00:00:00", $fecha_fin." 23:59:59"])
                                ->get();



        //                         ->toSql();
        // dd($fecha_ini,$fecha_fin, $servicios);


        $pdf = PDF::loadView('reporte.reporteInventario', compact('fecha_ini', 'fecha_fin', 'servicios'))->setPaper('letter');
        return $pdf->stream('reporteInventario.pdf');
    }

    public function  reporteInventarioExcel(Request $request){
        $fecha_ini = $request->input('fecha_ini');
        $fecha_fin = $request->input('fecha_fin');

        $servicios = Servicio::select('servicios.*')
                                ->where('servicios.estado','producto')
                                ->get();


        // $facturasQ = Factura::orderBy('id', 'asc');

        // $facturasQ->where('facturado', 'Si')
        //         ->whereBetween(DB::raw('LEFT(`fecha`, 10)'), [$fecha_ini, $fecha_fin])
        //         ->orderBy('numero', 'desc')
        //         ->get();

        // $facturas = $facturasQ->get();

        // -******* GENERECION DEL EXCEL -*******
        $fileName = 'reporte_inventario.xlsx';
            // return Excel::download(new CertificadoExport($carrera_persona_id), 'certificado.xlsx');
        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('B1', "REPORTE DE INVENTARIO INGRESOS Y SALIDAS");

        $sheet->setCellValue('A3', "DESDE $fecha_ini HASTA $fecha_fin");

        $sheet->setCellValue('A5', 'No');
        $sheet->setCellValue('B5', 'DESCRIPCION');
        $sheet->setCellValue('C5', 'ALMACEN');
        $sheet->setCellValue('D5', 'INGRESO');
        $sheet->setCellValue('E5', 'SALIDA');
        $sheet->setCellValue('F5', 'SALDO ACTUAL');
        $sheet->setCellValue('G5', 'PRECIO VENTA');
        $sheet->setCellValue('H5', 'PRECIO VENTA TOTAL');

        // $sheet->setCellValue('I5', 'IMPORTE DE LA VENTA');
        // $sheet->setCellValue('J5', 'IMPORTE ICE/IEHD/TASAS');
        // $sheet->setCellValue('K5', 'EXPORTACIONES Y OPERACIONES EXENTAS');
        // $sheet->setCellValue('L5', 'VENTAS GRAVADAS A TASA CERO');
        // $sheet->setCellValue('M5', 'SUBTOTAL');
        // $sheet->setCellValue('N5', 'DESCUENTOS, BONIFICACIONES Y REBAJAS OTORGADAS');
        // $sheet->setCellValue('O5', 'IMPORTE BASE PARA DEBITO FISCAL');
        // $sheet->setCellValue('P5', 'DEBITO FISCAL');
        // $sheet->setCellValue('Q5', 'CODIGO CONTROL');

        $contadorFilas = 6;

        foreach($servicios as $key => $s){

            $sheet->setCellValue("A$contadorFilas", $s->id);
            $sheet->setCellValue("B$contadorFilas", $s->descripcion);

            $ingresoFecha = $s->movimientos->whereBetween('fecha', [$fecha_ini." 00:00:00", $fecha_fin." 23:59:59"])
                                            ->sum('ingreso');
                                $salidaFecha = $s->movimientos->whereBetween('fecha', [$fecha_ini." 00:00:00", $fecha_fin." 23:59:59"])
                                            ->sum('salida');

                                $ingresoFechaNot = $s->movimientos->where('fecha', '<',$fecha_ini)
                                                ->whereNotBetween('fecha', [$fecha_ini." 00:00:00", $fecha_fin." 23:59:59"])
                                                ->sum('ingreso');
                                $salidaFechaNot = $s->movimientos->where('fecha', '<',$fecha_ini)
                                                ->whereNotBetween('fecha', [$fecha_ini." 00:00:00", $fecha_fin." 23:59:59"])
                                                ->sum('salida');


            $sheet->setCellValue("C$contadorFilas", $ingresoFechaNot - $salidaFechaNot);
            $sheet->setCellValue("D$contadorFilas", $ingresoFecha);
            $sheet->setCellValue("E$contadorFilas", $salidaFecha);
            $sheet->setCellValue("F$contadorFilas", ($ingresoFecha - $salidaFecha) + ($ingresoFechaNot - $salidaFechaNot));
            $sheet->setCellValue("G$contadorFilas", $s->precio);
            $sheet->setCellValue("H$contadorFilas", (int)($ingresoFecha - $salidaFecha) + ($ingresoFechaNot - $salidaFechaNot) * (int)$s->precio);

            // if($f->estado == null){
            //     $estadoFactura = 'V: VALIDA';
            // }else{
            //     $estadoFactura = 'V: ANULADO';
            // }

            // para sacar el debito fiscal
            // $debito = $f->total * 0.13;

            // $sheet->setCellValue("A$contadorFilas", 3);
            // $sheet->setCellValue("B$contadorFilas", ++$key);
            // $sheet->setCellValue("C$contadorFilas", date("d/m/Y",strtotime($f->fecha)));
            // $sheet->setCellValue("D$contadorFilas", $f->numero);
            // // $sheet->setCellValue("E$contadorFilas", $f->parametro->numero_autorizacion);
            // $sheet->setCellValue("E$contadorFilas", $f->cuf);
            // $sheet->setCellValue("F$contadorFilas", $estadoFactura);
            // $sheet->setCellValue("G$contadorFilas", $f->nit);
            // $sheet->setCellValue("H$contadorFilas", $f->razon_social);
            // $sheet->setCellValue("I$contadorFilas", $f->total);
            // $sheet->setCellValue("J$contadorFilas", 0);
            // $sheet->setCellValue("K$contadorFilas", 0);
            // $sheet->setCellValue("L$contadorFilas", 0);
            // $sheet->setCellValue("M$contadorFilas", $f->total);
            // $sheet->setCellValue("N$contadorFilas", 0);
            // $sheet->setCellValue("O$contadorFilas", $f->total);
            // $sheet->setCellValue("P$contadorFilas", $debito);
            // $sheet->setCellValue("Q$contadorFilas", $f->codigo_control);

            $contadorFilas++;
        }



         // damos el ancho a las celdas
         $contadorLetras = 68; //comenzamos a partir de la letra D
         for ($i=1; $i<=18; $i++) {
             // extraemos la letra para la celda
             $letra = chr($contadorLetras);

             $spreadsheet->getActiveSheet()->getColumnDimension($letra)->setWidth(10);

             $contadorLetras++;
         }

         $fuenteNegritaTitulo = array(
         'font'  => array(
             'bold'  => true,
             // 'color' => array('rgb' => 'FF0000'),
             'size'  => 22,
             // 'name'  => 'Verdana'
         ));

         $fuenteNegrita = array(
         'font'  => array(
             'bold'  => true,
             // 'color' => array('rgb' => 'FF0000'),
             'size'  => 14,
         ));

         $fuenteNegritaCabecera = array(
         'font'  => array(
             'bold'  => true,
             // 'color' => array('rgb' => 'FF0000'),
             'size'  => 12,
         ));


        $spreadsheet->getActiveSheet()->getStyle("B1")->applyFromArray($fuenteNegritaTitulo);
        $spreadsheet->getActiveSheet()->getStyle('A3')->applyFromArray($fuenteNegrita);
        $spreadsheet->getActiveSheet()->getStyle("A5:H5")->applyFromArray($fuenteNegritaCabecera);
        // Aplicar bordes a las celdas
        $contadorFilas--;
        $sheet->getStyle("A5:H$contadorFilas")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(30); // Establece el ancho de la columna B a 15

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
        $writer->save('php://output');
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

    public function reporteLibroVenta(Request $request){

        $fecha_ini = $request->input('fecha_ini');
        $fecha_fin = $request->input('fecha_fin');

        $facturasQ = Factura::orderBy('id', 'asc');

        $facturasQ->where('facturado', 'Si')
                ->whereBetween(DB::raw('LEFT(`fecha`, 10)'), [$fecha_ini, $fecha_fin])
                ->orderBy('numero', 'desc')
                ->get();

        $facturas = $facturasQ->get();

        // -******* GENERECION DEL EXCEL -*******
        $fileName = 'libro_ventas.xlsx';
            // return Excel::download(new CertificadoExport($carrera_persona_id), 'certificado.xlsx');
        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('H1', "LIBRO DE VENTAS");

        $sheet->setCellValue('A3', "PERIODO $fecha_ini HASTA $fecha_fin");

        $sheet->setCellValue('A5', 'ESPECIFICACION');
        $sheet->setCellValue('B5', 'No');
        $sheet->setCellValue('C5', 'FECHA DE LA FACTURA');
        $sheet->setCellValue('D5', 'No DE LA FACTURA');
        $sheet->setCellValue('E5', 'No DE AUTORIZACION');
        $sheet->setCellValue('F5', 'ESTADO');
        $sheet->setCellValue('G5', 'NIT/CI CLIENTE');
        $sheet->setCellValue('H5', 'NOMBRE O RAZON SOCIAL');
        $sheet->setCellValue('I5', 'IMPORTE DE LA VENTA');
        $sheet->setCellValue('J5', 'IMPORTE ICE/IEHD/TASAS');
        $sheet->setCellValue('K5', 'EXPORTACIONES Y OPERACIONES EXENTAS');
        $sheet->setCellValue('L5', 'VENTAS GRAVADAS A TASA CERO');
        $sheet->setCellValue('M5', 'SUBTOTAL');
        $sheet->setCellValue('N5', 'DESCUENTOS, BONIFICACIONES Y REBAJAS OTORGADAS');
        $sheet->setCellValue('O5', 'IMPORTE BASE PARA DEBITO FISCAL');
        $sheet->setCellValue('P5', 'DEBITO FISCAL');
        $sheet->setCellValue('Q5', 'CODIGO CONTROL');

        $contadorFilas = 6;

        foreach($facturas as $key => $f)
        {
            if($f->estado == null){
                $estadoFactura = 'V: VALIDA';
            }else{
                $estadoFactura = 'V: ANULADO';
            }

            // para sacar el debito fiscal
            $debito = $f->total * 0.13;

            $sheet->setCellValue("A$contadorFilas", 3);
            $sheet->setCellValue("B$contadorFilas", ++$key);
            $sheet->setCellValue("C$contadorFilas", date("d/m/Y",strtotime($f->fecha)));
            $sheet->setCellValue("D$contadorFilas", $f->numero);
            // $sheet->setCellValue("E$contadorFilas", $f->parametro->numero_autorizacion);
            $sheet->setCellValue("E$contadorFilas", $f->cuf);
            $sheet->setCellValue("F$contadorFilas", $estadoFactura);
            $sheet->setCellValue("G$contadorFilas", $f->nit);
            $sheet->setCellValue("H$contadorFilas", $f->razon_social);
            $sheet->setCellValue("I$contadorFilas", $f->total);
            $sheet->setCellValue("J$contadorFilas", 0);
            $sheet->setCellValue("K$contadorFilas", 0);
            $sheet->setCellValue("L$contadorFilas", 0);
            $sheet->setCellValue("M$contadorFilas", $f->total);
            $sheet->setCellValue("N$contadorFilas", 0);
            $sheet->setCellValue("O$contadorFilas", $f->total);
            $sheet->setCellValue("P$contadorFilas", $debito);
            $sheet->setCellValue("Q$contadorFilas", $f->codigo_control);

            $contadorFilas++;
        }



         // damos el ancho a las celdas
         $contadorLetras = 68; //comenzamos a partir de la letra D
         for ($i=1; $i<=18; $i++) {
             // extraemos la letra para la celda
             $letra = chr($contadorLetras);

             $spreadsheet->getActiveSheet()->getColumnDimension($letra)->setWidth(20);

             $contadorLetras++;
         }

         $fuenteNegritaTitulo = array(
         'font'  => array(
             'bold'  => true,
             // 'color' => array('rgb' => 'FF0000'),
             'size'  => 22,
             // 'name'  => 'Verdana'
         ));

         $fuenteNegrita = array(
         'font'  => array(
             'bold'  => true,
             // 'color' => array('rgb' => 'FF0000'),
             'size'  => 14,
         ));

         $fuenteNegritaCabecera = array(
         'font'  => array(
             'bold'  => true,
             // 'color' => array('rgb' => 'FF0000'),
             'size'  => 12,
         ));


        $spreadsheet->getActiveSheet()->getStyle("H1")->applyFromArray($fuenteNegritaTitulo);
        $spreadsheet->getActiveSheet()->getStyle('A3')->applyFromArray($fuenteNegrita);
        $spreadsheet->getActiveSheet()->getStyle("A5:Q5")->applyFromArray($fuenteNegritaCabecera);
        // Aplicar bordes a las celdas
        $contadorFilas--;
        $sheet->getStyle("A5:Q$contadorFilas")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);


        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
        $writer->save('php://output');
    }
}
