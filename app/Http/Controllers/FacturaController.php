<?php

namespace App\Http\Controllers;

use App\Firma\Firmadores\FirmadorBoliviaSingle;
use App\Mail\CorreoAnulacion;
use App\Mail\EnviaCorreo;
use App\Models\Cliente;
use App\Models\Factura;
use App\Models\Pago;
use App\Models\Vehiculo;
use App\Models\Detalle;
use App\Models\Movimiento;
use App\Models\TipoDocumento;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use SimpleXMLElement;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use PDF;
use Illuminate\Support\Str;
use PharData;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use PhpParser\Node\Expr\Cast\Double;

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

            // $servicios = Pago::select('pagos.*','servicios.codigoActividad', 'servicios.codigoProducto', 'servicios.unidadMedida', 'servicios.descripcion')
            //                     ->join('servicios', 'pagos.servicio_id','=', 'servicios.id')
            //                     ->where('pagos.estado',"paraPagar")
            //                     ->where('pagos.vehiculo_id',$vehiculo_d)
            //                     ->get();

            // $pagos =    $servicios->pluck('id');

            $servicios = Detalle::select('detalles.*','servicios.codigoActividad', 'servicios.codigoProducto', 'servicios.unidadMedida', 'servicios.descripcion')
                                ->join('servicios', 'detalles.servicio_id','=', 'servicios.id')
                                ->where('detalles.estado',"paraPagar")
                                ->where('detalles.vehiculo_id',$vehiculo_d)
                                ->get();

            $detalles =    $servicios->pluck('id');

            $data['lista']      = json_encode($servicios);
            $data['estado']     = 'success';
            $data['detalles']   = $detalles;

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
            // $pago               = Pago::find($request->input('pago_id'));
            $detalle               = Detalle::find($request->input('pago_id'));
            $detalle->descuento    = $request->input('valor');
            $detalle->importe      = ($detalle->precio * $detalle->cantidad)-$request->input('valor');
            $detalle->save();

            $vehiculo_id        = $detalle->vehiculo_id;

            // $sumaImporte = Pago::where('estado','paraPagar')
            //                     ->where('vehiculo_id',$vehiculo_id)
            //                     ->sum('total');
            $sumaImporte = Detalle::where('estado','paraPagar')
                                ->where('vehiculo_id',$vehiculo_id)
                                ->sum('total');


            // $sumaRebaja = Pago::where('estado','paraPagar')
            //                 ->where('vehiculo_id',$vehiculo_id)
            //                 ->sum('descuento');

            $sumaRebaja = Detalle::where('estado','paraPagar')
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

            $newNumeroFacturaDesdeBack = $this->numeroFactura() + 1;
            $datos['factura'][0]['cabecera']['numeroFactura'] = $newNumeroFacturaDesdeBack;

            // ********************************* ESTO ES PARA GENERAR LA FACTURA *********************************
            $datos           = $request->input('datos');
            $datosVehiculo   = $request->input('datosVehiculo');
            $valoresCabecera = $datos['factura'][0]['cabecera'];
            $puntoVenta      = Auth::user()->codigo_punto_venta;
            $tipo_factura    = $request->input('modalidad');
            $swFacturaEnvio  = true;

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
                // $data['estado'] = "error_email";
                // $data['msg']    = "La persona no tiene correo";
                // return $data;
                $swFacturaEnvio = false;
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

            $cufPro                                                 = $this->generarBase16($cadenaConM11).$scodigoControl;

            // dd($scufd);

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

            $firmador = new FirmadorBoliviaSingle('assets/certificate/softoken.p12', "Micar5427648");
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

            // // GUARDAMOS EN LA FACTURA
            // $factura                            = new Factura();
            // $factura->creador_id                = Auth::user()->id;
            // $factura->vehiculo_id               = $datosVehiculo['vehiculo_id'];
            // $factura->cliente_id                = $vehiculo->cliente_id;
            // $factura->razon_social              = $datos['factura'][0]['cabecera']['nombreRazonSocial'];
            // $factura->carnet                    = $vehiculo->cliente->cedula;
            // $factura->nit                       = $datos['factura'][0]['cabecera']['numeroDocumento'];;
            // $factura->fecha                     = $datos['factura'][0]['cabecera']['fechaEmision'];
            // $factura->total                     = $datos['factura'][0]['cabecera']['montoTotal'];
            // $factura->facturado                 = "Si";
            // $factura->tipo_pago                 = $request->input('tipo_pago');
            // $factura->monto_pagado              = $request->input('monto_pagado');
            // $factura->cambio_devuelto           = $request->input('cambio');
            // $factura->estado_pago               = (((int)$factura->monto_pagado - (int)$factura->cambio_devuelto) == $factura->total)? "Pagado" : "Deuda";
            // $factura->cuf                       = $datos['factura'][0]['cabecera']['cuf'];
            // $factura->codigo_metodo_pago_siat   = $datos['factura'][0]['cabecera']['codigoMetodoPago'];
            // $factura->monto_total_subjeto_iva   = $datos['factura'][0]['cabecera']['montoTotalSujetoIva'];
            // $factura->descuento_adicional       = $datos['factura'][0]['cabecera']['descuentoAdicional'];
            // $factura->productos_xml             = file_get_contents('assets/docs/facturaxml.xml');
            // if($tipo_factura === "online"){
            //     $factura->numero                    = $datos['factura'][0]['cabecera']['numeroFactura'];
            // }else{
            //     if($datosRecepcion['uso_cafc'] === "Si"){
            //         $factura->numero_cafc           = $datos['factura'][0]['cabecera']['numeroFactura'];
            //         $factura->uso_cafc              = "si";
            //     }else{
            //         $factura->numero                    = $datos['factura'][0]['cabecera']['numeroFactura'];
            //     }
            // }
            // $factura->tipo_factura              = $tipo_factura;
            // $factura->save();

            if($tipo_factura === "online"){

                $siat = app(SiatController::class);
                $for  = json_decode($siat->recepcionFactura($archivoZip, $valoresCabecera['fechaEmision'],$hashArchivo));

                // NUEVO CODIGO PARA EVITAR ERROES DE GENERACION DE FACTURAS Y EVITAR QUE SE CREE MAS FACTURAS ASI NOMAS
                if($for->estado === "success"){
                    $codigo_descripcion = $for->resultado->RespuestaServicioFacturacion->codigoDescripcion;
                    if($for->resultado->RespuestaServicioFacturacion->transaccion){

                        // ESTO ES PARA LA FACTURA LA CREACION
                        $facturaVerdad                          = new Factura();
                        $facturaVerdad->creador_id              = Auth::user()->id;
                        $facturaVerdad->vehiculo_id             = $datosVehiculo['vehiculo_id'];
                        $facturaVerdad->cliente_id              = $vehiculo->cliente_id;
                        $facturaVerdad->razon_social            = $datos['factura'][0]['cabecera']['nombreRazonSocial'];
                        $facturaVerdad->carnet                  = $vehiculo->cliente->cedula;
                        $facturaVerdad->nit                     = $datos['factura'][0]['cabecera']['numeroDocumento'];;
                        $facturaVerdad->fecha                   = $datos['factura'][0]['cabecera']['fechaEmision'];
                        $facturaVerdad->total                   = $datos['factura'][0]['cabecera']['montoTotal'];
                        $facturaVerdad->facturado               = "Si";
                        $facturaVerdad->tipo_pago               = $request->input('tipo_pago');
                        $facturaVerdad->monto_pagado            = $request->input('monto_pagado');
                        $facturaVerdad->cambio_devuelto         = $request->input('cambio');
                        // $facturaVerdad->estado_pago             = (((int)$facturaVerdad->monto_pagado - (int)$facturaVerdad->cambio_devuelto) == $facturaVerdad->total)? "Pagado" : "Deuda";
                        $facturaVerdad->estado_pago             = (((double)$facturaVerdad->monto_pagado - (double)$facturaVerdad->cambio_devuelto) == $facturaVerdad->total)? "Pagado" : "Deuda";
                        $facturaVerdad->cuf                     = $datos['factura'][0]['cabecera']['cuf'];
                        $facturaVerdad->codigo_metodo_pago_siat = $datos['factura'][0]['cabecera']['codigoMetodoPago'];
                        $facturaVerdad->monto_total_subjeto_iva = $datos['factura'][0]['cabecera']['montoTotalSujetoIva'];
                        $facturaVerdad->descuento_adicional     = $datos['factura'][0]['cabecera']['descuentoAdicional'];
                        $facturaVerdad->productos_xml           = file_get_contents('assets/docs/facturaxml.xml');
                        $facturaVerdad->numero                  = $datos['factura'][0]['cabecera']['numeroFactura'];
                        $facturaVerdad->codigo_descripcion      = $codigo_descripcion;
                        $facturaVerdad->codigo_recepcion        = $for->resultado->RespuestaServicioFacturacion->codigoRecepcion;
                        $facturaVerdad->codigo_trancaccion      = $for->resultado->RespuestaServicioFacturacion->transaccion;
                        $facturaVerdad->descripcion             = NULL;
                        $facturaVerdad->cuis                    = session('scuis');
                        $facturaVerdad->cufd                    = $scufd;
                        $facturaVerdad->fechaVigencia           = Carbon::parse($sfechaVigenciaCufd)->format('Y-m-d H:i:s');
                        $facturaVerdad->tipo_factura            = $tipo_factura;
                        $facturaVerdad->save();

                        // AHORA AREMOS PARA LOS PAGOS
                        Detalle::whereIn('id', $datosVehiculo['pagos'])
                                ->update([
                                    'estado'     => 'Finalizado',
                                    'factura_id' => $facturaVerdad->id
                                ]);

                        if($datosVehiculo['realizo_pago'] === "true"){
                            $pago                = new Pago();
                            $pago->creador_id    = Auth::user()->id;
                            $pago->factura_id    = $facturaVerdad->id;
                            $pago->caja_id       = $datosVehiculo['caja'];
                            // $pago->monto         = (int)$request->input('monto_pagado')-(int)$request->input('cambio');
                            $pago->monto         = (double)$request->input('monto_pagado')-(double)$request->input('cambio');
                            $pago->descripcion   = "VENTA";
                            $pago->apertura_caja = "No";
                            $pago->fecha         = date('Y-m-d H:i:s');
                            $pago->tipo_pago     = $request->input('tipo_pago');
                            $pago->estado        = ($pago->tipo_pago === 'efectivo' )? 'Ingreso' : 'Salida';
                            $pago->save();
                        }else{

                        }

                        $data['estado'] = $codigo_descripcion;

                        // ***************** ENVIAMOS EL CORREO DE LA FACTURA *****************
                        if($swFacturaEnvio){
                            $nombre = $cliente->nombres." ".$cliente->ap_paterno." ".$cliente->ap_materno;
                            $this->enviaCorreo(
                                $cliente->correo,
                                $nombre,
                                $facturaVerdad->numero,
                                $facturaVerdad->fecha,
                                $facturaVerdad->id
                            );
                        }

                    }else{
                        $data['estado'] = "RECHAZADA";
                        // dd($for);
                        // $data['msg'] = $for->resultado->RespuestaServicioFacturacion->mensajesList->descripcion;
                        $data['msg'] = json_encode($for->resultado->RespuestaServicioFacturacion->mensajesList);
                    }

                    // dd($for);

                }else{
                    $data['estado'] = "RECHAZADA";
                    $data['msg'] = $for->msg;
                }
                // dd($for);
                // if($for->estado === "error"){
                //     $codigo_descripcion = null;
                //     $codigo_trancaccion = null;
                //     $descripcion        = null;
                //     $codigo_recepcion   = null;
                // }else{
                //     if($for->resultado->RespuestaServicioFacturacion->transaccion){
                //         $codigo_recepcion = $for->resultado->RespuestaServicioFacturacion->codigoRecepcion;
                //         $descripcion      = NULL;
                //     }else{
                //         $codigo_recepcion = NULL;
                //         $descripcion      = $for->resultado->RespuestaServicioFacturacion->mensajesList->descripcion;
                //     }
                //     $codigo_descripcion = $for->resultado->RespuestaServicioFacturacion->codigoDescripcion;
                //     $codigo_trancaccion = $for->resultado->RespuestaServicioFacturacion->transaccion;
                // }
                // $data['estado'] = $codigo_descripcion;
            }else{


                // ESTO ES PARA LA FACTURA LA CREACION
                $facturaVerdad                          = new Factura();
                $facturaVerdad->creador_id              = Auth::user()->id;
                $facturaVerdad->vehiculo_id             = $datosVehiculo['vehiculo_id'];
                $facturaVerdad->cliente_id              = $vehiculo->cliente_id;
                $facturaVerdad->razon_social            = $datos['factura'][0]['cabecera']['nombreRazonSocial'];
                $facturaVerdad->carnet                  = $vehiculo->cliente->cedula;
                $facturaVerdad->nit                     = $datos['factura'][0]['cabecera']['numeroDocumento'];;
                $facturaVerdad->fecha                   = $datos['factura'][0]['cabecera']['fechaEmision'];
                $facturaVerdad->total                   = $datos['factura'][0]['cabecera']['montoTotal'];
                $facturaVerdad->facturado               = "Si";
                $facturaVerdad->tipo_pago               = $request->input('tipo_pago');
                $facturaVerdad->monto_pagado            = $request->input('monto_pagado');
                $facturaVerdad->cambio_devuelto         = $request->input('cambio');
                $facturaVerdad->estado_pago             = (((double)$facturaVerdad->monto_pagado - (double)$facturaVerdad->cambio_devuelto) == $facturaVerdad->total)? "Pagado" : "Deuda";
                // $facturaVerdad->estado_pago             = (((int)$facturaVerdad->monto_pagado - (int)$facturaVerdad->cambio_devuelto) == $facturaVerdad->total)? "Pagado" : "Deuda";
                $facturaVerdad->cuf                     = $datos['factura'][0]['cabecera']['cuf'];
                $facturaVerdad->codigo_metodo_pago_siat = $datos['factura'][0]['cabecera']['codigoMetodoPago'];
                $facturaVerdad->monto_total_subjeto_iva = $datos['factura'][0]['cabecera']['montoTotalSujetoIva'];
                $facturaVerdad->descuento_adicional     = $datos['factura'][0]['cabecera']['descuentoAdicional'];
                $facturaVerdad->productos_xml           = file_get_contents('assets/docs/facturaxml.xml');
                // $facturaVerdad->numero                  = $datos['factura'][0]['cabecera']['numeroFactura'];
                $facturaVerdad->codigo_descripcion      = NULL;
                $facturaVerdad->codigo_recepcion        = NULL;
                $facturaVerdad->codigo_trancaccion      = NULL;
                $facturaVerdad->descripcion             = NULL;

                if($datosRecepcion['uso_cafc'] === "Si"){
                    $facturaVerdad->numero_cafc = $datos['factura'][0]['cabecera']['numeroFactura'];
                    $facturaVerdad->uso_cafc    = "si";
                }else{
                    $facturaVerdad->numero = $datos['factura'][0]['cabecera']['numeroFactura'];
                }

                $facturaVerdad->cuis                    = session('scuis');
                $facturaVerdad->cufd                    = $scufd;
                $facturaVerdad->fechaVigencia           = Carbon::parse($sfechaVigenciaCufd)->format('Y-m-d H:i:s');
                $facturaVerdad->tipo_factura            = $tipo_factura;
                $facturaVerdad->save();

                // AHORA AREMOS PARA LOS PAGOS
                Detalle::whereIn('id', $datosVehiculo['pagos'])
                        ->update(['estado' => 'Finalizado']);

                if($datosVehiculo['realizo_pago'] === "true"){
                    $pago                = new Pago();
                    $pago->creador_id    = Auth::user()->id;
                    $pago->factura_id    = $facturaVerdad->id;
                    $pago->caja_id       = $datosVehiculo['caja'];
                    // $pago->monto         = (int)$request->input('monto_pagado')-(int)$request->input('cambio');
                    $pago->monto         = (double)$request->input('monto_pagado')-(double)$request->input('cambio');
                    $pago->descripcion   = "VENTA";
                    $pago->apertura_caja = "No";
                    $pago->fecha         = date('Y-m-d H:i:s');
                    $pago->tipo_pago     = $request->input('tipo_pago');
                    $pago->estado        = ($pago->tipo_pago === 'efectivo' )? 'Ingreso' : 'Salida';
                    $pago->save();
                }else{

                }

                if($swFacturaEnvio){
                    // ***************** ENVIAMOS EL CORREO DE LA FACTURA *****************
                    $nombre = $cliente->nombres." ".$cliente->ap_paterno." ".$cliente->ap_materno;
                    $this->enviaCorreo(
                        $cliente->correo,
                        $nombre,
                        $facturaVerdad->numero,
                        $facturaVerdad->fecha,
                        $facturaVerdad->id
                    );
                }

                $data['estado']     = 'OFFLINE';
            }

            // $facturaNew                     = Factura::find($factura->id);
            // $facturaNew->codigo_descripcion = $codigo_descripcion;
            // $facturaNew->codigo_recepcion   = $codigo_recepcion;
            // $facturaNew->codigo_trancaccion = $codigo_trancaccion;
            // $facturaNew->descripcion        = $descripcion;
            // $facturaNew->cuis               = session('scuis');
            // $facturaNew->cufd               = $scufd;
            // $facturaNew->fechaVigencia      = Carbon::parse($sfechaVigenciaCufd)->format('Y-m-d H:i:s');
            // $facturaNew->save();
            // ********************************* ESTO ES PARA GENERAR LA FACTURA *********************************

            // Detalle::whereIn('id', $datosVehiculo['pagos'])
            //         ->update(['estado' => 'Finalizado']);

            // if($datosVehiculo['realizo_pago'] === "true"){
            //     $pago                = new Pago();
            //     $pago->creador_id    = Auth::user()->id;
            //     $pago->factura_id    = $facturaNew->id;
            //     $pago->caja_id       = $datosVehiculo['caja'];
            //     $pago->monto         = (int)$request->input('monto_pagado')-(int)$request->input('cambio');
            //     $pago->descripcion   = "VENTA";
            //     $pago->apertura_caja = "No";
            //     $pago->fecha         = date('Y-m-d H:i:s');
            //     $pago->tipo_pago     = $request->input('tipo_pago');
            //     $pago->estado        = ($pago->tipo_pago === 'efectivo' )? 'Ingreso' : 'Salida';
            //     $pago->save();
            // }else{

            // }

            //ENVIAMOS EL CORREO DE LA FACTURA
            // $nombre = $cliente->nombres." ".$cliente->ap_paterno." ".$cliente->ap_materno;
            // $this->enviaCorreo(
            //     $cliente->correo,
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

        $numero = Factura::where('facturado', 'Si')
                            ->max(DB::raw('CAST(numero AS UNSIGNED)'));
        if($numero == 0)
            $numero  = 746; //camnio de facura

        return $numero;
    }

    public function anularFacturaNew(Request $request){
        if($request->ajax()){
            $idFactura  = $request->input('factura');
            $moivo      = $request->input('motivo');
            $fatura     = Factura::find($idFactura);
            $siat       = app(SiatController::class);
            $respuesta = json_decode($siat->anulacionFactura($moivo, $fatura->cuf));

            if($respuesta->resultado->RespuestaServicioFacturacion->transaccion){
                $fatura->estado = 'Anulado';

                // PARA ELIMINAR LOS PAGOS
                Pago::where('factura_id', $fatura->id)->delete();

                // PARA ELIMINAR LOS DETALLES
                Detalle::where('factura_id', $fatura->id)->delete();

                $cliente = Cliente::find($fatura->cliente_id);

                $correo = $cliente->correo;
                $nombre = $cliente->nombres." ".$cliente->ap_paterno." ".$cliente->ap_materno;
                $numero = $fatura->numero;
                $fecha  = $fatura->fecha;

                //protected function enviaCorreoAnulacion($correo, $nombre, $numero, $fecha){

                $this->enviaCorreoAnulacion($correo, $nombre, $numero, $fecha );

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
        // $textoQR = 'https://pilotosiat.impuestos.gob.bo/consulta/QR?nit=5427648016&cuf='.$cuf.'&numero='.$numeroFactura.'&t=2';
        $textoQR = $this->urlQr($cuf, $numeroFactura);
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
                mkdir($rutaCarpeta, 0755, true);

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
        // ******************************** DESARROLLO ********************************
        // $textoQR = 'https://pilotosiat.impuestos.gob.bo/consulta/QR?nit=5427648016&cuf='.$cuf.'&numero='.$numeroFactura.'&t=2';

        // ******************************** PRODUCCION ********************************
        // $textoQR = 'https://siat.impuestos.gob.bo/consulta/QR?nit=5427648016&cuf='.$cuf.'&numero='.$numeroFactura.'&t=2';

        $textoQR = $this->urlQr($cuf, $numeroFactura);


        // Genera la ruta temporal para guardar la imagen del código QR
        $rutaImagenQR = storage_path('app/public/qr_code.png');
        $urlImagenQR = asset(str_replace(storage_path('app/public'), 'storage', $rutaImagenQR));
        // Genera el código QR y guarda la imagen en la ruta temporal
        QrCode::generate($textoQR, $rutaImagenQR);
        // QrCode::format('png')->generate($textoQR, $rutaImagenQR);

        return view('pago.imprimeFactura')->with(compact('factura', 'archivoXML', 'cabeza'));
    }

    public function verificaItemsGeneracion(Request $request){

        $vehiculo_id = $request->input('vehiculo');

        $pagos = Detalle::where('vehiculo_id',$vehiculo_id)
                        ->where('estado','Parapagar')
                        ->count();

        $data['estado']   = 'success';
        $data['cantidad'] = $pagos;

        return $data;

        // dd($request->all(), $pagos);

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

            $firmador = new FirmadorBoliviaSingle('assets/certificate/softoken.p12', "Micar5427648");
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
                    $hijo = $xml_temporal->addChild("$key",htmlspecialchars("$value"));
                    $hijo->addAttribute('xsi:nil','true', $ns_xsi);
                }else{
                    $xml_temporal->addChild("$key", htmlspecialchars("$value"));
                }
            }
        }
    }

    protected function enviaCorreo($correo, $nombre, $numero, $fecha, $factura_id){

        // ********************************  ESTE SI FUNCIONA AHROA *******************

        $to         = $correo;
        $subject    = 'FACTURA ELECTRONICA EN LINEA MICAR';

        // Cargar el contenido de la vista del correo
        $templatePath = resource_path('views/mail/correoFactura.blade.php');
        $templateContent = file_get_contents($templatePath);
        $fecha = date('d/m/Y H:m:s');
        $data = [
            'title'     => 'Bienvenido a mi aplicación',
            'content'   => 'Gracias por unirte a nosotros. Esperamos que disfrutes de tu tiempo aquí.',
            'name'      => $nombre,
            'number'    => $numero,
            'date'  => $fecha
        ];
        foreach ($data as $key => $value)
            $templateContent = str_replace('{{ $' . $key . ' }}', $value, $templateContent);


        $mail = new PHPMailer(true);

        // Configuración de los parámetros SMTP
        $smtpHost       = 'mail.micarautolavado.com';
        $smtpPort       =  465;
        // $smtpUsername   = 'suscripcion@comercio-latino.com';
        // $smtpPassword   = 'Fc;D&0@A7(T%';
        $smtpUsername   = 'admin@micarautolavado.com';
        $smtpPassword   = '-Z{DjF[D@y8G';

        // $smtpUsername   = 'sistemas@comercio-latino.com';
        // $smtpPassword   = 'j@xKuZ(65VNK';

        try {
            // $mail->isSMTP();
            // $mail->Host         = $smtpHost;
            // $mail->Port         = $smtpPort;
            // $mail->SMTPAuth     = true;
            // $mail->Username     = $smtpUsername;
            // $mail->Password     = $smtpPassword;
            // $mail->SMTPSecure   = PHPMailer::ENCRYPTION_STARTTLS; no va este
            // $mail->SMTPSecure   = PHPMailer::ENCRYPTION_SMTPS;
            // ... Configura los parámetros SMTP ...
            $mail->setFrom('admin@micarautolavado.com', 'MI CAR AUTO LAVADO');
            $mail->addAddress($to);

            // Agregar direcciones de correo electrónico en copia (CC)
            // $mail->addCC('admin@comercio-latino.com', 'Administracion Comercio Latino');
            // $mail->addCC('soporte@comercio-latino.com', 'Soporte Comercio Latino');

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $templateContent;

            $factura       = Factura::find($factura_id);
            $xml           = $factura['productos_xml'];
            $archivoXML    = new SimpleXMLElement($xml);
            $cabeza        = (array) $archivoXML;
            $cuf           = (string)$cabeza['cabecera']->cuf;
            $numeroFactura = (string)$cabeza['cabecera']->numeroFactura;
              // Genera el texto para el código QR
            // $textoQR = 'https://pilotosiat.impuestos.gob.bo/consulta/QR?nit=5427648016&cuf='.$cuf.'&numero='.$numeroFactura.'&t=2';
            $textoQR = $this->urlQr($cuf, $numeroFactura);
              // Genera la ruta temporal para guardar la imagen del código QR
            $rutaImagenQR = storage_path('app/public/qr_code.png');
              // Genera el código QR y guarda la imagen en la ruta temporal
            QrCode::generate($textoQR, $rutaImagenQR);
            $pdf = PDF::loadView('pdf.generaPdfFacturaNew', compact('factura', 'archivoXML','rutaImagenQR'))->setPaper('letter');

            // Genera la ruta donde se guardará el archivo PDF
            $rutaPDF = storage_path('app/public/factura.pdf');
            // Guarda el PDF en la ruta especificada
            $pdf->save($rutaPDF);
            // $pdfPath = "assets/docs/facturapdf.pdf";
            $xmlPath = "assets/docs/facturaxml.xml";

            $mail->addAttachment($rutaPDF, 'Factura.pdf'); // Adjuntar archivo PDF
            $mail->addAttachment($xmlPath, 'Factura.xml'); // Adjuntar archivo XML


            $mail->send();

            // return 'Correo enviado correctamente';
            $data['estado'] = 'success';
            $data['msg']    = 'Correo enviado correctamente';

        } catch (Exception $e) {
            $data['estado'] = 'error';
            $data['msg'] = 'No se pudo enviar el correo: ' . $mail->ErrorInfo;
            // return 'No se pudo enviar el correo: ' . $mail->ErrorInfo;
        }



        // *****************ESTO ES EL OTRO METODO QUE NO SIRVE CHEEE *****************
        // $factura = Factura::find($factura_id);

        // $xml = $factura['productos_xml'];

        // $archivoXML = new SimpleXMLElement($xml);

        // $cabeza = (array) $archivoXML;

        // $cuf            = (string)$cabeza['cabecera']->cuf;
        // $numeroFactura  = (string)$cabeza['cabecera']->numeroFactura;

        // // Genera el texto para el código QR
        // $textoQR = 'https://pilotosiat.impuestos.gob.bo/consulta/QR?nit=5427648016&cuf='.$cuf.'&numero='.$numeroFactura.'&t=2';
        // // Genera la ruta temporal para guardar la imagen del código QR
        // $rutaImagenQR = storage_path('app/public/qr_code.png');
        // // Genera el código QR y guarda la imagen en la ruta temporal
        // QrCode::generate($textoQR, $rutaImagenQR);
        // $pdf = PDF::loadView('pdf.generaPdfFacturaNew', compact('factura', 'archivoXML','rutaImagenQR'))->setPaper('letter');

        // // Genera la ruta donde se guardará el archivo PDF
        // $rutaPDF = storage_path('app/public/factura.pdf');
        // // Guarda el PDF en la ruta especificada
        // $pdf->save($rutaPDF);

        // // $pdfPath = "assets/docs/facturapdf.pdf";
        // $xmlPath = "assets/docs/facturaxml.xml";

        // $mail = new EnviaCorreo($nombre, $numero, $fecha);
        // $mail->attach($rutaPDF, ['as' => 'Factura.pdf'])
        //     ->attach($xmlPath, ['as' => 'Factura.xml']);

        // $response = Mail::to($correo)->send($mail);

        // // Elimina el archivo PDF guardado en la ruta temporal
        // Storage::delete($rutaPDF);
    }

    protected function enviaCorreoAnulacion($correo, $nombre, $numero, $fecha){

        $to         = $correo;
        $subject    = 'ANULACION DE FACTURA ELECTRONICA EN LINEA MICAR';

            // Cargar el contenido de la vista del correo
            $templatePath = resource_path('views/mail/correoAnulacionFactura.blade.php');
            $templateContent = file_get_contents($templatePath);
            $fecha = date('d/m/Y H:m:s');
            $data = [
                'title'     => 'Bienvenido a mi aplicación',
                'content'   => 'Gracias por unirte a nosotros. Esperamos que disfrutes de tu tiempo aquí.',
                'name'      => $nombre,
                'number'    => $numero,
                'date'  => $fecha
            ];

            foreach ($data as $key => $value)
                $templateContent = str_replace('{{ $' . $key . ' }}', $value, $templateContent);

            // Configuración de los parámetros SMTP
            $smtpHost       = 'mail.micarautolavado.com';
            $smtpPort       =  465;
            // $smtpUsername   = 'suscripcion@comercio-latino.com';
            // $smtpPassword   = 'Fc;D&0@A7(T%';
            $smtpUsername   = 'admin@micarautolavado.com';
            $smtpPassword   = '-Z{DjF[D@y8G';

            // $smtpUsername   = 'sistemas@comercio-latino.com';
            // $smtpPassword   = 'j@xKuZ(65VNK';

            $mail = new PHPMailer(true);

            try {
                // $mail->isSMTP();
                // $mail->Host         = $smtpHost;
                // $mail->Port         = $smtpPort;
                // $mail->SMTPAuth     = true;
                // $mail->Username     = $smtpUsername;
                // $mail->Password     = $smtpPassword;
                // $mail->SMTPSecure   = PHPMailer::ENCRYPTION_STARTTLS; no va este
                // $mail->SMTPSecure   = PHPMailer::ENCRYPTION_SMTPS;
                // ... Configura los parámetros SMTP ...
                $mail->setFrom('admin@micarautolavado.com', 'MI CAR AUTO LAVADO');
                $mail->addAddress($to);

                // Agregar direcciones de correo electrónico en copia (CC)
                // $mail->addCC('admin@comercio-latino.com', 'Administracion Comercio Latino');
                // $mail->addCC('soporte@comercio-latino.com', 'Soporte Comercio Latino');

                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body    = $templateContent;

                //$factura       = Factura::find($factura_id);
                //$xml           = $factura['productos_xml'];
                //$archivoXML    = new SimpleXMLElement($xml);
                //$cabeza        = (array) $archivoXML;
                //$cuf           = (string)$cabeza['cabecera']->cuf;
                //$numeroFactura = (string)$cabeza['cabecera']->numeroFactura;
                  // Genera el texto para el código QR
                //$textoQR = 'https://pilotosiat.impuestos.gob.bo/consulta/QR?nit=5427648016&cuf='.$cuf.'&numero='.$numeroFactura.'&t=2';
                  // Genera la ruta temporal para guardar la imagen del código QR
                //$rutaImagenQR = storage_path('app/public/qr_code.png');
                  // Genera el código QR y guarda la imagen en la ruta temporal
                // QrCode::generate($textoQR, $rutaImagenQR);
                // $pdf = PDF::loadView('pdf.generaPdfFacturaNew', compact('factura', 'archivoXML','rutaImagenQR'))->setPaper('letter');

                // // Genera la ruta donde se guardará el archivo PDF
                // $rutaPDF = storage_path('app/public/factura.pdf');
                // // Guarda el PDF en la ruta especificada
                // $pdf->save($rutaPDF);
                // $pdfPath = "assets/docs/facturapdf.pdf";
                // $xmlPath = "assets/docs/facturaxml.xml";

                // $mail->addAttachment($rutaPDF, 'Factura.pdf'); // Adjuntar archivo PDF
                // $mail->addAttachment($xmlPath, 'Factura.xml'); // Adjuntar archivo XML


                $mail->send();

                // return 'Correo enviado correctamente';
                $data['estado'] = 'success';
                $data['msg']    = 'Correo enviado correctamente';

            } catch (Exception $e) {
                $data['estado'] = 'error';
                $data['msg'] = 'No se pudo enviar el correo: ' . $mail->ErrorInfo;
                // return 'No se pudo enviar el correo: ' . $mail->ErrorInfo;
            }

        // $mail = new CorreoAnulacion($nombre, $numero, $fecha);
        // $response = Mail::to($correo)->send($mail);
    }

    // ============================= PARA LA GENERACION DEL RECIBO ==================================================
    public function emitirRecibo(Request $request){
        if($request->ajax()){
            // dd($request->all());
            $vehiculo_d             = $request->input('vehiculo');
            $monto                  = $request->input('monto');
            $descuento_adicional    = $request->input('descuento_adicional');

            $vehiculo               = Vehiculo::find($vehiculo_d);

            // $servicios  = Pago::select('pagos.*','servicios.codigoActividad', 'servicios.codigoProducto', 'servicios.unidadMedida', 'servicios.descripcion')
            //                     ->join('servicios', 'pagos.servicio_id','=', 'servicios.id')
            //                     ->where('pagos.estado',"paraPagar")
            //                     ->where('pagos.vehiculo_id',$vehiculo_d)
            //                     ->get();

            $servicios  = Detalle::select('detalles.*','servicios.codigoActividad', 'servicios.codigoProducto', 'servicios.unidadMedida', 'servicios.descripcion')
                                ->join('servicios', 'detalles.servicio_id','=', 'servicios.id')
                                ->where('detalles.estado',"paraPagar")
                                ->where('detalles.vehiculo_id',$vehiculo_d)
                                ->get();
                                // ->toSql();
                                // dd($servicios, $vehiculo_d, $request->all());

            $factura                      = new Factura();
            $factura->creador_id          = Auth::user()->id;
            $factura->vehiculo_id         = $vehiculo->id;
            $factura->cliente_id          = $vehiculo->cliente->id;
            $factura->fecha               = date('Y-m-d H:m:s');
            $factura->total               = $monto;
            $factura->facturado           = "No";
            $factura->numero_recibo       = $this->numeroRecibo();
            $factura->tipo_pago           = $request->input('tipo_pago');
            $factura->monto_pagado        = $request->input('monto_pagado');
            $factura->cambio_devuelto     = $request->input('cambio');
            // $factura->estado_pago         = (((int)$factura->monto_pagado - (int)$factura->cambio_devuelto) == $factura->total)? "Pagado" : "Deuda";
            $factura->estado_pago         = (((double)$factura->monto_pagado - (double)$factura->cambio_devuelto) == $factura->total)? "Pagado" : "Deuda";
            $factura->descuento_adicional = $descuento_adicional;
            $factura->save();

            $pagos =    $servicios->pluck('id');

            Detalle::whereIn('id',$pagos)->update([
                'estado'        => 'Finalizado',
                'factura_id'    => $factura->id,
            ]);

            $sipago = $request->input('realizo_pago');

            if($sipago === "true"){
                $pago                = new Pago();
                $pago->creador_id    = Auth::user()->id;
                $pago->factura_id    = $factura->id;
                $pago->caja_id       = $request->input('caja');
                // $pago->monto         = (int)$request->input('monto_pagado')-(int)$request->input('cambio');
                $pago->monto         = (double)$request->input('monto_pagado')-(double)$request->input('cambio');
                $pago->fecha         = date('Y-m-d H:i:s');
                $pago->descripcion   = "VENTA";
                $pago->apertura_caja = "No";
                $pago->tipo_pago     = $request->input('tipo_pago');
                $pago->estado        = ($pago->tipo_pago === 'efectivo' )? 'Ingreso' : 'Salida';
                $pago->save();
            }else{

            }

            // Pago::whereIn('id',$pagos)->update([
            //     'estado'        => 'Pagado',
            //     'factura_id'    => $factura->id,
            // ]);

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
        // $pagos      = Pago::where('factura_id', $factura_id)->get();
        $pagos      = Detalle::where('factura_id', $factura_id)->get();
        return view('pago.imprimeRecibo')->with(compact('factura', 'pagos'));
    }

    public function anularRecibo(Request $request){
        if($request->ajax()){

            $factura_id      = $request->input('factura');
            $factura         = Factura::find($factura_id);
            $factura->estado = "Anulado";
            $factura->save();

            Pago::where('factura_id',$factura_id)->delete();

            $detalles_id = Detalle::where('factura_id', $factura_id)->get()->pluck('id');

            Detalle::where('factura_id', $factura_id)->delete();

            // ANULAMOS LOS MOVIMIENTOS
            Movimiento::whereIn('detalle_id', $detalles_id)->delete();

            // $ids = Pago::where('factura_id',$factura_id)->get()->pluck('id');
            // Pago::destroy($ids);

            $data['estado'] = 'success';
        }else{
            $data['estado'] = 'error';
        }
        return $data;
    }

    public function imprimeTicked(Request $request, $vehiculo_id){

        // dd($request->all(), $vehiculo_id);

        // $pagos = Pago::where('vehiculo_id',$vehiculo_id)
        //                 ->where('estado','Parapagar')
        //                 ->get();

        $pagos = Detalle::where('vehiculo_id',$vehiculo_id)
                        ->where('estado','Parapagar')
                        ->get();

        // dd($pagos);

        return view('pago.imprimeTicked')->with(compact('pagos'));
    }
    // ============================= PARA LA GENERACION DEL RECIBO END ==================================================


    // ============================= PARA LA GENERACION DE LA TRAMSFERECNAI DE LA FACTURA ==================================================
    public function recuperaFactura(Request $request){
        if($request->ajax()){

            $factura_id = $request->input('factura');

            $factura = Factura::find($factura_id);
            $detalles = Detalle::where('factura_id', $factura_id)->get();
            $tipoDocumento = TipoDocumento::all();

            $data['estado'] = 'success';
            $data['modal'] = view('pago.recuperaFactura')->with(compact('factura', 'detalles', 'tipoDocumento'))->render();
        }else{
            $data['estado'] = 'error';
        }

        return $data;
    }

    public function enviarTrasferenciaFactura(Request $request){
        if($request->ajax()){

            // VERIFICAMOS SI SE PUDE ANBULAR LA FACTURA
            $factura_id = $request->input('tramsfrencia_factura_id');
            $factura    = Factura::find($factura_id);

            // dd($factura, $factura->cuf);

            $moivo      = 1;
            $siat       = app(SiatController::class);
            $respuesta  = json_decode($siat->anulacionFactura($moivo, $factura->cuf));

            if($respuesta->resultado->RespuestaServicioFacturacion->transaccion){
                $factura->estado = 'Anulado';
                $factura->save();

                // EMPEZAMOS CON LA NUEVA FACUTRACION Y TRASLADOS DE LA MISMA
                $datelles   = Detalle::where('factura_id', $factura_id)->get();
                $xml        = $factura->productos_xml;
                $archivoXML = new SimpleXMLElement($xml);

                // Crear las variables con los valores correspondientes
                $nitEmisor                    = strval($archivoXML->cabecera->nitEmisor);
                $razonSocialEmisor            = strval($archivoXML->cabecera->razonSocialEmisor);
                $municipio                    = strval($archivoXML->cabecera->municipio);
                $telefono                     = strval($archivoXML->cabecera->telefono);
                $numeroFactura                = $this->numeroFactura() + 1;
                $cuf                          = "123456789";
                $cufd                         = "123456789";
                $codigoSucursal               = "0";
                $direccion                    = strval($archivoXML->cabecera->direccion);
                $codigoPuntoVenta             = Auth::user()->codigo_punto_venta;
                // $fechaEmision2                = now()->format('Y-m-d\TH:i:s.v');
                $fechaEmision                 = $request->input('fecha');
                $nombreRazonSocial            = $request->input('tramsfrencia_new_razon_social');
                $codigoTipoDocumentoIdentidad = $request->input('tramsfrencia_new_tipo_documento');
                $numeroDocumento              = $request->input('tramsfrencia_new_nit');
                $complemento                  = null;  //AVERIGUAR
                $codigoCliente                = $numeroDocumento;
                $codigoMetodoPago             = "1";
                $numeroTarjeta                = null;
                $montoTotal                   = $request->input('tramsfrencia_importe');
                $montoTotalSujetoIva          = $request->input('tramsfrencia_importe');
                $codigoMoneda                 = "1";
                $tipoCambio                   = "1";
                $montoTotalMoneda             = $request->input('tramsfrencia_importe');
                $montoGiftCard                = null;
                $descuentoAdicional           = strval($archivoXML->cabecera->descuento_adicional);
                $codigoExcepcion              = ($request->filled('tramsfrencia_new_execpion'))? "1" : "0";
                $cafc                         = null;
                $leyenda                      = "Ley N° 453: El proveedor deberá suministrar el servicio en las modalidades y términos ofertados o convenidos.";
                $usuario                      = Auth::user()->name;
                $codigoDocumentoSector        = "1";


                $json          = json_encode($archivoXML);
                $arrayDetalle  = json_decode($json, true);
                $detallesVenta = $arrayDetalle['detalle'];

                $vehiculoId                   = $factura->vehiculo_id;
                $pagos                        = $datelles;
                $usoCafc                      = "No";
                // $codigoCafcContingencia       = "10122205E166E";
                $codigoCafcContingencia       = null;
                $modalidad                    = "online";

                // Crear el array final con la estructura proporcionada
                $array = [
                    "datos" => [
                        "factura" => [
                            0 => [
                                "cabecera" => [
                                    "nitEmisor"                    => $nitEmisor,
                                    "razonSocialEmisor"            => $razonSocialEmisor,
                                    "municipio"                    => $municipio,
                                    "telefono"                     => $telefono,
                                    "numeroFactura"                => $numeroFactura,
                                    "cuf"                          => $cuf,
                                    "cufd"                         => $cufd,
                                    "codigoSucursal"               => $codigoSucursal,
                                    "direccion"                    => $direccion,
                                    "codigoPuntoVenta"             => $codigoPuntoVenta,
                                    "fechaEmision"                 => $fechaEmision,
                                    "nombreRazonSocial"            => $nombreRazonSocial,
                                    "codigoTipoDocumentoIdentidad" => $codigoTipoDocumentoIdentidad,
                                    "numeroDocumento"              => $numeroDocumento,
                                    "complemento"                  => $complemento,
                                    "codigoCliente"                => $codigoCliente,
                                    "codigoMetodoPago"             => $codigoMetodoPago,
                                    "numeroTarjeta"                => $numeroTarjeta,
                                    "montoTotal"                   => $montoTotal,
                                    "montoTotalSujetoIva"          => $montoTotalSujetoIva,
                                    "codigoMoneda"                 => $codigoMoneda,
                                    "tipoCambio"                   => $tipoCambio,
                                    "montoTotalMoneda"             => $montoTotalMoneda,
                                    "montoGiftCard"                => $montoGiftCard,
                                    "descuentoAdicional"           => $descuentoAdicional,
                                    "codigoExcepcion"              => $codigoExcepcion,
                                    "cafc"                         => $cafc,
                                    "leyenda"                      => $leyenda,
                                    "usuario"                      => $usuario,
                                    "codigoDocumentoSector"        => $codigoDocumentoSector,
                                ]
                            ],
                        ]
                    ],
                    "datosVehiculo" => [
                        "vehiculo_id"   => $vehiculoId,
                        "pagos"         => $pagos->pluck('id'),
                    ],
                    "datosRecepcion" => [
                        "uso_cafc"                  => $usoCafc,
                        "codigo_cafc_contingencia"  => $codigoCafcContingencia,
                    ],
                    "modalidad" => $modalidad,
                ];

                // Nuevo array reestructurado
                $nuevoArray = [];
                foreach ($detallesVenta as $index => $elemento) {
                    if(is_array($elemento)){
                        $elemento['numeroSerie'] = null;
                        $elemento['numeroImei'] = null;
                        $nuevoArray[(int)$index + 1] = [
                            "detalle" => $elemento
                        ];
                    }else{
                        $detallesVenta['numeroSerie'] = null;
                        $detallesVenta['numeroImei'] = null;
                        $nuevoArray[(int)$index + 1] = [
                            "detalle" => $detallesVenta
                        ];
                        break;
                    }
                }

                // Obtener el índice actual del último elemento de factura
                $indice = count($array['datos']['factura']);

                // Agregar los elementos del segundo array a 'detalles' en el primer array
                foreach ($nuevoArray as $elemento)
                    $array['datos']['factura'][$indice++] = $elemento;

                // ********************************* ESTO ES PARA GENERAR LA FACTURA *********************************
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
                $tipoEmision        = 1;

                $tipoFactura       = 1;
                $tipoFacturaSector = str_pad(1,2,"0",STR_PAD_LEFT);;
                $puntoVenta        = str_pad($puntoVenta,4,"0",STR_PAD_LEFT);
                $cadena            = $nitEmisor.$fechaEmision.$sucursal.$modalidad.$tipoEmision.$tipoFactura.$tipoFacturaSector.$numeroFactura.$puntoVenta;

                $vehiculo = Vehiculo::find($datosVehiculo['vehiculo_id']);
                $cliente = Cliente::find($vehiculo->cliente->id);
                if(!($cliente && $cliente->correo != null && $cliente->correo != '')){
                    $data['estado'] = "error_email";
                    $data['msg']    = "La persona no tiene correo";
                    return $data;
                }
                $cliente->nit              = $array['datos']['factura'][0]['cabecera']['numeroDocumento'];
                $cliente->razon_social     = $array['datos']['factura'][0]['cabecera']['nombreRazonSocial'];
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
                }

                $cufPro = $this->generarBase16($cadenaConM11).$scodigoControl;

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

                $firmador = new FirmadorBoliviaSingle('assets/certificate/softoken.p12', "Micar5427648");
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

                if($tipo_factura === "online"){
                    $siat = app(SiatController::class);
                    $for  = json_decode($siat->recepcionFactura($archivoZip, $valoresCabecera['fechaEmision'],$hashArchivo));
                    // NUEVO CODIGO PARA EVITAR ERROES DE GENERACION DE FACTURAS Y EVITAR QUE SE CREE MAS FACTURAS ASI NOMAS
                    if($for->estado === "success"){
                        $codigo_descripcion = $for->resultado->RespuestaServicioFacturacion->codigoDescripcion;
                        if($for->resultado->RespuestaServicioFacturacion->transaccion){

                            // ESTO ES PARA LA FACTURA LA CREACION
                            $facturaVerdad                          = new Factura();
                            $facturaVerdad->creador_id              = Auth::user()->id;
                            $facturaVerdad->vehiculo_id             = $datosVehiculo['vehiculo_id'];
                            $facturaVerdad->cliente_id              = $vehiculo->cliente_id;
                            $facturaVerdad->razon_social            = $datos['factura'][0]['cabecera']['nombreRazonSocial'];
                            $facturaVerdad->carnet                  = $vehiculo->cliente->cedula;
                            $facturaVerdad->nit                     = $datos['factura'][0]['cabecera']['numeroDocumento'];;
                            // $facturaVerdad->fecha                   = $datos['factura'][0]['cabecera']['fechaEmision'];
                            $facturaVerdad->fecha                   = $factura->fecha;
                            $facturaVerdad->total                   = $datos['factura'][0]['cabecera']['montoTotal'];
                            $facturaVerdad->facturado               = "Si";
                            $facturaVerdad->tipo_pago               = $factura->tipo_pago;
                            $facturaVerdad->monto_pagado            = $factura->monto_pagado;
                            $facturaVerdad->cambio_devuelto         = $factura->cambio_devuelto;
                            // $facturaVerdad->estado_pago             = (((int)$facturaVerdad->monto_pagado - (int)$facturaVerdad->cambio_devuelto) == $facturaVerdad->total)? "Pagado" : "Deuda";
                            $facturaVerdad->estado_pago             = (((double)$facturaVerdad->monto_pagado - (double)$facturaVerdad->cambio_devuelto) == $facturaVerdad->total)? "Pagado" : "Deuda";
                            $facturaVerdad->cuf                     = $datos['factura'][0]['cabecera']['cuf'];
                            $facturaVerdad->codigo_metodo_pago_siat = $datos['factura'][0]['cabecera']['codigoMetodoPago'];
                            $facturaVerdad->monto_total_subjeto_iva = $datos['factura'][0]['cabecera']['montoTotalSujetoIva'];
                            $facturaVerdad->descuento_adicional     = $factura->descuento_adicional;
                            $facturaVerdad->productos_xml           = file_get_contents('assets/docs/facturaxml.xml');
                            $facturaVerdad->numero                  = $datos['factura'][0]['cabecera']['numeroFactura'];
                            $facturaVerdad->codigo_descripcion      = $codigo_descripcion;
                            $facturaVerdad->codigo_recepcion        = $for->resultado->RespuestaServicioFacturacion->codigoRecepcion;
                            $facturaVerdad->codigo_trancaccion      = $for->resultado->RespuestaServicioFacturacion->transaccion;
                            $facturaVerdad->descripcion             = NULL;
                            $facturaVerdad->cuis                    = session('scuis');
                            $facturaVerdad->cufd                    = $scufd;
                            $facturaVerdad->fechaVigencia           = Carbon::parse($sfechaVigenciaCufd)->format('Y-m-d H:i:s');
                            $facturaVerdad->tipo_factura            = $tipo_factura;
                            $facturaVerdad->save();

                            // AHORA AREMOS PARA LOS PAGOS
                            Detalle::whereIn('factura_id', [$factura->id])
                                    ->update([
                                        'factura_id' => $facturaVerdad->id
                                    ]);

                            Pago::whereIn('factura_id', [$factura->id])
                                    ->update([
                                        'factura_id' => $facturaVerdad->id
                                    ]);

                            $data['estado'] = $codigo_descripcion;


                            // // ***************** ENVIAMOS EL CORREO DE LA FACTURA *****************
                            // $nombre = $cliente->nombres." ".$cliente->ap_paterno." ".$cliente->ap_materno;
                            // $this->enviaCorreo(
                            //     $cliente->correo,
                            //     $nombre,
                            //     $facturaVerdad->numero,
                            //     $facturaVerdad->fecha,
                            //     $facturaVerdad->id
                            // );

                        }else{
                            $data['estado'] = "RECHAZADA";
                            $data['msg'] = $for->resultado->RespuestaServicioFacturacion->mensajesList->descripcion;
                        }
                    }else{
                        $data['estado'] = "RECHAZADA";
                        $data['msg'] = $for->msg;
                    }
                }

                $data['estado'] = 'success';

            }else{

                $data['estado'] = 'error';
                $data['descripcion'] = $respuesta->resultado->RespuestaServicioFacturacion->mensajesList->descripcion;
                $data['detalle'] = $respuesta->resultado->RespuestaServicioFacturacion;

                return $data ;
            }

            // dd(
            //     // $factura,
            //     // $datelles,
            //     // $array
            //     // $detallesVenta,
            //     // $nuevoArray
            //     $data
            // );

        }else{
            $data['estado'] = 'error';
        }
        return $data;
    }
    // ============================= END PARA LA GENERACION DE LA TRAMSFERECNAI DE LA FACTURA ==================================================

    protected function urlQr($cuf, $numeroFactura){
        // ******************************** DESARROLLO ********************************
        // return $textoQR = 'https://pilotosiat.impuestos.gob.bo/consulta/QR?nit=5427648016&cuf='.$cuf.'&numero='.$numeroFactura.'&t=2';

        // ******************************** PRODUCCION ********************************
        return $textoQR = 'https://siat.impuestos.gob.bo/consulta/QR?nit=5427648016&cuf='.$cuf.'&numero='.$numeroFactura.'&t=2';
    }

}
