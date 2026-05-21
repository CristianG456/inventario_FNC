@extends('layouts.inventario')

@section('title', 'Importar Equipos')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 class="mb-0 fw-bold"><i class="bi bi-file-earmark-arrow-up me-2" style="color:#9e052b;"></i>Importar Equipos desde Excel</h2>
        <p class="text-muted mb-0 mt-1">Sube un archivo <strong>.xlsx</strong> o <strong>.xls</strong> para registrar equipos en masa.</p>
    </div>
    <a href="{{ route('equipos.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Volver
    </a>
</div>

{{-- ░░ RESUMEN RESULTADO ░░ --}}
@if (session()->has('import_insertados'))
    @php
        $insertados = session('import_insertados');
        $omitidos   = session('import_omitidos', 0);
        $failures      = session('import_failures', []);
        $importErrors  = session('import_errors', []);
        $fallidas      = count($failures) + count($importErrors);
        $total      = $insertados + $fallidas + $omitidos;
    @endphp

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center py-3">
                <div class="fs-1 fw-bold text-secondary">{{ $total }}</div>
                <div class="text-muted small">Filas procesadas</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center py-3 border-success">
                <div class="fs-1 fw-bold text-success">{{ $insertados }}</div>
                <div class="text-muted small">Registradas</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center py-3">
                <div class="fs-1 fw-bold text-warning">{{ $omitidos }}</div>
                <div class="text-muted small">Periféricos omitidos</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center py-3 {{ $fallidas > 0 ? 'border-danger' : '' }}">
                <div class="fs-1 fw-bold {{ $fallidas > 0 ? 'text-danger' : 'text-muted' }}">{{ $fallidas }}</div>
                <div class="text-muted small">Con errores</div>
            </div>
        </div>
    </div>

    {{-- Detalles de filas con fallo de validación --}}
    @if (count($failures) > 0)
        <div class="card border-danger mb-4 shadow-sm">
            <div class="card-header bg-danger text-white fw-bold">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>Filas con errores de validación
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width:90px;">Fila #</th>
                                <th>Motivo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($failures as $f)
                                <tr>
                                    <td class="fw-bold text-danger">{{ $f['fila'] }}</td>
                                    <td>
                                        @foreach ($f['errores'] as $err)
                                            <span class="badge bg-danger-subtle text-danger-emphasis me-1 mb-1">{{ $err }}</span>
                                        @endforeach
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    {{-- Errores de excepción --}}
    @if (count($importErrors) > 0)
        <div class="card border-warning mb-4 shadow-sm">
            <div class="card-header bg-warning text-dark fw-bold">
                <i class="bi bi-bug-fill me-2"></i>Errores inesperados durante la importación
            </div>
            <ul class="list-group list-group-flush">
                @foreach ($importErrors as $e)
                    <li class="list-group-item text-muted small">{{ $e['mensaje'] }}</li>
                @endforeach
            </ul>
        </div>
    @endif
@endif

{{-- ░░ FORMULARIO ░░ --}}
<div class="card shadow-sm border-0">
    <div class="card-header bg-white fw-semibold border-bottom py-3">
        <i class="bi bi-upload me-2" style="color:#9e052b;"></i>Seleccionar archivo Excel
    </div>
    <div class="card-body">
        <form action="{{ route('equipos.importar') }}" method="POST" enctype="multipart/form-data" id="formImportar">
            @csrf

            <div class="mb-4">
                <label for="archivo" class="form-label fw-semibold">Archivo Excel <span class="text-danger">*</span></label>
                <input type="file"
                       class="form-control @error('archivo') is-invalid @enderror"
                       id="archivo"
                       name="archivo"
                       accept=".xlsx,.xls"
                       required>
                @error('archivo')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text text-muted">Formatos admitidos: <strong>.xlsx</strong>, <strong>.xls</strong>. Tamaño máximo: 10 MB.</div>
            </div>

            {{-- Indicaciones de columnas esperadas --}}
            <div class="alert alert-light border mb-4">
                <p class="fw-semibold mb-2" style="color:#9e052b;">
                    <i class="bi bi-info-circle-fill me-1"></i>
                    El sistema detecta los encabezados automáticamente. Se aceptan múltiples nombres por columna.
                </p>
                <div class="row g-3">
                    <div class="col-md-4">
                        <p class="fw-semibold mb-1 small" style="color:#9e052b;"><i class="bi bi-laptop-fill me-1"></i>Equipo <span class="text-danger">(*)</span></p>
                        <table class="table table-sm table-borderless mb-0 small">
                            <tbody>
                                <tr><td class="text-danger fw-bold">*</td><td><code>tipo_recurso</code></td><td class="text-muted">tipo, device_type, categoria</td></tr>
                                <tr><td class="text-danger fw-bold">*</td><td><code>serial</code></td><td class="text-muted">serial_number, nro_serial</td></tr>
                                <tr><td></td><td><code>placa</code></td><td class="text-muted">asset_tag, activo_fijo</td></tr>
                                <tr><td></td><td><code>marca</code></td><td class="text-muted">brand, fabricante</td></tr>
                                <tr><td></td><td><code>modelo</code></td><td class="text-muted">model</td></tr>
                                <tr><td></td><td><code>nombre_equipo</code></td><td class="text-muted">hostname, computer_name</td></tr>
                                <tr><td></td><td><code>estado_operativo</code></td><td class="text-muted">estado, status</td></tr>
                                <tr><td></td><td><code>procesador</code></td><td class="text-muted">processor, cpu</td></tr>
                                <tr><td></td><td><code>ram</code></td><td class="text-muted">memory, memoria</td></tr>
                                <tr><td></td><td><code>disco</code></td><td class="text-muted">disk, storage, hdd, ssd</td></tr>
                                <tr><td></td><td><code>sistema_operativo</code></td><td class="text-muted">so, os, operating_system</td></tr>
                                <tr><td></td><td><code>fecha_compra</code></td><td class="text-muted">purchase_date</td></tr>
                                <tr><td></td><td><code>fin_garantia</code></td><td class="text-muted">warranty_end, garantia</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-4">
                        <p class="fw-semibold mb-1 small" style="color:#9e052b;"><i class="bi bi-person-fill me-1"></i>Usuario asignado</p>
                        <table class="table table-sm table-borderless mb-0 small">
                            <tbody>
                                <tr><td><code>nombre_usuario</code></td><td class="text-muted">usuario, empleado, full_name</td></tr>
                                <tr><td><code>cedula</code></td><td class="text-muted">documento, id_number, cc</td></tr>
                                <tr><td><code>empresa_propietaria</code></td><td class="text-muted">propietario, owner_company</td></tr>
                                <tr><td><code>dependencia</code></td><td class="text-muted">dependency, gerencia</td></tr>
                                <tr><td><code>fuente_recurso</code></td><td class="text-muted">fuente, funding_source</td></tr>
                                <tr><td><code>empresa_funcionario</code></td><td class="text-muted">employer</td></tr>
                                <tr><td><code>tipo_vinculacion</code></td><td class="text-muted">vinculacion, employment_type</td></tr>
                                <tr><td><code>shortname</code></td><td class="text-muted">login, username, usuario_red</td></tr>
                                <tr><td><code>departamento</code></td><td class="text-muted">department, depto</td></tr>
                                <tr><td><code>ciudad</code></td><td class="text-muted">city, municipio</td></tr>
                                <tr><td><code>cargo</code></td><td class="text-muted">position, job_title</td></tr>
                                <tr><td><code>area</code></td><td class="text-muted">area_trabajo</td></tr>
                                <tr><td><code>piso</code></td><td class="text-muted">floor, ubicacion</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-4">
                        <p class="fw-semibold mb-1 small text-warning-emphasis"><i class="bi bi-slash-circle me-1"></i>Se ignoran automáticamente</p>
                        <ul class="list-unstyled small text-muted mb-3">
                            <li>Filas con <code>tipo_recurso</code> que contenga:</li>
                            <li class="ms-2">• <em>telefono</em></li>
                            <li class="ms-2">• <em>teclado</em></li>
                            <li class="ms-2">• <em>mouse</em></li>
                            <li class="ms-2">• <em>camara</em></li>
                            <li class="mt-1">Filas completamente vacías</li>
                            <li>Seriales duplicados</li>
                        </ul>
                        <p class="fw-semibold mb-1 small text-success-emphasis"><i class="bi bi-check2-circle me-1"></i>Normalización automática</p>
                        <ul class="list-unstyled small text-muted mb-0">
                            <li>"<em>Serial Number</em>" → <code>serial_number</code></li>
                            <li>"<em>Tipo de Recurso</em>" → <code>tipo_de_recurso</code></li>
                            <li>Mayúsculas, acentos, espacios → sin problema</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-lg text-white" style="background:#9e052b;" id="btnImportar">
                    <i class="bi bi-cloud-upload me-2"></i>Importar Excel
                </button>
                <a href="{{ route('equipos.index') }}" class="btn btn-lg btn-outline-secondary">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('formImportar').addEventListener('submit', function () {
    const btn = document.getElementById('btnImportar');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Procesando...';
});
</script>
@endpush
