<?php

use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\EventoSignificativoController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MigracionController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\PuntoVentaCotroller;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\ServicioController;
use App\Http\Controllers\SincronizaCatalogo;
use App\Http\Controllers\VehiculoController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    // return view('welcome');
    // return view('auth.login');
    // return view('home.inicio');
    return redirect('home');
});

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    Route::get('/home', [HomeController::class, 'index']);

    // Route::get('/venta', [VentaController::class, 'listado']);
    // Route::post('/venta/guarda', [VentaController::class, 'guarda']);
    // Route::post('/venta/pagar', [VentaController::class, 'pagar']);

    // USUARIOS
    Route::get('/user', [UserController::class, 'listado']);
    Route::post('/user/ajaxListado', [UserController::class, 'ajaxListado']);
    Route::post('/user/guarda', [UserController::class, 'guarda']);
    Route::get('/user/detalle/{usuario_id}', [UserController::class, 'detalle']);
    Route::post('/user/cambioPass', [UserController::class, 'cambioPass']);
    Route::post('/user/permisos', [UserController::class, 'permisos']);
    Route::post('/user/guardarMenusPermisso', [UserController::class, 'guardarMenusPermisso']);
    Route::post('/user/actualizarUsuario', [UserController::class, 'actualizarUsuario']);
    Route::post('/user/eliminarUser', [UserController::class, 'eliminarUser']);



    // ROLES
    Route::get('/rol', [RolController::class, 'listado']);
    Route::post('/rol/guarda', [RolController::class, 'guarda']);
    Route::post('/rol/ajaxListado', [RolController::class, 'ajaxListado']);
    Route::post('/rol/eliminar', [RolController::class, 'eliminar']);
    Route::post('/rol/permisos', [RolController::class, 'permisos']);
    Route::post('/rol/guardarMenusPermisso', [RolController::class, 'guardarMenusPermisso']);

    // SERVICIOS
    Route::prefix('/servicio')->group(function(){
        Route::get('/', [ServicioController::class, 'listado']);
        Route::post('/guarda', [ServicioController::class, 'guarda']);
        Route::post('/ajaxListado', [ServicioController::class, 'ajaxListado']);
        Route::post('/eliminar', [ServicioController::class, 'eliminar']);
        Route::get('/producto', [ServicioController::class, 'producto']);
        Route::post('/ajaxListadoProducto', [ServicioController::class, 'ajaxListadoProducto']);
        Route::post('/guardaProdcuto', [ServicioController::class, 'guardaProdcuto']);
        Route::post('/cantidadAlmacen', [ServicioController::class, 'cantidadAlmacen']);
        Route::post('/ajaxListadoAsignaciones', [ServicioController::class, 'ajaxListadoAsignaciones']);
        Route::post('/guardarAsignacion', [ServicioController::class, 'guardarAsignacion']);
        Route::post('/agregarProdcuto', [ServicioController::class, 'agregarProdcuto']);
        Route::post('/eliminarProduto', [ServicioController::class, 'eliminarProduto']);
        Route::post('/eliminarAsignacion', [ServicioController::class, 'eliminarAsignacion']);
    });


    // CATEGORIAS
    Route::get('/categoria', [CategoriaController::class, 'listado']);
    Route::post('/categoria/guarda', [CategoriaController::class, 'guarda']);
    Route::post('/categoria/ajaxListado', [CategoriaController::class, 'ajaxListado']);
    Route::post('/categoria/eliminar', [CategoriaController::class, 'eliminar']);

    // CLIENTETS
    Route::get('/cliente', [ClienteController::class, 'listado']);
    Route::get('/cliente/nuevo', [ClienteController::class, 'nuevo']);
    Route::post('/cliente/guarda', [ClienteController::class, 'guarda']);
    Route::post('/cliente/ajaxListado', [ClienteController::class, 'ajaxListado']);
    Route::post('/cliente/ajaxListadoVehiculo', [ClienteController::class, 'ajaxListadoVehiculo']);
    Route::get('/cliente/perfil/{cliente_id}', [ClienteController::class, 'perfil']);
    Route::post('/cliente/actualizarUsuario', [ClienteController::class, 'actualizarUsuario']);
    // Route::post('/cliente/eliminar', [ClienteController::class, 'eliminar']);

    // VEHICULO
    Route::get('vehiculo', [VehiculoController::class, 'listado']);
    Route::post('vehiculo/ajaxListado', [VehiculoController::class, 'ajaxListado']);
    Route::post('vehiculo/ajaxRegistraVenta', [VehiculoController::class, 'ajaxRegistraVenta']);
    Route::post('vehiculo/eliminarVenta', [VehiculoController::class, 'eliminarVenta']);
    Route::get('vehiculo/imprimeNota/{pago_id}', [VehiculoController::class, 'imprimeNota']);
    Route::post('vehiculo/buscarVehiculo', [VehiculoController::class, 'buscarVehiculo']);
    Route::post('vehiculo/obtenerNitRazonSocial', [VehiculoController::class, 'obtenerNitRazonSocial']);
    Route::post('vehiculo/consultaPagosPorCobrar', [VehiculoController::class, 'consultaPagosPorCobrar']);
    Route::post('vehiculo/guarda', [VehiculoController::class, 'guarda']);
    Route::post('vehiculo/eliminarMovilidad', [VehiculoController::class, 'eliminarMovilidad']);



    // PAGOS
    Route::prefix('/pago')->group(function(){
        Route::get('/listado', [PagoController::class, 'listado']);
        Route::get('/detalle/{pago_id}', [PagoController::class, 'detalle']);
        Route::get('/porcobrar', [PagoController::class, 'porcobrar']);
        Route::get('/finanza', [PagoController::class, 'finanza']);
        Route::get('/infomearqueo', [PagoController::class, 'infomearqueo']);
        Route::get('/liquidacionNew', [PagoController::class, 'liquidacionNew']);
        Route::get('/liquidacionList', [PagoController::class, 'liquidacionList']);
        Route::get('/imprimeLiquidacionVendedor/{liquidacion_vendedor_pago_id}', [PagoController::class, 'imprimeLiquidacionVendedor']);
        Route::get('/imprimeTicked/{factura_id}/{vehiculo_id}', [PagoController::class, 'imprimeTicked']);


        Route::post('/ajaxListado', [PagoController::class, 'ajaxListado']);
        Route::post('/eliminarPago', [PagoController::class, 'eliminarPago']);
        Route::post('/emitirPorCobrar', [PagoController::class, 'emitirPorCobrar']);
        Route::post('/ajaxBuscarPorCobrar', [PagoController::class, 'ajaxBuscarPorCobrar']);
        Route::post('/ajaxServiciosMasa', [PagoController::class, 'ajaxServiciosMasa']);
        Route::post('/arrayCuotasPorCobrar', [PagoController::class, 'arrayCuotasPorCobrar']);
        Route::post('/pagarCuenta', [PagoController::class, 'pagarCuenta']);
        Route::post('/ajaxListadoFinanzas', [PagoController::class, 'ajaxListadoFinanzas']);
        Route::post('/guardarTipoIngresoSalida', [PagoController::class, 'guardarTipoIngresoSalida']);
        Route::post('/aperturaCaja', [PagoController::class, 'aperturaCaja']);
        Route::post('/ajaxListadoCajas', [PagoController::class, 'ajaxListadoCajas']);
        Route::post('/cierreCaja', [PagoController::class, 'cierreCaja']);
        Route::post('/buscarServicios', [PagoController::class, 'buscarServicios']);
        Route::post('/selecionarLavador', [PagoController::class, 'selecionarLavador']);
        Route::post('/buscarCuentasPorCobrar', [PagoController::class, 'buscarCuentasPorCobrar']);
        Route::post('/cancelarVendedor', [PagoController::class, 'cancelarVendedor']);
        Route::post('/verQueDebe', [PagoController::class, 'verQueDebe']);
    });

    // Route::post('/pago/ajaxListado', [PagoController::class, 'ajaxListado']);
    // Route::get('/pago', [PagoController::class, 'listado']);

    // MIGRACIONES
    Route::get('/migracion/migrarServicios', [MigracionController::class, 'migrarServicios']);
    Route::get('/migracion/migrarVehiculos', [MigracionController::class, 'migrarVehiculos']);
    Route::get('/migracion/migracionClienteVehiculo', [MigracionController::class, 'migracionClienteVehiculo']);
    Route::get('/migracion/migracionServicioLavador', [MigracionController::class, 'migracionServicioLavador']);

    // FACTURA
    Route::prefix('/factura')->group(function(){
        Route::get('/generaPdfFacturaNew/{factura_id}', [FacturaController::class, 'generaPdfFacturaNew']);
        Route::get('/pruebas', [FacturaController::class, 'pruebas']);
        Route::get('/emiteFacturaMasa', [FacturaController::class, 'emiteFacturaMasa']);
        Route::get('/imprimeFactura/{factura_id}', [FacturaController::class, 'imprimeFactura']);
        Route::get('/imprimeRecibo/{factura_id}', [FacturaController::class, 'imprimeRecibo']);
        Route::get('/imprimeTicked/{vehiculo_id}', [FacturaController::class, 'imprimeTicked']);

        Route::post('/verificaItemsGeneracion', [FacturaController::class, 'verificaItemsGeneracion']);
        Route::post('/arrayCuotasPagar', [FacturaController::class, 'arrayCuotasPagar']);
        Route::post('/actualizaDescuento', [FacturaController::class, 'actualizaDescuento']);
        Route::post('/sumaTotalMonto', [FacturaController::class, 'sumaTotalMonto']);
        Route::post('/emitirFactura', [FacturaController::class, 'emitirFactura']);
        Route::post('/anularFacturaNew', [FacturaController::class, 'anularFacturaNew']);
        Route::post('/muestraTableFacturaPaquete', [FacturaController::class, 'muestraTableFacturaPaquete']);
        Route::post('/mandarFacturasPaquete', [FacturaController::class, 'mandarFacturasPaquete']);
        Route::post('/sacaNumeroCafcUltimo', [FacturaController::class, 'sacaNumeroCafcUltimo']);
        Route::post('/sacaNumeroFactura', [FacturaController::class, 'sacaNumeroFactura']);
        Route::post('/verificaNit', [FacturaController::class, 'verificaNit']);
        Route::post('/emitirRecibo', [FacturaController::class, 'emitirRecibo']);
        Route::post('/anularRecibo', [FacturaController::class, 'anularRecibo']);
        Route::post('/recuperaFactura', [FacturaController::class, 'recuperaFactura']);
        Route::post('/enviarTrasferenciaFactura', [FacturaController::class, 'enviarTrasferenciaFactura']);
    });

    Route::prefix('/puntoVenta')->group(function () {
        Route::get('/listado', [PuntoVentaCotroller::class, 'listado']);
        Route::post('/ajaxListado', [PuntoVentaCotroller::class, 'ajaxListado']);
        Route::post('/guarda', [PuntoVentaCotroller::class, 'guarda']);
        Route::post('/eliminaPuntoVenta', [PuntoVentaCotroller::class, 'eliminaPuntoVenta']);
    });

    Route::prefix('/eventoSignificativo')->group(function () {
        Route::get('/listado', [EventoSignificativoController::class, 'listado']);
        Route::post('/consultaEventos', [EventoSignificativoController::class, 'consultaEventos']);
        Route::post('/registro', [EventoSignificativoController::class, 'registro']);
        Route::post('/buscarEventosSignificativos', [EventoSignificativoController::class, 'buscarEventosSignificativos']);
    });

    Route::prefix('/sincronizacionCatalogo')->group(function () {
        Route::get('/listado', [SincronizaCatalogo::class, 'listado']);
        Route::post('/ajaxListadoTipoDocumento', [SincronizaCatalogo::class, 'ajaxListadoTipoDocumento']);
        Route::post('/ajaxListadoMotivoAnulacion', [SincronizaCatalogo::class, 'ajaxListadoMotivoAnulacion']);
        Route::post('/ajaxListadoTipoEvento', [SincronizaCatalogo::class, 'ajaxListadoTipoEvento']);
        Route::post('/sincronizarTipoDocumento', [SincronizaCatalogo::class, 'sincronizarTipoDocumento']);
        Route::post('/sincronizarMotivoAnulacion', [SincronizaCatalogo::class, 'sincronizarMotivoAnulacion']);
        Route::post('/sincronizarTipoEvento', [SincronizaCatalogo::class, 'sincronizarTipoEvento']);
    });

    Route::prefix('/reporte')->group(function () {
        Route::get('/pagos', [ReporteController::class, 'pagos']);
        Route::get('/listado', [ReporteController::class, 'listado']);
        Route::post('/reporteCuentaPorCobrar', [ReporteController::class, 'reporteCuentaPorCobrar']);
        Route::post('/reporteInventario', [ReporteController::class, 'reporteInventario']);
        Route::post('/reporteInventarioExcel', [ReporteController::class, 'reporteInventarioExcel']);
        Route::post('/reporteInformeVenta', [ReporteController::class, 'reporteInformeVenta']);
        Route::post('/reporteLibroVenta', [ReporteController::class, 'reporteLibroVenta']);
        Route::post('/reporteInventarioGeneralSalida', [ReporteController::class, 'reporteInventarioGeneralSalida']);
        Route::post('/reporteInventarioGeneralIngreso', [ReporteController::class, 'reporteInventarioGeneralIngreso']);
    });

});

require __DIR__.'/auth.php';
