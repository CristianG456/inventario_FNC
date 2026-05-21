{{-- Partial compartido: campos de equipo para create/edit --}}

{{-- === DATOS DEL EQUIPO === --}}
<div class="card mb-4">
    <div class="card-header bg-primary bg-opacity-10 border-0 fw-semibold py-3">
        <i class="bi bi-laptop me-2 text-primary"></i>Datos del Equipo
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-medium">Tipo de Recurso <span class="text-danger">*</span></label>
                <select name="tipo_recurso_id"
                        class="form-select @error('tipo_recurso_id') is-invalid @enderror"
                        required>
                    <option value="">Seleccione...</option>
                    @foreach($tipoRecursos as $tipo)
                        <option value="{{ $tipo->id }}"
                            {{ old('tipo_recurso_id', $equipo->tipo_recurso_id ?? '') == $tipo->id ? 'selected' : '' }}>
                            {{ $tipo->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('tipo_recurso_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label fw-medium">Serial <span class="text-danger">*</span></label>
                <input type="text" name="serial"
                       class="form-control @error('serial') is-invalid @enderror"
                       value="{{ old('serial', $equipo->serial ?? '') }}"
                       required maxlength="100">
                @error('serial')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label fw-medium">Placa</label>
                <input type="text" name="placa"
                       class="form-control @error('placa') is-invalid @enderror"
                       value="{{ old('placa', $equipo->placa ?? '') }}"
                       maxlength="100">
                @error('placa')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label fw-medium">Marca <span class="text-danger">*</span></label>
                <input type="text" name="marca"
                       class="form-control @error('marca') is-invalid @enderror"
                       value="{{ old('marca', $equipo->marca ?? '') }}"
                       required maxlength="100">
                @error('marca')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label fw-medium">Modelo <span class="text-danger">*</span></label>
                <input type="text" name="modelo"
                       class="form-control @error('modelo') is-invalid @enderror"
                       value="{{ old('modelo', $equipo->modelo ?? '') }}"
                       required maxlength="100">
                @error('modelo')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label fw-medium">Nombre del Equipo <span class="text-danger">*</span></label>
                <input type="text" name="nombre_equipo"
                       class="form-control @error('nombre_equipo') is-invalid @enderror"
                       value="{{ old('nombre_equipo', $equipo->nombre_equipo ?? '') }}"
                       required maxlength="150">
                @error('nombre_equipo')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label fw-medium">Estado Operativo <span class="text-danger">*</span></label>
                <select name="estado_operativo"
                        class="form-select @error('estado_operativo') is-invalid @enderror"
                        required id="estadoSelect">
                    <option value="activo" {{ old('estado_operativo', $equipo->estado_operativo ?? 'activo') === 'activo' ? 'selected' : '' }}>Activo</option>
                    <option value="mantenimiento" {{ old('estado_operativo', $equipo->estado_operativo ?? '') === 'mantenimiento' ? 'selected' : '' }}>Mantenimiento</option>
                    <option value="baja" {{ old('estado_operativo', $equipo->estado_operativo ?? '') === 'baja' ? 'selected' : '' }}>Baja</option>
                </select>
                @error('estado_operativo')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-8" id="razonEstadoWrap"
                 style="{{ in_array(old('estado_operativo', $equipo->estado_operativo ?? 'activo'), ['mantenimiento','baja']) ? '' : 'display:none' }}">
                <label class="form-label fw-medium">Razón del Estado</label>
                <textarea name="razon_estado"
                          class="form-control @error('razon_estado') is-invalid @enderror"
                          rows="2" maxlength="500">{{ old('razon_estado', $equipo->razon_estado ?? '') }}</textarea>
                @error('razon_estado')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
</div>

{{-- === ESPECIFICACIONES TÉCNICAS === --}}
<div class="card mb-4">
    <div class="card-header bg-info bg-opacity-10 border-0 fw-semibold py-3">
        <i class="bi bi-cpu me-2 text-info"></i>Especificaciones Técnicas
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-medium">Procesador</label>
                <input type="text" name="procesador"
                       class="form-control @error('procesador') is-invalid @enderror"
                       value="{{ old('procesador', $equipo->procesador ?? '') }}"
                       maxlength="150">
                @error('procesador') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label fw-medium">RAM</label>
                <input type="text" name="ram"
                       class="form-control @error('ram') is-invalid @enderror"
                       value="{{ old('ram', $equipo->ram ?? '') }}"
                       placeholder="Ej: 16 GB" maxlength="50">
                @error('ram') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label fw-medium">Disco</label>
                <input type="text" name="disco"
                       class="form-control @error('disco') is-invalid @enderror"
                       value="{{ old('disco', $equipo->disco ?? '') }}"
                       placeholder="Ej: 512 GB SSD" maxlength="50">
                @error('disco') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label fw-medium">Sistema Operativo</label>
                <input type="text" name="sistema_operativo"
                       class="form-control @error('sistema_operativo') is-invalid @enderror"
                       value="{{ old('sistema_operativo', $equipo->sistema_operativo ?? '') }}"
                       maxlength="100">
                @error('sistema_operativo') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label fw-medium">Fecha de Compra</label>
                <input type="date" name="fecha_compra"
                       class="form-control @error('fecha_compra') is-invalid @enderror"
                       value="{{ old('fecha_compra', isset($equipo->fecha_compra) ? $equipo->fecha_compra->format('Y-m-d') : '') }}">
                @error('fecha_compra') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label fw-medium">Fin de Garantía</label>
                <input type="date" name="fin_garantia"
                       class="form-control @error('fin_garantia') is-invalid @enderror"
                       value="{{ old('fin_garantia', isset($equipo->fin_garantia) ? $equipo->fin_garantia->format('Y-m-d') : '') }}">
                @error('fin_garantia') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label fw-medium">Tiempo de Uso</label>
                <input type="text" name="tiempo_uso"
                       class="form-control @error('tiempo_uso') is-invalid @enderror"
                       value="{{ old('tiempo_uso', $equipo->tiempo_uso ?? '') }}"
                       placeholder="Ej: 2 años" maxlength="100">
                @error('tiempo_uso') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
    </div>
</div>

{{-- === USUARIO ASIGNADO === --}}
<div class="card mb-4">
    <div class="card-header bg-success bg-opacity-10 border-0 fw-semibold py-3">
        <i class="bi bi-person me-2 text-success"></i>Usuario Asignado
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-medium">Empresa Propietaria del Equipo</label>
                <input type="text" name="usuario_empresa_propietaria"
                       class="form-control @error('usuario_empresa_propietaria') is-invalid @enderror"
                       value="{{ old('usuario_empresa_propietaria', $equipo->usuarioAsignado->empresa_propietaria ?? '') }}"
                       maxlength="150">
                @error('usuario_empresa_propietaria') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label fw-medium">Dependencia</label>
                <input type="text" name="usuario_dependencia"
                       class="form-control @error('usuario_dependencia') is-invalid @enderror"
                       value="{{ old('usuario_dependencia', $equipo->usuarioAsignado->dependencia ?? '') }}"
                       maxlength="150">
                @error('usuario_dependencia') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label fw-medium">Fuente de Recurso</label>
                <input type="text" name="usuario_fuente_recurso"
                       class="form-control @error('usuario_fuente_recurso') is-invalid @enderror"
                       value="{{ old('usuario_fuente_recurso', $equipo->usuarioAsignado->fuente_recurso ?? '') }}"
                       maxlength="150">
                @error('usuario_fuente_recurso') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label fw-medium">Empresa Funcionario</label>
                <input type="text" name="usuario_empresa_funcionario"
                       class="form-control @error('usuario_empresa_funcionario') is-invalid @enderror"
                       value="{{ old('usuario_empresa_funcionario', $equipo->usuarioAsignado->empresa_funcionario ?? '') }}"
                       maxlength="150">
                @error('usuario_empresa_funcionario') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label fw-medium">Empleado o Contratista</label>
                <input type="text" name="usuario_tipo_vinculacion"
                       class="form-control @error('usuario_tipo_vinculacion') is-invalid @enderror"
                       value="{{ old('usuario_tipo_vinculacion', $equipo->usuarioAsignado->tipo_vinculacion ?? '') }}"
                       maxlength="100">
                @error('usuario_tipo_vinculacion') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label fw-medium">Shortname</label>
                <input type="text" name="usuario_shortname"
                       class="form-control @error('usuario_shortname') is-invalid @enderror"
                       value="{{ old('usuario_shortname', $equipo->usuarioAsignado->shortname ?? '') }}"
                       maxlength="100">
                @error('usuario_shortname') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label fw-medium">Nombre <span class="text-danger">*</span></label>
                <input type="text" name="usuario_nombre"
                       class="form-control @error('usuario_nombre') is-invalid @enderror"
                       value="{{ old('usuario_nombre', $equipo->usuarioAsignado->nombre ?? '') }}"
                       required maxlength="150">
                @error('usuario_nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-3">
                <label class="form-label fw-medium">Cédula <span class="text-danger">*</span></label>
                <input type="text" name="usuario_cedula"
                       class="form-control @error('usuario_cedula') is-invalid @enderror"
                       value="{{ old('usuario_cedula', $equipo->usuarioAsignado->cedula ?? '') }}"
                       required maxlength="20">
                @error('usuario_cedula') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-3">
                <label class="form-label fw-medium">Ciudad</label>
                <input type="text" name="usuario_ciudad"
                       class="form-control @error('usuario_ciudad') is-invalid @enderror"
                       value="{{ old('usuario_ciudad', $equipo->usuarioAsignado->ciudad ?? '') }}"
                       maxlength="100">
                @error('usuario_ciudad') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-3">
                <label class="form-label fw-medium">Departamento</label>
                <input type="text" name="usuario_departamento"
                       class="form-control @error('usuario_departamento') is-invalid @enderror"
                       value="{{ old('usuario_departamento', $equipo->usuarioAsignado->departamento ?? '') }}"
                       maxlength="100">
                @error('usuario_departamento') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-3">
                <label class="form-label fw-medium">Cargo</label>
                <input type="text" name="usuario_cargo"
                       class="form-control @error('usuario_cargo') is-invalid @enderror"
                       value="{{ old('usuario_cargo', $equipo->usuarioAsignado->cargo ?? '') }}"
                       maxlength="100">
                @error('usuario_cargo') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-3">
                <label class="form-label fw-medium">Área</label>
                <input type="text" name="usuario_area"
                       class="form-control @error('usuario_area') is-invalid @enderror"
                       value="{{ old('usuario_area', $equipo->usuarioAsignado->area ?? '') }}"
                       maxlength="100">
                @error('usuario_area') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-3">
                <label class="form-label fw-medium">Piso</label>
                <input type="text" name="usuario_piso"
                       class="form-control @error('usuario_piso') is-invalid @enderror"
                       value="{{ old('usuario_piso', $equipo->usuarioAsignado->piso ?? '') }}"
                       maxlength="20">
                @error('usuario_piso') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
    </div>
</div>

{{-- === PERIFÉRICOS === --}}
<div class="card mb-4">
    <div class="card-header bg-warning bg-opacity-10 border-0 fw-semibold py-3">
        <i class="bi bi-usb-plug me-2 text-warning"></i>Periféricos
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label fw-medium">Teléfono Fijo</label>
                <input type="text" name="periferico_telefono"
                       class="form-control @error('periferico_telefono') is-invalid @enderror"
                       value="{{ old('periferico_telefono', $equipo->periferico->telefono ?? '') }}"
                       maxlength="100">
                @error('periferico_telefono') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-3">
                <label class="form-label fw-medium">Teclado</label>
                <input type="text" name="periferico_teclado"
                       class="form-control @error('periferico_teclado') is-invalid @enderror"
                       value="{{ old('periferico_teclado', $equipo->periferico->teclado ?? '') }}"
                       maxlength="100">
                @error('periferico_teclado') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-3">
                <label class="form-label fw-medium">Mouse</label>
                <input type="text" name="periferico_mouse"
                       class="form-control @error('periferico_mouse') is-invalid @enderror"
                       value="{{ old('periferico_mouse', $equipo->periferico->mouse ?? '') }}"
                       maxlength="100">
                @error('periferico_mouse') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-3">
                <label class="form-label fw-medium">Cámara</label>
                <input type="text" name="periferico_camara"
                       class="form-control @error('periferico_camara') is-invalid @enderror"
                       value="{{ old('periferico_camara', $equipo->periferico->camara ?? '') }}"
                       maxlength="100">
                @error('periferico_camara') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Mostrar/ocultar razón de estado
    document.getElementById('estadoSelect').addEventListener('change', function () {
        const wrap = document.getElementById('razonEstadoWrap');
        wrap.style.display = ['mantenimiento', 'baja'].includes(this.value) ? '' : 'none';
    });
</script>
@endpush
