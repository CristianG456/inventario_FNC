@extends('layouts.inventario')

@section('title', 'Gestión de Mantenimientos')

@section('content')
<div class="page-header mb-4">
    <div>
        <h4 class="page-title">Gestión de Mantenimientos</h4>
        <p class="page-subtitle">Vista global de todas las hojas de vida y soporte técnico ({{ $registros->total() }} registros)</p>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card p-4 d-flex flex-row align-items-center justify-content-between h-100 shadow-sm border-0">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: #e0f2fe; color: #0284c7;">
                    <i class="bi bi-file-earmark-plus fs-4"></i>
                </div>
                <div class="fw-bold text-muted small text-uppercase">Creados</div>
            </div>
            <div class="fs-3 fw-bold">{{ $conteoCreados }}</div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card p-4 d-flex flex-row align-items-center justify-content-between h-100 shadow-sm border-0">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: #f3e8ff; color: #9333ea;">
                    <i class="bi bi-gear fs-4"></i>
                </div>
                <div class="fw-bold text-muted small text-uppercase">En Proceso</div>
            </div>
            <div class="fs-3 fw-bold">{{ $conteoProceso }}</div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card p-4 d-flex flex-row align-items-center justify-content-between h-100 shadow-sm border-0">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: #e0e7ff; color: #4f46e5;">
                    <i class="bi bi-pause-circle fs-4"></i>
                </div>
                <div class="fw-bold text-muted small text-uppercase">Suspendidos</div>
            </div>
            <div class="fs-3 fw-bold">{{ $conteosSuspendidos }}</div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card p-4 d-flex flex-row align-items-center justify-content-between h-100 shadow-sm border-0">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: #dcfce7; color: #16a34a;">
                    <i class="bi bi-check-circle fs-4"></i>
                </div>
                <div class="fw-bold text-muted small text-uppercase">Finalizados</div>
            </div>
            <div class="fs-3 fw-bold">{{ $conteoFinalizados }}</div>
        </div>
    </div>
</div>

<div class="card p-0">
    <div class="p-4 border-bottom border-light">
        <form action="{{ route('historial-tecnico.index') }}" method="GET" class="d-flex gap-3 align-items-center">
            <div class="search-bar flex-grow-1" style="max-width: 500px;">
                <i class="bi bi-search text-muted"></i>
                <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Busca por placa, descripción o caso...">
            </div>
            <button type="button" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="bi bi-funnel me-1"></i> Más Filtros
            </button>
        </form>
    </div>
    
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th class="ps-4">Activo</th>
                    <th>Tipo / Estado</th>
                    <th>Bitácora</th>
                    <th>Responsable</th>
                    <th>Fecha</th>
                    <th class="text-end pe-4">Acción</th>
                </tr>
            </thead>
            <tbody>
                @forelse($registros as $reg)
                <tr>
                    <td class="ps-4 fw-medium">
                        {{ $reg->equipo->nombre_equipo ?? 'N/A' }}
                        <br><small class="text-muted">{{ $reg->equipo->serial ?? '' }}</small>
                    </td>
                    <td>
                        <span class="badge bg-light text-dark border">{{ \Str::title(str_replace('_', ' ', $reg->tipo_evento)) }}</span><br>
                        <span class="badge mt-1 {{ 
                            $reg->estado == 'Finalizado' ? 'badge-success' : 
                            ($reg->estado == 'En proceso' ? 'badge-warning' : 
                            ($reg->estado == 'Suspendido' ? 'badge-danger' : 'badge-info')) 
                        }}">{{ $reg->estado }}</span>
                    </td>
                    <td class="text-muted" style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                        {{ $reg->descripcion }}
                    </td>
                    <td>{{ $reg->usuario_responsable }}</td>
                    <td>{{ \Carbon\Carbon::parse($reg->fecha_evento)->format('d M Y') }}</td>
                    <td class="text-end pe-4">
                        <a href="{{ route('historial-tecnico.show', $reg) }}" class="btn btn-sm btn-light rounded-circle"><i class="bi bi-eye"></i></a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        NO SE ENCONTRARON TICKETS REGISTRADOS
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($registros->hasPages())
    <div class="p-3 border-top">
        {{ $registros->links() }}
    </div>
    @endif
</div>
@endsection
