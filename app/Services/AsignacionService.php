<?php

namespace App\Services;

use App\Models\Asignacion;
use App\Models\Equipo;
use App\Models\HistorialAdministrativo;
use App\Models\HistorialTecnico;
use App\Models\Funcionario;
use App\Models\User;
use App\Models\UsuarioAsignado;
use App\Services\HistorialTecnicoService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AsignacionService
{
    public function __construct(
        private readonly HistorialService $historialService,
        private readonly HistorialTecnicoService $historialTecnicoService
    ) {}

    /**
     * Asigna un equipo a un usuario nuevo (sin asignación previa o con retiro previo).
     */
    public function asignar(Equipo $equipo, array $datos, User $responsable): Asignacion
    {
        return DB::transaction(function () use ($equipo, $datos, $responsable) {

            // Crear o actualizar usuario_asignado (tabla activa actual)
            $equipo->usuarioAsignado()->updateOrCreate(
                ['equipo_id' => $equipo->id],
                $this->camposUsuario($datos)
            );

            // Registrar en historial de asignaciones
            $asignacion = $equipo->asignaciones()->create([
                ...$this->camposSnapshot($datos),
                'tipo_accion'  => Asignacion::TIPO_ASIGNACION,
                'motivo'       => $datos['motivo'] ?? null,
                'observaciones'=> $datos['observaciones'] ?? null,
                'entregado_por'=> $datos['entregado_por'] ?? null,
                'user_id'      => $responsable->id,
                'fecha_accion' => $datos['fecha_accion'] ?? now(),
            ]);

            // Registrar en historial administrativo
            $this->historialService->registrarCambio(
                $equipo,
                'asignacion',
                null,
                $datos['nombre'] ?? 'N/A',
                "Equipo asignado a: {$datos['nombre']}",
                $responsable
            );

            // Cambiar estado a asignado
            $equipo->update(['estado_operativo' => 'asignado', 'razon_estado' => null]);

            // Sincronizar con el catálogo global de funcionarios
            $this->sincronizarFuncionario($datos);

            return $asignacion;
        });
    }

    /**
     * Reemplaza la asignación actual por un nuevo usuario.
     */
    public function reemplazar(Equipo $equipo, array $datos, User $responsable): Asignacion
    {
        return DB::transaction(function () use ($equipo, $datos, $responsable) {

            $usuarioAnterior = $equipo->usuarioAsignado?->nombre ?? 'Sin asignar';

            // Actualizar usuario activo
            $equipo->usuarioAsignado()->updateOrCreate(
                ['equipo_id' => $equipo->id],
                $this->camposUsuario($datos)
            );

            // Registrar reemplazo
            $asignacion = $equipo->asignaciones()->create([
                ...$this->camposSnapshot($datos),
                'tipo_accion'  => Asignacion::TIPO_REEMPLAZO,
                'motivo'       => $datos['motivo'] ?? null,
                'observaciones'=> $datos['observaciones'] ?? null,
                'entregado_por'=> $datos['entregado_por'] ?? null,
                'user_id'      => $responsable->id,
                'fecha_accion' => $datos['fecha_accion'] ?? now(),
            ]);

            $this->historialService->registrarCambio(
                $equipo,
                'asignacion',
                $usuarioAnterior,
                $datos['nombre'] ?? 'N/A',
                "Reemplazo: {$usuarioAnterior} → {$datos['nombre']}",
                $responsable
            );

            // Cambiar estado a asignado
            $equipo->update(['estado_operativo' => 'asignado', 'razon_estado' => null]);

            // Sincronizar con el catálogo global de funcionarios
            $this->sincronizarFuncionario($datos);

            return $asignacion;
        });
    }

    /**
     * Retira la asignación activa del equipo.
     */
    public function retirar(Equipo $equipo, string $motivo, User $responsable, ?string $observaciones = null): Asignacion
    {
        return DB::transaction(function () use ($equipo, $motivo, $responsable, $observaciones) {

            $usuario = $equipo->usuarioAsignado;
            $nombreAnterior = $usuario?->nombre ?? 'Sin asignar';

            // Snapshot del usuario retirado
            $snapshot = $usuario ? $this->camposSnapshotDesdeModel($usuario) : [];

            // Eliminar asignación activa
            $usuario?->delete();

            // Registrar retiro
            $asignacion = $equipo->asignaciones()->create([
                ...$snapshot,
                'tipo_accion'  => Asignacion::TIPO_RETIRO,
                'motivo'       => $motivo,
                'observaciones'=> $observaciones,
                'user_id'      => $responsable->id,
                'fecha_accion' => now(),
            ]);

            $this->historialService->registrarCambio(
                $equipo,
                'retiro',
                $nombreAnterior,
                null,
                "Retiro de asignación: {$nombreAnterior}. Motivo: {$motivo}",
                $responsable
            );

            // Cambiar estado a disponible
            $equipo->update(['estado_operativo' => 'disponible', 'razon_estado' => null]);

            return $asignacion;
        });
    }

    /**
     * Pasa el equipo a estado mantenimiento.
     */
    public function pasarAMantenimiento(Equipo $equipo, string $motivo, User $responsable, ?string $observaciones = null): Asignacion
    {
        return DB::transaction(function () use ($equipo, $motivo, $responsable, $observaciones) {

            $nombreUsuario = $equipo->usuarioAsignado?->nombre ?? 'Sin asignar';
            $snapshot      = $equipo->usuarioAsignado
                ? $this->camposSnapshotDesdeModel($equipo->usuarioAsignado)
                : [];

            // Cambiar estado del equipo
            $estadoAnterior = $equipo->estado_operativo;
            $equipo->update(['estado_operativo' => 'mantenimiento', 'razon_estado' => $motivo]);

            $asignacion = $equipo->asignaciones()->create([
                ...$snapshot,
                'tipo_accion'  => Asignacion::TIPO_MANTENIMIENTO,
                'motivo'       => $motivo,
                'observaciones'=> $observaciones,
                'user_id'      => $responsable->id,
                'fecha_accion' => now(),
            ]);

            $this->historialService->registrarCambio(
                $equipo,
                'cambio_estado',
                $estadoAnterior,
                'mantenimiento',
                "Estado cambiado a Mantenimiento. Motivo: {$motivo}",
                $responsable
            );

            // Crear registro automático en Historial Técnico para que aparezca en el módulo
            $this->historialTecnicoService->registrarEvento(
                $equipo,
                [
                    'tipo_evento'         => 'mantenimiento_preventivo',
                    'descripcion'         => "Enviado a mantenimiento desde asignaciones. Motivo: {$motivo}",
                    'observaciones'       => $observaciones,
                    'fecha_evento'        => now(),
                    'usuario_responsable' => $responsable->name,
                ],
                null,
                $responsable->id
            );

            return $asignacion;
        });
    }

    /**
     * Da de baja el equipo.
     */
    public function darDeBaja(Equipo $equipo, string $motivo, User $responsable, ?string $observaciones = null): Asignacion
    {
        return DB::transaction(function () use ($equipo, $motivo, $responsable, $observaciones) {

            $snapshot = $equipo->usuarioAsignado
                ? $this->camposSnapshotDesdeModel($equipo->usuarioAsignado)
                : [];

            $estadoAnterior = $equipo->estado_operativo;
            $equipo->update(['estado_operativo' => 'baja', 'razon_estado' => $motivo]);

            $asignacion = $equipo->asignaciones()->create([
                ...$snapshot,
                'tipo_accion'  => Asignacion::TIPO_BAJA,
                'motivo'       => $motivo,
                'observaciones'=> $observaciones,
                'user_id'      => $responsable->id,
                'fecha_accion' => now(),
            ]);

            $this->historialService->registrarCambio(
                $equipo,
                'cambio_estado',
                $estadoAnterior,
                'baja',
                "Equipo dado de baja. Motivo: {$motivo}",
                $responsable
            );

            return $asignacion;
        });
    }

    /**
     * Restaura un equipo previamente dado de baja o en mantenimiento.
     */
    public function restaurar(Equipo $equipo, string $motivo, User $responsable): Asignacion
    {
        return DB::transaction(function () use ($equipo, $motivo, $responsable) {

            $estadoAnterior = $equipo->estado_operativo;
            $equipo->update(['estado_operativo' => 'activo', 'razon_estado' => null]);

            $asignacion = $equipo->asignaciones()->create([
                'tipo_accion'  => Asignacion::TIPO_RESTAURACION,
                'motivo'       => $motivo,
                'user_id'      => $responsable->id,
                'fecha_accion' => now(),
            ]);

            $this->historialService->registrarCambio(
                $equipo,
                'restauracion',
                $estadoAnterior,
                'activo',
                "Equipo restaurado a Activo. Motivo: {$motivo}",
                $responsable
            );

            return $asignacion;
        });
    }

    // ── Helpers privados ──────────────────────────────────────────────────────

    /**
     * Campos para crear/actualizar el UsuarioAsignado activo.
     */
    private function camposUsuario(array $datos): array
    {
        return [
            'nombre'               => $datos['nombre'] ?? null,
            'cedula'               => $datos['cedula'] ?? null,
            'empresa_propietaria'  => $datos['empresa_propietaria'] ?? null,
            'dependencia'          => $datos['dependencia'] ?? null,
            'fuente_recurso'       => $datos['fuente_recurso'] ?? null,
            'empresa_funcionario'  => $datos['empresa_funcionario'] ?? null,
            'tipo_vinculacion'     => $datos['tipo_vinculacion'] ?? null,
            'shortname'            => $datos['shortname'] ?? null,
            'departamento'         => $datos['departamento'] ?? null,
            'ciudad'               => $datos['ciudad'] ?? null,
            'cargo'                => $datos['cargo'] ?? null,
            'area'                 => $datos['area'] ?? null,
            'piso'                 => $datos['piso'] ?? null,
            'distrito'             => $datos['distrito'] ?? null,
            'seccional'            => $datos['seccional'] ?? null,
        ];
    }

    /**
     * Campos snapshot prefijados con usuario_ para la tabla asignaciones.
     */
    private function camposSnapshot(array $datos): array
    {
        return [
            'usuario_nombre'               => $datos['nombre'] ?? null,
            'usuario_cedula'               => $datos['cedula'] ?? null,
            'usuario_empresa_propietaria'  => $datos['empresa_propietaria'] ?? null,
            'usuario_dependencia'          => $datos['dependencia'] ?? null,
            'usuario_fuente_recurso'       => $datos['fuente_recurso'] ?? null,
            'usuario_empresa_funcionario'  => $datos['empresa_funcionario'] ?? null,
            'usuario_tipo_vinculacion'     => $datos['tipo_vinculacion'] ?? null,
            'usuario_shortname'            => $datos['shortname'] ?? null,
            'usuario_departamento'         => $datos['departamento'] ?? null,
            'usuario_ciudad'               => $datos['ciudad'] ?? null,
            'usuario_cargo'                => $datos['cargo'] ?? null,
            'usuario_area'                 => $datos['area'] ?? null,
            'usuario_piso'                 => $datos['piso'] ?? null,
            'usuario_distrito'             => $datos['distrito'] ?? null,
            'usuario_seccional'            => $datos['seccional'] ?? null,
        ];
    }

    /**
     * Genera snapshot desde un modelo UsuarioAsignado existente.
     */
    private function camposSnapshotDesdeModel(UsuarioAsignado $ua): array
    {
        return [
            'usuario_nombre'               => $ua->nombre,
            'usuario_cedula'               => $ua->cedula,
            'usuario_empresa_propietaria'  => $ua->empresa_propietaria,
            'usuario_dependencia'          => $ua->dependencia,
            'usuario_fuente_recurso'       => $ua->fuente_recurso,
            'usuario_empresa_funcionario'  => $ua->empresa_funcionario,
            'usuario_tipo_vinculacion'     => $ua->tipo_vinculacion,
            'usuario_shortname'            => $ua->shortname,
            'usuario_departamento'         => $ua->departamento,
            'usuario_ciudad'               => $ua->ciudad,
            'usuario_cargo'                => $ua->cargo,
            'usuario_area'                 => $ua->area,
            'usuario_piso'                 => $ua->piso,
            'usuario_distrito'             => $ua->distrito,
            'usuario_seccional'            => $ua->seccional,
        ];
    }

    /**
     * Sincroniza el usuario asignado con la tabla de funcionarios (catálogo global).
     */
    private function sincronizarFuncionario(array $datos): void
    {
        $cedula = $datos['cedula'] ?? null;

        if (empty($cedula)) {
            return;
        }

        $nombreCompleto = trim($datos['nombre'] ?? '');
        $partes         = explode(' ', $nombreCompleto, 2);
        $nombres        = $partes[0] ?? $nombreCompleto;
        $apellidos      = $partes[1] ?? null;

        $funcionario = Funcionario::withTrashed()->updateOrCreate(
            ['identificacion' => $cedula],
            [
                'nombres'             => $nombres,
                'apellidos'           => $apellidos,
                'cargo'               => $datos['cargo'] ?? null,
                'area'                => $datos['area'] ?? null,
                'departamento'        => $datos['departamento'] ?? null,
                'ciudad'              => $datos['ciudad'] ?? null,
                'empresa_funcionario' => $datos['empresa_funcionario'] ?? null,
                'tipo_vinculacion'    => $datos['tipo_vinculacion'] ?? null,
                'estado'              => 'Activo',
            ]
        );
        
        if ($funcionario->trashed()) {
            $funcionario->restore();
        }
    }
}
