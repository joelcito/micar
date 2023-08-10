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

            foreach ($datosVehiculo['pagos'] as $key => $pago_id) {
                $pago = Pago::find($pago_id);
                // dd($datosVehiculo['pagos'], $pago_id, $pago);
                $pago->estado       = "Pagado";
                $pago->factura_id   = $facturaNew->id;
                $pago->save();
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
                                // dd($facturas);
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


            $idsToUpdate = [];
            foreach($checkboxes as $key => $chek){
                $ar = explode("_",$key);
                $factura = Factura::find($ar[1]);

                $idsToUpdate[] = (int)$ar[1];

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
                        // foreach($checkboxes as $key => $chek){
                        //     $data['estado'] = "success";
                        //     $ar = explode("_",$key);
                        //     $factura = Factura::find($ar[1]);
                        //     $factura->codigo_descripcion = $validad->resultado->RespuestaServicioFacturacion->codigoDescripcion;
                        //     $factura->codigo_recepcion  = $validad->resultado->RespuestaServicioFacturacion->codigoRecepcion;
                        //     $factura->save();
                        // }

                        $data['estado'] = "success";

                        // Realizar la actualización utilizando Eloquent
                        Factura::whereIn('id', $idsToUpdate)->update([
                            'codigo_descripcion'    => $validad->resultado->RespuestaServicioFacturacion->codigoDescripcion,
                            'codigo_recepcion'      => $validad->resultado->RespuestaServicioFacturacion->codigoRecepcion
                        ]);
                    }else{
                        // foreach($checkboxes as $key => $chek){
                        //     $ar = explode("_",$key);
                        //     $factura = Factura::find($ar[1]);
                        //     $factura->codigo_descripcion    = $validad->resultado->RespuestaServicioFacturacion->codigoDescripcion;
                        //     $factura->codigo_recepcion      = $validad->resultado->RespuestaServicioFacturacion->codigoRecepcion;
                        //     $factura->descripcion           = $validad->resultado->RespuestaServicioFacturacion->mensajesList;
                        //     $factura->save();
                        // }
                        $data['estado'] = "error";

                        // Realizar la actualización utilizando Eloquent
                        Factura::whereIn('id', $idsToUpdate)->update([
                            'codigo_descripcion'    => $validad->resultado->RespuestaServicioFacturacion->codigoDescripcion,
                            'codigo_recepcion'      => $validad->resultado->RespuestaServicioFacturacion->codigoRecepcion,
                            'descripcion'           => $validad->resultado->RespuestaServicioFacturacion->mensajesList
                        ]);
                    }
                    // dd($res, $validad, "habert");
                }else{
                    // dd($res);
                    $data['estado'] = "error";
                }

                // dd($checkboxes, $idsToUpdate);
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

    public function verificaNit(Request $request){
        if($request->ajax()){
            $nit = $request->input('nit');
            $siat = app(SiatController::class);
            $dato = json_decode($siat->verificarNit($nit));
            if($dato->estado === "success" ){
                $data['verificacion']   = $dato->resultado->RespuestaVerificarNit->transaccion;
                $data['msg']            = $dato->resultado->RespuestaVerificarNit->mensajesList->descripcion;
            }else{
                $data['msg']            = "ERROR";
            }
            $data['estado'] = 'success';
        }else{
            $data['estado'] = 'error';
        }
        return $data;
    }

    public function imprimeFactura(Request $request, $factura_id){

        $factura = Factura::find($factura_id);
        $xml = $factura['productos_xml'];

        $archivoXML = new SimpleXMLElement($xml);

        $cabeza = (array) $archivoXML;

        $cuf            = (string)$cabeza['cabecera']->cuf;
        $numeroFactura  = (string)$cabeza['cabecera']->numeroFactura;

        // Genera el texto para el código QR
        $textoQR = 'https://pilotosiat.impuestos.gob.bo/consulta/QR?nit=5427648016&cuf='.$cuf.'&numero='.$numeroFactura.'&t=2';
        // dd($cuf,$numeroFactura, $textoQR);
        // Genera la ruta temporal para guardar la imagen del código QR
        $rutaImagenQR = storage_path('app/public/qr_code.png');
        $urlImagenQR = asset(str_replace(storage_path('app/public'), 'storage', $rutaImagenQR));
        // Genera el código QR y guarda la imagen en la ruta temporal
        QrCode::generate($textoQR, $rutaImagenQR);
        // QrCode::format('png')->generate($textoQR, $rutaImagenQR);

        return view('pago.imprimeFactura')->with(compact('factura', 'archivoXML', 'cabeza'));
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


    // ********************  CREACION MASIVA FACTURAACION   *****************************
    public function emiteFacturaMasa(Request $request){

        // $array = [
        //     'datos' => [
        //         'factura' => [
        //             [
        //                 'cabecera' => [
        //                     'nitEmisor'                     => '178436029',
        //                     'razonSocialEmisor'             => 'INSTITUTO TECNICO "EF-GIPET" S.R.L.',
        //                     'municipio'                     => 'La Paz',
        //                     'telefono'                      => '73717199',
        //                     'numeroFactura'                 => '9023',
        //                     'cuf'                           => '123456789',
        //                     'cufd'                          => 'BQXxDaztSQUE=NzDJCOUE0MUI0MzY=Q0FEcEZOQ0hYVUJcyQzRBNUQ1RUFBN',
        //                     'codigoSucursal'                => '0',
        //                     'direccion'                     => 'PASAJE BERNARDO TRIGO NRO.447 EDIFICIO SIN NOMBRE PISO 3 DPTO. OFICINA 1 ZONA CENTRAL',
        //                     'codigoPuntoVenta'              => '0',
        //                     'fechaEmision'                  => '2023-07-01T13:06:49.283',
        //                     'nombreRazonSocial'             => 'FLORES',
        //                     'codigoTipoDocumentoIdentidad'  => '1',
        //                     'numeroDocumento'               => '8401524016',
        //                     'complemento'                   => null,
        //                     'codigoCliente'                 => '8401524016',
        //                     'nombreEstudiante'              => 'FLORES QUISPE JOEL JONATHAN',
        //                     'periodoFacturado'              => '1 MENSUALIDAD / 2023',
        //                     'codigoMetodoPago'              => '1',
        //                     'numeroTarjeta'                 => null,
        //                     'montoTotal'                    => '350',
        //                     'montoTotalSujetoIva'           => '350',
        //                     'codigoMoneda'                  => '1',
        //                     'tipoCambio'                    => '1',
        //                     'montoTotalMoneda'              => '350',
        //                     'montoGiftCard'                 => null,
        //                     'descuentoAdicional'            => '0',
        //                     'codigoExcepcion'               => '0',
        //                     'cafc'                          => null,
        //                     // 'cafc'                          => '111DE8BD3981C',
        //                     'leyenda'                       => 'Ley N° 453: El proveedor deberá suministrar el servicio en las modalidades y términos ofertados o convenidos.',
        //                     'usuario'                       => 'admin@gipet.net',
        //                     'codigoDocumentoSector'         => '11',
        //                 ],
        //                 // 'cabecera' => [
        //                 //     'nitEmisor'                     => '178436029',
        //                 //     'razonSocialEmisor'             => 'INSTITUTO TECNICO "EF-GIPET" S.R.L.',
        //                 //     'municipio'                     => 'La Paz',
        //                 //     'telefono'                      => '73717199',
        //                 //     'numeroFactura'                 => '9023',
        //                 //     'cuf'                           => '123456789',
        //                 //     'cufd'                          => null,
        //                 //     'codigoSucursal'                => '0',
        //                 //     'direccion'                     => null,
        //                 //     'codigoPuntoVenta'              => '0',
        //                 //     'fechaEmision'                  => '2023-07-01T20:06:59.886',
        //                 //     'nombreRazonSocial'             => 'FLORES',
        //                 //     'codigoTipoDocumentoIdentidad'  => '1',
        //                 //     'numeroDocumento'               => '8401524016',
        //                 //     'complemento'                   => null,
        //                 //     'codigoCliente'                 => '8401524016',
        //                 //     'nombreEstudiante'              => 'FLORES QUISPE JOEL JONATHAN',
        //                 //     'periodoFacturado'              => '4 MENSUALIDAD / 2023',
        //                 //     'codigoMetodoPago'              => '1',
        //                 //     'numeroTarjeta'                 => null,
        //                 //     'montoTotal'                    => '350',
        //                 //     'montoTotalSujetoIva'           => '350',
        //                 //     'codigoMoneda'                  => '1',
        //                 //     'tipoCambio'                    => '1',
        //                 //     'montoTotalMoneda'              => '350',
        //                 //     'montoGiftCard'                 => null,
        //                 //     'descuentoAdicional'            => '0',
        //                 //     'codigoExcepcion'               => '0',
        //                 //     'cafc'                          => null,
        //                 //     'leyenda'                       => 'Ley N° 453: El proveedor deberá suministrar el servicio en las modalidades y términos ofertados o convenidos.',
        //                 //     'usuario'                       => 'admin@gipet.net',
        //                 //     'codigoDocumentoSector'         => '11',
        //                 // ],
        //             ],
        //             [
        //                 'detalle' => [
        //                     'actividadEconomica'    => '853000',
        //                     'codigoProductoSin'     => '92510',
        //                     'codigoProducto'        => '2',
        //                     'descripcion'           => '0 MENSUALIDAD',
        //                     'cantidad'              => '1',
        //                     'unidadMedida'          => '58',
        //                     'precioUnitario'        => '350',
        //                     'montoDescuento'        => null,
        //                     'subTotal'              => '350',
        //                 ],
        //             ],
        //         ],
        //     ],
        //     'datosPersona' => [
        //         'persona_id'    => '3806',
        //         'carnet'        => '8401524',
        //     ],
        //     'datosRecepcion' => [
        //         'uso_cafc'                  => 'no',
        //         'codigo_cafc_contingencia'  => null,
        //     ],
        //     'modalidad' => 'offline',
        // ];


        // Crear las variables con los valores correspondientes
        $nitEmisor = "5427648016";
        $razonSocialEmisor = "MICAELA QUIROZ ESCOBAR";
        $municipio = "Santa Cruz";
        $telefono = "73130500";
        $numeroFactura = "3";
        $cuf = "123456789";
        $cufd = "BQTlCdy9xRkE=NzjQxOTQyMEZCMDY=QmVReUZKRElYVUJcyQ0IxQjVBNzQ5Q";
        $codigoSucursal = "0";
        $direccion = "CALLE RIO ESPEJILLOS NRO.S/N ZONA VILLA FATIMA UV:0051 MZA:0049";
        $codigoPuntoVenta = "0";
        $fechaEmision = "2023-08-02T09:33:52.766";
        $nombreRazonSocial = "AUTO";
        $codigoTipoDocumentoIdentidad = "1";
        $numeroDocumento = "6242";
        $complemento = null;
        $codigoCliente = "6242";
        $codigoMetodoPago = "1";
        $numeroTarjeta = null;
        $montoTotal = "200";
        $montoTotalSujetoIva = "200";
        $codigoMoneda = "1";
        $tipoCambio = "1";
        $montoTotalMoneda = "200";
        $montoGiftCard = null;
        $descuentoAdicional = "0";
        $codigoExcepcion = "0";
        $cafc = null;
        $leyenda = "Ley N° 453: El proveedor deberá suministrar el servicio en las modalidades y términos ofertados o convenidos.";
        $usuario = "JOEL JONATHAN FLORES QUIPE";
        $codigoDocumentoSector = "1";

        $actividadEconomica = "452000";
        $codigoProductoSin = "87141";
        $codigoProducto = "17";
        $descripcionItem = "LAVADO EXTERIOR DE VAGONETA/CAMIONETA XL";
        $cantidad = "5.00";
        $unidadMedida = "58";
        $precioUnitario = "40.00";
        $montoDescuento = "0.00";
        $subTotal = "200";
        $numeroSerie = null;
        $numeroImei = null;

        $vehiculoId = "10081";
        $pagos = ["4"];

        $usoCafc = "Si";
        $codigoCafcContingencia = "10122205E166E";

        $modalidad = "offline";

        // Crear el array final con la estructura proporcionada
        $array = [
            "datos" => [
                "factura" => [
                    0 => [
                        "cabecera" => [
                            "nitEmisor" => $nitEmisor,
                            "razonSocialEmisor" => $razonSocialEmisor,
                            "municipio" => $municipio,
                            "telefono" => $telefono,
                            "numeroFactura" => $numeroFactura,
                            "cuf" => $cuf,
                            "cufd" => $cufd,
                            "codigoSucursal" => $codigoSucursal,
                            "direccion" => $direccion,
                            "codigoPuntoVenta" => $codigoPuntoVenta,
                            "fechaEmision" => $fechaEmision,
                            "nombreRazonSocial" => $nombreRazonSocial,
                            "codigoTipoDocumentoIdentidad" => $codigoTipoDocumentoIdentidad,
                            "numeroDocumento" => $numeroDocumento,
                            "complemento" => $complemento,
                            "codigoCliente" => $codigoCliente,
                            "codigoMetodoPago" => $codigoMetodoPago,
                            "numeroTarjeta" => $numeroTarjeta,
                            "montoTotal" => $montoTotal,
                            "montoTotalSujetoIva" => $montoTotalSujetoIva,
                            "codigoMoneda" => $codigoMoneda,
                            "tipoCambio" => $tipoCambio,
                            "montoTotalMoneda" => $montoTotalMoneda,
                            "montoGiftCard" => $montoGiftCard,
                            "descuentoAdicional" => $descuentoAdicional,
                            "codigoExcepcion" => $codigoExcepcion,
                            "cafc" => $cafc,
                            "leyenda" => $leyenda,
                            "usuario" => $usuario,
                            "codigoDocumentoSector" => $codigoDocumentoSector,
                        ]
                    ],
                    1 => [
                        "detalle" => [
                            "actividadEconomica"        => $actividadEconomica,
                            "codigoProductoSin"         => $codigoProductoSin,
                            "codigoProducto"            => $codigoProducto,
                            "descripcion"               => $descripcionItem,
                            "cantidad"                  => $cantidad,
                            "unidadMedida"              => $unidadMedida,
                            "precioUnitario"            => $precioUnitario,
                            "montoDescuento"            => $montoDescuento,
                            "subTotal"                  => $subTotal,
                            "numeroSerie"               => $numeroSerie,
                            "numeroImei"                => $numeroImei,
                        ]
                    ]
                ]
            ],
            "datosVehiculo" => [
                "vehiculo_id"   => $vehiculoId,
                "pagos"         => $pagos,
            ],
            "datosRecepcion" => [
                "uso_cafc"                  => $usoCafc,
                "codigo_cafc_contingencia"  => $codigoCafcContingencia,
            ],
            "modalidad" => $modalidad,
        ];

        // Imprimir el array final
        // print_r($arrayFinal);


        for ($k=1; $k <= 500 ; $k++) {

            echo $k."<br>";

            // PARA LA HORA
            $microtime = microtime(true);
            $seconds = floor($microtime);
            $milliseconds = round(($microtime - $seconds) * 1000);
            $formattedDateTime = date("Y-m-d\TH:i:s.") . str_pad($milliseconds, 3, '0', STR_PAD_LEFT);
            $array['datos']['factura'][0]['cabecera']['fechaEmision'] = $formattedDateTime;

            // PARA EL NUMERO
            // $array['datos']['factura'][0]['cabecera']['numeroFactura'] = $this->numeroFactura()+1;
            $array['datos']['factura'][0]['cabecera']['numeroFactura'] = 1;

            // PARA LA MENSUALIDAD
            // $array['datos']['factura'][1]['detalle']['descripcion']         = "$k MENSUALIDAD";
            // $array['datos']['factura'][0]['cabecera']['periodoFacturado']   = "$k MENSUALIDAD / 2023";

            // ******** DE AQUI YA VIENE PARA LA GENERACION DE LA FACTUR ********
            $datos              = $array['datos'];
            $datosVehiculo      = $array['datosVehiculo'];
            $valoresCabecera    = $datos['factura'][0]['cabecera'];
            $puntoVenta         = Auth::user()->codigo_punto_venta;
            $tipo_factura       = $array['modalidad'];

            $nitEmisor          = str_pad($valoresCabecera['nitEmisor'],13,"0",STR_PAD_LEFT);
            $fechaEmision       = str_replace(".","",str_replace(":","",str_replace("-","",str_replace("T", "",$valoresCabecera['fechaEmision']))));
            $sucursal           = str_pad(0,4,"0",STR_PAD_LEFT);
            $modalidad          = 1;
            $numeroFactura      = str_pad($valoresCabecera['numeroFactura'],10,"0",STR_PAD_LEFT);

            if($tipo_factura === "online"){
                $tipoEmision        = 1;
            }
            else{
                $datosRecepcion       = $array['datosRecepcion'];
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
            $cliente->nit              = $datos['factura'][0]['cabecera']['numeroDocumento'];
            $cliente->razon_social     = $datos['factura'][0]['cabecera']['nombreRazonSocial'];
            $cliente->save();

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

            $temporal = $datos['factura'];

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

            $facturaNew                     = Factura::find($factura->id);
            $facturaNew->codigo_descripcion = $codigo_descripcion;
            $facturaNew->codigo_recepcion   = $codigo_recepcion;
            $facturaNew->codigo_trancaccion = $codigo_trancaccion;
            $facturaNew->descripcion        = $descripcion;
            $facturaNew->cuis               = session('scuis');
            $facturaNew->cufd               = $scufd;
            $facturaNew->fechaVigencia      = Carbon::parse($sfechaVigenciaCufd)->format('Y-m-d H:i:s');
            $facturaNew->save();
            // foreach ($datosVehiculo['pagos'] as $key => $pago_id) {
            //     $pago = Pago::find($pago_id);
            //     // dd($datosVehiculo['pagos'], $pago_id, $pago);
            //     $pago->estado       = "Pagado";
            //     $pago->factura_id   = $facturaNew->id;
            //     $pago->save();
            // }
            // ******** DE AQUI YA VIENE PARA LA GENERACION DE LA FACTUR ********








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

            // dd($array);

            // return $data;



            echo $formattedDateTime."<br>";
            sleep(2);
        }
    }
    // ********************  CREACION MASIVA FACTURAACION   *****************************


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


    // ============================= PARA LA GENERACION DEL RECIBO ==================================================
    public function emitirRecibo(Request $request){
        if($request->ajax()){
            // dd($request->all());
            $vehiculo_d             = $request->input('vehiculo');
            $monto                  = $request->input('monto');
            $descuento_adicional    = $request->input('descuento_adicional');

            $vehiculo               = Vehiculo::find($vehiculo_d);

            $servicios  = Pago::select('pagos.*','servicios.codigoActividad', 'servicios.codigoProducto', 'servicios.unidadMedida', 'servicios.descripcion')
                                ->join('servicios', 'pagos.servicio_id','=', 'servicios.id')
                                ->where('pagos.estado',"paraPagar")
                                ->where('pagos.vehiculo_id',$vehiculo_d)
                                ->get();

            $factura                        = new Factura();
            $factura->creador_id            = Auth::user()->id;
            $factura->vehiculo_id           = $vehiculo->id;
            $factura->cliente_id            = $vehiculo->cliente->id;
            $factura->fecha                 = date('Y-m-d H:m:s');
            $factura->total                 = $monto;
            $factura->facturado             = "No";
            $factura->numero_recibo         = $this->numeroRecibo();
            $factura->descuento_adicional   = $descuento_adicional;
            $factura->save();

            $pagos =    $servicios->pluck('id');

            Pago::whereIn('id',$pagos)->update([
                'estado'        => 'Pagado',
                'factura_id'    => $factura->id,
            ]);

            $data['estado'] = 'success';
            $data['factura'] = $factura->id;
        }else{
            $data['estado'] = 'error';
        }
        return $data;
    }

    protected function numeroRecibo(){
        $dato = Factura::where('facturado', 'No')
                    ->select(DB::raw('MAX(CAST(numero_recibo AS UNSIGNED)) as max_numero_recibo'))
                    ->first();

        if($dato){
            $numero = $dato->max_numero_recibo + 1;
        }else{
            $numero = 1;
        }

        return $numero;
    }

    public function imprimeRecibo(Request $request, $factura_id){
        $factura    = Factura::find($factura_id);
        $pagos      = Pago::where('factura_id', $factura_id)->get();
        return view('pago.imprimeRecibo')->with(compact('factura', 'pagos'));
    }
    // ============================= PARA LA GENERACION DEL RECIBO END ==================================================

}
