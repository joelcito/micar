<?php

use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\ServicioController;
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
    return view('welcome');
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

    // Route::prefix('/venta')->group(function () {
    // });

});

require __DIR__.'/auth.php';
