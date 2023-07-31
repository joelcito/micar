<?php

namespace App\Http\Controllers;

use App\Firma\Firmadores\FirmadorBoliviaSingle;
use App\Models\Cliente;
use App\Models\Factura;
use App\Models\Pago;
use App\Models\Vehiculo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use SimpleXMLElement;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use PDF;
use Illuminate\Support\Str;
use PharData;

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

            $pagos =    $servicios->pluck('id');

            $data['lista']  = json_encode($servicios);
            $data['estado'] = 'success';
            $data['pagos'] = $pagos;

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

            $datos              = $request->input('datos');
            $datosVehiculo      = $request->input('datosVehiculo');
            $valoresCabecera    = $datos['factura'][0]['cabecera'];
            $puntoVenta         = Auth::user()->codigo_punto_venta;
            $tipo_factura       = $request->input('modalidad');

            $nitEmisor          = str_pad($valoresCabecera['nitEmisor'],13,"0",STR_PAD_LEFT);
            $fechaEmision       = str_replace(".","",str_replace(":","",str_replace("-","",str_replace("T", "",$valoresCabecera['fechaEmision']))));
            $sucursal           = str_pad(0,4,"0",STR_PAD_LEFT);
            $modalidad          = 1;
            $numeroFactura      = str_pad($valoresCabecera['numeroFactura'],10,"0",STR_PAD_LEFT);

            if($tipo_factura === "online"){
                $tipoEmision        = 1;
            }
            else{
                $datosRecepcion       = $request->input('datosRecepcion');
                // dd($datosRecepcion);
                if($datosRecepcion['uso_cafc'] === "Si"){
                    $datos['factura'][0]['cabecera']['cafc'] = $datosRecepcion['codigo_cafc_contingencia'];
                }
                $tipoEmision        = 2;
            }

            $tipoFactura        = 1;
            $tipoFacturaSector  = str_pad(1,2,"0",STR_PAD_LEFT);;
            $puntoVenta         = str_pad($puntoVenta,4,"0",STR_PAD_LEFT);

            $cadena = $nitEmisor.$fechaEmision.$sucursal.$modalidad.$tipoEmision.$tipoFactura.$tipoFacturaSector.$numeroFactura.$puntoVenta;

            // VERIFICAMOS SI EXISTE LOS DATOS SUFICINTES APRA EL MANDAO DEL CORREO
            $vehiculo = Vehiculo::find($datosVehiculo['vehiculo_id']);
            $cliente = Cliente::find($vehiculo->cliente->id);
            if(!($cliente && $cliente->correo != null && $cliente->correo != '')){
                $data['estado'] = "error_email";
                $data['msg']    = "La persona no tiene correo";
                return $data;
            }
            $cliente->nit              = $request->input('datos')['factura'][0]['cabecera']['numeroDocumento'];
            $cliente->razon_social     = $request->input('datos')['factura'][0]['cabecera']['nombreRazonSocial'];
            $cliente->save();


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
                $cufdController             = app(CufdController::class);
                $datosCufdOffLine           = $cufdController->sacarCufdVigenteFueraLinea();
                if($datosCufdOffLine['estado'] === "success"){
                    $scufd                  = $datosCufdOffLine['scufd'];
                    $scodigoControl         = $datosCufdOffLine['scodigoControl'];
                    $sdireccion             = $datosCufdOffLine['sdireccion'];
                    $sfechaVigenciaCufd     = $datosCufdOffLine['sfechaVigenciaCufd'];
                }else{

                }
            }

            $cufPro                                         = $this->generarBase16($cadenaConM11).$scodigoControl;

            $datos['factura'][0]['cabecera']['cuf']                 = $cufPro;
            $datos['factura'][0]['cabecera']['cufd']                = $scufd;
            $datos['factura'][0]['cabecera']['direccion']           = $sdireccion;
            $datos['factura'][0]['cabecera']['codigoPuntoVenta']    = $puntoVenta;

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
            /*
                $dar = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
                            <facturaComputarizadaSectorEducativo xsi:noNamespaceSchemaLocation="facturaComputarizadaSectorEducativo.xsd" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
                            </facturaComputarizadaSectorEducativo>';
            */
            $dar = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
                        <facturaElectronicaCompraVenta xsi:noNamespaceSchemaLocation="facturaElectronicaCompraVenta.xsd" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
                        </facturaElectronicaCompraVenta>';
            $xml_temporal = new SimpleXMLElement($dar);
            $this->formato_xml($temporal, $xml_temporal);

            $xml_temporal->asXML("assets/docs/facturaxml.xml");


            //  =========================   DE AQUI COMENZAMOS EL FIRMADO CHEEEEE ==============================\

            $firmador = new FirmadorBoliviaSingle('assets/certificate/softoken.p12', "5427648Scz");
            $xmlFirmado = $firmador->firmarRuta('assets/docs/facturaxml.xml');
            file_put_contents('assets/docs/facturaxml.xml', $xmlFirmado);

            // ========================== FINAL DE AQUI COMENZAMOS EL FIRMADO CHEEEEE  ==========================

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
            $factura->cuf                       = $datos['factura'][0]['cabecera']['cuf'];
            $factura->codigo_metodo_pago_siat   = $datos['factura'][0]['cabecera']['codigoMetodoPago'];
            $factura->monto_total_subjeto_iva   = $datos['factura'][0]['cabecera']['montoTotalSujetoIva'];
            $factura->descuento_adicional       = $datos['factura'][0]['cabecera']['descuentoAdicional'];
            $factura->productos_xml             = file_get_contents('assets/docs/facturaxml.xml');
            if($tipo_factura === "online"){
                $factura->numero                    = $datos['factura'][0]['cabecera']['numeroFactura'];
            }else{
                if($datosRecepcion['uso_cafc'] === "Si"){
                    $factura->numero_cafc           = $datos['factura'][0]['cabecera']['numeroFactura'];
                    $factura->uso_cafc              = "si";
                }else{
                    $factura->numero                    = $datos['factura'][0]['cabecera']['numeroFactura'];
                }
            }
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
            $facturaNew->cuis               = session('scuis');
            // $facturaNew->cufd               = session('scufd');
            // $facturaNew->fechaVigencia      = session('sfechaVigenciaCufd');
            $facturaNew->cufd               = $scufd;
            $facturaNew->fechaVigencia      = Carbon::parse($sfechaVigenciaCufd)->format('Y-m-d H:i:s');
            $facturaNew->save();


            // $data['estado'] = $facturaNew->codigo_descripcion;

            // dd($datos['factura'][1]['detalle'], $datosVehiculo);

            foreach ($datosVehiculo['pagos'] as $key => $pago_id) {
                $pago = Pago::find($pago_id);
                // dd($datosVehiculo['pagos'], $pago_id, $pago);
                $pago->estado       = "Pagado";
                $pago->factura_id   = $facturaNew->id;
                $pago->save();
            }


            // for ($i=1; $i < count($datos['factura']) ; $i++) {

            //     $servicio = $datos['factura'][$i]['detalle']['codigoProducto'];

            //     // PREGUNTAMOS SI ES MENSUALIDAD
            //     if($servicio === "2"){
            //         $arrayMen = explode(" ", $datos['factura'][$i]['detalle']['descripcion']);
            //         $pago = Pago::where('persona_id',$datosPersona['persona_id'])
            //                     ->where('estado', 'paraPagar')
            //                     ->where('anio_vigente', date('Y'))
            //                     ->where('mensualidad', $arrayMen[0])
            //                     ->first();

            //         if($pago){
            //             $pago->descuento    = ($datos['factura'][$i]['detalle']['montoDescuento'] == null)? 0 :  $datos['factura'][$i]['detalle']['montoDescuento'];
            //             $pago->subTotal     = ($datos['factura'][$i]['detalle']['subTotal'] == null)? 0 :  $datos['factura'][$i]['detalle']['subTotal'];
            //             $pago->estado       = "Pagado";
            //             $pago->fecha        = $valoresCabecera['fechaEmision'];
            //             $pago->factura_id   = $facturaNew->id;
            //             $pago->user_id      = Auth::user()->id;

            //             $pago->save();
            //         }
            //     }else{
            //         $pago = Pago::where('persona_id',$datosPersona['persona_id'])
            //                     ->where('estado', 'paraPagar')
            //                     ->where('anio_vigente', date('Y'))
            //                     ->where('servicio_id', $servicio)
            //                     ->first();

            //         if($pago){
            //             $pago->descuento    = ($datos['factura'][$i]['detalle']['montoDescuento'] == null)? 0 :  $datos['factura'][$i]['detalle']['montoDescuento'];
            //             $pago->subTotal     = ($datos['factura'][$i]['detalle']['subTotal'] == null)? 0 :  $datos['factura'][$i]['detalle']['subTotal'];
            //             $pago->estado       = "Pagado";
            //             $pago->fecha        = $valoresCabecera['fechaEmision'];
            //             $pago->factura_id   = $facturaNew->id;
            //             $pago->user_id      = Auth::user()->id;
            //             $pago->save();
            //         }
            //     }

            // }


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


    public function numeroFactura(){
        return Factura::where('facturado', 'Si')
                        // ->max('numero');
                        ->max(DB::raw('CAST(numero AS UNSIGNED)'));
    }

    public function anularFacturaNew(Request $request){
        if($request->ajax()){
            $idFactura  = $request->input('factura');
            $moivo      = $request->input('motivo');
            $fatura     = Factura::find($idFactura);
            $siat       = app(SiatController::class);
            $respuesta = json_decode($siat->anulacionFactura($moivo, $fatura->cuf));

            // dd($respuesta);

            if($respuesta->resultado->RespuestaServicioFacturacion->transaccion){
                $fatura->estado = 'Anulado';
                $pagos = Pago::where('factura_id', $fatura->id)
                                ->get();
                foreach($pagos as $p){
                    Pago::destroy($p->id);
                    // if($p->servicio_id == 2){
                    //     $ePago              = Pago::find($p->id);
                    //     $ePago->factura_id  = null;
                    //     $ePago->importe     = 0;
                    //     $ePago->faltante    = 0;
                    //     $ePago->fecha       = null;
                    //     $ePago->estado      = null;
                    //     $ePago->save();
                    // }else{
                    //     Pago::destroy($p->id);
                    // }
                }
            }else{
                $fatura->descripcion = $respuesta->resultado->RespuestaServicioFacturacion->mensajesList->descripcion;
            }
            $data['estado'] = $respuesta->resultado->RespuestaServicioFacturacion->transaccion;
            $data['descripcion'] = $respuesta->resultado->RespuestaServicioFacturacion->codigoDescripcion;
            $fatura->save();
        }else{

        }
        return $data;
    }

    public function generaPdfFacturaNew(Request $request, $factura_id){
        $factura = Factura::find($factura_id);
        $xml = $factura['productos_xml'];

        $archivoXML = new SimpleXMLElement($xml);

        $cabeza = (array) $archivoXML;

        $cuf            = (string)$cabeza['cabecera']->cuf;
        $numeroFactura  = (string)$cabeza['cabecera']->numeroFactura;

        // Genera el texto para el código QR
        $textoQR = 'https://pilotosiat.impuestos.gob.bo/consulta/QR?nit=5427648016&cuf='.$cuf.'&numero='.$numeroFactura.'&t=2';
        // Genera la ruta temporal para guardar la imagen del código QR
        $rutaImagenQR = storage_path('app/public/qr_code.png');
        // Genera el código QR y guarda la imagen en la ruta temporal
        QrCode::generate($textoQR, $rutaImagenQR);

        $pdf = PDF::loadView('pdf.generaPdfFacturaNew', compact('factura', 'archivoXML','rutaImagenQR'))->setPaper('letter');
        // unlink($rutaImagenQR);
        return $pdf->stream('factura.pdf');
    }

    public function muestraTableFacturaPaquete(Request $request){
        if($request->ajax()){
            $facturas = Factura::where('tipo_factura', 'offline')
                                ->where('facturado', "Si")
                                // ->where('codigo_descripcion', "!=", 'VALIDADA')
                                ->WhereNull('codigo_descripcion')
                                ->orderBy('id', 'desc')
                                ->get();
            $data['listado'] = view('pago.ajaxMuestraTableFacturaPaquete')->with(compact('facturas'))->render();
            $data['estado'] = "success";
        }else{
            $data['estado'] = "error";
        }
        return $data;
    }

    public function mandarFacturasPaquete(Request $request){
        if($request->ajax()){
            $datos = $request->all();
            // dd($datos);
            $checkboxes = collect($datos)->filter(function ($value, $key) {
                return Str::startsWith($key, 'check_');
            })->toArray();

            $codigo_evento_significativo    = $request->input('contingencia');
            $siat                           = app(SiatController::class);
            // $codigo_cafc_contingencia       = NULL;
            $codigo_cafc_contingencia       = "10122205E166E";
            $fechaActual                    = date('Y-m-d\TH:i:s.v');
            $fechaEmicion                   = $fechaActual;

            $contado = 0;

            $rutaCarpeta = "assets/docs/paquete";
            // Verificar si la carpeta existe
            if (!file_exists($rutaCarpeta))
                mkdir($rutaCarpeta, true);

            // Obtener lista de archivos en la carpeta
            $archivos = glob($rutaCarpeta . '/*');
            // Eliminar cada archivo
            foreach ($archivos as $archivo) {
                if (is_file($archivo))
                    unlink($archivo);
            }
            $file = public_path('assets/docs/paquete.tar.gz');
            if (file_exists($file))
                unlink($file);

            $file = public_path('assets/docs/paquete.tar');
            if (file_exists($file))
                unlink($file);

            foreach($checkboxes as $key => $chek){
                $ar = explode("_",$key);
                $factura = Factura::find($ar[1]);

                $xml                            = $factura->productos_xml;
                // $uso_cafc                       = $request->input("uso_cafc");
                $archivoXML                     = new SimpleXMLElement($xml);

                // GUARDAMOS EN LA CARPETA EL XML
                $archivoXML->asXML("assets/docs/paquete/facturaxmlContingencia$ar[1].xml");
                $contado++;
            }

            // Ruta de la carpeta que deseas comprimir
            $rutaCarpeta = "assets/docs/paquete";

            // Nombre y ruta del archivo TAR resultante
            $archivoTar = "assets/docs/paquete.tar";

            // Crear el archivo TAR utilizando la biblioteca PharData
            $tar = new PharData($archivoTar);
            $tar->buildFromDirectory($rutaCarpeta);

            // Ruta y nombre del archivo comprimido en formato Gzip
            $archivoGzip = "assets/docs/paquete.tar.gz";

            // Comprimir el archivo TAR en formato Gzip
            // $comandoGzip = "gzip -c $archivoTar > $archivoGzip";
            // exec($comandoGzip);

            // ESTE ES OTRO CHEEE
            // Abre el archivo .gz en modo de escritura
            $gz = gzopen($archivoGzip, 'wb');
            // Abre el archivo .tar en modo de lectura
            $archivo = fopen($archivoTar, 'rb');
            // Lee el contenido del archivo .tar y escribe en el archivo .gz
            while (!feof($archivo)) {
                gzwrite($gz, fread($archivo, 8192));
            }
            // Cierra los archivos
            fclose($archivo);
            gzclose($gz);

            // Leer el contenido del archivo comprimido
            $contenidoArchivo = file_get_contents($archivoGzip);

            // Calcular el HASH (SHA256) del contenido del archivo
            $hashArchivo = hash('sha256', $contenidoArchivo);


            try {
                // Código que puede lanzar el error
                // Por ejemplo, puedes tener algo como:
                // $resultado = obtenerResultado();
                $res = json_decode($siat->recepcionPaqueteFactura($contenidoArchivo, $fechaEmicion, $hashArchivo, $codigo_cafc_contingencia, $contado, $codigo_evento_significativo));
                if($res->resultado->RespuestaServicioFacturacion->transaccion){
                    $validad = json_decode($siat->validacionRecepcionPaqueteFactura(2,$res->resultado->RespuestaServicioFacturacion->codigoRecepcion));
                    if($validad->resultado->RespuestaServicioFacturacion->transaccion){
                        foreach($checkboxes as $key => $chek){
                            $data['estado'] = "success";
                            $ar = explode("_",$key);
                            $factura = Factura::find($ar[1]);
                            $factura->codigo_descripcion = $validad->resultado->RespuestaServicioFacturacion->codigoDescripcion;
                            $factura->codigo_recepcion  = $validad->resultado->RespuestaServicioFacturacion->codigoRecepcion;
                            $factura->save();
                        }
                    }else{
                        foreach($checkboxes as $key => $chek){
                            $ar = explode("_",$key);
                            $factura = Factura::find($ar[1]);
                            $factura->codigo_descripcion    = $validad->resultado->RespuestaServicioFacturacion->codigoDescripcion;
                            $factura->codigo_recepcion      = $validad->resultado->RespuestaServicioFacturacion->codigoRecepcion;
                            $factura->descripcion           = $validad->resultado->RespuestaServicioFacturacion->mensajesList;
                            $factura->save();
                        }
                        $data['estado'] = "error";
                    }
                    // dd($res, $validad, "habert");
                }else{
                    // dd($res);
                    $data['estado'] = "error";
                }
                // Intentar acceder a la propiedad RespuestaServicioFacturacion
                // $valor = $resultado->RespuestaServicioFacturacion;
            } catch (\Throwable $e) {
                // Capturar y manejar el error
                // Aquí puedes realizar acciones para tratar el error, como registrar un mensaje de error, mostrar un mensaje al usuario, etc.
                // Puedes acceder al mensaje de error específico usando $e->getMessage()
                // También puedes acceder al número de línea y el archivo donde ocurrió el error usando $e->getLine() y $e->getFile()
                echo "Error capturado: " . $e->getMessage();
                $data['estado'] = "error";
            }

            // $data['estado'] = "success";
        }else{
            $data['estado'] = "error";
        }

        return $data;

    }

    public function sacaNumeroCafcUltimo(Request $request){
        if($request->ajax()){

            $maxValue = Factura::where('uso_cafc', 'si')->max(DB::raw('CAST(numero_cafc AS UNSIGNED)'));
            if($maxValue === null)
                $numero = 1;
            else
                $numero = $maxValue + 1;

            $data['numero'] = $numero;
            $data['estado'] = 'success';

        }else{
            $data['estado'] = 'error';
        }

        return $data;
    }

    public function sacaNumeroFactura(Request $request){
        if($request->ajax()){
            $numero         = $this->numeroFactura() + 1;
            $data['numero'] = $numero;
            $data['estado'] = 'success';
        }else{
            $data['estado'] = 'error';
        }
        return $data;
    }

    // ********************  PRUEBAS FACUTRAS SINCRONIZACION   *****************************
    public function pruebas(){
        $siat = app(SiatController::class);

        for ($i = 1; $i <= 50 ; $i++) {

            $sincronizarActividades                         = json_decode($siat->sincronizarActividades());
            $sincronizarFechaHora                           = json_decode($siat->sincronizarFechaHora());
            $sincronizarListaActividadesDocumentoSector     = json_decode($siat->sincronizarListaActividadesDocumentoSector());
            $sincronizarListaLeyendasFactura                = json_decode($siat->sincronizarListaLeyendasFactura());
            $sincronizarListaMensajesServicios              = json_decode($siat->sincronizarListaMensajesServicios());
            $sincronizarListaProductosServicios             = json_decode($siat->sincronizarListaProductosServicios());
            $sincronizarParametricaEventosSignificativos    = json_decode($siat->sincronizarParametricaEventosSignificativos());
            $sincronizarParametricaMotivoAnulacion          = json_decode($siat->sincronizarParametricaMotivoAnulacion());
            $sincronizarParametricaPaisOrigen               = json_decode($siat->sincronizarParametricaPaisOrigen());
            $sincronizarParametricaTipoDocumentoIdentidad   = json_decode($siat->sincronizarParametricaTipoDocumentoIdentidad());
            $sincronizarParametricaTipoDocumentoSector      = json_decode($siat->sincronizarParametricaTipoDocumentoSector());
            $sincronizarParametricaTipoEmision              = json_decode($siat->sincronizarParametricaTipoEmision());
            $sincronizarParametricaTipoHabitacion           = json_decode($siat->sincronizarParametricaTipoHabitacion());
            $sincronizarParametricaTipoMetodoPago           = json_decode($siat->sincronizarParametricaTipoMetodoPago());
            $sincronizarParametricaTipoMoneda               = json_decode($siat->sincronizarParametricaTipoMoneda());
            $sincronizarParametricaTipoPuntoVenta           = json_decode($siat->sincronizarParametricaTipoPuntoVenta());
            $sincronizarParametricaTiposFactura             = json_decode($siat->sincronizarParametricaTiposFactura());
            $sincronizarParametricaUnidadMedida             = json_decode($siat->sincronizarParametricaUnidadMedida());

            var_dump($sincronizarActividades);
            echo "<br><br><br>";
            var_dump($sincronizarFechaHora);
            echo "<br><br><br>";
            var_dump($sincronizarListaActividadesDocumentoSector);
            echo "<br><br><br>";
            var_dump($sincronizarListaLeyendasFactura);
            echo "<br><br><br>";
            var_dump($sincronizarListaMensajesServicios);
            echo "<br><br><br>";
            var_dump($sincronizarListaProductosServicios);
            echo "<br><br><br>";
            var_dump($sincronizarParametricaEventosSignificativos);
            echo "<br><br><br>";
            var_dump($sincronizarParametricaMotivoAnulacion);
            echo "<br><br><br>";
            var_dump($sincronizarParametricaPaisOrigen);
            echo "<br><br><br>";
            var_dump($sincronizarParametricaTipoDocumentoIdentidad);
            echo "<br><br><br>";
            var_dump($sincronizarParametricaTipoDocumentoSector);
            echo "<br><br><br>";
            var_dump($sincronizarParametricaTipoEmision);
            echo "<br><br><br>";
            var_dump($sincronizarParametricaTipoHabitacion);
            echo "<br><br><br>";
            var_dump($sincronizarParametricaTipoMetodoPago);
            echo "<br><br><br>";
            var_dump($sincronizarParametricaTipoMoneda);
            echo "<br><br><br>";
            var_dump($sincronizarParametricaTipoPuntoVenta);
            echo "<br><br><br>";
            var_dump($sincronizarParametricaTiposFactura);
            echo "<br><br><br>";
            var_dump($sincronizarParametricaUnidadMedida);
            echo "****************** => <h1>".$i."</h1><= ******************";
            sleep(3);
        }
    }
    // ********************  PRUEBAS FACUTRAS SINCRONIZACION   *****************************



    // ===================  FUNCIOENES PROTEGIDAS  ========================
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

}
