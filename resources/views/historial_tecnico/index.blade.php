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
    <div class="col-12 col-md-3">
        <div class="card p-4 d-flex flex-row align-items-center justify-content-between h-100 shadow-sm border-0">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center maint-icon-wrapper maint-bg-info">
                    <i class="bi bi-file-earmark-plus fs-4"></i>
                </div>
                <div class="fw-bold text-muted small text-uppercase">Creados</div>
            </div>
            <div class="fs-3 fw-bold">{{ $conteoCreados }}</div>
        </div>
    </div>
    
    <div class="col-12 col-md-3">
        <div class="card p-4 d-flex flex-row align-items-center justify-content-between h-100 shadow-sm border-0">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center maint-icon-wrapper maint-bg-purple">
                    <i class="bi bi-gear fs-4"></i>
                </div>
                <div class="fw-bold text-muted small text-uppercase">En Proceso</div>
            </div>
            <div class="fs-3 fw-bold">{{ $conteoProceso }}</div>
        </div>
    </div>
    
    <div class="col-12 col-md-3">
        <div class="card p-4 d-flex flex-row align-items-center justify-content-between h-100 shadow-sm border-0">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center maint-icon-wrapper maint-bg-indigo">
                    <i class="bi bi-pause-circle fs-4"></i>
                </div>
                <div class="fw-bold text-muted small text-uppercase">Suspendidos</div>
            </div>
            <div class="fs-3 fw-bold">{{ $conteosSuspendidos }}</div>
        </div>
    </div>
    
    <div class="col-12 col-md-3">
        <div class="card p-4 d-flex flex-row align-items-center justify-content-between h-100 shadow-sm border-0">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center maint-icon-wrapper maint-bg-success">
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
        <form action="{{ route('historial-tecnico.index') }}" method="GET" class="d-flex flex-column flex-md-row gap-3 align-items-stretch align-items-md-center w-100">
            <div class="search-bar flex-grow-1 maint-search-bar w-100">
                <i class="bi bi-search text-muted"></i>
                <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Busca por placa, descripción o caso..." class="w-100 border-0 outline-none">
            </div>
            <button type="button" class="btn btn-outline-secondary rounded-pill px-4 text-nowrap">
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
                    <td class="text-muted maint-truncate-td">
                        {{ $reg->observaciones ?: $reg->descripcion }}
                    </td>
                    <td>{{ $reg->usuario_responsable_label }}</td>
                    <td>{{ \Carbon\Carbon::parse($reg->fecha_evento)->format('d M Y') }}</td>
                    <td class="text-end pe-4">
                        <div class="d-inline-flex align-items-center gap-1">
                            <a href="{{ route('historial-tecnico.show', $reg) }}"
                               class="btn btn-sm btn-light rounded-circle"
                               title="Ver detalle">
                                <i class="bi bi-eye"></i>
                            </a>
                            @if($reg->equipo_id)
                                <a href="{{ route('historial-tecnico.por-equipo', ['equipo' => $reg->equipo_id, 'return_to' => request()->fullUrl()]) }}"
                                   class="btn btn-sm btn-light rounded-circle"
                                   title="Ver historial del activo">
                                    <i class="bi bi-clock-history"></i>
                                </a>
                            @endif
                            @can('equipos.crear')
                            @if($reg->equipo && in_array($reg->equipo->estado_operativo, ['mantenimiento', 'baja'], true))
                                <form method="POST"
                                      action="{{ route('asignaciones.store') }}"
                                                                            class="d-inline js-form-restaurar"
                                                                            data-equipo="{{ $reg->equipo->nombre_equipo ?? 'este equipo' }}">
                                    @csrf
                                    <input type="hidden" name="equipo_id" value="{{ $reg->equipo_id }}">
                                    <input type="hidden" name="tipo_accion" value="restauracion">
                                                                        <input type="hidden" name="return_to" value="{{ route('historial-tecnico.por-equipo', ['equipo' => $reg->equipo_id, 'return_to' => request()->fullUrl()]) }}">
                                    <button type="submit"
                                            class="btn btn-sm btn-outline-success rounded-circle"
                                            title="Restaurar equipo a Activo">
                                        <i class="bi bi-arrow-repeat"></i>
                                    </button>
                                </form>
                            @endif
                            @endcan
                        </div>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.js-form-restaurar').forEach((form) => {
        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            const nombreEquipo = form.dataset.equipo || 'este equipo';
            const resultado = await Swal.fire({
                title: 'Restaurar equipo',
                html: `Se cambiará <strong>${nombreEquipo}</strong> a estado <strong>Activo</strong>.<br>¿Deseas continuar?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, restaurar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#198754',
                cancelButtonColor: '#6c757d',
                reverseButtons: true,
                focusCancel: true,
            });

            if (resultado.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>
@endpush
