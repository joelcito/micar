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

    Route::get('/venta', [VentaController::class, 'listado']);
    Route::post('/venta/guarda', [VentaController::class, 'guarda']);
    Route::post('/venta/pagar', [VentaController::class, 'pagar']);

    // USUARIOS
    Route::get('/user', [UserController::class, 'listado']);
    Route::post('/user/ajaxListado', [UserController::class, 'ajaxListado']);
    Route::post('/user/guarda', [UserController::class, 'guarda']);

    // ROLES
    Route::get('/rol', [RolController::class, 'listado']);
    Route::post('/rol/guarda', [RolController::class, 'guarda']);
    Route::post('/rol/ajaxListado', [RolController::class, 'ajaxListado']);
    Route::post('/rol/eliminar', [RolController::class, 'eliminar']);

    // SERVICIOS
    Route::prefix('/servicio')->group(function(){
        Route::get('/', [ServicioController::class, 'listado']);
        Route::post('/guarda', [ServicioController::class, 'guarda']);
        Route::post('/ajaxListado', [ServicioController::class, 'ajaxListado']);
        Route::post('/eliminar', [ServicioController::class, 'eliminar']);
        Route::get('/producto', [ServicioController::class, 'producto']);
        Route::post('/ajaxListadoProducto', [ServicioController::class, 'ajaxListadoProducto']);
        Route::post('/guardaProdcuto', [ServicioController::class, 'guardaProdcuto']);

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



    // PAGOS
    Route::prefix('/pago')->group(function(){
        Route::get('/listado', [PagoController::class, 'listado']);
        Route::post('/ajaxListado', [PagoController::class, 'ajaxListado']);
        Route::get('/detalle/{pago_id}', [PagoController::class, 'detalle']);
        Route::post('/eliminarPago', [PagoController::class, 'eliminarPago']);
        Route::post('/emitirPorCobrar', [PagoController::class, 'emitirPorCobrar']);
        Route::get('/porcobrar', [PagoController::class, 'porcobrar']);
        Route::post('/ajaxBuscarPorCobrar', [PagoController::class, 'ajaxBuscarPorCobrar']);
        Route::post('/ajaxServiciosMasa', [PagoController::class, 'ajaxServiciosMasa']);
        Route::post('/arrayCuotasPorCobrar', [PagoController::class, 'arrayCuotasPorCobrar']);
        Route::post('/pagarCuenta', [PagoController::class, 'pagarCuenta']);
        Route::get('/finanza', [PagoController::class, 'finanza']);
        Route::post('/ajaxListadoFinanzas', [PagoController::class, 'ajaxListadoFinanzas']);
    });

    // Route::post('/pago/ajaxListado', [PagoController::class, 'ajaxListado']);
    // Route::get('/pago', [PagoController::class, 'listado']);

    // MIGRACIONES
    Route::get('/migracion/migrarServicios', [MigracionController::class, 'migrarServicios']);
    Route::get('/migracion/migrarVehiculos', [MigracionController::class, 'migrarVehiculos']);
    Route::get('/migracion/migracionClienteVehiculo', [MigracionController::class, 'migracionClienteVehiculo']);

    // FACTURA
    Route::prefix('/factura')->group(function(){
        Route::post('/arrayCuotasPagar', [FacturaController::class, 'arrayCuotasPagar']);
        Route::post('/actualizaDescuento', [FacturaController::class, 'actualizaDescuento']);
        Route::post('/sumaTotalMonto', [FacturaController::class, 'sumaTotalMonto']);
        Route::post('/emitirFactura', [FacturaController::class, 'emitirFactura']);
        Route::post('/anularFacturaNew', [FacturaController::class, 'anularFacturaNew']);
        Route::get('/generaPdfFacturaNew/{factura_id}', [FacturaController::class, 'generaPdfFacturaNew']);
        Route::post('/muestraTableFacturaPaquete', [FacturaController::class, 'muestraTableFacturaPaquete']);
        Route::post('/mandarFacturasPaquete', [FacturaController::class, 'mandarFacturasPaquete']);
        Route::post('/sacaNumeroCafcUltimo', [FacturaController::class, 'sacaNumeroCafcUltimo']);
        Route::post('/sacaNumeroFactura', [FacturaController::class, 'sacaNumeroFactura']);
        Route::get('/pruebas', [FacturaController::class, 'pruebas']);
        Route::get('/emiteFacturaMasa', [FacturaController::class, 'emiteFacturaMasa']);
        Route::get('/imprimeFactura/{factura_id}', [FacturaController::class, 'imprimeFactura']);
        Route::post('/verificaNit', [FacturaController::class, 'verificaNit']);
        Route::post('/emitirRecibo', [FacturaController::class, 'emitirRecibo']);
        Route::get('/imprimeRecibo/{factura_id}', [FacturaController::class, 'imprimeRecibo']);
        Route::post('/anularRecibo', [FacturaController::class, 'anularRecibo']);
        Route::get('/imprimeTicked/{vehiculo_id}', [FacturaController::class, 'imprimeTicked']);
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
    });

});

require __DIR__.'/auth.php';
