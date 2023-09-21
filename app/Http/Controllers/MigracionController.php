<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Servicio;
use App\Models\Vehiculo;
use App\Models\Movimiento;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Auth;

use Maatwebsite\Excel\Facades\Excel;
//use Excel;

class MigracionController extends Controller
{

    public function migrarServicios(Request $request){
        // Ruta del archivo Excel
        $filePath = public_path('servicios_mas_pro.xlsx');
        // $filePath = public_path('servicios.xlsx');

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
                $servicio->estado       = $row[6];
                $servicio->save();

                if($row[6] === 'producto' && $row[5] > 0){
                    $movimiento              = new Movimiento();
                    $movimiento->creador_id  = Auth::user()->id;
                    $movimiento->servicio_id = $servicio->id;
                    $movimiento->ingreso     = $row[5];
                    $movimiento->salida      = 0;
                    $movimiento->fecha       = date('Y-m-d H:i:s');
                    $movimiento->descripcion = "MIGRACION";
                    $movimiento->save();
                }
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

    public function migracionClienteVehiculo(Request $request){
        // Ruta del archivo Excel
        $filePath = public_path('vehiculoscliente.xlsx');

        // Lee el archivo Excel y obtiene todas las filas
        $rows = Excel::toArray([], $filePath);
        $contador = 1;

        $clienteArray = array();
        // Itera sobre cada fila y accede a los datos
        foreach ($rows[0] as $key =>  $row) {
            // Accede a los datos de cada columna en la fila
            $column0 = $row[0]; // Valor de la primera columna
            $column1 = $row[1]; // Valor de la segunda columna
            $column2 = $row[2]; // Valor de la segunda columna
            // $column3 = $row[3]; // Valor de la segunda columna
            // $column4 = $row[4]; // Valor de la segunda columna
            echo $key." => "."|0| ".$column0." <> |1|".$column1." <> |2|".$column2." | ".$contador."<br>" ;
            if($key !== 0 && is_numeric(substr($column0, 0, 3))){
                $nit                = $row[1];
                $placa              = $row[0];
                $razon_social       = $row[2];
                if(!in_array($nit, $clienteArray)){
                    $clienteArray[] = $nit;
                    $cliente = new Cliente();
                    $cliente->nombres       = $razon_social;
                    $cliente->nit           = $nit;
                    $cliente->razon_social  = $razon_social;
                    $cliente->save();
                }else{
                    $cliente = Cliente::where('nit', $nit)->first();
                    if($cliente == null){
                        echo "<b><h1>ERROR</h1><br><br><br><br>".$key." => "."|0| ".$column0." <> |1|".$column1." <> |2|".$column2." | ".$contador."</b>";
                        exit;
                    }
                }
                $vehiculo               = new Vehiculo();
                $vehiculo->creador_id   = 1;
                $vehiculo->cliente_id   = $cliente->id;
                $vehiculo->placa        = $placa;
                $vehiculo->save();
            }
        }

    }

}
