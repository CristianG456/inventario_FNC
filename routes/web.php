<?php

use App\Http\Controllers\AsignacionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EquipoController;
use App\Http\Controllers\HistorialAdministrativoController;
use App\Http\Controllers\HistorialTecnicoController;
use App\Http\Controllers\PlantillaPdfController;
use App\Http\Controllers\TipoRecursoController;
use App\Http\Controllers\ChecklistController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FuncionarioController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\LicenciaController;
use App\Http\Controllers\LicenciaAsignacionController;
use App\Http\Controllers\LicenciaHistorialController;
use Illuminate\Support\Facades\Route;

// Ruta raíz redirige al dashboard (o al login si no está autenticado)
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// === Rutas protegidas por autenticación ===
Route::middleware(['auth', 'verified', 'prevent-back-history'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Perfil (generado por Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Exportar debe declararse ANTES del resource para evitar conflicto con 'show'
    Route::get('/equipos/exportar', [EquipoController::class, 'exportar'])->name('equipos.exportar');

    // Importación y Exportación de Equipos
    Route::get('/equipos/importar', [EquipoController::class, 'importarForm'])->name('equipos.importar.form');
    Route::post('/equipos/importar', [EquipoController::class, 'importar'])->name('equipos.importar');
    Route::get('/equipos/{equipo}/acta', [EquipoController::class, 'descargarActa'])->name('equipos.acta');

    // Historial de vida del equipo (ANTES del resource)
    Route::get('/equipos/{equipo}/historial-vida', [EquipoController::class, 'historialVida'])
        ->name('equipos.historial-vida');

    // Equipos (CRUD)
    Route::resource('equipos', EquipoController::class);

    // ── Asignaciones ──────────────────────────────────────────────────────────
    Route::get('/asignaciones', [AsignacionController::class, 'index'])
        ->name('asignaciones.index');

    Route::post('/asignaciones', [AsignacionController::class, 'store'])
        ->name('asignaciones.store');

    Route::get('/asignaciones/{asignacion}', [AsignacionController::class, 'show'])
        ->name('asignaciones.show');

    Route::get('/equipos/{equipo}/asignaciones', [AsignacionController::class, 'porEquipo'])
        ->name('asignaciones.por-equipo');

    Route::get('/asignaciones/{asignacion}/pdf', [AsignacionController::class, 'generarPdf'])
        ->name('asignaciones.pdf');

    // ── Historial Técnico ─────────────────────────────────────────────────────
    Route::get('/equipos/{equipo}/historial-tecnico', [HistorialTecnicoController::class, 'porEquipo'])
        ->name('historial-tecnico.por-equipo');

    Route::resource('historial-tecnico', HistorialTecnicoController::class)
        ->parameters(['historial-tecnico' => 'historialTecnico']);

    // ── Historial Administrativo (solo lectura) ───────────────────────────────
    Route::get('/historial-administrativo', [HistorialAdministrativoController::class, 'index'])
        ->name('historial-administrativo.index');

    Route::get('/equipos/{equipo}/historial-administrativo', [HistorialAdministrativoController::class, 'porEquipo'])
        ->name('historial-administrativo.por-equipo');

    // ── Plantillas PDF ────────────────────────────────────────────────────────
    Route::resource('plantillas-pdf', PlantillaPdfController::class)
        ->parameters(['plantillas-pdf' => 'plantillasPdf']);

    // ── Tipos de Recurso ──────────────────────────────────────────────────────
    Route::resource('tipo-recursos', TipoRecursoController::class);

    // ── Checklists ────────────────────────────────────────────────────────────
    Route::resource('checklists', ChecklistController::class);

    // ── Funcionarios ──────────────────────────────────────────────────────────
    Route::resource('funcionarios', FuncionarioController::class)->only(['index', 'create', 'store']);

    // ── HelpDesk (Tickets) ────────────────────────────────────────────────────
    Route::resource('tickets', TicketController::class);

    // ── Reportes ──────────────────────────────────────────────────────────────
    Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
    Route::get('/reportes/activos-funcionario', [ReporteController::class, 'activosPorFuncionario'])->name('reportes.activos-funcionario');
    Route::get('/reportes/asignaciones', [ReporteController::class, 'asignaciones'])->name('reportes.asignaciones');
    Route::get('/reportes/mantenimientos', [ReporteController::class, 'mantenimientos'])->name('reportes.mantenimientos');
    Route::get('/reportes/garantias', [ReporteController::class, 'garantias'])->name('reportes.garantias');
    Route::get('/reportes/estadisticas-pdf', [ReporteController::class, 'estadisticasPdf'])->name('reportes.estadisticas-pdf');

    // ── Licencias ─────────────────────────────────────────────────────────────
    Route::get('/licencias/reportes', [LicenciaController::class, 'reportes'])->name('licencias.reportes');
    Route::get('/licencias/exportar', [LicenciaController::class, 'exportar'])->name('licencias.exportar');
    Route::get('/licencias/historial', [LicenciaHistorialController::class, 'index'])->name('licencias.historial');
    Route::resource('licencias', LicenciaController::class);
    Route::resource('licencia-asignaciones', LicenciaAsignacionController::class);
});

require __DIR__ . '/auth.php';
