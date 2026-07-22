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
use App\Http\Controllers\LicenciaSerialController;
use App\Http\Controllers\LicenciaAsignacionController;
use App\Http\Controllers\LicenciaHistorialController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuditController;

// Ruta raíz redirige a la pantalla principal (o al login si no está autenticado)
Route::get('/', function () {
    return redirect()->route('inicio');
});

// === Rutas protegidas por autenticación ===
Route::middleware(['auth', 'verified', 'prevent-back-history'])->group(function () {

    // Pantalla principal
    Route::get('/inicio', [DashboardController::class, 'index'])->name('inicio')->middleware('permission:dashboard.ver');
    Route::get('/inicia', [DashboardController::class, 'index'])->name('inicia')->middleware('permission:dashboard.ver');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('permission:dashboard.ver');

    // Perfil (generado por Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ====== CAMPOS PERSONALIZADOS ======
    Route::resource('campos-personalizados', \App\Http\Controllers\CampoPersonalizadoController::class)
        ->except(['show', 'create', 'edit'])
        ->middleware('permission:campos_personalizados.ver');
    Route::post('/campos-personalizados/reorder', [\App\Http\Controllers\CampoPersonalizadoController::class, 'reorder'])
        ->name('campos-personalizados.reorder')
        ->middleware('permission:campos_personalizados.editar');

    // Módulo Usuarios y Asignación de Roles (solo administradores o con permiso)
    Route::resource('usuarios', UserController::class)->middleware('permission:roles.ver');
    
    // Módulo de Roles y Permisos (solo administradores o con permiso)
    Route::resource('roles', RoleController::class)->middleware('permission:roles.ver');
    
    // Módulo de Auditoría
    Route::get('/auditoria', [AuditController::class, 'index'])->name('auditoria.index')->middleware('permission:roles.ver');

    // Exportar debe declararse ANTES del resource para evitar conflicto con 'show'
    Route::get('/equipos/exportar', [EquipoController::class, 'exportar'])->name('equipos.exportar')->middleware('permission:equipos.exportar');

    // Importación y Exportación de Equipos
    Route::get('/equipos/importar', [EquipoController::class, 'importarForm'])->name('equipos.importar.form')->middleware('permission:equipos.importar');
    Route::post('/equipos/importar', [EquipoController::class, 'importar'])->name('equipos.importar')->middleware('permission:equipos.importar');
    Route::get('/equipos/{equipo}/acta', [EquipoController::class, 'descargarActa'])->name('equipos.acta')->middleware('permission:equipos.ver');

    // Historial de vida del equipo (ANTES del resource)
    Route::get('/equipos/{equipo}/historial-vida', [EquipoController::class, 'historialVida'])
        ->name('equipos.historial-vida')->middleware('permission:equipos.ver');

    // Equipos (CRUD) con permisos por acción
    Route::resource('equipos', EquipoController::class)
        ->middlewareFor(['index', 'show'], 'permission:equipos.ver')
        ->middlewareFor(['create', 'store'], 'permission:equipos.crear')
        ->middlewareFor(['edit', 'update'], 'permission:equipos.editar')
        ->middlewareFor(['destroy'], 'permission:equipos.eliminar');

    // ── Asignaciones ──────────────────────────────────────────────────────────
    Route::get('/asignaciones', [AsignacionController::class, 'index'])
        ->name('asignaciones.index')->middleware('permission:equipos.ver');

    Route::post('/asignaciones', [AsignacionController::class, 'store'])
        ->name('asignaciones.store')->middleware('permission:equipos.crear');

    Route::get('/asignaciones/funcionarios-elegibles', [AsignacionController::class, 'funcionariosElegibles'])
        ->name('asignaciones.funcionarios-elegibles')->middleware('permission:equipos.ver');

    Route::get('/asignaciones/{asignacion}', [AsignacionController::class, 'show'])
        ->name('asignaciones.show')->middleware('permission:equipos.ver');

    Route::get('/equipos/{equipo}/asignaciones', [AsignacionController::class, 'porEquipo'])
        ->name('asignaciones.por-equipo')->middleware('permission:equipos.ver');

    Route::get('/asignaciones/{asignacion}/pdf', [AsignacionController::class, 'generarPdf'])
        ->name('asignaciones.pdf')->middleware('permission:equipos.exportar');

    // ── Actas Firmadas ────────────────────────────────────────────────────────
    Route::get('/actas-firmadas', [\App\Http\Controllers\ActaFirmadaController::class, 'index'])->name('actas-firmadas.index')->middleware('permission:equipos.ver');
    Route::post('/actas-firmadas', [\App\Http\Controllers\ActaFirmadaController::class, 'store'])->name('actas-firmadas.store')->middleware('permission:equipos.crear');
    Route::put('/actas-firmadas/{id}', [\App\Http\Controllers\ActaFirmadaController::class, 'update'])->name('actas-firmadas.update')->middleware('permission:equipos.crear');
    Route::delete('/actas-firmadas/{id}', [\App\Http\Controllers\ActaFirmadaController::class, 'destroy'])->name('actas-firmadas.destroy')->middleware('permission:equipos.crear');
    Route::get('/actas-firmadas/{id}/download', [\App\Http\Controllers\ActaFirmadaController::class, 'download'])->name('actas-firmadas.download')->middleware('permission:equipos.ver');
    Route::get('/actas-firmadas/versions/{id}/download', [\App\Http\Controllers\ActaFirmadaController::class, 'downloadVersion'])->name('actas-firmadas.download-version')->middleware('permission:equipos.ver');
    Route::get('/actas-firmadas/{id}/history', [\App\Http\Controllers\ActaFirmadaController::class, 'history'])->name('actas-firmadas.history')->middleware('permission:equipos.ver');

    // ── Historial Técnico ─────────────────────────────────────────────────────
    Route::get('/equipos/{equipo}/historial-tecnico', [HistorialTecnicoController::class, 'porEquipo'])
        ->name('historial-tecnico.por-equipo')->middleware('permission:historial.ver');

    Route::resource('historial-tecnico', HistorialTecnicoController::class)
        ->parameters(['historial-tecnico' => 'historialTecnico'])->middleware('permission:historial.ver');

    // ── Historial Administrativo (solo lectura) ───────────────────────────────
    Route::get('/historial-administrativo', [HistorialAdministrativoController::class, 'index'])
        ->name('historial-administrativo.index')->middleware('permission:historial.ver');

    Route::get('/equipos/{equipo}/historial-administrativo', [HistorialAdministrativoController::class, 'porEquipo'])
        ->name('historial-administrativo.por-equipo')->middleware('permission:historial.ver');

    // ── Plantillas PDF ────────────────────────────────────────────────────────
    Route::resource('plantillas-pdf', PlantillaPdfController::class)
        ->parameters(['plantillas-pdf' => 'plantillasPdf'])->middleware('permission:configuracion.editar');

    // ── Tipos de Recurso ──────────────────────────────────────────────────────
    Route::resource('tipo-recursos', TipoRecursoController::class)->middleware('permission:configuracion.editar');

    // ── Checklists ────────────────────────────────────────────────────────────
    Route::resource('checklists', ChecklistController::class)->middleware('permission:checklist.ver');

    // ── Funcionarios ──────────────────────────────────────────────────────────
    Route::resource('funcionarios', FuncionarioController::class)
        ->only(['index', 'create', 'store', 'show'])
        ->middleware('permission:usuarios.ver');
    Route::get('/funcionarios/{funcionario}/edit', [FuncionarioController::class, 'edit'])
        ->name('funcionarios.edit')
        ->middleware('permission:usuarios.editar');
    Route::put('/funcionarios/{funcionario}', [FuncionarioController::class, 'update'])
        ->name('funcionarios.update')
        ->middleware('permission:usuarios.editar');
    Route::post('/funcionarios/{funcionario}/autorizaciones', [FuncionarioController::class, 'storeAutorizacion'])
        ->name('funcionarios.autorizaciones.store')
        ->middleware('permission:usuarios.crear');
    Route::patch('/funcionarios/{funcionario}/autorizaciones/{autorizacion}/anular', [FuncionarioController::class, 'anularAutorizacion'])
        ->name('funcionarios.autorizaciones.anular')
        ->middleware('permission:usuarios.editar');

    // ── HelpDesk (Tickets) ────────────────────────────────────────────────────
    Route::resource('tickets', TicketController::class)->middleware('permission:mesaayuda.ver');

    // ── Reportes ──────────────────────────────────────────────────────────────
    Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index')->middleware('permission:dashboard.ver');
    Route::get('/reportes/activos-funcionario', [ReporteController::class, 'activosPorFuncionario'])->name('reportes.activos-funcionario')->middleware('permission:dashboard.ver');
    Route::get('/reportes/asignaciones', [ReporteController::class, 'asignaciones'])->name('reportes.asignaciones')->middleware('permission:dashboard.ver');
    Route::get('/reportes/mantenimientos', [ReporteController::class, 'mantenimientos'])->name('reportes.mantenimientos')->middleware('permission:dashboard.ver');
    Route::get('/reportes/garantias', [ReporteController::class, 'garantias'])->name('reportes.garantias')->middleware('permission:dashboard.ver');
    Route::get('/reportes/estadisticas-pdf', [ReporteController::class, 'estadisticasPdf'])->name('reportes.estadisticas-pdf')->middleware('permission:dashboard.ver');

    // ── Licencias ─────────────────────────────────────────────────────────────
    Route::get('/licencias/reportes', [LicenciaController::class, 'reportes'])->name('licencias.reportes')->middleware('permission:licencias.ver');
    Route::get('/licencias/exportar', [LicenciaController::class, 'exportar'])->name('licencias.exportar')->middleware('permission:licencias.exportar');
    Route::get('/licencias/historial', [LicenciaHistorialController::class, 'index'])->name('licencias.historial')->middleware('permission:licencias.ver');
    Route::resource('licencias', LicenciaController::class)->middleware('permission:licencias.ver');
    Route::resource('licencias.seriales', LicenciaSerialController::class)->only(['store', 'update', 'destroy'])->middleware('permission:licencias.editar');
    Route::resource('licencia-asignaciones', LicenciaAsignacionController::class)
        ->parameters(['licencia-asignaciones' => 'licencia_asignacion'])
        ->middleware('permission:licencias.editar');

    // ── Suscripciones y Vitalicias (Redirigen al módulo unificado) ────────────
    Route::redirect('/suscripciones', '/licencias');
    Route::redirect('/vitalicias', '/licencias');


    Route::get('/test-logo', function() {
        $pngPath = public_path('imagenes/federacion cafeteros logo.png');
        if (!file_exists($pngPath)) {
            return 'NOT_FOUND: ' . $pngPath;
        }
        return 'EXISTS. Base64 length: ' . strlen(base64_encode(file_get_contents($pngPath)));
    });

    Route::get('/instalar-plantilla-fnc', function() {
        \App\Models\PlantillaPdf::where('activa', true)->update(['activa' => false]);

        $html = '<style>
    @page { margin: 15px 20px; size: letter landscape; font-family: Arial, sans-serif; font-size: 10px; }
    body { font-family: Arial, sans-serif; font-size: 10px; color: #000; padding: 0; }
    table { width: 100%; border-collapse: collapse; }
    td, th { border: 1px solid #000; padding: 3px; text-align: center; vertical-align: middle; }
    .no-border, .no-border td { border: none !important; }
    .header-table { margin-bottom: 8px; }
    .header-table td { font-weight: bold; }
    .logo-cell { width: 15%; padding: 2px; }
    .title-cell { width: 65%; font-size: 13px; }
    .meta-cell { width: 20%; font-size: 9px; text-align: left; padding-left: 5px; }
    .info-section { margin-bottom: 8px; }
    .info-section td { text-align: center; }
    .info-label { font-weight: bold; font-size: 10px; display: inline-block; margin-right: 5px; }
    .info-box { border: 1px solid #000; padding: 2px 10px; display: inline-block; min-width: 100px; text-align: center; font-weight: normal; }
    .main-table { margin-bottom: 8px; }
    .main-table th { background-color: #d9d9d9; font-weight: bold; font-size: 9px; }
    .main-table td { font-size: 9px; height: 16px; text-transform: uppercase; }
    .text-na { color: #888; text-transform: none !important; }
    .fw-bold { font-weight: bold; }
    .signatures { margin-top: 15px; width: 100%; page-break-inside: avoid; }
    .sig-box { border: 1px solid #000; text-align: center; padding: 3px; margin-bottom: 2px; font-weight: bold; font-size: 9px; min-height: 12px; text-transform: uppercase; }
    .sig-label { text-align: center; font-weight: bold; font-size: 9px; margin-bottom: 8px; }
    .sig-col { width: 45%; }
    .sig-spacer { width: 10%; }
</style>

<table class="header-table">
    <tr>
        <td rowspan="2" class="logo-cell"><img src="{{logo_fnc}}" alt="Logo" width="165" height="110" style="object-fit: contain;"></td>
        <td class="title-cell">FEDERACIÓN NACIONAL DE CAFETEROS DE COLOMBIA</td>
        <td class="meta-cell">Código: FE-BS-F-0069<br><br>Fecha: 11/04/2017</td>
    </tr>
    <tr>
        <td class="title-cell">NOVEDAD DE ACTIVO</td>
        <td class="meta-cell">Versión: 1</td>
    </tr>
</table>

<table class="no-border info-section">
    <tr>
        <td style="width: 33%;">
            <span class="info-label">Tipo de Novedad</span>
            <div class="info-box" style="color: #666;">Inventario Físico</div>
        </td>
        <td style="width: 33%;">
            <span class="info-label">Ubicación:</span>
            <div class="info-box">{{ciudad}}</div>
        </td>
        <td style="width: 33%;">
            <span class="info-label">Fecha:</span>
            <div class="info-box">{{fecha_generacion}}</div>
        </td>
    </tr>
</table>

<table class="main-table">
    <thead>
        <tr>
            <th style="width: 10%;">Activo fijo</th>
            <th style="width: 6%;">SN°</th>
            <th style="width: 22%;">Denominación del activo fijo</th>
            <th style="width: 10%;">Marca</th>
            <th style="width: 12%;">Modelo</th>
            <th style="width: 12%;">Serie</th>
            <th style="width: 10%;">Placa de inventario</th>
            <th style="width: 18%;">Observaciones</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="text-na">N/A</td>
            <td class="text-na">N/A</td>
            <td class="fw-bold">{{tipo_recurso}}</td>
            <td class="fw-bold">{{marca}}</td>
            <td class="fw-bold">{{modelo}}</td>
            <td class="fw-bold">{{serial}}</td>
            <td class="fw-bold">{{activo_fijo}}</td>
            <td class="fw-bold">ENTREGA DE EQUIPO</td>
        </tr>
        <tr><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td></tr>
        <tr><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td></tr>
        <tr><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td></tr>
        <tr><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td></tr>
        <tr><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td></tr>
        <tr><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td></tr>
        <tr><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td></tr>
        <tr><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td></tr>
        <tr><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td></tr>
    </tbody>
</table>

<table class="no-border signatures">
    <tr>
        <td class="sig-col" style="vertical-align: top;">
            <div class="sig-box">TI</div>
            <div class="sig-label">DEPENDENCIA</div>

            <div class="sig-box" style="margin-top: 15px;">{{usuario_sistema}}</div>
            <div class="sig-label">NOMBRE Y FIRMA DE QUIEN ENTREGA</div>

            <table class="no-border" style="width: 100%; margin-top: 15px;">
                <tr>
                    <td style="width: 30%; padding: 0 5px 0 0;">
                        <div class="sig-box"></div>
                        <div class="sig-label" style="margin-bottom: 0;">Cod. Personal</div>
                    </td>
                    <td style="width: 70%; padding: 0 0 0 5px;">
                        <div class="sig-box">ANALISTA TIC</div>
                        <div class="sig-label" style="margin-bottom: 0;">Cargo</div>
                    </td>
                </tr>
            </table>
        </td>
        
        <td class="sig-spacer"></td>

        <td class="sig-col" style="vertical-align: top;">
            <div class="sig-box">{{seccional}}</div>
            <div class="sig-label">DEPENDENCIA</div>

            <div class="sig-box" style="margin-top: 15px;">{{nombre_usuario}}</div>
            <div class="sig-label">NOMBRE Y FIRMA DE QUIEN RECIBE</div>

            <table class="no-border" style="width: 100%; margin-top: 15px;">
                <tr>
                    <td style="width: 30%; padding: 0 5px 0 0;">
                        <div class="sig-box"></div>
                        <div class="sig-label" style="margin-bottom: 0;">Cod. Personal</div>
                    </td>
                    <td style="width: 70%; padding: 0 0 0 5px;">
                        <div class="sig-box">{{cargo}}</div>
                        <div class="sig-label" style="margin-bottom: 0;">Cargo</div>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>';
        
        \App\Models\PlantillaPdf::create([
            'nombre' => 'Acta Oficial FNC',
            'tipo' => 'acta_entrega',
            'contenido' => $html,
            'activa' => true,
            'user_id' => auth()->id() ?? 1
        ]);
        return redirect()->route('plantillas-pdf.index')->with('success', 'Plantilla Oficial FNC instalada y activada. La anterior fue desactivada.');
    });
});

require __DIR__ . '/auth.php';
