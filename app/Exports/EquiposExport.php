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

    public static function columnasCmdbPrincipalEtiquetas(): array
    {
        return [
            'cmdb_empresa_propietario_equipo' => 'EMPRESA PROPIETARIO DEL EQUIPO',
            'cmdb_dependencia' => 'DEPENDENCIA',
            'cmdb_fuente_recurso' => 'FUENTE DE RECURSO',
            'cmdb_empresa_funcionario' => 'EMPRESA FUNCIONARIO',
            'cmdb_empleado_contratista' => 'EMPLEADO O CONTRATISTA',
            'cmdb_cedula_funcionario' => 'CEDULA DE FUNCIONARIO / CONTRATISTA',
            'cmdb_shortname' => 'SHORTNAME',
            'cmdb_nombres_apellidos' => 'NOMBRES Y APELLIDOS',
            'cmdb_departamento' => 'DEPARTAMENTO',
            'cmdb_ciudad' => 'CIUDAD',
            'cmdb_cargo' => 'CARGO',
            'cmdb_area' => 'ÁREA',
            'cmdb_ubicacion_piso' => 'UBICACIÓN Y PISO',
            'cmdb_tipo_recurso' => 'TIPO DE RECURSO',
            'cmdb_tipo' => 'TIPO',
            'cmdb_serial' => 'SERIAL',
            'cmdb_placa' => 'PLACA',
            'cmdb_marca' => 'MARCA',
            'cmdb_modelo' => 'MODELO',
            'cmdb_nombre_equipo' => 'NOMBRE DE EQUIPO',
            'cmdb_estado_operativo' => 'ESTADO OPERATIVO',
            'cmdb_razon_estado' => 'RAZÓN DEL ESTADO',
            'cmdb_administrador_controlado' => 'ADMINISTRADOR CONTROLADO',
            'cmdb_procesador' => 'PROCESADOR',
            'cmdb_memoria_ram' => 'MEMORIA RAM',
            'cmdb_tamano_disco_duro' => 'TAMAÑO DISCO DURO',
            'cmdb_sistema_operativo' => 'SISTEMA OPERATIVO',
            'cmdb_fecha_compra' => 'FECHA DE COMPRA',
            'cmdb_fin_garantia' => 'FIN DE GARANTÍA',
            'cmdb_tiempo_uso_anos' => 'TIEMPO USO (AÑOS)',
            'cmdb_tipo_propiedad' => 'TIPO DE PROPIEDAD',
            'cmdb_checklist_responsable_ti' => 'CHECKLIST (RESPONSABLE TI)',
            'cmdb_orden_remision' => 'ORDEN DE REMISIÓN',
            'cmdb_observaciones' => 'OBSERVACIONES',
            'cmdb_cruce_at' => 'CRUCE AT 23-12-2025',
            'cmdb_cruce_sistema' => 'CRUCE SISTEMA',
            'cmdb_resultado_cruce_at' => 'RESULTADO CRUCE AT',
            'cmdb_tipo_aprobado' => 'TIPO APROBADO',
            'cmdb_fnc' => 'FNC',
            'cmdb_version_windows' => 'VERSION WINDOWS',
            'cmdb_marca_equipo' => 'MARCA EQUIPO',
        ];
    }

    public static function columnasCmdbPrincipal(): array
    {
        return array_keys(self::columnasCmdbPrincipalEtiquetas());
    }

    public static function columnasEstandarDisponibles(): array
    {
        return [
            'id'                         => 'ID Interno',
            'nombre_equipo'              => 'Nombre del Equipo',
            'serial'                     => 'Serial',
            'activo_fijo'                => 'Activo Fijo',
            'placa'                      => 'Placa / Inventario',
            'tipo'                       => 'Tipo de Equipo',
            'marca'                      => 'Marca',
            'modelo'                     => 'Modelo',
            'estado'                     => 'Estado Operativo',
            'razon_estado'               => 'Razón de Estado',
            'procesador'                 => 'Procesador',
            'ram'                        => 'RAM',
            'disco'                      => 'Disco',
            'sistema_operativo'          => 'Sistema Operativo',
            'fecha_compra'               => 'Fecha de Compra',
            'fin_garantia'               => 'Fin de Garantía',
            'tiempo_uso'                 => 'Tiempo de Uso',
            'responsable_nombre'         => 'Responsable del Activo (Nombre)',
            'responsable_cedula'         => 'Responsable del Activo (Cédula)',
            'responsable_cargo'          => 'Responsable del Activo (Cargo)',
            'responsable_ciudad'         => 'Responsable del Activo (Ciudad)',
            'responsable_area'           => 'Responsable del Activo (Área)',
            'responsable_tipo_recurso'   => 'Responsable del Activo (Tipo Recurso)',
            'fecha_inicio_responsable'   => 'Responsable Desde',
            'fecha_fin_responsable'      => 'Responsable Hasta',
            'usuario_asignado'           => 'Usuario Asignado (Nombre)',
            'cedula_asignado'            => 'Usuario Asignado (Cédula)',
            'usuario_cargo'              => 'Usuario Asignado (Cargo)',
            'usuario_area'               => 'Usuario Asignado (Área)',
            'usuario_dependencia'        => 'Usuario Asignado (Dependencia)',
            'usuario_empresa_propietaria'=> 'Usuario Asignado (Empresa Propietaria)',
            'usuario_empresa_funcionario'=> 'Usuario Asignado (Empresa Funcionario)',
            'usuario_tipo_vinculacion'   => 'Usuario Asignado (Tipo Vinculación)',
            'usuario_ciudad'             => 'Usuario Asignado (Ciudad)',
            'usuario_departamento'       => 'Usuario Asignado (Departamento)',
            'usuario_shortname'          => 'Usuario Asignado (Shortname)',
            'usuario_piso'               => 'Usuario Asignado (Piso)',
            'usuario_distrito'           => 'Usuario Asignado (Distrito)',
            'usuario_seccional'          => 'Usuario Asignado (Seccional)',
            'usuario_fuente_recurso'     => 'Usuario Asignado (Fuente Recurso)',
            'periferico_telefono'        => 'Periférico (Teléfono)',
            'periferico_teclado'         => 'Periférico (Teclado)',
            'periferico_mouse'           => 'Periférico (Mouse)',
            'periferico_camara'          => 'Periférico (Cámara)',
        ];
    }

    public static function columnasAdicionalesSobreCmdbPrincipal(): array
    {
        return array_diff_key(
            self::columnasEstandarDisponibles(),
            array_flip(self::columnasCmdbPrincipal())
        );
    }

    public static function columnasCmdbPorDefecto(): array
    {
        return self::columnasCmdbPrincipal();
    }

    public static function columnasCompletas(): array
    {
        return [
            'id',
            'nombre_equipo',
            'serial',
            'activo_fijo',
            'placa',
            'tipo',
            'marca',
            'modelo',
            'estado',
            'razon_estado',
            'procesador',
            'ram',
            'disco',
            'sistema_operativo',
            'fecha_compra',
            'fin_garantia',
            'tiempo_uso',
            'responsable_nombre',
            'responsable_cedula',
            'responsable_cargo',
            'responsable_ciudad',
            'responsable_area',
            'responsable_tipo_recurso',
            'fecha_inicio_responsable',
            'fecha_fin_responsable',
            'usuario_asignado',
            'cedula_asignado',
            'usuario_cargo',
            'usuario_area',
            'usuario_dependencia',
            'usuario_empresa_propietaria',
            'usuario_empresa_funcionario',
            'usuario_tipo_vinculacion',
            'usuario_ciudad',
            'usuario_departamento',
            'usuario_shortname',
            'usuario_piso',
            'usuario_distrito',
            'usuario_seccional',
            'usuario_fuente_recurso',
            'periferico_telefono',
            'periferico_teclado',
            'periferico_mouse',
            'periferico_camara',
        ];
    }

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
        return Equipo::with(['tipoRecurso', 'usuarioAsignado', 'periferico', 'latestChecklist', 'camposPersonalizadosValores'])
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

        $nombresEstandar = array_merge(self::columnasCmdbPrincipalEtiquetas(), self::columnasEstandarDisponibles());

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
                case 'cmdb_empresa_propietario_equipo':
                    $row[] = $equipo->usuarioAsignado?->empresa_propietaria ?? '';
                    break;
                case 'cmdb_dependencia':
                    $row[] = $equipo->usuarioAsignado?->dependencia ?? '';
                    break;
                case 'cmdb_fuente_recurso':
                    $row[] = $equipo->usuarioAsignado?->fuente_recurso ?? '';
                    break;
                case 'cmdb_empresa_funcionario':
                    $row[] = $equipo->usuarioAsignado?->empresa_funcionario ?? '';
                    break;
                case 'cmdb_empleado_contratista':
                    $row[] = $equipo->usuarioAsignado?->tipo_vinculacion ?? '';
                    break;
                case 'cmdb_cedula_funcionario':
                    $row[] = $equipo->usuarioAsignado?->cedula ?? '';
                    break;
                case 'cmdb_shortname':
                    $row[] = $equipo->usuarioAsignado?->shortname ?? '';
                    break;
                case 'cmdb_nombres_apellidos':
                    $row[] = $equipo->usuarioAsignado?->nombre ?? '';
                    break;
                case 'cmdb_departamento':
                    $row[] = $equipo->usuarioAsignado?->departamento ?? '';
                    break;
                case 'cmdb_ciudad':
                    $row[] = $equipo->usuarioAsignado?->ciudad ?? '';
                    break;
                case 'cmdb_cargo':
                    $row[] = $equipo->usuarioAsignado?->cargo ?? '';
                    break;
                case 'cmdb_area':
                    $row[] = $equipo->usuarioAsignado?->area ?? '';
                    break;
                case 'cmdb_ubicacion_piso':
                    $row[] = $equipo->usuarioAsignado?->piso ?? '';
                    break;
                case 'cmdb_tipo_recurso':
                    $row[] = $equipo->responsable_tipo_recurso ?? '';
                    break;
                case 'cmdb_tipo':
                    $row[] = $equipo->tipoRecurso?->nombre ?? '';
                    break;
                case 'cmdb_serial':
                    $row[] = $equipo->serial ?? '';
                    break;
                case 'cmdb_placa':
                    $row[] = $equipo->placa ?: ($equipo->activo_fijo ?? '');
                    break;
                case 'cmdb_marca':
                    $row[] = $equipo->marca ?? '';
                    break;
                case 'cmdb_modelo':
                    $row[] = $equipo->modelo ?? '';
                    break;
                case 'cmdb_nombre_equipo':
                    $row[] = $equipo->nombre_equipo ?? '';
                    break;
                case 'cmdb_estado_operativo':
                    $row[] = mb_strtoupper((string) $equipo->estado_label);
                    break;
                case 'cmdb_razon_estado':
                    $row[] = $equipo->razon_estado ?? '';
                    break;
                case 'cmdb_administrador_controlado':
                    $row[] = $equipo->responsable_nombre ? 'CONTROLADO' : '';
                    break;
                case 'cmdb_procesador':
                    $row[] = $equipo->procesador ?? '';
                    break;
                case 'cmdb_memoria_ram':
                    $row[] = $equipo->ram ?? '';
                    break;
                case 'cmdb_tamano_disco_duro':
                    $row[] = $equipo->disco ?? '';
                    break;
                case 'cmdb_sistema_operativo':
                    $row[] = $equipo->sistema_operativo ?? '';
                    break;
                case 'cmdb_fecha_compra':
                    $row[] = optional($equipo->fecha_compra)->format('d/m/Y');
                    break;
                case 'cmdb_fin_garantia':
                    $row[] = optional($equipo->fin_garantia)->format('d/m/Y');
                    break;
                case 'cmdb_tiempo_uso_anos':
                    $row[] = $equipo->tiempo_uso ?? '';
                    break;
                case 'cmdb_tipo_propiedad':
                    $row[] = '';
                    break;
                case 'cmdb_checklist_responsable_ti':
                    $row[] = $equipo->latestChecklist?->responsable_ti ?: ($equipo->responsable_nombre ?? '');
                    break;
                case 'cmdb_orden_remision':
                    $row[] = $equipo->latestChecklist?->orden_trabajo ?? '';
                    break;
                case 'cmdb_observaciones':
                    $row[] = $equipo->latestChecklist?->observaciones ?? '';
                    break;
                case 'cmdb_cruce_at':
                    $row[] = $equipo->latestChecklist?->cruce_av ?? '';
                    break;
                case 'cmdb_cruce_sistema':
                    $row[] = $equipo->latestChecklist?->crece_software ?? '';
                    break;
                case 'cmdb_resultado_cruce_at':
                    $row[] = $equipo->latestChecklist?->resultado ?? '';
                    break;
                case 'cmdb_tipo_aprobado':
                    $row[] = $equipo->latestChecklist?->tipo_aprobado ?? '';
                    break;
                case 'cmdb_fnc':
                    $row[] = $equipo->latestChecklist?->fnc ?? '';
                    break;
                case 'cmdb_version_windows':
                    $row[] = $equipo->sistema_operativo ?? '';
                    break;
                case 'cmdb_marca_equipo':
                    $row[] = $equipo->marca ?? '';
                    break;
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
                case 'razon_estado':
                    $row[] = $equipo->razon_estado;
                    break;
                case 'procesador':
                    $row[] = $equipo->procesador;
                    break;
                case 'ram':
                    $row[] = $equipo->ram;
                    break;
                case 'disco':
                    $row[] = $equipo->disco;
                    break;
                case 'sistema_operativo':
                    $row[] = $equipo->sistema_operativo;
                    break;
                case 'fecha_compra':
                    $row[] = optional($equipo->fecha_compra)->format('Y-m-d');
                    break;
                case 'fin_garantia':
                    $row[] = optional($equipo->fin_garantia)->format('Y-m-d');
                    break;
                case 'tiempo_uso':
                    $row[] = $equipo->tiempo_uso;
                    break;
                case 'responsable_nombre':
                    $row[] = $equipo->responsable_nombre;
                    break;
                case 'responsable_cedula':
                    $row[] = $equipo->responsable_cedula;
                    break;
                case 'responsable_cargo':
                    $row[] = $equipo->responsable_cargo;
                    break;
                case 'responsable_ciudad':
                    $row[] = $equipo->responsable_ciudad;
                    break;
                case 'responsable_area':
                    $row[] = $equipo->responsable_area;
                    break;
                case 'responsable_tipo_recurso':
                    $row[] = $equipo->responsable_tipo_recurso;
                    break;
                case 'fecha_inicio_responsable':
                    $row[] = optional($equipo->fecha_inicio_responsable)->format('Y-m-d');
                    break;
                case 'fecha_fin_responsable':
                    $row[] = optional($equipo->fecha_fin_responsable)->format('Y-m-d');
                    break;
                case 'usuario_asignado': 
                    $row[] = $equipo->usuarioAsignado?->nombre ?? 'Sin asignar'; 
                    break;
                case 'cedula_asignado': 
                    $row[] = $equipo->usuarioAsignado?->cedula ?? 'N/A'; 
                    break;
                case 'usuario_cargo':
                    $row[] = $equipo->usuarioAsignado?->cargo ?? '';
                    break;
                case 'usuario_area':
                    $row[] = $equipo->usuarioAsignado?->area ?? '';
                    break;
                case 'usuario_dependencia':
                    $row[] = $equipo->usuarioAsignado?->dependencia ?? '';
                    break;
                case 'usuario_empresa_propietaria':
                    $row[] = $equipo->usuarioAsignado?->empresa_propietaria ?? '';
                    break;
                case 'usuario_empresa_funcionario':
                    $row[] = $equipo->usuarioAsignado?->empresa_funcionario ?? '';
                    break;
                case 'usuario_tipo_vinculacion':
                    $row[] = $equipo->usuarioAsignado?->tipo_vinculacion ?? '';
                    break;
                case 'usuario_ciudad':
                    $row[] = $equipo->usuarioAsignado?->ciudad ?? '';
                    break;
                case 'usuario_departamento':
                    $row[] = $equipo->usuarioAsignado?->departamento ?? '';
                    break;
                case 'usuario_shortname':
                    $row[] = $equipo->usuarioAsignado?->shortname ?? '';
                    break;
                case 'usuario_piso':
                    $row[] = $equipo->usuarioAsignado?->piso ?? '';
                    break;
                case 'usuario_distrito':
                    $row[] = $equipo->usuarioAsignado?->distrito ?? '';
                    break;
                case 'usuario_seccional':
                    $row[] = $equipo->usuarioAsignado?->seccional ?? '';
                    break;
                case 'usuario_fuente_recurso':
                    $row[] = $equipo->usuarioAsignado?->fuente_recurso ?? '';
                    break;
                case 'periferico_telefono':
                    $row[] = $equipo->periferico?->telefono ?? '';
                    break;
                case 'periferico_teclado':
                    $row[] = $equipo->periferico?->teclado ?? '';
                    break;
                case 'periferico_mouse':
                    $row[] = $equipo->periferico?->mouse ?? '';
                    break;
                case 'periferico_camara':
                    $row[] = $equipo->periferico?->camara ?? '';
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
