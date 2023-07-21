<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SoapFault;

class SiatController extends Controller
{
    public function __construct(){
        $this->codigoPuntoVenta = Auth::user()->codigo_punto_venta;
        // $this->codigoPuntoVenta = 0;
        if(!session()->has('scuis')){
            $codigoCuis = json_decode($this->cuis());
            if($codigoCuis->estado === "success"){
                // dd($codigoCuis);
                session(['scuis'                => $codigoCuis->resultado->RespuestaCuis->codigo]);
                session(['sfechaVigenciaCuis'   => $codigoCuis->resultado->RespuestaCuis->fechaVigencia]);
                $data['$codigoCuis->estado === "success"'] = 'si';
            }else{
                // dd("no");
                $data['$codigoCuis->estado === "success"'] = 'no';
            }
            $data['!session()->has("scuis")'] = 'si';
        }else{
            // dd("no");
            $data['!session()->has("scuis")'] = 'no';
        }
    }

    protected $header                   = "apikey: TokenApi eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJzdWIiOiI1NDI3NjQ4U2N6IiwiY29kaWdvU2lzdGVtYSI6Ijc3MkNCMUI1QTc0OUI0MTk0MjBGQjA2Iiwibml0IjoiSDRzSUFBQUFBQUFBQURNMU1USTNNN0V3TURRREFBc2lNQ29LQUFBQSIsImlkIjoxMDE3OTY5LCJleHAiOjE3MDY2NTkyMDAsImlhdCI6MTY4NzkwMjY2NCwibml0RGVsZWdhZG8iOjU0Mjc2NDgwMTYsInN1YnNpc3RlbWEiOiJTRkUifQ.-KqCf5VBYsN-aLJj6nO5ik-Ctj1Orx5F3ws2q9j1WtEzE7MGp57nKeimKq4G3lUv8muc6c0_t9dUuGTdP0QSQA";
    protected $timeout                  = 5;                            // TIEMPO EN ESPERA PARA QUE RESPONDA SITA
    protected $codigoAmbiente           = 2;                            // si estamos desarrollo o pruebas  1 Produccion --- 2 Desarrollo
    protected $codigoModalidad          = 1;                            // que modalidad de facturacion es  1 Electronica --- 2 Computarizada
    protected $codigoPuntoVenta;                                        // NUMOER DE QUE PUNTO DE VENTA ES
    protected $codigoSistema            = "772CB1B5A749B419420FB06";    // CODIGO DE SISTEMA QUE TE DA SIAT
    protected $codigoSucursal           = 0;                            // CODIGO DE TU SUCURSAL
    protected $nit                      = "5427648016";                 // NIT DE LA EMPRESA
    protected $codigoDocumentoSector    = 1;                            // COMPRA Y VENTA

    public function verificarComunicacion(){
        $wsdl = "https://pilotosiatservicios.impuestos.gob.bo/v2/FacturacionCodigos?wsdl";
        $aoptions = array(
            'http' => array(
                // 'header' => "apikey: TokenApi eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJzdWIiOiI4NDM5ODU2THB6IiwiY29kaWdvU2lzdGVtYSI6Ijc3MkM0QTVENUVBQTQyQjlBNDFCNDM2Iiwibml0IjoiSDRzSUFBQUFBQUFBQURNMHR6QXhOak13c2dRQUYyano4UWtBQUFBPSIsImlkIjo2NTAyNjYsImV4cCI6MTY5MTk3MTIwMCwiaWF0IjoxNjg0MDkxNTc0LCJuaXREZWxlZ2FkbyI6MTc4NDM2MDI5LCJzdWJzaXN0ZW1hIjoiU0ZFIn0.09bh1_ENu-jzmY5kh31AEfmmLY79ucj_XN_nHnmaBDayNYq6_QcwaiwDc87PMHNRj2y5bDvAFBC0g3HXqTgZaA",
                'header' => $this->header,
                'timeout' => $this->timeout
            ),
        );

        $context = stream_context_create($aoptions);

        $data = array();

        try {
            $client = new \SoapClient($wsdl,[
                'stream_context' => $context,
                'cache_wsdl' => WSDL_CACHE_NONE,
                'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | SOAP_COMPRESSION_DEFLATE
            ]);
            $resultado = $client->verificarComunicacion();
            $data['estado'] = 'success';
            $data['resultado'] = $resultado;
        } catch (SoapFault $fault) {
            $resultado = false;
            $data['estado'] = 'error';
            $data['resultado'] = $resultado;
        }

        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function cuis(){

        $wsdl               = "https://pilotosiatservicios.impuestos.gob.bo/v2/FacturacionCodigos?wsdl";
        $codigoAmbiente     = $this->codigoAmbiente;
        $codigoModalidad    = $this->codigoModalidad;
        // $codigoPuntoVenta   = 0; //
        $codigoPuntoVenta   = $this->codigoPuntoVenta; //
        $codigoSistema      = $this->codigoSistema;
        $codigoSucursal     = $this->codigoSucursal;
        $nit                = $this->nit;

        // dd(
        //     $codigoAmbiente,
        //     $codigoModalidad,
        //     $codigoPuntoVenta,
        //     $codigoSistema,
        //     $codigoSucursal,
        //     $nit
        // );

        $parametros         =  array(
            'SolicitudCuis' => array(
                'codigoAmbiente'    => $codigoAmbiente,
                'codigoModalidad'   => $codigoModalidad,
                'codigoPuntoVenta'  => $codigoPuntoVenta,
                'codigoSistema'     => $codigoSistema,
                'codigoSucursal'    => $codigoSucursal,
                'nit'               => $nit
            )
        );

        $aoptions = array(
            'http' => array(
                'header' => $this->header,
                'timeout' => $this->timeout
            ),
        );

        $context = stream_context_create($aoptions);

        try {
            $client = new \SoapClient($wsdl,[
                'stream_context' => $context,
                'cache_wsdl' => WSDL_CACHE_NONE,
                'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | SOAP_COMPRESSION_DEFLATE
            ]);

            $resultado = $client->cuis($parametros);

            $data['estado'] = 'success';
            $data['resultado'] = $resultado;
        } catch (SoapFault $fault) {
            $resultado = false;
            $data['estado'] = 'error';
            $data['resultado'] = $resultado;
        }
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function cufd(){
        // $this->verificarConeccion();
        // dd(session()->all());
        $this->verificarComunicacion();

        $wsdl               = "https://pilotosiatservicios.impuestos.gob.bo/v2/FacturacionCodigos?wsdl";
        $codigoAmbiente     = $this->codigoAmbiente;
        $codigoModalidad    = $this->codigoModalidad;
        $codigoPuntoVenta   = $this->codigoPuntoVenta;
        $codigoSistema      = $this->codigoSistema;
        $codigoSucursal     = $this->codigoSucursal;
        $cuis               = session('scuis');
        $nit                = $this->nit;

        // dd(session()->all());

        $parametros         =  array(
            'SolicitudCufd' => array(
                'codigoAmbiente'    => $codigoAmbiente,
                'codigoModalidad'   => $codigoModalidad,
                'codigoPuntoVenta'  => $codigoPuntoVenta,
                'codigoSistema'     => $codigoSistema,
                'codigoSucursal'    => $codigoSucursal,
                'cuis'              => $cuis,
                'nit'               => $nit
            )
        );

        $aoptions = array(
            'http' => array(
                'header' => $this->header,
                'timeout' => $this->timeout
            ),
        );

        $context = stream_context_create($aoptions);

        try {
            $client = new \SoapClient($wsdl,[
                'stream_context' => $context,
                'cache_wsdl' => WSDL_CACHE_NONE,
                'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | SOAP_COMPRESSION_DEFLATE
            ]);

            $resultado = $client->cufd($parametros);

            $data['estado']     = 'success';
            $data['resultado']  = $resultado;
        } catch (SoapFault $fault) {
            $resultado           = false;
            $data['estado']      = 'error';
            $data['resultado']   = $resultado;
            $data['error']       = $fault;
            $data['msgS']        = $fault->getMessage();
        }   catch (Exception $e) {
            // Captura cualquier otra excepción y maneja el error
            $data['msgE']        = $e->getMessage();

            // echo "Se produjo un error: " . $e->getMessage();
        }
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function sincronizarListaProductosServicios(){
        $this->verificarConeccion();
        $wsdl               = "https://pilotosiatservicios.impuestos.gob.bo/v2/FacturacionSincronizacion?wsdl";
        $codigoAmbiente     = $this->codigoAmbiente;
        $codigoPuntoVenta   = $this->codigoPuntoVenta;
        $codigoSistema      = $this->codigoSistema;
        $codigoSucursal     = $this->codigoSucursal;
        $cuis               = session('scuis');
        $nit                = $this->nit;

        $parametros         =  array(
            'SolicitudSincronizacion' => array(
                'codigoAmbiente'    => $codigoAmbiente,
                'codigoPuntoVenta'  => $codigoPuntoVenta,
                'codigoSistema'     => $codigoSistema,
                'codigoSucursal'    => $codigoSucursal,
                'cuis'              => $cuis,
                'nit'               => $nit
            )
        );

        $aoptions = array(
            'http' => array(
                'header' => $this->header,
                'timeout' => $this->timeout
            ),
        );

        $context = stream_context_create($aoptions);

        try {
            $client = new \SoapClient($wsdl,[
                'stream_context' => $context,
                'cache_wsdl' => WSDL_CACHE_NONE,
                'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | SOAP_COMPRESSION_DEFLATE
            ]);

            $resultado = $client->sincronizarListaProductosServicios($parametros);

            $data['estado'] = 'success';
            $data['resultado'] = $resultado;
        } catch (SoapFault $fault) {
            $resultado = false;
            $data['estado'] = 'error';
            $data['resultado'] = $resultado;
        }

        // dd($data);

        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function sincronizarListaLeyendasFactura(){
        $this->verificarConeccion();
        $wsdl               = "https://pilotosiatservicios.impuestos.gob.bo/v2/FacturacionSincronizacion?wsdl";
        $codigoAmbiente     = $this->codigoAmbiente;
        $codigoPuntoVenta   = $this->codigoPuntoVenta;
        $codigoSistema      = $this->codigoSistema;
        $codigoSucursal     = $this->codigoSucursal;
        $cuis               = session('scuis');
        $nit                = $this->nit;

        $parametros         =  array(
            'SolicitudSincronizacion' => array(
                'codigoAmbiente'    => $codigoAmbiente,
                'codigoPuntoVenta'  => $codigoPuntoVenta,
                'codigoSistema'     => $codigoSistema,
                'codigoSucursal'    => $codigoSucursal,
                'cuis'              => $cuis,
                'nit'               => $nit
            )
        );

        $aoptions = array(
            'http' => array(
                'header' => $this->header,
                'timeout' => $this->timeout
            ),
        );

        $context = stream_context_create($aoptions);

        try {
            $client = new \SoapClient($wsdl,[
                'stream_context' => $context,
                'cache_wsdl' => WSDL_CACHE_NONE,
                'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | SOAP_COMPRESSION_DEFLATE
            ]);

            $resultado = $client->sincronizarListaLeyendasFactura($parametros);

            $data['estado'] = 'success';
            $data['resultado'] = $resultado;
        } catch (SoapFault $fault) {
            $resultado = false;
            $data['estado'] = 'error';
            $data['resultado'] = $resultado;
        }

        // dd($data);

        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function recepcionFactura($arch, $fecEnv, $hasArch){
        $this->verificarConeccion();
        $wsdl                   = "https://pilotosiatservicios.impuestos.gob.bo/v2/ServicioFacturacionCompraVenta?wsdl";

        $codigoAmbiente         = $this->codigoAmbiente;
        $codigoDocumentoSector  = $this->codigoDocumentoSector;     //NUEVO SECTOR EDUCATIIVO
        $codigoEmision          = 1;                                //NUEVO LINENA
        $codigoModalidad        = $this->codigoModalidad;
        $codigoPuntoVenta       = $this->codigoPuntoVenta;
        $codigoSistema          = $this->codigoSistema;
        $codigoSucursal         = $this->codigoSucursal;
        $cufd                   = session('scufd'); //NUEVO
        $cuis                   = session('scuis');
        $nit                    = $this->nit;
        $tipoFacturaDocumento   = 1;                        //NUEVO FACTURA CON DERECHO A CREDITO FISCAL
        $archivo                = $arch;
        $fechaEnvio             = $fecEnv;
        $hashArchivo            = $hasArch;

        $parametros         =  array(
            'SolicitudServicioRecepcionFactura' => array(
                'codigoAmbiente'            => $codigoAmbiente,
                'codigoDocumentoSector'     => $codigoDocumentoSector,
                'codigoEmision'             => $codigoEmision,
                'codigoModalidad'           => $codigoModalidad,
                'codigoPuntoVenta'          => $codigoPuntoVenta,
                'codigoSistema'             => $codigoSistema,
                'codigoSucursal'            => $codigoSucursal,
                'cufd'                      => $cufd,
                'cuis'                      => $cuis,
                'nit'                       => $nit,
                'tipoFacturaDocumento'      => $tipoFacturaDocumento,
                'archivo'                   => $archivo,
                'fechaEnvio'                => $fechaEnvio,
                'hashArchivo'               => $hashArchivo
            )
        );

        $aoptions = array(
            'http' => array(
                'header' => $this->header,
                'timeout' => $this->timeout
            ),
        );

        $context = stream_context_create($aoptions);

        try {
            $client = new \SoapClient($wsdl,[
                'stream_context' => $context,
                'cache_wsdl' => WSDL_CACHE_NONE,
                'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | SOAP_COMPRESSION_DEFLATE
            ]);

            $resultado = $client->recepcionFactura($parametros);

            $data['estado'] = 'success';
            $data['resultado'] = $resultado;
        } catch (SoapFault $fault) {
            $resultado = false;
            $data['estado'] = 'error';
            $data['resultado'] = $resultado;
            $data['msg'] = $fault->getMessage();
        }
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function recepcionPaqueteFactura($arch, $fechaenv,$hasarch, $cafcC, $canFact, $codEvent){
        // dd($arch, $fechaenv,$hasarch, $cafcC, $canFact, $codEvent);
        $this->verificarConeccion();
        $wsdl                   = "https://pilotosiatservicios.impuestos.gob.bo/v2/ServicioFacturacionComputarizada?wsdl";
        $codigoAmbiente         = $this->codigoAmbiente;
        $codigoDocumentoSector  = $this->codigoDocumentoSector;     // SECTOR EDUCATIVO
        $codigoEmision          = 2;                                // FUERA DE  LINEA (LINEA = 1 | FUERA DE LINEA = 2)
        $codigoModalidad        = $this->codigoModalidad;
        $codigoPuntoVenta       = $this->codigoPuntoVenta;
        $codigoSistema          = $this->codigoSistema;
        $codigoSucursal         = $this->codigoSucursal;
        $cufd                   = session('scufd');
        $cuis                   = session('scuis');
        $nit                    = $this->nit;
        $tipoFacturaDocumento   = 1;                        //NUEVO FACTURA CON DERECHO A CREDITO FISCAL
        $archivo                = $arch;
        $fechaEnvio             = $fechaenv;
        $hashArchivo            = $hasarch;
        $cafc                   = $cafcC;
        $cantidadFacturas       = $canFact;
        $codigoEvento           = $codEvent;

        // dd(
        //     "wsdl => ".$wsdl,
        //     "codigoAmbiente => ".$codigoAmbiente,
        //     "codigoDocumentoSector => ".$codigoDocumentoSector,
        //     "codigoEmision => ".$codigoEmision,
        //     "codigoModalidad => ".$codigoModalidad,
        //     "codigoPuntoVenta => ".$codigoPuntoVenta,
        //     "codigoSistema => ".$codigoSistema,
        //     "codigoSucursal => ".$codigoSucursal,
        //     "cufd => ".$cufd,
        //     "cuis => ".$cuis,
        //     "nit => ".$nit,
        //     "tipoFacturaDocumento => ".$tipoFacturaDocumento,
        //     "archivo => ".$archivo,
        //     "fechaEnvio => ".$fechaEnvio,
        //     "hashArchivo => ".$hashArchivo,
        //     "cafc => ".$cafc,
        //     "cantidadFacturas => ".$cantidadFacturas,
        //     "codigoEvento => ".$codigoEvento
        // );

        $parametros         =  array(
            'SolicitudServicioRecepcionPaquete' => array(
                'codigoAmbiente'            => $codigoAmbiente,
                'codigoDocumentoSector'     => $codigoDocumentoSector,
                'codigoEmision'             => $codigoEmision,
                'codigoModalidad'           => $codigoModalidad,
                'codigoPuntoVenta'          => $codigoPuntoVenta,
                'codigoSistema'             => $codigoSistema,
                'codigoSucursal'            => $codigoSucursal,
                'cufd'                      => $cufd,
                'cuis'                      => $cuis,
                'nit'                       => $nit,
                'tipoFacturaDocumento'      => $tipoFacturaDocumento,
                'archivo'                   => $archivo,
                'fechaEnvio'                => $fechaEnvio,
                'hashArchivo'               => $hashArchivo,
                'cafc'                      => $cafc,
                'cantidadFacturas'          => $cantidadFacturas,
                'codigoEvento'              => $codigoEvento
            )
        );

        $aoptions = array(
            'http' => array(
                'header' => $this->header,
                'timeout' => $this->timeout
            ),
        );

        $context = stream_context_create($aoptions);

        try {
            $client = new \SoapClient($wsdl,[
                'stream_context' => $context,
                'cache_wsdl' => WSDL_CACHE_NONE,
                'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | SOAP_COMPRESSION_DEFLATE
            ]);

            $resultado = $client->recepcionPaqueteFactura($parametros);

            $data['estado'] = 'success';
            $data['resultado'] = $resultado;
        } catch (SoapFault $fault) {
            $resultado = false;
            $data['estado'] = 'error';
            $data['resultado'] = $resultado;
            $data['msg'] = $fault->getMessage();
        }
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    // public function anulacionFactura($arch, $fecEnv, $hasArch){
    public function anulacionFactura($codMot, $cuf1){
        $this->verificarConeccion();
        $wsdl                   = "https://pilotosiatservicios.impuestos.gob.bo/v2/ServicioFacturacionCompraVenta?wsdl";
        $codigoAmbiente         = $this->codigoAmbiente;
        $codigoDocumentoSector  = $this->codigoDocumentoSector; //NUEVO SECTOR EDUCATIIVO
        $codigoEmision          = 1; //NUEVO LINENA
        $codigoModalidad        = $this->codigoModalidad;
        $codigoPuntoVenta       = $this->codigoPuntoVenta;
        $codigoSistema          = $this->codigoSistema;
        $codigoSucursal         = $this->codigoSucursal;
        $cufd                   = session('scufd'); //NUEVO
        $cuis                   = session('scuis');
        $nit                    = $this->nit;
        $tipoFacturaDocumento   = 1; //NUEVO FACTURA CON DERECHO A CREDITO FISCAL
        $codigoMotivo           = $codMot;
        $cuf                    = $cuf1;

        $parametros         =  array(
            'SolicitudServicioAnulacionFactura' => array(
                'codigoAmbiente'            => $codigoAmbiente,
                'codigoDocumentoSector'     => $codigoDocumentoSector,
                'codigoEmision'             => $codigoEmision,
                'codigoModalidad'           => $codigoModalidad,
                'codigoPuntoVenta'          => $codigoPuntoVenta,
                'codigoSistema'             => $codigoSistema,
                'codigoSucursal'            => $codigoSucursal,
                'cufd'                      => $cufd,
                'cuis'                      => $cuis,
                'nit'                       => $nit,
                'tipoFacturaDocumento'      => $tipoFacturaDocumento,
                'codigoMotivo'              => $codigoMotivo,
                'cuf'                       => $cuf,
            )
        );

        $aoptions = array(
            'http' => array(
                'header' => $this->header,
                'timeout' => $this->timeout
            ),
        );

        $context = stream_context_create($aoptions);

        try {
            $client = new \SoapClient($wsdl,[
                'stream_context' => $context,
                'cache_wsdl' => WSDL_CACHE_NONE,
                'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | SOAP_COMPRESSION_DEFLATE
            ]);

            $resultado = $client->anulacionFactura($parametros);

            $data['estado'] = 'success';
            $data['resultado'] = $resultado;
        } catch (SoapFault $fault) {
            $resultado = false;
            $data['estado'] = 'error';
            $data['resultado'] = $resultado;
        }
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function consultaPuntoVenta(){
        $this->verificarConeccion();
        $wsdl               = "https://pilotosiatservicios.impuestos.gob.bo/v2/FacturacionOperaciones?wsdl";
        $codigoAmbiente     = $this->codigoAmbiente;
        $codigoSistema      = $this->codigoSistema;
        $codigoSucursal     = $this->codigoSucursal;
        $cuis               = session('scuis');
        $nit                = $this->nit;
        // dd(Auth::user()->codigo_punto_venta);

        $parametros         =  array(
            'SolicitudConsultaPuntoVenta' => array(
                'codigoAmbiente'    => $codigoAmbiente,
                'codigoSistema'     => $codigoSistema,
                'codigoSucursal'    => $codigoSucursal,
                'cuis'              => $cuis,
                'nit'               => $nit
            )
        );

        $aoptions = array(
            'http' => array(
                'header' => $this->header,
                'timeout' => $this->timeout
            ),
        );

        $context = stream_context_create($aoptions);

        try {
            $client = new \SoapClient($wsdl,[
                'stream_context' => $context,
                'cache_wsdl' => WSDL_CACHE_NONE,
                'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | SOAP_COMPRESSION_DEFLATE
            ]);

            $resultado = $client->consultaPuntoVenta($parametros);

            $data['estado'] = 'success';
            $data['resultado'] = $resultado;
        } catch (SoapFault $fault) {
            $resultado = false;
            $data['estado'] = 'error';
            $data['resultado'] = $resultado;
        }
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function registroPuntoVenta($des, $nom){
        $this->verificarConeccion();
        $wsdl                   = "https://pilotosiatservicios.impuestos.gob.bo/v2/FacturacionOperaciones?wsdl";
        $codigoAmbiente         = $this->codigoAmbiente;
        $codigoModalidad        = $this->codigoModalidad;
        $codigoSistema          = $this->codigoSistema;
        $codigoSucursal         = $this->codigoSucursal;
        $codigoTipoPuntoVenta   = 2;                        //PUNTO VENTA VENTANILLA DE COBRANZA
        $cuis                   = session('scuis');
        $descripcion            = $des;
        $nit                    = $this->nit;
        $nombrePuntoVenta       = $nom;


        $parametros         =  array(
            'SolicitudRegistroPuntoVenta' => array(
                'codigoAmbiente'        => $codigoAmbiente,
                'codigoModalidad'       => $codigoModalidad,
                'codigoSistema'         => $codigoSistema,
                'codigoSucursal'        => $codigoSucursal,
                'codigoTipoPuntoVenta'  => $codigoTipoPuntoVenta,
                'cuis'                  => $cuis,
                'descripcion'           => $descripcion,
                'nit'                   => $nit,
                'nombrePuntoVenta'      => $nombrePuntoVenta
            )
        );

        $aoptions = array(
            'http' => array(
                'header' => $this->header,
                'timeout' => $this->timeout
            ),
        );

        $context = stream_context_create($aoptions);

        try {
            $client = new \SoapClient($wsdl,[
                'stream_context' => $context,
                'cache_wsdl' => WSDL_CACHE_NONE,
                'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | SOAP_COMPRESSION_DEFLATE
            ]);

            $resultado = $client->registroPuntoVenta($parametros);

            $data['estado'] = 'success';
            $data['resultado'] = $resultado;
        } catch (SoapFault $fault) {
            $resultado = false;
            $data['estado'] = 'error';
            $data['resultado'] = $resultado;
        }
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function cierrePuntoVenta($cod){
        $this->verificarConeccion();
        $wsdl                   = "https://pilotosiatservicios.impuestos.gob.bo/v2/FacturacionOperaciones?wsdl";
        $codigoAmbiente         = $this->codigoAmbiente;
        $codigoPuntoVenta       = $cod;
        $codigoSistema          = $this->codigoSistema;
        $codigoSucursal         = $this->codigoSucursal;
        $cuis                   = session('scuis');
        $nit                    = $this->nit;


        $parametros         =  array(
            'SolicitudCierrePuntoVenta' => array(
                'codigoAmbiente'    => $codigoAmbiente,
                'codigoPuntoVenta'  => $codigoPuntoVenta,
                'codigoSistema'     => $codigoSistema,
                'codigoSucursal'    => $codigoSucursal,
                'cuis'              => $cuis,
                'nit'               => $nit
            )
        );

        $aoptions = array(
            'http' => array(
                'header' => $this->header,
                'timeout' => $this->timeout
            ),
        );

        $context = stream_context_create($aoptions);

        try {
            $client = new \SoapClient($wsdl,[
                'stream_context' => $context,
                'cache_wsdl' => WSDL_CACHE_NONE,
                'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | SOAP_COMPRESSION_DEFLATE
            ]);

            $resultado = $client->cierrePuntoVenta($parametros);

            $data['estado'] = 'success';
            $data['resultado'] = $resultado;
        } catch (SoapFault $fault) {
            $resultado = false;
            $data['estado'] = 'error';
            $data['resultado'] = $resultado;
        }
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function sincronizarParametricaPaisOrigen(){
        $this->verificarConeccion();
        $wsdl                   = "https://pilotosiatservicios.impuestos.gob.bo/v2/FacturacionSincronizacion?wsdl";
        $codigoAmbiente         = $this->codigoAmbiente;
        $codigoPuntoVenta       = $this->codigoPuntoVenta;
        $codigoSistema          = $this->codigoSistema;
        $codigoSucursal         = $this->codigoSucursal;
        $cuis                   = session('scuis');
        $nit                    = $this->nit;


        $parametros         =  array(
            'SolicitudSincronizacion' => array(
                'codigoAmbiente'    => $codigoAmbiente,
                'codigoPuntoVenta'  => $codigoPuntoVenta,
                'codigoSistema'     => $codigoSistema,
                'codigoSucursal'    => $codigoSucursal,
                'cuis'              => $cuis,
                'nit'               => $nit
            )
        );

        $aoptions = array(
            'http' => array(
                'header' => $this->header,
                'timeout' => $this->timeout
            ),
        );

        $context = stream_context_create($aoptions);

        try {
            $client = new \SoapClient($wsdl,[
                'stream_context' => $context,
                'cache_wsdl' => WSDL_CACHE_NONE,
                'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | SOAP_COMPRESSION_DEFLATE
            ]);

            $resultado = $client->sincronizarParametricaPaisOrigen($parametros);

            $data['estado'] = 'success';
            $data['resultado'] = $resultado;
        } catch (SoapFault $fault) {
            $resultado = false;
            $data['estado'] = 'error';
            $data['resultado'] = $resultado;
        }
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function sincronizarParametricaTipoDocumentoIdentidad(){
        $this->verificarConeccion();
        $wsdl                   = "https://pilotosiatservicios.impuestos.gob.bo/v2/FacturacionSincronizacion?wsdl";
        $codigoAmbiente         = $this->codigoAmbiente;
        $codigoPuntoVenta       = $this->codigoPuntoVenta;
        $codigoSistema          = $this->codigoSistema;
        $codigoSucursal         = $this->codigoSucursal;
        $cuis                   = session('scuis');
        $nit                    = $this->nit;

        $parametros         =  array(
            'SolicitudSincronizacion' => array(
                'codigoAmbiente'    => $codigoAmbiente,
                'codigoPuntoVenta'  => $codigoPuntoVenta,
                'codigoSistema'     => $codigoSistema,
                'codigoSucursal'    => $codigoSucursal,
                'cuis'              => $cuis,
                'nit'               => $nit
            )
        );

        $aoptions = array(
            'http' => array(
                'header' => $this->header,
                'timeout' => $this->timeout
            ),
        );

        $context = stream_context_create($aoptions);

        try {
            $client = new \SoapClient($wsdl,[
                'stream_context' => $context,
                'cache_wsdl' => WSDL_CACHE_NONE,
                'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | SOAP_COMPRESSION_DEFLATE
            ]);

            $resultado = $client->sincronizarParametricaTipoDocumentoIdentidad($parametros);

            $data['estado'] = 'success';
            $data['resultado'] = $resultado;
        } catch (SoapFault $fault) {
            $resultado = false;
            $data['estado'] = 'error';
            $data['resultado'] = $resultado;
        }
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function sincronizarParametricaTipoEmision(){
        $this->verificarConeccion();
        $wsdl                   = "https://pilotosiatservicios.impuestos.gob.bo/v2/FacturacionSincronizacion?wsdl";
        $codigoAmbiente         = $this->codigoAmbiente;
        $codigoPuntoVenta       = $this->codigoPuntoVenta;
        $codigoSistema          = $this->codigoSistema;
        $codigoSucursal         = $this->codigoSucursal;
        $cuis                   = session('scuis');
        $nit                    = $this->nit;

        $parametros         =  array(
            'SolicitudSincronizacion' => array(
                'codigoAmbiente'    => $codigoAmbiente,
                'codigoPuntoVenta'  => $codigoPuntoVenta,
                'codigoSistema'     => $codigoSistema,
                'codigoSucursal'    => $codigoSucursal,
                'cuis'              => $cuis,
                'nit'               => $nit
            )
        );

        $aoptions = array(
            'http' => array(
                'header' => $this->header,
                'timeout' => $this->timeout
            ),
        );

        $context = stream_context_create($aoptions);

        try {
            $client = new \SoapClient($wsdl,[
                'stream_context' => $context,
                'cache_wsdl' => WSDL_CACHE_NONE,
                'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | SOAP_COMPRESSION_DEFLATE
            ]);

            $resultado = $client->sincronizarParametricaTipoEmision($parametros);

            $data['estado'] = 'success';
            $data['resultado'] = $resultado;
        } catch (SoapFault $fault) {
            $resultado = false;
            $data['estado'] = 'error';
            $data['resultado'] = $resultado;
        }
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function sincronizarParametricaTipoHabitacion(){
        $this->verificarConeccion();
        $wsdl                   = "https://pilotosiatservicios.impuestos.gob.bo/v2/FacturacionSincronizacion?wsdl";
        $codigoAmbiente         = $this->codigoAmbiente;
        $codigoPuntoVenta       = $this->codigoPuntoVenta;
        $codigoSistema          = $this->codigoSistema;
        $codigoSucursal         = $this->codigoSucursal;
        $cuis                   = session('scuis');
        $nit                    = $this->nit;

        $parametros         =  array(
            'SolicitudSincronizacion' => array(
                'codigoAmbiente'    => $codigoAmbiente,
                'codigoPuntoVenta'  => $codigoPuntoVenta,
                'codigoSistema'     => $codigoSistema,
                'codigoSucursal'    => $codigoSucursal,
                'cuis'              => $cuis,
                'nit'               => $nit
            )
        );

        $aoptions = array(
            'http' => array(
                'header' => $this->header,
                'timeout' => $this->timeout
            ),
        );

        $context = stream_context_create($aoptions);

        try {
            $client = new \SoapClient($wsdl,[
                'stream_context' => $context,
                'cache_wsdl' => WSDL_CACHE_NONE,
                'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | SOAP_COMPRESSION_DEFLATE
            ]);

            $resultado = $client->sincronizarParametricaTipoHabitacion($parametros);

            $data['estado'] = 'success';
            $data['resultado'] = $resultado;
        } catch (SoapFault $fault) {
            $resultado = false;
            $data['estado'] = 'error';
            $data['resultado'] = $resultado;
        }
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function sincronizarParametricaTipoMetodoPago(){
        $this->verificarConeccion();
        $wsdl                   = "https://pilotosiatservicios.impuestos.gob.bo/v2/FacturacionSincronizacion?wsdl";
        $codigoAmbiente         = $this->codigoAmbiente;
        $codigoPuntoVenta       = $this->codigoPuntoVenta;
        $codigoSistema          = $this->codigoSistema;
        $codigoSucursal         = $this->codigoSucursal;
        $cuis                   = session('scuis');
        $nit                    = $this->nit;

        $parametros         =  array(
            'SolicitudSincronizacion' => array(
                'codigoAmbiente'    => $codigoAmbiente,
                'codigoPuntoVenta'  => $codigoPuntoVenta,
                'codigoSistema'     => $codigoSistema,
                'codigoSucursal'    => $codigoSucursal,
                'cuis'              => $cuis,
                'nit'               => $nit
            )
        );

        $aoptions = array(
            'http' => array(
                'header' => $this->header,
                'timeout' => $this->timeout
            ),
        );

        $context = stream_context_create($aoptions);

        try {
            $client = new \SoapClient($wsdl,[
                'stream_context' => $context,
                'cache_wsdl' => WSDL_CACHE_NONE,
                'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | SOAP_COMPRESSION_DEFLATE
            ]);

            $resultado = $client->sincronizarParametricaTipoMetodoPago($parametros);

            $data['estado'] = 'success';
            $data['resultado'] = $resultado;
        } catch (SoapFault $fault) {
            $resultado = false;
            $data['estado'] = 'error';
            $data['resultado'] = $resultado;
        }
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function sincronizarParametricaTipoMoneda(){
        $this->verificarConeccion();
        $wsdl                   = "https://pilotosiatservicios.impuestos.gob.bo/v2/FacturacionSincronizacion?wsdl";
        $codigoAmbiente         = $this->codigoAmbiente;
        $codigoPuntoVenta       = $this->codigoPuntoVenta;
        $codigoSistema          = $this->codigoSistema;
        $codigoSucursal         = $this->codigoSucursal;
        $cuis                   = session('scuis');
        $nit                    = $this->nit;

        $parametros         =  array(
            'SolicitudSincronizacion' => array(
                'codigoAmbiente'    => $codigoAmbiente,
                'codigoPuntoVenta'  => $codigoPuntoVenta,
                'codigoSistema'     => $codigoSistema,
                'codigoSucursal'    => $codigoSucursal,
                'cuis'              => $cuis,
                'nit'               => $nit
            )
        );

        $aoptions = array(
            'http' => array(
                'header' => $this->header,
                'timeout' => $this->timeout
            ),
        );

        $context = stream_context_create($aoptions);

        try {
            $client = new \SoapClient($wsdl,[
                'stream_context' => $context,
                'cache_wsdl' => WSDL_CACHE_NONE,
                'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | SOAP_COMPRESSION_DEFLATE
            ]);

            $resultado = $client->sincronizarParametricaTipoMoneda($parametros);

            $data['estado'] = 'success';
            $data['resultado'] = $resultado;
        } catch (SoapFault $fault) {
            $resultado = false;
            $data['estado'] = 'error';
            $data['resultado'] = $resultado;
        }
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function sincronizarParametricaTipoPuntoVenta(){
        $this->verificarConeccion();
        $wsdl                   = "https://pilotosiatservicios.impuestos.gob.bo/v2/FacturacionSincronizacion?wsdl";
        $codigoAmbiente         = $this->codigoAmbiente;
        $codigoPuntoVenta       = $this->codigoPuntoVenta;
        $codigoSistema          = $this->codigoSistema;
        $codigoSucursal         = $this->codigoSucursal;
        $cuis                   = session('scuis');
        $nit                    = $this->nit;

        $parametros         =  array(
            'SolicitudSincronizacion' => array(
                'codigoAmbiente'    => $codigoAmbiente,
                'codigoPuntoVenta'  => $codigoPuntoVenta,
                'codigoSistema'     => $codigoSistema,
                'codigoSucursal'    => $codigoSucursal,
                'cuis'              => $cuis,
                'nit'               => $nit
            )
        );

        $aoptions = array(
            'http' => array(
                'header' => $this->header,
                'timeout' => $this->timeout
            ),
        );

        $context = stream_context_create($aoptions);

        try {
            $client = new \SoapClient($wsdl,[
                'stream_context' => $context,
                'cache_wsdl' => WSDL_CACHE_NONE,
                'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | SOAP_COMPRESSION_DEFLATE
            ]);

            $resultado = $client->sincronizarParametricaTipoPuntoVenta($parametros);

            $data['estado'] = 'success';
            $data['resultado'] = $resultado;
        } catch (SoapFault $fault) {
            $resultado = false;
            $data['estado'] = 'error';
            $data['resultado'] = $resultado;
        }
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function sincronizarParametricaTiposFactura(){
        $this->verificarConeccion();
        $wsdl                   = "https://pilotosiatservicios.impuestos.gob.bo/v2/FacturacionSincronizacion?wsdl";
        $codigoAmbiente         = $this->codigoAmbiente;
        $codigoPuntoVenta       = $this->codigoPuntoVenta;
        $codigoSistema          = $this->codigoSistema;
        $codigoSucursal         = $this->codigoSucursal;
        $cuis                   = session('scuis');
        $nit                    = $this->nit;

        $parametros         =  array(
            'SolicitudSincronizacion' => array(
                'codigoAmbiente'    => $codigoAmbiente,
                'codigoPuntoVenta'  => $codigoPuntoVenta,
                'codigoSistema'     => $codigoSistema,
                'codigoSucursal'    => $codigoSucursal,
                'cuis'              => $cuis,
                'nit'               => $nit
            )
        );

        $aoptions = array(
            'http' => array(
                'header' => $this->header,
                'timeout' => $this->timeout
            ),
        );

        $context = stream_context_create($aoptions);

        try {
            $client = new \SoapClient($wsdl,[
                'stream_context' => $context,
                'cache_wsdl' => WSDL_CACHE_NONE,
                'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | SOAP_COMPRESSION_DEFLATE
            ]);

            $resultado = $client->sincronizarParametricaTiposFactura($parametros);

            $data['estado'] = 'success';
            $data['resultado'] = $resultado;
        } catch (SoapFault $fault) {
            $resultado = false;
            $data['estado'] = 'error';
            $data['resultado'] = $resultado;
        }
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function sincronizarParametricaUnidadMedida(){
        $this->verificarConeccion();
        $wsdl                   = "https://pilotosiatservicios.impuestos.gob.bo/v2/FacturacionSincronizacion?wsdl";
        $codigoAmbiente         = $this->codigoAmbiente;
        $codigoPuntoVenta       = $this->codigoPuntoVenta;
        $codigoSistema          = $this->codigoSistema;
        $codigoSucursal         = $this->codigoSucursal;
        $cuis                   = session('scuis');
        $nit                    = $this->nit;

        $parametros         =  array(
            'SolicitudSincronizacion' => array(
                'codigoAmbiente'    => $codigoAmbiente,
                'codigoPuntoVenta'  => $codigoPuntoVenta,
                'codigoSistema'     => $codigoSistema,
                'codigoSucursal'    => $codigoSucursal,
                'cuis'              => $cuis,
                'nit'               => $nit
            )
        );

        $aoptions = array(
            'http' => array(
                'header' => $this->header,
                'timeout' => $this->timeout
            ),
        );

        $context = stream_context_create($aoptions);

        try {
            $client = new \SoapClient($wsdl,[
                'stream_context' => $context,
                'cache_wsdl' => WSDL_CACHE_NONE,
                'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | SOAP_COMPRESSION_DEFLATE
            ]);

            $resultado = $client->sincronizarParametricaUnidadMedida($parametros);

            $data['estado'] = 'success';
            $data['resultado'] = $resultado;
        } catch (SoapFault $fault) {
            $resultado = false;
            $data['estado'] = 'error';
            $data['resultado'] = $resultado;
        }
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function sincronizarParametricaEventosSignificativos(){
        $this->verificarConeccion();
        $wsdl                   = "https://pilotosiatservicios.impuestos.gob.bo/v2/FacturacionSincronizacion?wsdl";
        $codigoAmbiente         = $this->codigoAmbiente;
        $codigoPuntoVenta       = $this->codigoPuntoVenta;
        $codigoSistema          = $this->codigoSistema;
        $codigoSucursal         = $this->codigoSucursal;
        $cuis                   = session('scuis');
        $nit                    = $this->nit;

        $parametros         =  array(
            'SolicitudSincronizacion' => array(
                'codigoAmbiente'    => $codigoAmbiente,
                'codigoPuntoVenta'  => $codigoPuntoVenta,
                'codigoSistema'     => $codigoSistema,
                'codigoSucursal'    => $codigoSucursal,
                'cuis'              => $cuis,
                'nit'               => $nit
            )
        );

        $aoptions = array(
            'http' => array(
                'header' => $this->header,
                'timeout' => $this->timeout
            ),
        );

        $context = stream_context_create($aoptions);

        try {
            $client = new \SoapClient($wsdl,[
                'stream_context' => $context,
                'cache_wsdl' => WSDL_CACHE_NONE,
                'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | SOAP_COMPRESSION_DEFLATE
            ]);

            $resultado = $client->sincronizarParametricaEventosSignificativos($parametros);

            $data['estado'] = 'success';
            $data['resultado'] = $resultado;
        } catch (SoapFault $fault) {
            $resultado = false;
            $data['estado'] = 'error';
            $data['resultado'] = $resultado;
        }
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function consultaEventoSignificativo($fecha){
        $this->verificarConeccion();
        $wsdl                   = "https://pilotosiatservicios.impuestos.gob.bo/v2/FacturacionOperaciones?wsdl";
        $codigoAmbiente         = $this->codigoAmbiente;
        $codigoPuntoVenta       = $this->codigoPuntoVenta;
        $codigoSistema          = $this->codigoSistema;
        $codigoSucursal         = $this->codigoSucursal;
        $cufd                   = session('scufd');
        $cuis                   = session('scuis');
        // $fechaEvento            = (new DateTime())->setTimeZone(new DateTimeZone('UTC'));
        // $fechaEvento            = Carbon::now()->format('Y-m-d\TH:i:s');
        $fechaEvento            = $fecha;
        $nit                    = $this->nit;

        // dd(
        //     "codigoPuntoVenta ".$codigoPuntoVenta,
        //     "codigoAmbiente ".$codigoAmbiente,
        //     "codigoSistema ".$codigoSistema,
        //     "codigoSucursal ".$codigoSucursal,
        //     "cufd ".$cufd,
        //     "cuis ".$cuis,
        //     "fechaEvento ".$fechaEvento,
        //     "nit ".$nit
        // );

        $parametros         =  array(
            'SolicitudConsultaEvento' => array(
                'codigoAmbiente'    => $codigoAmbiente,
                'codigoPuntoVenta'  => $codigoPuntoVenta,
                'codigoSistema'     => $codigoSistema,
                'codigoSucursal'    => $codigoSucursal,
                'cufd'              => $cufd,
                'cuis'              => $cuis,
                'fechaEvento'       => $fechaEvento,
                'nit'               => $nit
            )
        );

        $aoptions = array(
            'http' => array(
                'header' => $this->header,
                'timeout' => $this->timeout
            ),
        );

        $context = stream_context_create($aoptions);

        try {
            $client = new \SoapClient($wsdl,[
                'stream_context' => $context,
                'cache_wsdl' => WSDL_CACHE_NONE,
                'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | SOAP_COMPRESSION_DEFLATE
            ]);

            $resultado = $client->consultaEventoSignificativo($parametros);

            $data['estado'] = 'success';
            $data['resultado'] = $resultado;
        } catch (SoapFault $fault) {
            $resultado = false;
            $data['estado'] = 'error';
            $data['resultado'] = $resultado;
        }
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function registroEventoSignificativo($codMotEvent, $cufdEvent, $desc, $fechaIni, $fechaFin){
        $this->verificarConeccion();
        $wsdl                   = "https://pilotosiatservicios.impuestos.gob.bo/v2/FacturacionOperaciones?wsdl";
        $codigoAmbiente         = $this->codigoAmbiente;
        $codigoMotivoEvento     = $codMotEvent;
        $codigoPuntoVenta       = $this->codigoPuntoVenta;
        $codigoSistema          = $this->codigoSistema;
        $codigoSucursal         = $this->codigoSucursal;
        $cufd                   = session('scufd');
        $cufdEvento             = $cufdEvent;
        $cuis                   = session('scuis');
        $descripcion            = $desc;
        $fechaHoraFinEvento     = $fechaFin;
        $fechaHoraInicioEvento  = $fechaIni;
        $nit                    = $this->nit;

        $parametros         =  array(
            'SolicitudEventoSignificativo' => array(
                'codigoAmbiente'            => $codigoAmbiente,
                'codigoMotivoEvento'        => $codigoMotivoEvento,
                'codigoPuntoVenta'          => $codigoPuntoVenta,
                'codigoSistema'             => $codigoSistema,
                'codigoSucursal'            => $codigoSucursal,
                'cufd'                      => $cufd,
                'cufdEvento'                => $cufdEvento,
                'cuis'                      => $cuis,
                'descripcion'               => $descripcion,
                'fechaHoraFinEvento'        => $fechaHoraFinEvento,
                'fechaHoraInicioEvento'     => $fechaHoraInicioEvento,
                'nit'                       => $nit
            )
        );

        $aoptions = array(
            'http' => array(
                'header' => $this->header,
                'timeout' => $this->timeout
            ),
        );

        $context = stream_context_create($aoptions);

        try {
            $client = new \SoapClient($wsdl,[
                'stream_context' => $context,
                'cache_wsdl' => WSDL_CACHE_NONE,
                'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | SOAP_COMPRESSION_DEFLATE
            ]);

            $resultado = $client->registroEventoSignificativo($parametros);

            $data['estado'] = 'success';
            $data['resultado'] = $resultado;
        } catch (SoapFault $fault) {
            $resultado = false;
            $data['estado'] = 'error';
            $data['resultado'] = $resultado;
            $data['msg'] = $fault;
        }
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    // public function validacionRecepcionPaqueteFactura($codMotEvent, $cufdEvent, $desc, $fechaIni, $fechaFin){
    public function validacionRecepcionPaqueteFactura($codEmision, $codRecepcion){
        $this->verificarConeccion();
        $wsdl                   = "https://pilotosiatservicios.impuestos.gob.bo/v2/ServicioFacturacionComputarizada?wsdl";
        $codigoAmbiente         = $this->codigoAmbiente;
        $codigoDocumentoSector  = $this->codigoDocumentoSector;                           //SECTOR EDUCATIVO
        $codigoEmision          = $codEmision;                  //NUEVO LINENA 1 LINEA | 2 FUENRA DE LINEA
        $codigoModalidad        = $this->codigoModalidad;
        $codigoPuntoVenta       = $this->codigoPuntoVenta;
        $codigoSistema          = $this->codigoSistema;
        $codigoSucursal         = $this->codigoSucursal;
        $cufd                   = session('scufd');
        $cuis                   = session('scuis');
        $nit                    = $this->nit;
        $tipoFacturaDocumento   = 1;                            //NUEVO FACTURA CON DERECHO A CREDITO FISCAL
        $codigoRecepcion        = $codRecepcion;

        $parametros         =  array(
            'SolicitudServicioValidacionRecepcionPaquete' => array(
                'codigoAmbiente'          => $codigoAmbiente,
                'codigoDocumentoSector'   => $codigoDocumentoSector,
                'codigoEmision'           => $codigoEmision,
                'codigoModalidad'         => $codigoModalidad,
                'codigoPuntoVenta'        => $codigoPuntoVenta,
                'codigoSistema'           => $codigoSistema,
                'codigoSucursal'          => $codigoSucursal,
                'cufd'                    => $cufd,
                'cuis'                    => $cuis,
                'nit'                     => $nit,
                'tipoFacturaDocumento'    => $tipoFacturaDocumento,
                'codigoRecepcion'         => $codigoRecepcion,
            )
        );

        $aoptions = array(
            'http' => array(
                'header' => $this->header,
                'timeout' => $this->timeout
            ),
        );

        $context = stream_context_create($aoptions);

        try {
            $client = new \SoapClient($wsdl,[
                'stream_context' => $context,
                'cache_wsdl' => WSDL_CACHE_NONE,
                'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP | SOAP_COMPRESSION_DEFLATE
            ]);

            $resultado = $client->validacionRecepcionPaqueteFactura($parametros);

            $data['estado'] = 'success';
            $data['resultado'] = $resultado;
        } catch (SoapFault $fault) {
            $resultado = false;
            $data['estado'] = 'error';
            $data['resultado'] = $resultado;
            $data['msg'] = $fault;
        }
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function verificarConeccion(){
        if(!session()->has('scufd')){
            $cufd = json_decode($this->cufd());
            if($cufd->estado === "success"){
                if($cufd->resultado->RespuestaCufd->transaccion){
                    session(['scufd'                => $cufd->resultado->RespuestaCufd->codigo]);
                    session(['scodigoControl'       => $cufd->resultado->RespuestaCufd->codigoControl]);
                    session(['sdireccion'           => $cufd->resultado->RespuestaCufd->direccion]);
                    session(['sfechaVigenciaCufd'   => $cufd->resultado->RespuestaCufd->fechaVigencia]);

                    $cufdNew = app(CufdController::class);
                    $cufdNew->create(
                                    $cufd->resultado->RespuestaCufd->codigo,
                                    $cufd->resultado->RespuestaCufd->codigoControl,
                                    $cufd->resultado->RespuestaCufd->direccion,
                                    $cufd->resultado->RespuestaCufd->fechaVigencia
                                );

                    $data['$cufd->resultado->RespuestaCufd->transaccion'] = 'si';
                }else{
                    $data['$cufd->resultado->RespuestaCufd->transaccion'] = 'no';
                }
                $data['!session()->has("scufd")'] = 'si';
            }else{
                // dd("chw");
            }
        }else{
            // dd("no");
            $fechaVigencia = str_replace("T"," ",substr(session('sfechaVigenciaCufd'),0,16));
            if($fechaVigencia < date('Y-m-d H:i')){
                $cufd = json_decode($this->cufd());
                if($cufd->estado === "success"){
                    if($cufd->resultado->RespuestaCufd->transaccion){
                        session(['scufd' => $cufd->resultado->RespuestaCufd->codigo]);
                        session(['scodigoControl' => $cufd->resultado->RespuestaCufd->codigoControl]);
                        session(['sdireccion' => $cufd->resultado->RespuestaCufd->direccion]);
                        session(['sfechaVigenciaCufd' => $cufd->resultado->RespuestaCufd->fechaVigencia]);

                        $cufdNew = app(CufdController::class);
                        $cufdNew->create(
                                        $cufd->resultado->RespuestaCufd->codigo,
                                        $cufd->resultado->RespuestaCufd->codigoControl,
                                        $cufd->resultado->RespuestaCufd->direccion,
                                        $cufd->resultado->RespuestaCufd->fechaVigencia
                                    );
                        $data['$cufd->resultado->RespuestaCufd->transaccion 2'] = 'si';
                    }else{
                        $data['$cufd->resultado->RespuestaCufd->transaccion 2'] = 'no';
                    }
                    $data['$cufd->estado === "success"'] = 'si';
                }else{
                    $data['$cufd->estado === "success"'] = 'no';
                }
                $data['$fechaVigencia < date("Y-m-d H:i")'] = 'si';
            }else{
                $data['$fechaVigencia < date("Y-m-d H:i")'] = 'no';
            }
            $data['!session()->has("scufd")'] = 'no';
        }

    }
}
