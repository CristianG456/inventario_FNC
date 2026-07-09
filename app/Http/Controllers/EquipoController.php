<?php

namespace App\Http\Controllers;

use App\Exports\EquiposExport;
use App\Http\Requests\EquipoRequest;
use App\Imports\EquiposImport;
use App\Models\Equipo;
use App\Models\Funcionario;
use App\Models\TipoRecurso;
use App\Services\HistorialService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class EquipoController extends Controller
{
    public function __construct(
        private readonly HistorialService $historialService
    ) {}

    /**
     * Listado de equipos con búsqueda y filtros.
     */
    public function index(Request $request): View
    {
        $buscar = trim((string) $request->input('buscar', ''));
        $filtroActivoFijo = trim((string) $request->input('activo_fijo', ''));

        $query = Equipo::select([
                'id',
                'tipo_recurso_id',
                'serial',
                'activo_fijo',
                'placa',
                'marca',
                'modelo',
                'nombre_equipo',
                'estado_operativo',
                'created_at',
            ])
            ->with([
                'tipoRecurso:id,nombre',
                'usuarioAsignado:id,equipo_id,nombre,cedula',
            ])
            ->when($buscar !== '', function ($q) use ($buscar) {
                $termino = '%' . $buscar . '%';
                $q->where(function ($sub) use ($termino) {
                    $sub->where('serial', 'like', $termino)
                        ->orWhere('nombre_equipo', 'like', $termino)
                        ->orWhere('marca', 'like', $termino)
                        ->orWhere('activo_fijo', 'like', $termino)
                        ->orWhereHas('usuarioAsignado', fn($u) => $u->where('nombre', 'like', $termino));
                });
            })
            ->when($request->filled('tipo'), fn($q) => $q->where('tipo_recurso_id', $request->tipo))
            ->when($request->filled('estado'), fn($q) => $q->where('estado_operativo', $request->estado))
            ->when($filtroActivoFijo !== '', fn($q) => $q->where('activo_fijo', 'like', '%' . $filtroActivoFijo . '%'))
            ->latest();

        $equipos      = $query->paginate(15)->withQueryString();
        $cedulasAsignadas = $equipos->getCollection()
            ->pluck('usuarioAsignado.cedula')
            ->filter()
            ->unique()
            ->values();

        $funcionariosPorCedula = Funcionario::whereIn('identificacion', $cedulasAsignadas)
            ->get(['identificacion', 'nombres', 'apellidos'])
            ->keyBy('identificacion');

        $tipoRecursos = TipoRecurso::select('id', 'nombre')->orderBy('nombre')->get();
        $camposExportables = \App\Models\CampoPersonalizado::where('modulo', 'equipos')
            ->where('exportable', true)
            ->select('id', 'nombre', 'exportar_por_defecto')
            ->orderBy('orden')
            ->get();
        $plantillasExportacion = \App\Models\PlantillaExportacion::where('modulo', 'equipos')
                                    ->select('id', 'nombre', 'configuracion_json')
                                    ->orderBy('nombre')
                                    ->get();

        return view('equipos.index', compact('equipos', 'tipoRecursos', 'plantillasExportacion', 'funcionariosPorCedula', 'camposExportables'));
    }

    /**
     * Formulario de creación.
     */
    public function create(): View
    {
        $tipoRecursos = TipoRecurso::select('id', 'nombre')->orderBy('nombre')->get();
        $camposPersonalizados = \App\Models\CampoPersonalizado::select([
                                    'id',
                                    'nombre',
                                    'descripcion',
                                    'tipo',
                                    'obligatorio',
                                    'orden',
                                ])
                                ->with(['opciones:id,campo_personalizado_id,valor,orden'])
                                ->where('modulo', 'equipos')
                                ->where('activo', true)
                                ->orderBy('orden')->get();
        return view('equipos.create', compact('tipoRecursos', 'camposPersonalizados'));
    }

    /**
     * Guardar nuevo equipo con usuario y periféricos.
     */
    public function store(EquipoRequest $request): RedirectResponse
    {
        $datosEquipo = $request->only([
            'tipo_recurso_id', 'serial', 'activo_fijo', 'placa', 'marca', 'modelo',
            'nombre_equipo', 'estado_operativo', 'razon_estado',
            'procesador', 'ram', 'disco', 'sistema_operativo',
            'fecha_compra', 'fin_garantia', 'tiempo_uso',
            'responsable_cedula', 'responsable_nombre', 'responsable_cargo',
            'responsable_ciudad', 'responsable_area', 'responsable_tipo_recurso',
            'fecha_inicio_responsable', 'fecha_fin_responsable'
        ]);

        if ($request->sin_serial_fisico && empty($datosEquipo['serial'])) {
            $datosEquipo['serial'] = 'SIN_SERIAL_' . strtoupper(uniqid());
        }

        $equipo = Equipo::create($datosEquipo);

        $equipo->usuarioAsignado()->create([
            'nombre'               => $request->usuario_nombre,
            'cedula'               => $request->usuario_cedula,
            'empresa_propietaria'  => $request->usuario_empresa_propietaria,
            'dependencia'          => $request->usuario_dependencia,
            'fuente_recurso'       => $request->usuario_fuente_recurso,
            'empresa_funcionario'  => $request->usuario_empresa_funcionario,
            'tipo_vinculacion'     => $request->usuario_tipo_vinculacion,
            'shortname'            => $request->usuario_shortname,
            'departamento'         => $request->usuario_departamento,
            'ciudad'               => $request->usuario_ciudad,
            'cargo'                => $request->usuario_cargo,
            'area'                 => $request->usuario_area,
            'piso'                 => $request->usuario_piso,
            'distrito'             => $request->usuario_distrito,
            'seccional'            => $request->usuario_seccional,
        ]);

        $equipo->periferico()->create([
            'telefono' => $request->periferico_telefono,
            'teclado'  => $request->periferico_teclado,
            'mouse'    => $request->periferico_mouse,
            'camara'   => $request->periferico_camara,
        ]);

        if ($request->has('campos_personalizados')) {
            foreach ($request->campos_personalizados as $campo_id => $valor) {
                // Si es un array (multiselect), lo guardamos como JSON
                $valorGuardar = is_array($valor) ? json_encode($valor) : $valor;
                $equipo->camposPersonalizadosValores()->create([
                    'campo_personalizado_id' => $campo_id,
                    'valor' => $valorGuardar
                ]);
            }
        }

        // Sincronizar funcionario en la tabla de funcionarios
        $this->sincronizarFuncionario($request);

        $this->historialService->registrarCambio(
            $equipo,
            'creacion',
            null,
            $equipo->serial,
            "Equipo '{$equipo->nombre_equipo}' registrado en el sistema.",
            auth()->user()
        );

        return redirect()->route('equipos.index')
            ->with('success', 'Equipo registrado correctamente.');
    }

    /**
     * Detalle de un equipo.
     */
    public function show(Equipo $equipo): View
    {
        $equipo->load([
            'tipoRecurso',
            'usuarioAsignado',
            'periferico',
            'checklists',
            'licenciaAsignaciones.licencia',
            'camposPersonalizadosValores.campoPersonalizado',
            'asignaciones' => fn($q) => $q->latest('fecha_accion')->limit(5),
            'historialTecnico' => fn($q) => $q->latest('fecha_evento')->limit(5),
        ]);
        return view('equipos.show', compact('equipo'));
    }

    /**
     * Formulario de edición.
     */
    public function edit(Equipo $equipo): View
    {
        $equipo->load([
            'usuarioAsignado:id,equipo_id,nombre,cedula,empresa_propietaria,dependencia,fuente_recurso,empresa_funcionario,tipo_vinculacion,shortname,departamento,ciudad,cargo,area,piso,distrito,seccional',
            'periferico:id,equipo_id,telefono,teclado,mouse,camara',
            'camposPersonalizadosValores:id,entidad_id,campo_personalizado_id,valor',
        ]);
        $tipoRecursos = TipoRecurso::select('id', 'nombre')->orderBy('nombre')->get();
        $camposPersonalizados = \App\Models\CampoPersonalizado::select([
                                    'id',
                                    'nombre',
                                    'descripcion',
                                    'tipo',
                                    'obligatorio',
                                    'orden',
                                ])
                                ->with(['opciones:id,campo_personalizado_id,valor,orden'])
                                ->where('modulo', 'equipos')
                                ->where('activo', true)
                                ->orderBy('orden')->get();
        return view('equipos.edit', compact('equipo', 'tipoRecursos', 'camposPersonalizados'));
    }

    /**
     * Actualizar equipo existente con registro de historial de cambios.
     */
    public function update(EquipoRequest $request, Equipo $equipo): RedirectResponse
    {
        $camposAnteriores = $equipo->only([
            'serial', 'activo_fijo', 'estado_operativo', 'marca', 'modelo',
            'nombre_equipo', 'procesador', 'ram', 'disco', 'sistema_operativo',
        ]);

        $datosEquipo = $request->only([
            'tipo_recurso_id', 'serial', 'activo_fijo', 'placa', 'marca', 'modelo',
            'nombre_equipo', 'estado_operativo', 'razon_estado',
            'procesador', 'ram', 'disco', 'sistema_operativo',
            'fecha_compra', 'fin_garantia', 'tiempo_uso',
            'responsable_cedula', 'responsable_nombre', 'responsable_cargo',
            'responsable_ciudad', 'responsable_area', 'responsable_tipo_recurso',
            'fecha_inicio_responsable', 'fecha_fin_responsable'
        ]);

        if ($request->sin_serial_fisico && empty($datosEquipo['serial'])) {
            $datosEquipo['serial'] = 'SIN_SERIAL_' . strtoupper(uniqid());
        }

        $equipo->update($datosEquipo);

        $camposNuevos = $equipo->fresh()->only(array_keys($camposAnteriores));
        $this->historialService->registrarCambiosCampos(
            $equipo,
            $camposAnteriores,
            $camposNuevos,
            auth()->user()
        );

        $equipo->usuarioAsignado()->updateOrCreate(
            ['equipo_id' => $equipo->id],
            [
                'nombre'               => $request->usuario_nombre,
                'cedula'               => $request->usuario_cedula,
                'empresa_propietaria'  => $request->usuario_empresa_propietaria,
                'dependencia'          => $request->usuario_dependencia,
                'fuente_recurso'       => $request->usuario_fuente_recurso,
                'empresa_funcionario'  => $request->usuario_empresa_funcionario,
                'tipo_vinculacion'     => $request->usuario_tipo_vinculacion,
                'shortname'            => $request->usuario_shortname,
                'departamento'         => $request->usuario_departamento,
                'ciudad'               => $request->usuario_ciudad,
                'cargo'                => $request->usuario_cargo,
                'area'                 => $request->usuario_area,
                'piso'                 => $request->usuario_piso,
                'distrito'             => $request->usuario_distrito,
                'seccional'            => $request->usuario_seccional,
            ]
        );

        $equipo->periferico()->updateOrCreate(
            ['equipo_id' => $equipo->id],
            [
                'telefono' => $request->periferico_telefono,
                'teclado'  => $request->periferico_teclado,
                'mouse'    => $request->periferico_mouse,
                'camara'   => $request->periferico_camara,
            ]
        );

        if ($request->has('campos_personalizados')) {
            foreach ($request->campos_personalizados as $campo_id => $valor) {
                $valorGuardar = is_array($valor) ? json_encode($valor) : $valor;
                $equipo->camposPersonalizadosValores()->updateOrCreate(
                    ['campo_personalizado_id' => $campo_id],
                    ['valor' => $valorGuardar]
                );
            }
        }

        // Sincronizar funcionario en la tabla de funcionarios
        $this->sincronizarFuncionario($request);

        return redirect()->route('equipos.index')
            ->with('success', 'Equipo actualizado correctamente.');
    }

    /**
     * Eliminar equipo (soft delete) con registro en historial.
     */
    public function destroy(Equipo $equipo): RedirectResponse
    {
        $this->historialService->registrarCambio(
            $equipo,
            'eliminacion',
            'activo',
            'eliminado',
            "Equipo '{$equipo->nombre_equipo}' eliminado del sistema.",
            auth()->user()
        );

        $equipo->delete();

        return redirect()->route('equipos.index')
            ->with('success', 'Equipo eliminado correctamente.');
    }

    /**
     * Exportar equipos a Excel.
     */
    public function exportar(Request $request): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $columnasEstandar = $request->input('columnas_estandar', []);
        $columnasPersonalizadas = $request->input('columnas_personalizadas', []);
        
        // Guardar plantilla si se solicita
        if ($request->input('guardar_plantilla') && $request->filled('nombre_plantilla')) {
            \App\Models\PlantillaExportacion::create([
                'nombre' => $request->nombre_plantilla,
                'modulo' => 'equipos',
                'configuracion_json' => [
                    'columnas_estandar' => $columnasEstandar,
                    'columnas_personalizadas' => $columnasPersonalizadas,
                ],
            ]);
        }

        // Si no se selecciona nada (ej. llamada directa sin modal), exportar todo lo por defecto
        if (empty($columnasEstandar) && empty($columnasPersonalizadas)) {
            $columnasEstandar = [
                'id', 'nombre_equipo', 'serial', 'activo_fijo', 'placa',
                'marca', 'modelo', 'tipo', 'estado', 'usuario_asignado', 'cedula_asignado'
            ];
            $columnasPersonalizadas = \App\Models\CampoPersonalizado::where('modulo', 'equipos')
                                        ->where('exportar_por_defecto', true)
                                        ->pluck('id')->toArray();
        }

        return Excel::download(new EquiposExport($columnasEstandar, $columnasPersonalizadas, $request->all()), 'inventario_equipos.xlsx');
    }

    /**
     * Formulario de importación desde Excel.
     */
    public function importarForm(): View
    {
        return view('equipos.importar');
    }

    /**
     * Procesar el archivo Excel subido.
     * Detección automática del formato (CMDB / propio).
     */
    public function importar(Request $request): RedirectResponse
    {
        ini_set('memory_limit', '-1');
        set_time_limit(300);

        $request->validate([
            'archivo' => ['required', 'file', 'mimes:xlsx,xls', 'max:10240'],
        ], [
            'archivo.required' => 'Debes seleccionar un archivo Excel.',
            'archivo.file'     => 'El archivo no es válido.',
            'archivo.mimes'    => 'Solo se permiten archivos .xlsx o .xls.',
            'archivo.max'      => 'El archivo no puede superar 10 MB.',
        ]);
        $filePath = $request->file('archivo')->getRealPath();
        $import = new EquiposImport($filePath);
        Excel::import($import, $request->file('archivo'));

        $rowFailures  = $import->getRowFailures();
        $phpErrors    = $import->errors();
        $insertados   = $import->getInsertados();
        $omitidos     = $import->getOmitidos();
        $columnReport = $import->getMapper()->getColumnReport();

        $errorsData = collect($phpErrors)->map(fn($e) => [
            'mensaje' => class_basename(get_class($e)) . ': ' . $e->getMessage(),
        ])->toArray();

        return redirect()->route('equipos.importar.form')
            ->with('import_insertados', $insertados)
            ->with('import_omitidos', $omitidos)
            ->with('import_failures', $rowFailures)
            ->with('import_errors', $errorsData)
            ->with('import_column_report', $columnReport);
    }

    /**
     * Vista de historial de vida del equipo (timeline combinado).
     */
    public function historialVida(Equipo $equipo, HistorialService $historialService): View
    {
        $eventos = $historialService->obtenerLineaDeTiempo($equipo);
        return view('equipos.historial_vida', compact('equipo', 'eventos'));
    }

    /**
     * Generar Acta de Entrega PDF
     */
    public function descargarActa(Equipo $equipo, \App\Services\PdfService $pdfService)
    {
        $equipo->load(['tipoRecurso', 'usuarioAsignado']);
        
        if (!$equipo->usuarioAsignado) {
            return back()->with('error', 'El equipo no tiene un funcionario asignado actualmente.');
        }

        return $pdfService->generarActaDesdeEquipo($equipo);
    }

    /**
     * Sincroniza el usuario asignado del equipo con la tabla de funcionarios.
     * Busca por cédula; si existe actualiza, si no crea un nuevo registro.
     */
    private function sincronizarFuncionario(Request $request): void
    {
        $cedula = $request->usuario_cedula;

        if (empty($cedula)) {
            return;
        }

        // Separar nombre completo en nombres y apellidos (por el primer espacio)
        $nombreCompleto = trim($request->usuario_nombre ?? '');
        $partes         = explode(' ', $nombreCompleto, 2);
        $nombres        = $partes[0] ?? $nombreCompleto;
        $apellidos      = $partes[1] ?? null;

        $funcionario = Funcionario::withTrashed()->updateOrCreate(
            ['identificacion' => $cedula],
            [
                'nombres'             => $nombres,
                'apellidos'           => $apellidos,
                'cargo'               => $request->usuario_cargo,
                'area'                => $request->usuario_area,
                'departamento'        => $request->usuario_departamento,
                'ciudad'              => $request->usuario_ciudad,
                'empresa_funcionario' => $request->usuario_empresa_funcionario,
                'tipo_vinculacion'    => $request->usuario_tipo_vinculacion,
                'estado'              => 'Activo',
            ]
        );
        
        // Si el funcionario estaba eliminado, lo restauramos
        if ($funcionario->trashed()) {
            $funcionario->restore();
        }
    }
}
