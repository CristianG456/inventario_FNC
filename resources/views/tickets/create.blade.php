@extends('layouts.inventario')

@section('title', 'Nuevo Caso de Soporte')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        
        <div class="card mb-4 border-0 shadow-sm">
            <div class="p-4 d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-bold mb-1">Nuevo Caso de Soporte</h4>
                    <p class="text-muted mb-0">Registra un incidente o requerimiento</p>
                </div>
                <a href="{{ route('tickets.index') }}" class="btn btn-light rounded-circle">
                    <i class="bi bi-x-lg"></i>
                </a>
            </div>
        </div>

        <form action="{{ route('tickets.store') }}" method="POST" enctype="multipart/form-data" class="card border-0 shadow-sm p-4">
            @csrf

            <!-- DETALLES DEL REQUERIMIENTO -->
            <h6 class="text-uppercase fw-bold text-dark mb-4 pb-2 border-bottom d-flex align-items-center gap-2 ticket-section-title">
                <i class="bi bi-info-circle text-primary"></i> DETALLES DEL REQUERIMIENTO
            </h6>

            <div class="mb-4">
                <label class="form-label fw-semibold text-dark">Título <span class="text-danger">*</span></label>
                <input type="text" name="titulo" class="form-control px-3 py-2 border-light-subtle rounded-3 bg-light focus-ring" placeholder="Ej. PROBLEMA CON ACCESO AL CORREO" required value="{{ old('titulo') }}">
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-dark">Tipo <span class="text-danger">*</span></label>
                    <select name="tipo" class="form-select px-3 py-2 border-light-subtle rounded-3 bg-light" required>
                        <option value="Requerimiento (Solicitud nueva)">Requerimiento (Solicitud nueva)</option>
                        <option value="Incidente (Falla de servicio)">Incidente (Falla de servicio)</option>
                        <option value="Soporte Técnico">Soporte Técnico</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-dark">Prioridad <span class="text-danger">*</span></label>
                    <select name="prioridad" class="form-select px-3 py-2 border-light-subtle rounded-3 bg-light" required>
                        <option value="Baja">Baja</option>
                        <option value="Media" selected>Media</option>
                        <option value="Alta">Alta</option>
                        <option value="Crítica">Crítica</option>
                    </select>
                </div>
            </div>

            <div class="mb-5">
                <label class="form-label fw-semibold text-dark">Descripción <span class="text-danger">*</span></label>
                <textarea name="descripcion" rows="4" class="form-control px-3 py-2 border-light-subtle rounded-3 bg-light" placeholder="Describe el problema con el mayor detalle posible..." required>{{ old('descripcion') }}</textarea>
            </div>


            <!-- ASOCIACIÓN -->
            <h6 class="text-uppercase fw-bold text-dark mb-4 pb-2 border-bottom d-flex align-items-center gap-2 ticket-section-title">
                <i class="bi bi-link-45deg text-dark"></i> ASOCIACIÓN
            </h6>

            <div class="row mb-5">
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-dark">Funcionario Solicitante <span class="text-danger">*</span></label>
                    <select name="funcionario_id" class="form-select px-3 py-2 border-light-subtle rounded-3 bg-light" required>
                        <option value="">Buscar solicitante...</option>
                        @foreach($funcionarios as $func)
                            <option value="{{ $func->id }}">{{ $func->nombres }} {{ $func->apellidos }} ({{ $func->identificacion }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold text-muted">Activo Relacionado (Opcional)</label>
                    <select name="equipo_id" class="form-select px-3 py-2 border-light-subtle rounded-3 bg-light text-muted" disabled>
                        <option value="">Selecciona un funcionario primero...</option>
                    </select>
                    <div class="form-text mt-2 ticket-hint"><i class="bi bi-tag text-primary"></i> Asocia un funcionario para ver sus equipos.</div>
                </div>
            </div>


            <!-- EVIDENCIAS -->
            <h6 class="text-uppercase fw-bold text-dark mb-4 pb-2 border-bottom d-flex align-items-center gap-2 ticket-section-title">
                <i class="bi bi-paperclip text-primary"></i> EVIDENCIAS ADJUNTAS (OPCIONAL)
            </h6>

            <div class="mb-5">
                <div class="border border-2 border-dashed rounded-4 p-5 text-center bg-light ticket-file-dropzone">
                    <i class="bi bi-paperclip text-muted mb-2 ticket-file-icon"></i>
                    <h6 class="fw-semibold text-dark mb-1">Arrastra archivos o haz clic para adjuntar</h6>
                    <p class="text-muted small mb-0">Imágenes, PDF, Word — máx. 5 MB por archivo</p>
                    <input type="file" name="archivos[]" class="d-none" multiple>
                </div>
            </div>

            <!-- BUTTONS -->
            <div class="d-flex justify-content-end gap-3 pt-3 border-top">
                <a href="{{ route('tickets.index') }}" class="btn btn-light border px-4 rounded-pill">Cancelar</a>
                <button type="submit" class="btn btn-primary px-4 rounded-pill ticket-btn-blue">
                    <i class="bi bi-check2 me-1"></i> Crear Caso
                </button>
            </div>

        </form>
    </div>
</div>

@endsection
