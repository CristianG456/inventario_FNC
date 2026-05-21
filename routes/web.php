<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EquipoController;
use App\Http\Controllers\TipoRecursoController;
use App\Http\Controllers\ChecklistController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Ruta raíz redirige al dashboard (o al login si no está autenticado)
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// === Rutas protegidas por autenticación ===
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Perfil (generado por Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Exportar debe declararse ANTES del resource para evitar conflicto con 'show'
    Route::get('/equipos/exportar', [EquipoController::class, 'exportar'])->name('equipos.exportar');

    // Importar (GET = formulario, POST = procesar)
    Route::get('/equipos/importar',  [EquipoController::class, 'importarForm'])->name('equipos.importar.form');
    Route::post('/equipos/importar', [EquipoController::class, 'importar'])->name('equipos.importar');

    // Equipos (CRUD)
    Route::resource('equipos', EquipoController::class);

    // Tipos de Recurso (CRUD)
    Route::resource('tipo-recursos', TipoRecursoController::class);

    // Checklists (CRUD)
    Route::resource('checklists', ChecklistController::class);
});

require __DIR__ . '/auth.php';

