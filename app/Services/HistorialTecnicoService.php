<?php

namespace App\Services;

use App\Models\Equipo;
use App\Models\HistorialTecnico;
use Illuminate\Http\UploadedFile;

class HistorialTecnicoService
{
    /**
     * Registra un nuevo evento técnico, incluyendo archivos y el snapshot del usuario.
     */
    public function registrarEvento(Equipo $equipo, array $datos, ?array $archivosSubidos = null, int $userId): HistorialTecnico
    {
        $usuario = $equipo->usuarioAsignado;

        // Capturar snapshot del usuario asignado en este momento
        $snapshot = $usuario ? [
            'nombre'      => $usuario->nombre,
            'cedula'      => $usuario->cedula,
            'cargo'       => $usuario->cargo,
            'area'        => $usuario->area,
            'dependencia' => $usuario->dependencia,
            'shortname'   => $usuario->shortname,
            'distrito'    => $usuario->distrito,
            'seccional'   => $usuario->seccional,
        ] : null;

        // Manejar archivos adjuntos
        $archivosFormateados = [];
        if (!empty($archivosSubidos)) {
            foreach ($archivosSubidos as $archivo) {
                if ($archivo instanceof UploadedFile) {
                    $ruta = $archivo->store('historial_tecnicos', 'public');
                    $archivosFormateados[] = [
                        'nombre' => $archivo->getClientOriginalName(),
                        'ruta'   => $ruta,
                        'mime'   => $archivo->getMimeType(),
                    ];
                }
            }
        }

        return HistorialTecnico::create([
            'fecha_evento'              => now(),
            ...$datos,
            'equipo_id'                 => $equipo->id,
            'usuario_asignado_snapshot' => $snapshot,
            'archivos'                  => !empty($archivosFormateados) ? $archivosFormateados : null,
            'user_id'                   => $userId,
        ]);
    }
}
