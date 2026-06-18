<?php

namespace App\Exports;

use App\Models\Equipo;
use App\Models\CampoPersonalizado;
use Illuminate\Support\Collection;
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
    private array $columnasEstandar;
    private array $columnasPersonalizadas;
    private Collection $camposInfo;
    private array $filtros;

    public function __construct(array $columnasEstandar = [], array $columnasPersonalizadas = [], array $filtros = [])
    {
        $this->columnasEstandar = $columnasEstandar;
        $this->columnasPersonalizadas = $columnasPersonalizadas;
        $this->filtros = $filtros;

        if (!empty($this->columnasPersonalizadas)) {
            $this->camposInfo = CampoPersonalizado::whereIn('id', $this->columnasPersonalizadas)
                                ->orderBy('orden')
                                ->get();
        } else {
            $this->camposInfo = collect();
        }
    }

    /**
     * Carga los equipos con sus relaciones (sin periféricos).
     */
    public function query()
    {
        return Equipo::with(['tipoRecurso', 'usuarioAsignado', 'camposPersonalizadosValores'])
            ->when(!empty($this->filtros['buscar']), function ($q) {
                $q->where(function ($sub) {
                    $buscar = $this->filtros['buscar'];
                    $sub->where('serial', 'like', '%' . $buscar . '%')
                        ->orWhere('nombre_equipo', 'like', '%' . $buscar . '%')
                        ->orWhere('marca', 'like', '%' . $buscar . '%')
                        ->orWhere('activo_fijo', 'like', '%' . $buscar . '%')
                        ->orWhereHas('usuarioAsignado', fn($u) => $u->where('nombre', 'like', '%' . $buscar . '%'));
                });
            })
            ->when(!empty($this->filtros['tipo']), fn($q) => $q->where('tipo_recurso_id', $this->filtros['tipo']))
            ->when(!empty($this->filtros['estado']), fn($q) => $q->where('estado_operativo', $this->filtros['estado']))
            ->when(!empty($this->filtros['activo_fijo']), fn($q) => $q->where('activo_fijo', 'like', '%' . $this->filtros['activo_fijo'] . '%'))
            ->orderBy('nombre_equipo');
    }

    public function title(): string
    {
        return 'Inventario de Equipos';
    }

    public function headings(): array
    {
        $headings = [];

        $nombresEstandar = [
            'id' => 'ID Interno',
            'nombre_equipo' => 'Nombre del Equipo',
            'serial' => 'Serial',
            'activo_fijo' => 'Activo Fijo',
            'placa' => 'Placa / Inventario',
            'marca' => 'Marca',
            'modelo' => 'Modelo',
            'tipo' => 'Tipo de Equipo',
            'estado' => 'Estado Operativo',
            'usuario_asignado' => 'Usuario Asignado (Nombre)',
            'cedula_asignado' => 'Usuario Asignado (Cédula)'
        ];

        foreach ($this->columnasEstandar as $col) {
            if (isset($nombresEstandar[$col])) {
                $headings[] = mb_strtoupper($nombresEstandar[$col]);
            }
        }

        foreach ($this->camposInfo as $campo) {
            $headings[] = mb_strtoupper($campo->nombre);
        }

        return $headings;
    }

    /**
     * Mapeo de cada fila.
     */
    public function map($equipo): array
    {
        $row = [];
        
        foreach ($this->columnasEstandar as $col) {
            switch ($col) {
                case 'id': 
                    $row[] = $equipo->id; 
                    break;
                case 'nombre_equipo': 
                    $row[] = $equipo->nombre_equipo; 
                    break;
                case 'serial': 
                    $row[] = $equipo->serial; 
                    break;
                case 'activo_fijo': 
                    $row[] = $equipo->activo_fijo; 
                    break;
                case 'placa': 
                    $row[] = $equipo->placa; 
                    break;
                case 'marca': 
                    $row[] = $equipo->marca; 
                    break;
                case 'modelo': 
                    $row[] = $equipo->modelo; 
                    break;
                case 'tipo': 
                    $row[] = $equipo->tipoRecurso?->nombre ?? 'N/A'; 
                    break;
                case 'estado': 
                    $row[] = ucfirst($equipo->estado_operativo); 
                    break;
                case 'usuario_asignado': 
                    $row[] = $equipo->usuarioAsignado?->nombre ?? 'Sin asignar'; 
                    break;
                case 'cedula_asignado': 
                    $row[] = $equipo->usuarioAsignado?->cedula ?? 'N/A'; 
                    break;
            }
        }

        foreach ($this->camposInfo as $campo) {
            $valorRelacion = $equipo->camposPersonalizadosValores->where('campo_personalizado_id', $campo->id)->first();
            $valor = $valorRelacion ? $valorRelacion->valor : '';
            
            if ($campo->tipo === 'multiselect' && !empty($valor)) {
                $decodificado = is_string($valor) ? json_decode($valor, true) : $valor;
                if (is_array($decodificado)) {
                    $valor = implode(', ', $decodificado);
                }
            } else if ($campo->tipo === 'boolean' && $valor !== '') {
                $valor = $valor == '1' ? 'Sí' : 'No';
            }
            $row[] = $valor;
        }

        return $row;
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
