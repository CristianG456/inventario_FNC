<?php

namespace App\Http\Controllers;

use App\Exports\EquiposExport;
use App\Http\Requests\EquipoRequest;
use App\Imports\EquiposImport;
use App\Models\Equipo;
use App\Models\TipoRecurso;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class EquipoController extends Controller
{
    /**
     * Listado de equipos con búsqueda y filtros.
     */
    public function index(Request $request): View
    {
        $query = Equipo::with(['tipoRecurso', 'usuarioAsignado'])
            ->when($request->filled('buscar'), function ($q) use ($request) {
                $q->where(function ($sub) use ($request) {
                    $sub->where('serial', 'like', '%' . $request->buscar . '%')
                        ->orWhere('nombre_equipo', 'like', '%' . $request->buscar . '%')
                        ->orWhere('marca', 'like', '%' . $request->buscar . '%')
                        ->orWhereHas('usuarioAsignado', fn($u) => $u->where('nombre', 'like', '%' . $request->buscar . '%'));
                });
            })
            ->when($request->filled('tipo'), fn($q) => $q->where('tipo_recurso_id', $request->tipo))
            ->when($request->filled('estado'), fn($q) => $q->where('estado_operativo', $request->estado))
            ->latest();

        $equipos      = $query->paginate(15)->withQueryString();
        $tipoRecursos = TipoRecurso::orderBy('nombre')->get();

        return view('equipos.index', compact('equipos', 'tipoRecursos'));
    }

    /**
     * Formulario de creación.
     */
    public function create(): View
    {
        $tipoRecursos = TipoRecurso::orderBy('nombre')->get();
        return view('equipos.create', compact('tipoRecursos'));
    }

    /**
     * Guardar nuevo equipo con usuario y periféricos.
     */
    public function store(EquipoRequest $request): RedirectResponse
    {
        $equipo = Equipo::create($request->only([
            'tipo_recurso_id', 'serial', 'placa', 'marca', 'modelo',
            'nombre_equipo', 'estado_operativo', 'razon_estado',
            'procesador', 'ram', 'disco', 'sistema_operativo',
            'fecha_compra', 'fin_garantia', 'tiempo_uso',
        ]));

        // Crear usuario asignado
        $equipo->usuarioAsignado()->create([
            'nombre'       => $request->usuario_nombre,
            'cedula'       => $request->usuario_cedula,
            'empresa_propietaria' => $request->usuario_empresa_propietaria,
            'dependencia' => $request->usuario_dependencia,
            'fuente_recurso' => $request->usuario_fuente_recurso,
            'empresa_funcionario' => $request->usuario_empresa_funcionario,
            'tipo_vinculacion' => $request->usuario_tipo_vinculacion,
            'shortname' => $request->usuario_shortname,
            'departamento' => $request->usuario_departamento,
            'ciudad'       => $request->usuario_ciudad,
            'cargo'        => $request->usuario_cargo,
            'area'         => $request->usuario_area,
            'piso'         => $request->usuario_piso,
        ]);

        // Crear periféricos
        $equipo->periferico()->create([
            'telefono' => $request->periferico_telefono,
            'teclado'  => $request->periferico_teclado,
            'mouse'    => $request->periferico_mouse,
            'camara'   => $request->periferico_camara,
        ]);

        return redirect()->route('equipos.index')
            ->with('success', 'Equipo registrado correctamente.');
    }

    /**
     * Detalle de un equipo.
     */
    public function show(Equipo $equipo): View
    {
        $equipo->load(['tipoRecurso', 'usuarioAsignado', 'periferico', 'checklists']);
        return view('equipos.show', compact('equipo'));
    }

    /**
     * Formulario de edición.
     */
    public function edit(Equipo $equipo): View
    {
        $equipo->load(['usuarioAsignado', 'periferico']);
        $tipoRecursos = TipoRecurso::orderBy('nombre')->get();
        return view('equipos.edit', compact('equipo', 'tipoRecursos'));
    }

    /**
     * Actualizar equipo existente.
     */
    public function update(EquipoRequest $request, Equipo $equipo): RedirectResponse
    {
        $equipo->update($request->only([
            'tipo_recurso_id', 'serial', 'placa', 'marca', 'modelo',
            'nombre_equipo', 'estado_operativo', 'razon_estado',
            'procesador', 'ram', 'disco', 'sistema_operativo',
            'fecha_compra', 'fin_garantia', 'tiempo_uso',
        ]));

        // Actualizar o crear usuario asignado
        $equipo->usuarioAsignado()->updateOrCreate(
            ['equipo_id' => $equipo->id],
            [
                'nombre'       => $request->usuario_nombre,
                'cedula'       => $request->usuario_cedula,
                'empresa_propietaria' => $request->usuario_empresa_propietaria,
                'dependencia' => $request->usuario_dependencia,
                'fuente_recurso' => $request->usuario_fuente_recurso,
                'empresa_funcionario' => $request->usuario_empresa_funcionario,
                'tipo_vinculacion' => $request->usuario_tipo_vinculacion,
                'shortname' => $request->usuario_shortname,
                'departamento' => $request->usuario_departamento,
                'ciudad'       => $request->usuario_ciudad,
                'cargo'        => $request->usuario_cargo,
                'area'         => $request->usuario_area,
                'piso'         => $request->usuario_piso,
            ]
        );

        // Actualizar o crear periféricos
        $equipo->periferico()->updateOrCreate(
            ['equipo_id' => $equipo->id],
            [
                'telefono' => $request->periferico_telefono,
                'teclado'  => $request->periferico_teclado,
                'mouse'    => $request->periferico_mouse,
                'camara'   => $request->periferico_camara,
            ]
        );

        return redirect()->route('equipos.index')
            ->with('success', 'Equipo actualizado correctamente.');
    }

    /**
     * Eliminar equipo (soft delete).
     */
    public function destroy(Equipo $equipo): RedirectResponse
    {
        $equipo->delete();

        return redirect()->route('equipos.index')
            ->with('success', 'Equipo eliminado correctamente.');
    }

    /**
     * Exportar equipos a Excel (sin periféricos).
     */
    public function exportar(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        return Excel::download(new EquiposExport(), 'inventario_equipos.xlsx');
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
     */
    public function importar(Request $request): RedirectResponse
    {
        // PhpSpreadsheet carga el XLSX completo en RAM antes de procesar.
        // Se libera el límite solo para esta solicitud.
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

        $import = new EquiposImport();

        Excel::import($import, $request->file('archivo'));

        $rowFailures  = $import->getRowFailures();    // errores de validación manual
        $phpErrors    = $import->errors();             // excepciones PHP capturadas
        $insertados   = $import->getInsertados();
        $omitidos     = $import->getOmitidos();

        // $rowFailures ya tiene formato ['fila' => N, 'errores' => [...]]
        $errorsData = collect($phpErrors)->map(fn($e) => [
            'mensaje' => class_basename(get_class($e)) . ': ' . $e->getMessage(),
        ])->toArray();

        return redirect()->route('equipos.importar.form')
            ->with('import_insertados', $insertados)
            ->with('import_omitidos', $omitidos)
            ->with('import_failures', $rowFailures)
            ->with('import_errors', $errorsData);
    }
}
