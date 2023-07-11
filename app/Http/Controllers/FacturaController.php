<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use App\Models\Pago;
use App\Models\Vehiculo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SimpleXMLElement;

class FacturaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function arrayCuotasPagar(Request $request){
        if($request->ajax()){

            $vehiculo_d = $request->input('vehiculo');

            $servicios = Pago::select('pagos.*','servicios.codigoActividad', 'servicios.codigoProducto', 'servicios.unidadMedida', 'servicios.descripcion')
                                ->join('servicios', 'pagos.servicio_id','=', 'servicios.id')
                                ->where('pagos.estado',"paraPagar")
                                ->where('pagos.vehiculo_id',$vehiculo_d)
                                ->get();

            $data['lista']  = json_encode($servicios);
            $data['estado'] = 'success';

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
    public function actualizaDescuento(Request $request){
        if($request->ajax()){
            $pago               = Pago::find($request->input('pago_id'));
            $pago->descuento    = $request->input('valor');
            $pago->importe      = ($pago->precio * $pago->cantidad)-$request->input('valor');
            $pago->save();

            $vehiculo_id        = $pago->vehiculo_id;

            $sumaImporte = Pago::where('estado','paraPagar')
                                ->where('vehiculo_id',$vehiculo_id)
                                ->sum('total');

            $sumaRebaja = Pago::where('estado','paraPagar')
                                ->where('vehiculo_id',$vehiculo_id)
                                ->sum('descuento');

            $data['valor'] = ($sumaImporte-$sumaRebaja);
            $data['estado'] = 'success';

        }else{
            $data['estado'] = 'error';
        }

        return $data;
    }

    public function sumaTotalMonto(Request $request){
        if($request->ajax()){

            // dd($request->all());
            $vehiculo_id = $request->input('vehiculo');

            $sumaImporte = Pago::where('estado','paraPagar')
                                ->where('vehiculo_id',$vehiculo_id)
                                ->sum('total');

            $sumaRebaja = Pago::where('estado','paraPagar')
                                ->where('vehiculo_id',$vehiculo_id)
                                ->sum('descuento');

            $data['valor'] = ($sumaImporte-$sumaRebaja);
            $data['estado'] = "success";
        }else{
            $data['estado'] = "error";
        }
        return $data;
    }

    public function emitirFactura(Request $request){
        if($request->ajax()){

            // dd($request->all());

            // dd(
            //     "este chee",
            //     $request->all(),
            //     $request->input('datos')['factura'][0]['cabecera']['numeroDocumento'],
            //     $request->input('datos')['factura'][0]['cabecera']['nombreRazonSocial'],
            //     $request->input('modalidad')
            // );

            $datos              = $request->input('datos');
            $datosVehiculo       = $request->input('datosVehiculo');
            $valoresCabecera    = $datos['factura'][0]['cabecera'];
            $puntoVenta         = Auth::user()->codigo_punto_venta;
            // $puntoVenta         = 0;
            $tipo_factura       = $request->input('modalidad');


            $nitEmisor          = str_pad($valoresCabecera['nitEmisor'],13,"0",STR_PAD_LEFT);
            $fechaEmision       = str_replace(".","",str_replace(":","",str_replace("-","",str_replace("T", "",$valoresCabecera['fechaEmision']))));
            $sucursal           = str_pad(0,4,"0",STR_PAD_LEFT);
            $modalidad          = 2;
            $numeroFactura      = str_pad($valoresCabecera['numeroFactura'],10,"0",STR_PAD_LEFT);

            if($tipo_factura === "online"){
                $tipoEmision        = 1;
            }
            else{
                // $datosRecepcion       = $request->input('datosRecepcion');
                // if($datosRecepcion['uso_cafc'] === "si"){
                //     $datos['factura'][0]['cafc'] = $datosRecepcion['codigo_cafc_contingencia'];
                // }
                // $tipoEmision        = 2;
            }

            $tipoFactura        = 1;
            $tipoFacturaSector  = 1;
            $puntoVenta         = str_pad($puntoVenta,4,"0",STR_PAD_LEFT);

            $cadena = $nitEmisor.$fechaEmision.$sucursal.$modalidad.$tipoEmision.$tipoFactura.$tipoFacturaSector.$numeroFactura.$puntoVenta;

            // VERIFICAMOS SI EXISTE LOS DATOS SUFICINTES APRA EL MANDAO DEL CORREO
            $vehiculo = Vehiculo::find($datosVehiculo['vehiculo_id']);
            if(!($vehiculo && $vehiculo->cliente->correo != null && $vehiculo->cliente->correo != '')){
                $data['estado'] = "error_email";
                $data['msg']    = "La persona no tiene correo";
                return $data;
            }
            $vehiculo->nit              = $request->input('datos')['factura'][0]['cabecera']['numeroDocumento'];
            $vehiculo->razon_social     = $request->input('datos')['factura'][0]['cabecera']['nombreRazonSocial'];
            $vehiculo->save();

            // CODIGO DEL VIDEO PARACE QUE SIRVE NOMAS
            // ini_set('soap.wsdl_cache_enable',0);
            // $wdls = "https://indexingenieria.com/webservices/wssiatcuf.php?wsdl";
            // $client = new SoapClient($wdls);
            // $client->__getFunctions();
            // $params = array(
            //     'factura_numero' => $numeroFactura,
            //     'nit_emisor' => $nitEmisor,
            //     'fechaEmision' => $valoresCabecera['fechaEmision'],
            //     'codigoControl' => session('scodigoControl')
            // );
            // $cuf = $client->__soapCall('generaCuf', $params);
            // $datos['factura'][0]['cabecera']['cuf'] = $cuf;

            // CODIGO DE JOEL ESETE LO HIZMOMOS NOSOTROS
            $cadenaConM11 = $cadena.$this->calculaDigitoMod11($cadena, 1, 9, false);
            if($tipo_factura === "online"){
                if(!session()->has('scufd')){
                    $siat = app(SiatController::class);
                    $siat->verificarConeccion();
                }
                $scufd                  = session('scufd');
                $scodigoControl         = session('scodigoControl');
                $sdireccion             = session('sdireccion');
                $sfechaVigenciaCufd     = session('sfechaVigenciaCufd');
            }else{
                // $cufdController             = app(CufdController::class);
                // $datosCufdOffLine           = $cufdController->sacarCufdVigenteFueraLinea();
                // if($datosCufdOffLine['estado'] === "success"){
                //     $scufd                  = $datosCufdOffLine['scufd'];
                //     $scodigoControl         = $datosCufdOffLine['scodigoControl'];
                //     $sdireccion             = $datosCufdOffLine['sdireccion'];
                //     $sfechaVigenciaCufd     = $datosCufdOffLine['sfechaVigenciaCufd'];
                // }else{

                // }
            }

            $cufPro                                         = $this->generarBase16($cadenaConM11).$scodigoControl;

            // dd($cufPro, $scodigoControl, $this->generarBase16($cadenaConM11), $cadenaConM11);

            // dd($datos['factura'][0]['cabecera']['codigoPuntoVenta']);

            $datos['factura'][0]['cabecera']['cuf']                 = $cufPro;
            $datos['factura'][0]['cabecera']['cufd']                = $scufd;
            $datos['factura'][0]['cabecera']['direccion']           = $sdireccion;
            $datos['factura'][0]['cabecera']['codigoPuntoVenta']    = $puntoVenta;

            // dd($datos);

            // $datos['factura'][0]['cabecera']['codigoPuntoVenta']    = 3;
            // $datos['factura'][0]['cabecera']['codigoPuntoVenta']    = 1;

            // dd($datos['factura']);

            // VERIFICAMOS QUE SEA MENSUALIDAD
            // for ($i=1; $i < count($datos['factura']); $i++) {
            //     if($datos['factura'][$i]['detalle']['codigoProducto'] != 2){
            //         $g = explode(' ', $datos['factura'][$i]['detalle']['descripcion']);
            //         $datos['factura'][$i]['detalle']['descripcion'] = $g[1];
            //     }
            // }

            // VERIFICAMOS EN EL PERIDOD
            // $periodo = explode(' ', $datos['factura'][0]['cabecera']['periodoFacturado']);
            // if(array_intersect(["null","undefined"],$periodo))
            //     $datos['factura'][0]['cabecera']['periodoFacturado'] = $periodo[1];


            $temporal = $datos['factura'];
            $dar = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
                        <facturaComputarizadaSectorEducativo xsi:noNamespaceSchemaLocation="facturaComputarizadaSectorEducativo.xsd" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
                        </facturaComputarizadaSectorEducativo>';

            $dar = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
                        <facturaElectronicaCompraVenta xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="facturaElectronicaCompraVenta.xsd">
                        </facturaElectronicaCompraVenta>';
            $xml_temporal = new SimpleXMLElement($dar);
            $this->formato_xml($temporal, $xml_temporal);

            $xml_temporal->asXML("assets/docs/facturaxml.xml");

            // COMPRIMIMOS EL ARCHIVO A ZIP
            $gzdato = gzencode(file_get_contents('assets/docs/facturaxml.xml',9));
            $fiape = fopen('assets/docs/facturaxml.xml.zip',"w");
            fwrite($fiape,$gzdato);
            fclose($fiape);

            //  hashArchivo EL ARCHIVO
            $archivoZip = $gzdato;
            $hashArchivo = hash("sha256", file_get_contents('assets/docs/facturaxml.xml'));

            // GUARDAMOS EN LA FACTURA
            // dd($datos['factura'][0]['cabecera']['nombreRazonSocial']);

            $factura                            = new Factura();
            $factura->creador_id                = Auth::user()->id;
            $factura->vehiculo_id               = $datosVehiculo['vehiculo_id'];
            $factura->cliente_id                = $vehiculo->cliente_id;
            $factura->razon_social              = $datos['factura'][0]['cabecera']['nombreRazonSocial'];
            $factura->carnet                    = $vehiculo->cliente->cedula;
            $factura->nit                       = $datos['factura'][0]['cabecera']['numeroDocumento'];;
            $factura->fecha                     = $datos['factura'][0]['cabecera']['fechaEmision'];
            $factura->total                     = $datos['factura'][0]['cabecera']['montoTotal'];
            $factura->facturado                 = "Si";
            $factura->numero                    = $datos['factura'][0]['cabecera']['numeroFactura'];
            $factura->cuf                       = $datos['factura'][0]['cabecera']['cuf'];
            $factura->codigo_metodo_pago_siat   = $datos['factura'][0]['cabecera']['codigoMetodoPago'];
            $factura->monto_total_subjeto_iva   = $datos['factura'][0]['cabecera']['montoTotalSujetoIva'];
            $factura->descuento_adicional       = $datos['factura'][0]['cabecera']['descuentoAdicional'];
            $factura->productos_xml             = file_get_contents('assets/docs/facturaxml.xml');
            $factura->tipo_factura              = $tipo_factura;

            $factura->save();

            // dd("haber", $datosVehiculo, $vehiculo, $tipo_factura);

            // $factura = new Factura();

            // $factura->user_id                   = Auth::user()->id;
            // $factura->persona_id                = $datosPersona['persona_id'];
            // $factura->carnet                    = $datosPersona['carnet'];
            // $factura->razon_social              = $datos['factura'][0]['cabecera']['nombreRazonSocial'];
            // $factura->nit                       = $datos['factura'][0]['cabecera']['numeroDocumento'];
            // $factura->fecha                     = $datos['factura'][0]['cabecera']['fechaEmision'];
            // $factura->total                     = $datos['factura'][0]['cabecera']['montoTotal'];
            // $factura->facturado                 = "Si";
            // if($tipo_factura === "online"){
            //     $factura->numero                = $datos['factura'][0]['cabecera']['numeroFactura'];
            // }else{
            //     if($datosRecepcion['uso_cafc'] === "si"){
            //         $factura->numero_cafc           = $datos['factura'][0]['cabecera']['numeroFactura'];
            //         $factura->uso_cafc              = "si";
            //     }
            //     else{
            //         $factura->numero                = $datos['factura'][0]['cabecera']['numeroFactura'];
            //     }
            // }
            // $factura->anio_vigente              = date('Y');
            // $factura->cuf                       = $datos['factura'][0]['cabecera']['cuf'];
            // $factura->codigo_metodo_pago_siat   = $datos['factura'][0]['cabecera']['codigoMetodoPago'];
            // $factura->monto_total_subjeto_iva   = $datos['factura'][0]['cabecera']['montoTotalSujetoIva'];
            // $factura->descuento_adicional       = $datos['factura'][0]['cabecera']['descuentoAdicional'];
            // $factura->productos_xml             = file_get_contents('assets/docs/facturaxml.xml');
            // $factura->tipo_factura              = $tipo_factura;

            // $factura->save();

            if($tipo_factura === "online"){
                $siat = app(SiatController::class);
                $for = json_decode($siat->recepcionFactura($archivoZip, $valoresCabecera['fechaEmision'],$hashArchivo));

                dd($for);

                if($for->estado === "error"){
                    $codigo_descripcion = null;
                    $codigo_trancaccion = null;
                    $descripcion        = null;
                    $codigo_recepcion   = null;
                }else{
                    if($for->resultado->RespuestaServicioFacturacion->transaccion){
                        $codigo_recepcion   = $for->resultado->RespuestaServicioFacturacion->codigoRecepcion;
                        $descripcion        = NULL;
                    }else{
                        $codigo_recepcion   = NULL;
                        $descripcion        = $for->resultado->RespuestaServicioFacturacion->mensajesList->descripcion;
                    }
                    $codigo_descripcion     = $for->resultado->RespuestaServicioFacturacion->codigoDescripcion;
                    $codigo_trancaccion     = $for->resultado->RespuestaServicioFacturacion->transaccion;
                }

                $data['estado'] = $codigo_descripcion;

            }else{
                $codigo_descripcion = null;
                $codigo_recepcion   = null;
                $codigo_trancaccion = null;
                $descripcion        = null;

                $data['estado']     = 'OFFLINE';

            }

            // $siat = app(SiatController::class);
            // $for = json_decode($siat->recepcionFactura($archivoZip, $valoresCabecera['fechaEmision'],$hashArchivo));

            // if($for->estado === "error"){
            //     $codigo_descripcion = null;
            //     $codigo_trancaccion = null;
            //     $descripcion        = null;
            //     $codigo_recepcion   = null;
            // }else{
            //     if($for->resultado->RespuestaServicioFacturacion->transaccion){
            //         $codigo_recepcion = $for->resultado->RespuestaServicioFacturacion->codigoRecepcion;
            //         $descripcion = NULL;
            //     }else{
            //         $codigo_recepcion = NULL;
            //         $descripcion = $for->resultado->RespuestaServicioFacturacion->mensajesList->descripcion;
            //     }
            //     $codigo_descripcion = $for->resultado->RespuestaServicioFacturacion->codigoDescripcion;
            //     $codigo_trancaccion = $for->resultado->RespuestaServicioFacturacion->transaccion;
            // }

            $facturaNew                     = Factura::find($factura->id);
            $facturaNew->codigo_descripcion = $codigo_descripcion;
            $facturaNew->codigo_recepcion   = $codigo_recepcion;
            $facturaNew->codigo_trancaccion = $codigo_trancaccion;
            $facturaNew->descripcion        = $descripcion;
            // $facturaNew->cuis               = session('scuis');
            // $facturaNew->cufd               = session('scufd');
            // $facturaNew->fechaVigencia      = session('sfechaVigenciaCufd');
            $facturaNew->cuis               = session('scuis');
            $facturaNew->cufd               = $scufd;
            $facturaNew->fechaVigencia      = $sfechaVigenciaCufd;
            $facturaNew->save();


            // $data['estado'] = $facturaNew->codigo_descripcion;

            for ($i=1; $i < count($datos['factura']) ; $i++) {

                $servicio = $datos['factura'][$i]['detalle']['codigoProducto'];

                // PREGUNTAMOS SI ES MENSUALIDAD
                if($servicio === "2"){
                    $arrayMen = explode(" ", $datos['factura'][$i]['detalle']['descripcion']);
                    $pago = Pago::where('persona_id',$datosPersona['persona_id'])
                                ->where('estado', 'paraPagar')
                                ->where('anio_vigente', date('Y'))
                                ->where('mensualidad', $arrayMen[0])
                                ->first();

                    if($pago){
                        $pago->descuento    = ($datos['factura'][$i]['detalle']['montoDescuento'] == null)? 0 :  $datos['factura'][$i]['detalle']['montoDescuento'];
                        $pago->subTotal     = ($datos['factura'][$i]['detalle']['subTotal'] == null)? 0 :  $datos['factura'][$i]['detalle']['subTotal'];
                        $pago->estado       = "Pagado";
                        $pago->fecha        = $valoresCabecera['fechaEmision'];
                        $pago->factura_id   = $facturaNew->id;
                        $pago->user_id      = Auth::user()->id;
                        // $pago->cuis         = session('scuis');
                        // $pago->cufd         = session('scufd');
                        // $pago->fechaVigencia= session('sfechaVigenciaCufd');

                        $pago->save();
                    }
                }else{
                    $pago = Pago::where('persona_id',$datosPersona['persona_id'])
                                ->where('estado', 'paraPagar')
                                ->where('anio_vigente', date('Y'))
                                ->where('servicio_id', $servicio)
                                ->first();

                    if($pago){
                        $pago->descuento    = ($datos['factura'][$i]['detalle']['montoDescuento'] == null)? 0 :  $datos['factura'][$i]['detalle']['montoDescuento'];
                        $pago->subTotal     = ($datos['factura'][$i]['detalle']['subTotal'] == null)? 0 :  $datos['factura'][$i]['detalle']['subTotal'];
                        $pago->estado       = "Pagado";
                        $pago->fecha        = $valoresCabecera['fechaEmision'];
                        $pago->factura_id   = $facturaNew->id;
                        $pago->user_id      = Auth::user()->id;
                        // $pago->cuis         = session('scuis');
                        // $pago->cufd         = session('scufd');
                        // $pago->fechaVigencia= session('sfechaVigenciaCufd');

                        $pago->save();
                    }
                }

            }


            // ENVIAMOS EL CORREO DE LA FACTURA
            // $nombre = $persona->nombres." ".$persona->apellido_paterno." ".$persona->apellido_materno;
            // $this->enviaCorreo(
            //     $persona->email,
            //     $nombre,
            //     $factura->numero,
            //     $factura->fecha,
            //     $factura->id
            // );

            // PARA VALIDAR EL XML
            // $this->validar();

        }else{

        }

        return $data;

    }

    protected function calculaDigitoMod11($cadena, $numDig, $limMult, $x10){

        $mult = 0;
        $suma = 0;
        $dig = 0;
        $i = 0;
        $n = 0;

        if (!$x10) {
            $numDig = 1;
        }

        for ($n = 1; $n <= $numDig; $n++) {
            $suma = 0;
            $mult = 2;

            for ($i = strlen($cadena) - 1; $i >= 0; $i--) {
                $suma += ($mult * intval(substr($cadena, $i, 1)));

                if (++$mult > $limMult) {
                    $mult = 2;
                }
            }

            if ($x10) {
                $dig = (($suma * 10) % 11) % 10;
            } else {
                $dig = $suma % 11;
            }

            if ($dig == 10) {
                $cadena .= "1";
            }

            if ($dig == 11) {
                $cadena .= "0";
            }

            if ($dig < 10) {
                $cadena .= strval($dig);
            }
        }

        return substr($cadena, strlen($cadena) - $numDig, $numDig);
    }

    protected function generarBase16($caracteres) {
        $pString = ltrim($caracteres, '0');
        $vValor = gmp_init($pString);
        return strtoupper(gmp_strval($vValor, 16));
    }

    protected function formato_xml($temporal, $xml_temporal){
        $ns_xsi = "http://www.w3.org/2001/XMLSchema-instance";
        foreach($temporal as $key => $value){
            if(is_array($value)){
                if(!is_numeric($key)){
                    $subnodo = $xml_temporal->addChild("$key");
                    $this->formato_xml($value, $subnodo);
                }else{
                    $this->formato_xml($value, $xml_temporal);
                }
            }else{
                if($value == null && $value <> '0'){
                    $hijo = $xml_temporal->addChild("$key","$value");
                    $hijo->addAttribute('xsi:nil','true', $ns_xsi);
                }else{
                    $xml_temporal->addChild("$key", "$value");
                }
            }
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Factura  $factura
     * @return \Illuminate\Http\Response
     */
    public function edit(Factura $factura)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Factura  $factura
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Factura $factura)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Factura  $factura
     * @return \Illuminate\Http\Response
     */
    public function destroy(Factura $factura)
    {
        //
    }
}
