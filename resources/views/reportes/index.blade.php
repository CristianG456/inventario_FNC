@extends('layouts.inventario')

@section('title', 'Panel de Reportes')

@section('content')
<div class="page-header mb-4">
    <div>
        <h4 class="page-title">Panel de Reportes</h4>
        <p class="page-subtitle">Generación de informes de inventario TIC</p>
    </div>
</div>

<div class="row g-4">
    <!-- Card 1 -->
    <div class="col-md-4">
        <div class="card p-4 h-100 text-center shadow-sm border-0 transition-hover">
            <div class="mx-auto bg-light rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px; color: var(--primary-color);">
                <i class="bi bi-laptop fs-3"></i>
            </div>
            <h5 class="fw-bold">Inventario General</h5>
            <p class="text-muted small mb-4">Listado completo de equipos de cómputo, impresoras y periféricos activos.</p>
            <div class="mt-auto">
                <a href="{{ route('equipos.exportar') }}" class="btn btn-outline-danger rounded-pill px-4 btn-sm w-100"><i class="bi bi-file-earmark-excel me-1"></i> Descargar Excel</a>
            </div>
        </div>
    </div>
    
    <!-- Card 2 -->
    <div class="col-md-4">
        <div class="card p-4 h-100 text-center shadow-sm border-0">
            <div class="mx-auto bg-light rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px; color: var(--primary-color);">
                <i class="bi bi-person-badge fs-3"></i>
            </div>
            <h5 class="fw-bold">Activos por Funcionario</h5>
            <p class="text-muted small mb-4">Reporte de equipos asignados a cada empleado del Comité Tolima.</p>
            <div class="mt-auto">
                <a href="{{ route('reportes.activos-funcionario') }}" class="btn btn-outline-danger rounded-pill px-4 btn-sm w-100"><i class="bi bi-file-earmark-excel me-1"></i> Descargar Excel</a>
            </div>
        </div>
    </div>

    <!-- Card 3 -->
    <div class="col-md-4">
        <div class="card p-4 h-100 text-center shadow-sm border-0">
            <div class="mx-auto bg-light rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px; color: var(--primary-color);">
                <i class="bi bi-person-gear fs-3"></i>
            </div>
            <h5 class="fw-bold">Asignaciones / Movimientos</h5>
            <p class="text-muted small mb-4">Histórico de actas de entrega, devoluciones y traslados.</p>
            <div class="mt-auto">
                <a href="{{ route('reportes.asignaciones') }}" class="btn btn-outline-danger rounded-pill px-4 btn-sm w-100"><i class="bi bi-file-earmark-excel me-1"></i> Descargar Excel</a>
            </div>
        </div>
    </div>

    <!-- Card 4 -->
    <div class="col-md-4">
        <div class="card p-4 h-100 text-center shadow-sm border-0">
            <div class="mx-auto bg-light rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px; color: var(--primary-color);">
                <i class="bi bi-tools fs-3"></i>
            </div>
            <h5 class="fw-bold">Mantenimientos</h5>
            <p class="text-muted small mb-4">Registro de mantenimientos preventivos y correctivos ejecutados.</p>
            <div class="mt-auto">
                <a href="{{ route('reportes.mantenimientos') }}" class="btn btn-outline-danger rounded-pill px-4 btn-sm w-100"><i class="bi bi-file-earmark-excel me-1"></i> Descargar Excel</a>
            </div>
        </div>
    </div>

    <!-- Card 5 -->
    <div class="col-md-4">
        <div class="card p-4 h-100 text-center shadow-sm border-0">
            <div class="mx-auto bg-light rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px; color: var(--primary-color);">
                <i class="bi bi-shield-check fs-3"></i>
            </div>
            <h5 class="fw-bold">Garantías</h5>
            <p class="text-muted small mb-4">Equipos con garantía vigente y próximos a expirar.</p>
            <div class="mt-auto">
                <a href="{{ route('reportes.garantias') }}" class="btn btn-outline-danger rounded-pill px-4 btn-sm w-100"><i class="bi bi-file-earmark-excel me-1"></i> Descargar Excel</a>
            </div>
        </div>
    </div>

    <!-- Card 6 -->
    <div class="col-md-4">
        <div class="card p-4 h-100 text-center shadow-sm border-0">
            <div class="mx-auto bg-light rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px; color: var(--primary-color);">
                <i class="bi bi-pie-chart fs-3"></i>
            </div>
            <h5 class="fw-bold">Estadísticas</h5>
            <p class="text-muted small mb-4">Métricas generales de la plataforma y hardware del sistema.</p>
            <div class="mt-auto">
                <a href="{{ route('reportes.estadisticas-pdf') }}" target="_blank" class="btn btn-outline-danger rounded-pill px-4 btn-sm w-100"><i class="bi bi-file-earmark-pdf me-1"></i> Generar PDF</a>
            </div>
        </div>
    </div>
</div>
@endsection
