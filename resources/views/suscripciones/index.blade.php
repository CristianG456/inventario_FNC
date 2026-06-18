@extends('layouts.inventario')
@section('title', 'Suscripciones')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="bi bi-calendar-check me-2 text-primary"></i>Gestión de Suscripciones</h4>
        <a href="{{ route('suscripciones.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Nueva Suscripción
        </a>
    </div>
    
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-light">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Total Suscripciones</h6>
                    <h3 class="mb-0 text-primary">0</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Nombre</th>
                            <th>Fabricante</th>
                            <th>Vencimiento</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No hay suscripciones registradas</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
