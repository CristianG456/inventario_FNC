<?php

namespace App\Exports;

use App\Models\Equipo;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class EquiposExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithTitle
{
    /**
     * Carga los equipos con sus relaciones (sin periféricos).
     */
    public function query()
    {
        return Equipo::with(['tipoRecurso', 'usuarioAsignado', 'checklists'])
            ->orderBy('nombre_equipo');
    }

    public function title(): string
    {
        return 'Inventario de Equipos';
    }

    public function headings(): array
    {
        return [
            // Bloque usuario asignado
            'EMPRESA PROPIETARIA DEL EQUIPO',
            'Departamento',
            'FUENTE DE RECURSO',
            'EMPRESA FUNCIONARIO',
            'EMPLEADO O CONTRATISTA',
            'CÉDULA DEL FUNCIONARIO/CONTRATISTA',
            'SHORTNAME',
            'NOMBRES Y APELLIDOS',
            'DEPARTAMENTO',
            'Ciudad',
            'CARGO',
            'Área',
            'UBICACIÓN Y PISO',
            'TIPO DE RECURSO',

            // Bloque equipo
            'TIPO',
            'SERIAL',
            'PLACA',
            'MARCA',
            'MODELO',
            'NOMBRE DE EQUIPO',
            'ESTADO OPERATIVO',
            'RAZÓN DEL ESTADO',
            'ADMINISTRADO/COMPRADO',
            'PROCESADOR',
            'MEMORIA RAM',
            'TAMAÑO DISCO DURO',
            'SISTEMA OPERATIVO',
            'FECHA DE COMPRA',
            'FIN DE GARANTÍA',
            'TIEMPO USO (AÑO)',
            'TIPO DE PROPIEDAD',

            // Bloque checklist
            'CHECKLIST (RESPONSABLE TI)',
            'ORDEN DE REVISIÓN',
            'OBSERVACIONES',
            'CRUCE AV 23-12-2022',
            'CRECE SHORTNAME',
            'RESULTADO CRECE AV',
            'TIPO APROBADO',
            'FNC',
            'VERSIÓN WINDOWS',
            'MARCA EQUIPO',
        ];
    }

    /**
     * Mapeo de cada fila — periféricos excluidos explícitamente.
     */
    public function map($equipo): array
    {
        $usuario = $equipo->usuarioAsignado;
        $checklist = $equipo->checklists->sortByDesc('created_at')->first();

        return [
            // Bloque usuario asignado
            $usuario?->empresa_propietaria,
            $usuario?->dependencia,
            $usuario?->fuente_recurso,
            $usuario?->empresa_funcionario,
            $usuario?->tipo_vinculacion,
            $usuario?->cedula,
            $usuario?->shortname,
            $usuario?->nombre,
            $usuario?->departamento,
            $usuario?->ciudad,
            $usuario?->cargo,
            $usuario?->area,
            $usuario?->piso,
            $equipo->tipoRecurso?->nombre ?? 'N/A',

            // Bloque equipo
            '',
            $equipo->serial,
            $equipo->placa,
            $equipo->marca,
            $equipo->modelo,
            $equipo->nombre_equipo,
            ucfirst($equipo->estado_operativo),
            $equipo->razon_estado,
            '',
            $equipo->procesador,
            $equipo->ram,
            $equipo->disco,
            $equipo->sistema_operativo,
            $equipo->fecha_compra?->format('d/m/Y'),
            $equipo->fin_garantia?->format('d/m/Y'),
            $equipo->tiempo_uso,
            '',

            // Bloque checklist
            $checklist?->responsable_ti,
            $checklist?->orden_trabajo,
            $checklist?->observaciones,
            $checklist?->cruce_av,
            $checklist?->crece_software,
            $checklist?->resultado,
            $checklist?->tipo_aprobado,
            $checklist?->fnc,
            $equipo->sistema_operativo,
            $equipo->marca,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            // Fila de encabezados en negrita con fondo azul oscuro
            1 => [
                'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1E3A5F']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }
}
