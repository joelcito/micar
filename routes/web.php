<?php

use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MigracionController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\ServicioController;
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
    return view('auth.login');
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
    Route::get('/servicio', [ServicioController::class, 'listado']);
    Route::post('/servicio/guarda', [ServicioController::class, 'guarda']);
    Route::post('/servicio/ajaxListado', [ServicioController::class, 'ajaxListado']);
    Route::post('/servicio/eliminar', [ServicioController::class, 'eliminar']);

    // CATEGORIAS
    Route::get('/categoria', [CategoriaController::class, 'listado']);
    Route::post('/categoria/guarda', [CategoriaController::class, 'guarda']);
    Route::post('/categoria/ajaxListado', [CategoriaController::class, 'ajaxListado']);
    Route::post('/categoria/eliminar', [CategoriaController::class, 'eliminar']);

    // CLIENTETS
    Route::get('/cliente', [ClienteController::class, 'listado']);
    Route::get('/cliente/nuevo', [ClienteController::class, 'nuevo']);
    // Route::post('/cliente/guarda', [ClienteController::class, 'guarda']);
    Route::post('/cliente/ajaxListado', [ClienteController::class, 'ajaxListado']);
    // Route::post('/cliente/eliminar', [ClienteController::class, 'eliminar']);

    // VEHICULO
    Route::get('/vehiculo', [VehiculoController::class, 'listado']);
    Route::post('/vehiculo/ajaxListado', [VehiculoController::class, 'ajaxListado']);
    Route::post('/vehiculo/ajaxRegistraVenta', [VehiculoController::class, 'ajaxRegistraVenta']);
    Route::post('/vehiculo/eliminarVenta', [VehiculoController::class, 'eliminarVenta']);
    Route::get('/vehiculo/imprimeNota/{pago_id}', [VehiculoController::class, 'imprimeNota']);
    Route::post('/vehiculo/buscarVehiculo', [VehiculoController::class, 'buscarVehiculo']);


    // PAGOS
    Route::prefix('/pago')->group(function(){
        Route::get('/listado', [PagoController::class, 'listado']);
        Route::post('/ajaxListado', [PagoController::class, 'ajaxListado']);
        Route::get('/detalle/{pago_id}', [PagoController::class, 'detalle']);
        Route::post('/eliminarPago', [PagoController::class, 'eliminarPago']);
    });

    // Route::post('/pago/ajaxListado', [PagoController::class, 'ajaxListado']);
    // Route::get('/pago', [PagoController::class, 'listado']);

    // MIGRACIONES
    Route::get('/migracion/migrarServicios', [MigracionController::class, 'migrarServicios']);
    Route::get('/migracion/migrarVehiculos', [MigracionController::class, 'migrarVehiculos']);

    // FACTURA
    Route::prefix('/factura')->group(function(){
        Route::post('/arrayCuotasPagar', [FacturaController::class, 'arrayCuotasPagar']);
        Route::post('/actualizaDescuento', [FacturaController::class, 'actualizaDescuento']);
        Route::post('/sumaTotalMonto', [FacturaController::class, 'sumaTotalMonto']);
        Route::post('/emitirFactura', [FacturaController::class, 'emitirFactura']);
        Route::post('/anularFacturaNew', [FacturaController::class, 'anularFacturaNew']);
        Route::get('/generaPdfFacturaNew/{factura_id}', [FacturaController::class, 'generaPdfFacturaNew']);




        // Route::post('/ajaxListado', [PagoController::class, 'ajaxListado']);
        // Route::get('/detalle/{pago_id}', [PagoController::class, 'detalle']);
        // Route::post('/eliminarPago', [PagoController::class, 'eliminarPago']);
    });



    // Route::prefix('/venta')->group(function () {
    // });

});

require __DIR__.'/auth.php';
