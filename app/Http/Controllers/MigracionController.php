<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use App\Models\Vehiculo;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

use Maatwebsite\Excel\Facades\Excel;
//use Excel;

class MigracionController extends Controller
{

    public function migrarServicios(Request $request){
        // Ruta del archivo Excel
        $filePath = public_path('servicios.xlsx');

        // Lee el archivo Excel y obtiene todas las filas
        $rows = Excel::toArray([], $filePath);

        // Itera sobre cada fila y accede a los datos
        foreach ($rows[0] as $key =>  $row) {

            // Accede a los datos de cada columna en la fila
            $column0 = $row[0]; // Valor de la primera columna
            $column1 = $row[1]; // Valor de la segunda columna
            $column2 = $row[2]; // Valor de la segunda columna
            $column3 = $row[3]; // Valor de la segunda columna
            $column4 = $row[4]; // Valor de la segunda columna
            echo $key." => "."|0| ".$column0." <> |1|".$column1." <> |2|".$column2." <> |3|".$column3." <> |4|".$column4."<br>" ;
            if($key !== 0){
                $servicio               = new Servicio();
                $servicio->creador_id   = 1;
                $servicio->categoria_id = $row[2];
                $servicio->codigo       = $row[0];
                $servicio->descripcion  = $row[1];
                $servicio->unidad_venta = $row[3];
                $servicio->precio       = $row[4];
                $servicio->save();
            }
        }

    }

    public function migrarVehiculos(Request $request){
        // Ruta del archivo Excel
        $filePath = public_path('vehiculos.xlsx');

        // Lee el archivo Excel y obtiene todas las filas
        $rows = Excel::toArray([], $filePath);
        $contador = 1;

        // Itera sobre cada fila y accede a los datos
        foreach ($rows[0] as $key =>  $row) {

            // Accede a los datos de cada columna en la fila
            $column0 = $row[0]; // Valor de la primera columna
            $column1 = $row[1]; // Valor de la segunda columna
            $column2 = $row[2]; // Valor de la segunda columna
            $column3 = $row[3]; // Valor de la segunda columna
            $column4 = $row[4]; // Valor de la segunda columna
            echo $key." => "."|0| ".$column0." <> |1|".$column1." <> |2|".$column2." | ".$contador."<br>" ;

            if($key !== 0 && is_numeric(substr($column0, 0, 3))){
                $vehiculo               = new Vehiculo();
                $vehiculo->creador_id   = 1;
                $vehiculo->cliente_id   = 1;
                $vehiculo->placa        = $row[0];
                $vehiculo->nit          = $row[1];
                $vehiculo->razon_social = $row[2];
                $vehiculo->save();
                $contador++;
            }

            // if($key === 2000)
            //     break;
        }

    }

}
