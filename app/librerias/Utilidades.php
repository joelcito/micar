<?php

namespace App\librerias;

class Utilidades{

    function fechaHoraCastellano($fecha)
    {
        $arrayFecha = explode(" ", $fecha);

        $fecha = substr($fecha, 0, 10);
        $numeroDia = date('d', strtotime($fecha));
        $dia = date('l', strtotime($fecha));
        $mes = date('F', strtotime($fecha));
        $anio = date('Y', strtotime($fecha));
        $dias_ES = array("Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo");
        $dias_EN = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
        $nombredia = str_replace($dias_EN, $dias_ES, $dia);
        $meses_ES = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre",
            "Noviembre", "Diciembre");
        $meses_EN = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October",
            "November", "December");
        $nombreMes = str_replace($meses_EN, $meses_ES, $mes);

        return $nombredia . ", " . $numeroDia . " de " . $nombreMes . " del " . $anio. ' - Hora: ' . $arrayFecha[1];
    }

    function fechaCastellano($fecha)
    {
        $fecha = substr($fecha, 0, 10);
        $numeroDia = date('d', strtotime($fecha));
        $dia = date('l', strtotime($fecha));
        $mes = date('F', strtotime($fecha));
        $anio = date('Y', strtotime($fecha));
        $dias_ES = array("Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo");
        $dias_EN = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
        $nombredia = str_replace($dias_EN, $dias_ES, $dia);
        $meses_ES = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre",
            "Noviembre", "Diciembre");
        $meses_EN = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October",
            "November", "December");
        $nombreMes = str_replace($meses_EN, $meses_ES, $mes);

        return $nombredia . ", " . $numeroDia . " de " . $nombreMes . " del " . $anio;
    }

     /**
    * Devuelve la información del usuario sobre la cuenta
    *
    * This method is used to retrieve the account corresponding
    * to a given login. <b>Note:</b> it is not required that
    * the user be currently logged in.
    *
    * @access public
    * @param string $ciudad nombre de la ciudad 'La Paz'
    * @return 'LP'
    */

    function cambiaExpedido($ciudad)
    {
        switch ($ciudad) {
            case 'La Paz':
                $expedido = 'L.P.';
                break;
            case 'Oruro':
                $expedido = 'OR';
                break;
            case 'Potosi':
                $expedido = 'PT';
                break;
            case 'Cochabamba':
                $expedido = 'CB';
                break;
            case 'Santa Cruz':
                $expedido = 'S.C.';
                break;
            case 'Beni':
                $expedido = 'BN';
                break;
            case 'Pando':
                $expedido = 'PA';
                break;
            case 'Tarija':
                $expedido = 'TJ';
                break;
            case 'Chuquisaca':
                $expedido = 'CH';
                break;
            default:
                $expedido = '';
        }

        return $expedido;
    }
}