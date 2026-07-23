@extends('layouts.inventario')

@section('title', 'Checklists Técnicos')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <h4 class="fw-bold mb-0"><i class="bi bi-clipboard-check me-2 text-primary"></i>Checklists Técnicos</h4>
    <a href="{{ route('checklists.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Nuevo Checklist
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Equipo</th>
                        <th>Responsable TI</th>
                        <th>Orden de Trabajo</th>
                        <th>Resultado</th>
                        <th>Tipo Aprobado</th>
                        <th>FNC</th>
                        <th>Fecha</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($checklists as $checklist)
                        <tr>
                            <td>
                                @if($checklist->equipo)
                                    <a href="{{ route('equipos.show', $checklist->equipo) }}"
                                       class="text-decoration-none fw-medium">
                                        {{ $checklist->equipo->nombre_equipo }}
                                    </a>
                                    <br><small class="text-muted">{{ $checklist->equipo->serial }}</small>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>{{ $checklist->responsable_ti ?? '—' }}</td>
                            <td>{{ $checklist->orden_trabajo ?? '—' }}</td>
                            <td>{{ $checklist->resultado ?? '—' }}</td>
                            <td>{{ $checklist->tipo_aprobado ?? '—' }}</td>
                            <td>{{ $checklist->fnc ?? '—' }}</td>
                            <td>{{ $checklist->created_at->format('d/m/Y') }}</td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('checklists.show', $checklist) }}"
                                       class="btn btn-outline-info" title="Ver">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('checklists.edit', $checklist) }}"
                                       class="btn btn-outline-warning" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button"
                                            class="btn btn-outline-danger"
                                            data-delete-url="{{ route('checklists.destroy', $checklist) }}"
                                            data-delete-name="{{ $checklist->equipo?->nombre_equipo ?? 'este checklist' }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-2 d-block mb-2"></i>Sin checklists registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($checklists->hasPages())
        <div class="card-footer bg-white border-0">
            {{ $checklists->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

@endsection
