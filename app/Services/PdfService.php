<?php

namespace App\Services;

use App\Models\Asignacion;
use App\Models\PlantillaPdf;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class PdfService
{
    /**
     * Obtiene la plantilla activa para un tipo dado.
     * Si no existe plantilla activa, retorna null (se usará la vista por defecto).
     */
    public function obtenerPlantillaActiva(string $tipo = 'acta_entrega'): ?PlantillaPdf
    {
        return PlantillaPdf::where('tipo', $tipo)
            ->where('activa', true)
            ->latest()
            ->first();
    }

    /**
     * Genera el PDF del acta de entrega para una asignación.
     * Descarga directamente como respuesta HTTP.
     */
    public function generarActaEntrega(Asignacion $asignacion): Response
    {
        $asignacion->load(['equipo.tipoRecurso', 'registradoPor']);
        $equipo = $asignacion->equipo;

        $datos = $this->prepararDatos($asignacion, $equipo);

        // Intentar usar plantilla personalizada
        $plantilla = $this->obtenerPlantillaActiva('acta_entrega');

        if ($plantilla) {
            $contenidoHtml = $plantilla->procesarVariables($datos);
            $html          = view('pdf.acta_entrega_wrapper', compact('contenidoHtml', 'equipo', 'asignacion'))->render();
        } else {
            // Vista por defecto
            $html = view('pdf.acta_entrega', compact('asignacion', 'equipo', 'datos'))->render();
        }

        $pdf = Pdf::loadHTML($html)
            ->setPaper('letter', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled'         => false,
                'defaultFont'          => 'sans-serif',
            ]);

        $nombreArchivo = sprintf(
            'acta_entrega_%s_%s.pdf',
            str_replace(' ', '_', $equipo->nombre_equipo ?? $equipo->serial),
            now()->format('Ymd_His')
        );

        return $pdf->stream($nombreArchivo);
    }

    /**
     * Genera el PDF del acta de entrega usando los datos actuales del equipo
     * en lugar de un snapshot de asignación.
     */
    public function generarActaDesdeEquipo(\App\Models\Equipo $equipo): Response
    {
        $equipo->load(['tipoRecurso', 'usuarioAsignado']);
        
        $datos = $this->prepararDatosDesdeEquipo($equipo);

        // Intentar usar plantilla personalizada
        $plantilla = $this->obtenerPlantillaActiva('acta_entrega');

        if ($plantilla) {
            $contenidoHtml = $plantilla->procesarVariables($datos);
            $html          = view('pdf.acta_entrega_wrapper', compact('contenidoHtml', 'equipo'))->render();
        } else {
            // Vista por defecto
            $html = view('pdf.acta_entrega', ['equipo' => $equipo, 'asignacion' => null, 'datos' => $datos])->render();
        }

        $pdf = Pdf::loadHTML($html)
            ->setPaper('letter', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled'         => false,
                'defaultFont'          => 'sans-serif',
            ]);

        $nombreArchivo = sprintf(
            'acta_entrega_%s_%s.pdf',
            str_replace(' ', '_', $equipo->nombre_equipo ?? $equipo->serial),
            now()->format('Ymd_His')
        );

        return $pdf->stream($nombreArchivo);
    }

    /**
     * Prepara el array de datos para reemplazo de variables en la plantilla.
     */
    private function prepararDatos(Asignacion $asignacion, \App\Models\Equipo $equipo): array
    {
        return [
            // Equipo
            'nombre_equipo'    => $equipo->nombre_equipo,
            'serial'           => $equipo->serial,
            'activo_fijo'      => $equipo->activo_fijo ?? '—',
            'placa'            => $equipo->placa ?? '—',
            'marca'            => $equipo->marca,
            'modelo'           => $equipo->modelo,
            'tipo_recurso'     => $equipo->tipoRecurso?->nombre ?? '—',
            'procesador'       => $equipo->procesador ?? '—',
            'ram'              => $equipo->ram ?? '—',
            'disco'            => $equipo->disco ?? '—',
            'sistema_operativo'=> $equipo->sistema_operativo ?? '—',
            'estado_operativo' => ucfirst($equipo->estado_operativo),
            'fecha_compra'     => $equipo->fecha_compra?->format('d/m/Y') ?? '—',
            'fin_garantia'     => $equipo->fin_garantia?->format('d/m/Y') ?? '—',
            // Usuario asignado (snapshot)
            'nombre_usuario'      => $asignacion->usuario_nombre ?? '—',
            'cedula'              => $asignacion->usuario_cedula ?? '—',
            'cargo'               => $asignacion->usuario_cargo ?? '—',
            'area'                => $asignacion->usuario_area ?? '—',
            'dependencia'         => $asignacion->usuario_dependencia ?? '—',
            'empresa_propietaria' => $asignacion->usuario_empresa_propietaria ?? '—',
            'empresa_funcionario' => $asignacion->usuario_empresa_funcionario ?? '—',
            'departamento'        => $asignacion->usuario_departamento ?? '—',
            'ciudad'              => $asignacion->usuario_ciudad ?? '—',
            'piso'                => $asignacion->usuario_piso ?? '—',
            'distrito'            => $asignacion->usuario_distrito ?? '—',
            'seccional'           => $asignacion->usuario_seccional ?? '—',
            'shortname'           => $asignacion->usuario_shortname ?? '—',
            // Asignación
            'fecha_asignacion' => $asignacion->fecha_accion?->format('d/m/Y H:i') ?? '—',
            'entregado_por'    => $asignacion->entregado_por ?? '—',
            'tipo_accion'      => $asignacion->tipo_accion_label,
            'motivo'           => $asignacion->motivo ?? '—',
            // Sistema
            'fecha_generacion' => now()->format('d/m/Y H:i'),
            'usuario_sistema'  => auth()->user()?->name ?? '—',
        ];
    }

    /**
     * Prepara el array de datos para reemplazo de variables usando el modelo actual del equipo.
     */
    private function prepararDatosDesdeEquipo(\App\Models\Equipo $equipo): array
    {
        $usuario = $equipo->usuarioAsignado;
        return [
            // Equipo
            'nombre_equipo'    => $equipo->nombre_equipo,
            'serial'           => $equipo->serial,
            'activo_fijo'      => $equipo->activo_fijo ?? '—',
            'placa'            => $equipo->placa ?? '—',
            'marca'            => $equipo->marca,
            'modelo'           => $equipo->modelo,
            'tipo_recurso'     => $equipo->tipoRecurso?->nombre ?? '—',
            'procesador'       => $equipo->procesador ?? '—',
            'ram'              => $equipo->ram ?? '—',
            'disco'            => $equipo->disco ?? '—',
            'sistema_operativo'=> $equipo->sistema_operativo ?? '—',
            'estado_operativo' => ucfirst($equipo->estado_operativo),
            'fecha_compra'     => $equipo->fecha_compra?->format('d/m/Y') ?? '—',
            'fin_garantia'     => $equipo->fin_garantia?->format('d/m/Y') ?? '—',
            // Usuario asignado
            'nombre_usuario'      => $usuario->nombre ?? '—',
            'cedula'              => $usuario->cedula ?? '—',
            'cargo'               => $usuario->cargo ?? '—',
            'area'                => $usuario->area ?? '—',
            'dependencia'         => $usuario->dependencia ?? '—',
            'empresa_propietaria' => $usuario->empresa_propietaria ?? '—',
            'empresa_funcionario' => $usuario->empresa_funcionario ?? '—',
            'departamento'        => $usuario->departamento ?? '—',
            'ciudad'              => $usuario->ciudad ?? '—',
            'piso'                => $usuario->piso ?? '—',
            'distrito'            => $usuario->distrito ?? '—',
            'seccional'           => $usuario->seccional ?? '—',
            'shortname'           => $usuario->shortname ?? '—',
            // Asignación (simulada)
            'fecha_asignacion' => $usuario->created_at?->format('d/m/Y H:i') ?? '—',
            'entregado_por'    => '—', // no se guarda explícitamente en usuarioAsignado
            'tipo_accion'      => 'Asignación Actual',
            'motivo'           => '—',
            // Sistema
            'fecha_generacion' => now()->format('d/m/Y H:i'),
            'usuario_sistema'  => auth()->user()?->name ?? '—',
        ];
    }
}
