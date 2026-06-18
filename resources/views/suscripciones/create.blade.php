@extends('layouts.inventario')
@section('title', 'Crear Suscripción')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="bi bi-calendar-plus me-2 text-primary"></i>Nueva Suscripción</h4>
        <a href="{{ route('suscripciones.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form action="{{ route('suscripciones.store') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nombre de la suscripción *</label>
                        <input type="text" name="nombre" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Fabricante</label>
                        <input type="text" name="fabricante" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Cantidad comprada *</label>
                        <input type="number" name="cantidad_comprada" class="form-control" value="1" min="1" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Fecha de Compra</label>
                        <input type="date" name="fecha_compra" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Fecha de Vencimiento Global</label>
                        <input type="date" name="fecha_vencimiento_global" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Estado *</label>
                        <select name="estado" class="form-select" required>
                            <option value="Activa">Activa</option>
                            <option value="Próxima a vencer">Próxima a vencer</option>
                            <option value="Vencida">Vencida</option>
                            <option value="Suspendida">Suspendida</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Guardar Suscripción</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
