@extends('layouts.inventario')

@section('title', 'Mesa de Ayuda')

@section('content')
<div class="page-header mb-4">
    <div>
        <h4 class="page-title">Mesa de Ayuda TIC</h4>
        <p class="page-subtitle">Centro de gestión de incidentes y servicios tecnológicos ({{ $totalTickets }} tickets)</p>
    </div>
    <a href="{{ route('tickets.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Nuevo Ticket
    </a>
</div>

<div class="card p-0">
    <div class="p-4 border-bottom border-light">
        <form action="{{ route('tickets.index') }}" method="GET" class="d-flex gap-3 align-items-center">
            <div class="search-bar flex-grow-1 ticket-search-bar">
                <i class="bi bi-search text-muted"></i>
                <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Busca por título, funcionario o ID...">
            </div>
            
            <div class="search-bar ticket-status-filter">
                <select name="estado" class="form-select border-0 bg-transparent text-muted" onchange="this.form.submit()">
                    <option value="">Cualquier Estado</option>
                    <option value="Abierto" {{ request('estado') == 'Abierto' ? 'selected' : '' }}>Abierto</option>
                    <option value="En Progreso" {{ request('estado') == 'En Progreso' ? 'selected' : '' }}>En Progreso</option>
                    <option value="Resuelto" {{ request('estado') == 'Resuelto' ? 'selected' : '' }}>Resuelto</option>
                </select>
            </div>
        </form>
    </div>
    
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th class="ps-4">Ticket</th>
                    <th>Solicitante</th>
                    <th>Estado</th>
                    <th>Responsable</th>
                    <th>Fecha</th>
                    <th class="text-end pe-4">Detalle</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tickets as $ticket)
                <tr>
                    <td class="ps-4 fw-medium">
                        #{{ str_pad($ticket->id, 4, '0', STR_PAD_LEFT) }}<br>
                        <small class="text-muted fw-normal">{{ $ticket->titulo }}</small>
                    </td>
                    <td>
                        {{ $ticket->funcionario->nombres ?? 'Desconocido' }}<br>
                        <small class="text-muted">{{ $ticket->funcionario->cargo ?? '' }}</small>
                    </td>
                    <td>
                        <span class="badge {{ 
                            $ticket->estado == 'Abierto' ? 'badge-warning' : 
                            ($ticket->estado == 'Resuelto' ? 'badge-success' : 'badge-info') 
                        }}">{{ $ticket->estado }}</span><br>
                        <small class="text-muted">{{ $ticket->prioridad }}</small>
                    </td>
                    <td class="text-muted">{{ $ticket->responsable->name ?? 'Sin asignar' }}</td>
                    <td>{{ $ticket->created_at->format('d M Y') }}</td>
                    <td class="text-end pe-4">
                        <button class="btn btn-sm btn-light rounded-circle"><i class="bi bi-chevron-right"></i></button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        <div class="my-4 text-uppercase fw-semibold ticket-empty-state">
                            NO SE ENCONTRARON TICKETS REGISTRADOS
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($tickets->hasPages())
    <div class="p-3 border-top">
        {{ $tickets->links() }}
    </div>
    @endif
</div>
@endsection
