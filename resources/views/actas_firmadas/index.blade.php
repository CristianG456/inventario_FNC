@extends('layouts.inventario')

@section('title', 'Actas Firmadas')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0"><i class="bi bi-file-earmark-pdf me-2 text-danger"></i>Actas Firmadas</h4>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
        <i class="bi bi-upload me-1"></i>Subir Acta Firmada
    </button>
</div>

<!-- Filtros -->
<div class="card mb-4 border-0 shadow-sm">
    <div class="card-body">
        <form method="GET" action="{{ route('actas-firmadas.index') }}" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Número de Acta</label>
                <input type="text" name="numero_acta" class="form-control" value="{{ request('numero_acta') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Tipo de Acta</label>
                <select name="tipo_acta" class="form-select">
                    <option value="">Todos</option>
                    <option value="Entrega" {{ request('tipo_acta') == 'Entrega' ? 'selected' : '' }}>Entrega</option>
                    <option value="Vitalicia" {{ request('tipo_acta') == 'Vitalicia' ? 'selected' : '' }}>Vitalicia</option>
                    <option value="Novedad" {{ request('tipo_acta') == 'Novedad' ? 'selected' : '' }}>Novedad</option>
                    <option value="Auditoria" {{ request('tipo_acta') == 'Auditoria' ? 'selected' : '' }}>Auditoría</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Fecha del Documento</label>
                <input type="date" name="fecha_documento" class="form-control" value="{{ request('fecha_documento') }}">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-dark w-100"><i class="bi bi-search me-2"></i>Buscar</button>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>N° Acta</th>
                        <th>Tipo</th>
                        <th>Fecha Documento</th>
                        <th>Subido Por</th>
                        <th>Observaciones</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($actas as $acta)
                        <tr>
                            <td class="fw-bold">{{ $acta->numero_acta }}</td>
                            <td><span class="badge bg-secondary">{{ $acta->tipo_acta }}</span></td>
                            <td>{{ $acta->fecha_documento->format('d/m/Y') }}</td>
                            <td>{{ $acta->user->name ?? 'Sistema' }}</td>
                            <td><small class="text-muted">{{ Str::limit($acta->observaciones, 50) }}</small></td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('actas-firmadas.download', $acta->id) }}" class="btn btn-outline-primary" title="Descargar PDF Actual">
                                        <i class="bi bi-download"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-warning" title="Reemplazar PDF" onclick="openReplaceModal({{ $acta->id }}, '{{ $acta->numero_acta }}')">
                                        <i class="bi bi-arrow-repeat"></i>
                                    </button>
                                    <a href="{{ route('actas-firmadas.history', $acta->id) }}" class="btn btn-outline-info" title="Historial de Versiones">
                                        <i class="bi bi-clock-history"></i>
                                    </a>
                                    <form action="{{ route('actas-firmadas.destroy', $acta->id) }}" method="POST" class="form-eliminar-acta" style="display: inline-block; m-0; p-0;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Eliminar Acta" style="border-top-left-radius: 0; border-bottom-left-radius: 0;">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-2 d-block mb-2"></i>No hay actas firmadas registradas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($actas->hasPages())
        <div class="card-footer bg-white border-0 pt-3">
            {{ $actas->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

<!-- Modal Upload -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form action="{{ route('actas-firmadas.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="uploadModalLabel">Subir Acta Firmada</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
                <label class="form-label text-danger">Archivo PDF *</label>
                <input type="file" name="archivo_pdf" class="form-control" accept=".pdf" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Número de Acta *</label>
                <input type="text" name="numero_acta" class="form-control" placeholder="Ej. ACT-2026-001" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Tipo de Acta *</label>
                <select name="tipo_acta" class="form-select" required>
                    <option value="Entrega">Entrega</option>
                    <option value="Vitalicia">Vitalicia</option>
                    <option value="Novedad">Novedad</option>
                    <option value="Auditoria">Auditoría</option>
                    <option value="Otro">Otro</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Fecha del Documento *</label>
                <input type="date" name="fecha_documento" class="form-control" value="{{ date('Y-m-d') }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Observaciones</label>
                <textarea name="observaciones" class="form-control" rows="2"></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary"><i class="bi bi-cloud-upload me-1"></i>Guardar Acta</button>
          </div>
        </div>
    </form>
  </div>
</div>

<!-- Modal Replace -->
<div class="modal fade" id="replaceModal" tabindex="-1" aria-labelledby="replaceModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="replaceForm" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="replaceModalLabel">Reemplazar Acta Firmada</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p>Se reemplazará el archivo para el acta: <strong id="replaceNumActa"></strong></p>
            <p class="text-muted small">El PDF actual se guardará en el historial como una versión anterior.</p>
            
            <div class="mb-3">
                <label class="form-label text-danger">Nuevo Archivo PDF *</label>
                <input type="file" name="archivo_pdf" class="form-control" accept=".pdf" required>
            </div>
            <div class="mb-3">
                <label class="form-label text-danger">Motivo del Cambio *</label>
                <textarea name="motivo_cambio" class="form-control" rows="3" placeholder="Ej. El acta anterior no tenía la firma del jefe..." required></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-warning"><i class="bi bi-arrow-repeat me-1"></i>Reemplazar</button>
          </div>
        </div>
    </form>
  </div>
</div>

@endsection

@push('scripts')
<script>
    function openReplaceModal(id, numero) {
        document.getElementById('replaceNumActa').innerText = numero;
        document.getElementById('replaceForm').action = '/actas-firmadas/' + id;
        new bootstrap.Modal(document.getElementById('replaceModal')).show();
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.form-eliminar-acta').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: '¿Eliminar Acta?',
                    text: "Esta acción enviará el acta a la papelera. Podrás recuperarla si es necesario.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: '<i class="bi bi-trash"></i> Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>
@endpush
