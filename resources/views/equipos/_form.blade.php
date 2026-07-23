{{-- Partial compartido: campos de equipo para create/edit --}}

{{-- === DATOS DEL EQUIPO === --}}
<div class="card mb-4">
    <div class="card-header bg-primary bg-opacity-10 border-0 fw-semibold py-3">
        <i class="bi bi-laptop me-2 text-primary"></i>Datos del Equipo
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-12 col-md-4">
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

            <div class="col-12 col-md-4">
                <label class="form-label fw-medium">Serial</label>
                <input type="text" name="serial" id="serialInput"
                       class="form-control @error('serial') is-invalid @enderror"
                       value="{{ old('serial', $equipo->serial ?? '') }}"
                       maxlength="100">
                <div class="form-check mt-2">
                    <input class="form-check-input" type="checkbox" name="sin_serial_fisico" id="sinSerialCheck" value="true" {{ old('sin_serial_fisico') ? 'checked' : '' }}>
                    <label class="form-check-label text-muted small" for="sinSerialCheck">
                        Este equipo no posee serial físico
                    </label>
                </div>
                @error('serial')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-12 col-md-4">
                <label class="form-label fw-medium">Activo Fijo</label>
                <input type="text" name="activo_fijo"
                       class="form-control @error('activo_fijo') is-invalid @enderror"
                       value="{{ old('activo_fijo', $equipo->activo_fijo ?? '') }}"
                       placeholder="Ej: FNC-001234" maxlength="100">
                @error('activo_fijo')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-12 col-md-4">
                <label class="form-label fw-medium">Placa</label>
                <input type="text" name="placa"
                       class="form-control @error('placa') is-invalid @enderror"
                       value="{{ old('placa', $equipo->placa ?? '') }}"
                       maxlength="100">
                @error('placa')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-12 col-md-4">
                <label class="form-label fw-medium">Marca <span class="text-danger">*</span></label>
                <input type="text" name="marca"
                       class="form-control @error('marca') is-invalid @enderror"
                       value="{{ old('marca', $equipo->marca ?? '') }}"
                       required maxlength="100">
                @error('marca')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-12 col-md-4">
                <label class="form-label fw-medium">Modelo <span class="text-danger">*</span></label>
                <input type="text" name="modelo"
                       class="form-control @error('modelo') is-invalid @enderror"
                       value="{{ old('modelo', $equipo->modelo ?? '') }}"
                       required maxlength="100">
                @error('modelo')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-12 col-md-4">
                <label class="form-label fw-medium">Nombre del Equipo <span class="text-danger">*</span></label>
                <input type="text" name="nombre_equipo"
                       class="form-control @error('nombre_equipo') is-invalid @enderror"
                       value="{{ old('nombre_equipo', $equipo->nombre_equipo ?? '') }}"
                       required maxlength="150">
                @error('nombre_equipo')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-12 col-md-4">
                <label class="form-label fw-medium">Estado Operativo <span class="text-danger">*</span></label>
                <select name="estado_operativo"
                        class="form-select @error('estado_operativo') is-invalid @enderror"
                        required id="estadoSelect">
                    <option value="activo" {{ old('estado_operativo', $equipo->estado_operativo ?? 'activo') === 'activo' ? 'selected' : '' }}>Asignado</option>
                    <option value="asignado" {{ old('estado_operativo', $equipo->estado_operativo ?? '') === 'asignado' ? 'selected' : '' }}>Asignado</option>
                    <option value="disponible" {{ old('estado_operativo', $equipo->estado_operativo ?? '') === 'disponible' ? 'selected' : '' }}>Disponible</option>
                    <option value="almacenado" {{ old('estado_operativo', $equipo->estado_operativo ?? '') === 'almacenado' ? 'selected' : '' }}>Almacenado</option>
                    <option value="mantenimiento" {{ old('estado_operativo', $equipo->estado_operativo ?? '') === 'mantenimiento' ? 'selected' : '' }}>Mantenimiento</option>
                    <option value="baja" {{ old('estado_operativo', $equipo->estado_operativo ?? '') === 'baja' ? 'selected' : '' }}>Baja</option>
                </select>
                @error('estado_operativo')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-12 col-md-8 {{ in_array(old('estado_operativo', $equipo->estado_operativo ?? 'activo'), ['mantenimiento','baja']) ? '' : 'd-none' }}" id="razonEstadoWrap">
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
            <div class="col-12 col-md-4">
                <label class="form-label fw-medium">Procesador</label>
                <input type="text" name="procesador"
                       class="form-control @error('procesador') is-invalid @enderror"
                       value="{{ old('procesador', $equipo->procesador ?? '') }}"
                       maxlength="150">
                @error('procesador') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label fw-medium">RAM</label>
                <input type="text" name="ram"
                       class="form-control @error('ram') is-invalid @enderror"
                       value="{{ old('ram', $equipo->ram ?? '') }}"
                       placeholder="Ej: 16 GB" maxlength="50">
                @error('ram') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label fw-medium">Disco</label>
                <input type="text" name="disco"
                       class="form-control @error('disco') is-invalid @enderror"
                       value="{{ old('disco', $equipo->disco ?? '') }}"
                       placeholder="Ej: 512 GB SSD" maxlength="50">
                @error('disco') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label fw-medium">Sistema Operativo</label>
                <input type="text" name="sistema_operativo"
                       class="form-control @error('sistema_operativo') is-invalid @enderror"
                       value="{{ old('sistema_operativo', $equipo->sistema_operativo ?? '') }}"
                       maxlength="100">
                @error('sistema_operativo') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label fw-medium">Fecha de Compra</label>
                <input type="date" name="fecha_compra"
                       class="form-control @error('fecha_compra') is-invalid @enderror"
                       value="{{ old('fecha_compra', isset($equipo->fecha_compra) ? $equipo->fecha_compra->format('Y-m-d') : '') }}">
                @error('fecha_compra') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label fw-medium">Fin de Garantía</label>
                <input type="date" name="fin_garantia"
                       class="form-control @error('fin_garantia') is-invalid @enderror"
                       value="{{ old('fin_garantia', isset($equipo->fin_garantia) ? $equipo->fin_garantia->format('Y-m-d') : '') }}">
                @error('fin_garantia') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12 col-md-4">
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
        @if (!$equipo->exists)
            <div class="alert alert-light border mb-3">
                <strong>Registro inicial sin funcionario asignado.</strong>
                El funcionario se registra posteriormente desde el modulo de préstamos.
            </div>
        @endif
        <div class="row g-3">
            <div class="col-12 col-md-4">
                <label class="form-label fw-medium">Empresa Propietaria del Equipo</label>
                <input type="text" name="usuario_empresa_propietaria"
                       class="form-control @error('usuario_empresa_propietaria') is-invalid @enderror"
                       value="{{ old('usuario_empresa_propietaria', $equipo->usuarioAsignado->empresa_propietaria ?? '') }}"
                       maxlength="150">
                @error('usuario_empresa_propietaria') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label fw-medium">Dependencia</label>
                <input type="text" name="usuario_dependencia"
                       class="form-control @error('usuario_dependencia') is-invalid @enderror"
                       value="{{ old('usuario_dependencia', $equipo->usuarioAsignado->dependencia ?? '') }}"
                       maxlength="150">
                @error('usuario_dependencia') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label fw-medium">Fuente de Recurso</label>
                <input type="text" name="usuario_fuente_recurso"
                       class="form-control @error('usuario_fuente_recurso') is-invalid @enderror"
                       value="{{ old('usuario_fuente_recurso', $equipo->usuarioAsignado->fuente_recurso ?? '') }}"
                       maxlength="150">
                @error('usuario_fuente_recurso') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label fw-medium">Empresa Funcionario</label>
                <input type="text" name="usuario_empresa_funcionario"
                       class="form-control @error('usuario_empresa_funcionario') is-invalid @enderror"
                       value="{{ old('usuario_empresa_funcionario', $equipo->usuarioAsignado->empresa_funcionario ?? '') }}"
                       maxlength="150">
                @error('usuario_empresa_funcionario') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label fw-medium">Empleado o Contratista</label>
                <input type="text" name="usuario_tipo_vinculacion"
                       class="form-control @error('usuario_tipo_vinculacion') is-invalid @enderror"
                       value="{{ old('usuario_tipo_vinculacion', $equipo->usuarioAsignado->tipo_vinculacion ?? '') }}"
                       maxlength="100">
                @error('usuario_tipo_vinculacion') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label fw-medium">Shortname</label>
                <input type="text" name="usuario_shortname"
                       class="form-control @error('usuario_shortname') is-invalid @enderror"
                       value="{{ old('usuario_shortname', $equipo->usuarioAsignado->shortname ?? '') }}"
                       maxlength="100">
                @error('usuario_shortname') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12 col-md-6">
                  <label class="form-label fw-medium">Nombre</label>
                <input type="text" name="usuario_nombre"
                       class="form-control @error('usuario_nombre') is-invalid @enderror"
                       value="{{ old('usuario_nombre', $equipo->usuarioAsignado->nombre ?? '') }}"
                      maxlength="150">
                @error('usuario_nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12 col-md-3">
                  <label class="form-label fw-medium">Cédula</label>
                <input type="text" name="usuario_cedula"
                       class="form-control @error('usuario_cedula') is-invalid @enderror"
                       value="{{ old('usuario_cedula', $equipo->usuarioAsignado->cedula ?? '') }}"
                      maxlength="20">
                @error('usuario_cedula') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label fw-medium">Ciudad</label>
                <input type="text" name="usuario_ciudad"
                       class="form-control @error('usuario_ciudad') is-invalid @enderror"
                       value="{{ old('usuario_ciudad', $equipo->usuarioAsignado->ciudad ?? '') }}"
                       maxlength="100">
                @error('usuario_ciudad') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label fw-medium">Departamento</label>
                <input type="text" name="usuario_departamento"
                       class="form-control @error('usuario_departamento') is-invalid @enderror"
                       value="{{ old('usuario_departamento', $equipo->usuarioAsignado->departamento ?? '') }}"
                       maxlength="100">
                @error('usuario_departamento') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label fw-medium">Cargo</label>
                <input type="text" name="usuario_cargo"
                       class="form-control @error('usuario_cargo') is-invalid @enderror"
                       value="{{ old('usuario_cargo', $equipo->usuarioAsignado->cargo ?? '') }}"
                       maxlength="100">
                @error('usuario_cargo') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label fw-medium">Área</label>
                <input type="text" name="usuario_area"
                       class="form-control @error('usuario_area') is-invalid @enderror"
                       value="{{ old('usuario_area', $equipo->usuarioAsignado->area ?? '') }}"
                       maxlength="100">
                @error('usuario_area') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label fw-medium">Piso</label>
                <input type="text" name="usuario_piso"
                       class="form-control @error('usuario_piso') is-invalid @enderror"
                       value="{{ old('usuario_piso', $equipo->usuarioAsignado->piso ?? '') }}"
                       maxlength="20">
                @error('usuario_piso') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label fw-medium">Distrito</label>
                <input type="text" name="usuario_distrito"
                       class="form-control @error('usuario_distrito') is-invalid @enderror"
                       value="{{ old('usuario_distrito', $equipo->usuarioAsignado->distrito ?? '') }}"
                       maxlength="150">
                @error('usuario_distrito') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label fw-medium">Seccional</label>
                <input type="text" name="usuario_seccional"
                       class="form-control @error('usuario_seccional') is-invalid @enderror"
                       value="{{ old('usuario_seccional', $equipo->usuarioAsignado->seccional ?? '') }}"
                       maxlength="150">
                @error('usuario_seccional') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
    </div>
</div>

{{-- === RESPONSABLE DEL ACTIVO === --}}
<div class="card mb-4">
    <div class="card-header bg-info bg-opacity-10 border-0 fw-semibold py-3">
        <i class="bi bi-person-badge me-2 text-info"></i>Responsable del Activo
    </div>
    <div class="card-body">
        @if (!$equipo->exists)
            {{-- CREATE: Mostrar tarjeta informativa --}}
            <div class="alert alert-info border-0 rounded d-flex align-items-center">
                <i class="bi bi-info-circle me-3" style="font-size: 1.25rem;"></i>
                <div>
                    <strong>Responsable del Activo (Asignación Automática):</strong><br>
                    El responsable inicial se asigna automáticamente al Analista TIC institucional (rol Soporte TI). 
                    Esta responsabilidad puede ser modificada posteriormente por usuarios con permisos de edición, 
                    según los cambios administrativos en el control del activo.
                </div>
            </div>
        @else
            {{-- EDIT: Mostrar campos editables --}}
            <div class="row g-3">
                <div class="col-12 col-md-3">
                    <label class="form-label fw-medium">Cédula</label>
                    <input type="text" name="responsable_cedula"
                           class="form-control @error('responsable_cedula') is-invalid @enderror"
                           value="{{ old('responsable_cedula', $equipo->responsable_cedula ?? '') }}"
                           maxlength="20">
                    @error('responsable_cedula') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-12 col-md-5">
                    <label class="form-label fw-medium">Nombre</label>
                    <input type="text" name="responsable_nombre"
                           class="form-control @error('responsable_nombre') is-invalid @enderror"
                           value="{{ old('responsable_nombre', $equipo->responsable_nombre ?? '') }}"
                           maxlength="150">
                    @error('responsable_nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label fw-medium">Cargo</label>
                    <input type="text" name="responsable_cargo"
                           class="form-control @error('responsable_cargo') is-invalid @enderror"
                           value="{{ old('responsable_cargo', $equipo->responsable_cargo ?? '') }}"
                           maxlength="100">
                    @error('responsable_cargo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label fw-medium">Ciudad</label>
                    <input type="text" name="responsable_ciudad"
                           class="form-control @error('responsable_ciudad') is-invalid @enderror"
                           value="{{ old('responsable_ciudad', $equipo->responsable_ciudad ?? '') }}"
                           maxlength="100">
                    @error('responsable_ciudad') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label fw-medium">Área</label>
                    <input type="text" name="responsable_area"
                           class="form-control @error('responsable_area') is-invalid @enderror"
                           value="{{ old('responsable_area', $equipo->responsable_area ?? '') }}"
                           maxlength="100">
                    @error('responsable_area') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label fw-medium">Tipo de Recurso</label>
                    <input type="text" name="responsable_tipo_recurso"
                           class="form-control @error('responsable_tipo_recurso') is-invalid @enderror"
                           value="{{ old('responsable_tipo_recurso', $equipo->responsable_tipo_recurso ?? '') }}"
                           maxlength="100">
                    @error('responsable_tipo_recurso') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label fw-medium">Fecha Inicio</label>
                    <input type="date" name="fecha_inicio_responsable"
                           class="form-control @error('fecha_inicio_responsable') is-invalid @enderror"
                           value="{{ old('fecha_inicio_responsable', isset($equipo->fecha_inicio_responsable) ? $equipo->fecha_inicio_responsable->format('Y-m-d') : '') }}">
                    @error('fecha_inicio_responsable') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label fw-medium">Fecha Fin</label>
                    <input type="date" name="fecha_fin_responsable"
                           class="form-control @error('fecha_fin_responsable') is-invalid @enderror"
                           value="{{ old('fecha_fin_responsable', isset($equipo->fecha_fin_responsable) ? $equipo->fecha_fin_responsable->format('Y-m-d') : '') }}">
                    @error('fecha_fin_responsable') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
        @endif
    </div>
</div>

{{-- === PERIFÉRICOS === --}}
<div class="card mb-4">
    <div class="card-header bg-warning bg-opacity-10 border-0 fw-semibold py-3">
        <i class="bi bi-usb-plug me-2 text-warning"></i>Periféricos
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-12 col-md-3">
                <label class="form-label fw-medium">Teléfono Fijo</label>
                <input type="text" name="periferico_telefono"
                       class="form-control @error('periferico_telefono') is-invalid @enderror"
                       value="{{ old('periferico_telefono', $equipo->periferico->telefono ?? '') }}"
                       maxlength="100">
                @error('periferico_telefono') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label fw-medium">Teclado</label>
                <input type="text" name="periferico_teclado"
                       class="form-control @error('periferico_teclado') is-invalid @enderror"
                       value="{{ old('periferico_teclado', $equipo->periferico->teclado ?? '') }}"
                       maxlength="100">
                @error('periferico_teclado') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label fw-medium">Mouse</label>
                <input type="text" name="periferico_mouse"
                       class="form-control @error('periferico_mouse') is-invalid @enderror"
                       value="{{ old('periferico_mouse', $equipo->periferico->mouse ?? '') }}"
                       maxlength="100">
                @error('periferico_mouse') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-12 col-md-3">
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

{{-- === CAMPOS PERSONALIZADOS (DINÁMICOS) === --}}
@if(isset($camposPersonalizados) && $camposPersonalizados->count() > 0)
<div class="card mb-4">
    <div class="card-header bg-dark bg-opacity-10 border-0 fw-semibold py-3">
        <i class="bi bi-ui-checks-grid me-2 text-dark"></i>Información Adicional (Personalizada)
    </div>
    <div class="card-body">
        <div class="row g-3">
            @foreach($camposPersonalizados as $campo)
                @php
                    $valorActual = null;
                    if (isset($equipo)) {
                        $valorGuardado = $equipo->camposPersonalizadosValores->where('campo_personalizado_id', $campo->id)->first();
                        $valorActual = $valorGuardado ? $valorGuardado->valor : null;
                    }
                    $valorOld = old("campos_personalizados.{$campo->id}", $valorActual);
                @endphp
                
                <div class="col-12 col-md-4">
                    <label class="form-label fw-medium">{{ $campo->nombre }} {!! $campo->obligatorio ? '<span class="text-danger">*</span>' : '' !!}</label>
                    
                    @if($campo->tipo === 'texto')
                        <input type="text" name="campos_personalizados[{{ $campo->id }}]" class="form-control" value="{{ $valorOld }}" {{ $campo->obligatorio ? 'required' : '' }}>
                    
                    @elseif($campo->tipo === 'textarea')
                        <textarea name="campos_personalizados[{{ $campo->id }}]" class="form-control" rows="2" {{ $campo->obligatorio ? 'required' : '' }}>{{ $valorOld }}</textarea>
                    
                    @elseif($campo->tipo === 'numero')
                        <input type="number" step="any" name="campos_personalizados[{{ $campo->id }}]" class="form-control" value="{{ $valorOld }}" {{ $campo->obligatorio ? 'required' : '' }}>
                    
                    @elseif($campo->tipo === 'fecha')
                        <input type="date" name="campos_personalizados[{{ $campo->id }}]" class="form-control" value="{{ $valorOld }}" {{ $campo->obligatorio ? 'required' : '' }}>
                    
                    @elseif($campo->tipo === 'boolean')
                        <select name="campos_personalizados[{{ $campo->id }}]" class="form-select" {{ $campo->obligatorio ? 'required' : '' }}>
                            <option value="">Seleccione...</option>
                            <option value="1" {{ $valorOld == '1' ? 'selected' : '' }}>Sí</option>
                            <option value="0" {{ $valorOld == '0' ? 'selected' : '' }}>No</option>
                        </select>
                        
                    @elseif($campo->tipo === 'select')
                        <select name="campos_personalizados[{{ $campo->id }}]" class="form-select" {{ $campo->obligatorio ? 'required' : '' }}>
                            <option value="">Seleccione...</option>
                            @foreach($campo->opciones as $opcion)
                                <option value="{{ $opcion->valor }}" {{ $valorOld == $opcion->valor ? 'selected' : '' }}>{{ $opcion->valor }}</option>
                            @endforeach
                        </select>
                        
                    @elseif($campo->tipo === 'multiselect')
                        @php
                            // Intentar decodificar si es JSON
                            $valoresSeleccionados = is_string($valorOld) ? json_decode($valorOld, true) : $valorOld;
                            if(!is_array($valoresSeleccionados)) $valoresSeleccionados = [];
                        @endphp
                        <select name="campos_personalizados[{{ $campo->id }}][]" class="form-select" multiple {{ $campo->obligatorio ? 'required' : '' }} size="3">
                            @foreach($campo->opciones as $opcion)
                                <option value="{{ $opcion->valor }}" {{ in_array($opcion->valor, $valoresSeleccionados) ? 'selected' : '' }}>{{ $opcion->valor }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Mantén presionado Ctrl (Windows) o Cmd (Mac) para seleccionar varios.</small>
                    @else
                        <input type="text" name="campos_personalizados[{{ $campo->id }}]" class="form-control" value="{{ $valorOld }}" {{ $campo->obligatorio ? 'required' : '' }}>
                    @endif
                    
                    @if($campo->descripcion)
                        <small class="text-muted fst-italic">{{ $campo->descripcion }}</small>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
    // Mostrar/ocultar razón de estado
    document.getElementById('estadoSelect').addEventListener('change', function () {
        const wrap = document.getElementById('razonEstadoWrap');
        wrap.style.display = ['mantenimiento', 'baja'].includes(this.value) ? 'block' : 'none';
    });

    // Lógica para checkbox "sin serial"
    document.addEventListener('DOMContentLoaded', function() {
        const serialInput = document.getElementById('serialInput');
        const sinSerialCheck = document.getElementById('sinSerialCheck');
        
        function toggleSerialInput() {
            if (sinSerialCheck.checked) {
                serialInput.value = '';
                serialInput.disabled = true;
            } else {
                serialInput.disabled = false;
            }
        }
        
        if(sinSerialCheck && serialInput) {
            sinSerialCheck.addEventListener('change', toggleSerialInput);
            toggleSerialInput(); // init
        }
    });
</script>
@endpush
