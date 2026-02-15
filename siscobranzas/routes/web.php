<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PatenteController;
use App\Http\Controllers\ContribuyenteController;
use App\Http\Controllers\InspeccionesController;
use App\Http\Controllers\PagoPatenteController;
use App\Http\Controllers\ObservacionesController;

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


use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

Route::get('/', function (Request $request) {
    if (!Auth::check()) {
        return redirect()->route('login');
    }
    return redirect()->route('patentes.index');
});

Route::get('/patente', [PatenteController::class, 'index'])->name('patentes.index');
Route::get('/patente/{numero_patente}', [PatenteController::class, 'show'])->name('patentes.show');
Route::post('/patentes', [PatenteController::class, 'store'])->name('patentes.store');

Route::get('/inspecciones', [InspeccionesController::class, 'create'])->name('inspecciones.create');
Route::post('/inspecciones', [InspeccionesController::class, 'store'])->name('inspecciones.store');


// 📌 Listado + edición de inspecciones
// Registrar inspecciones (ya existente)
Route::get('/inspecciones', [InspeccionesController::class, 'index'])->name('inspecciones.index');

Route::post('/inspecciones', [InspeccionesController::class, 'store'])->name('inspecciones.store');


// 🟡 Nueva vista: gestión / edición
Route::get('/inspecciones/editar', [InspeccionesController::class, 'lista'])->name('inspecciones.editar');


// ✏️ Editar inspección
Route::put('/inspecciones/{id}', [InspeccionesController::class, 'update'])->name('inspecciones.update');



// 📝 Agregar observación
Route::post('/inspecciones/{id}/observaciones', [InspeccionesController::class, 'storeObservacion'])->name('inspecciones.observaciones.store');

// 📌 Actualizar inspección (estado / fecha)
Route::post('/inspecciones/{id}/actualizar', [InspeccionesController::class, 'update']) ->name('inspecciones.update');

// 📌 Guardar observaciones
Route::post('/inspecciones/{id}/observaciones', [InspeccionesController::class, 'guardarObservaciones'])->name('inspecciones.observaciones');



// Buscar patente desde la vista
Route::get('/pagos/buscar', [PagoPatenteController::class, 'buscarPatente'])->name('pagos.buscar');

// Mostrar formulario contribuyente
Route::get('/contribuyente', [ContribuyenteController::class, 'create'])->name('contribuyentes.create');
// Enviar formulario al API
Route::post('/contribuyente', [ContribuyenteController::class, 'store'])->name('contribuyentes.store');

// Rutas para observaciones (GET -> mostrar, POST -> crear)
Route::get('/observaciones', [ObservacionesController::class, 'index'])->name('observaciones.index');
Route::post('/observaciones', [ObservacionesController::class, 'store'])->name('observaciones.store');

// Endpoints internos para combos/selects
Route::get('/estados-patente/select', [\App\Http\Controllers\EstadoPatenteController::class, 'getEstados']);
Route::get('/tipos-patente/select', [\App\Http\Controllers\TipoPatenteController::class, 'getForSelect']);
Route::get('/contribuyentes/select', [\App\Http\Controllers\ContribuyenteController::class, 'getForSelect']);

Auth::routes();

// Redirigir /home a la vista de patentes
Route::get('/home', function () {
    return redirect()->route('patentes.index');
})->name('home');
Route::get('/patentes/{id}/pdf', [PatenteController::class, 'generarPdf'])->name('patentes.pdf');
Route::post('/patentes/importar', [PatenteController::class, 'importExcel']) ->name('patentes.import-excel');
Route::put('/patentes/{id}/estado', [PatenteController::class, 'updateEstado'])->name('patentes.updateEstado');
Route::put('/patentes/{id}/editar', [PatenteController::class, 'updateDatos']) ->name('patentes.update');