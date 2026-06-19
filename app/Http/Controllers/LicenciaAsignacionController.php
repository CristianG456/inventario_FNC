<?php

namespace App\Http\Controllers;

use App\Models\LicenciaAsignacion;
use App\Models\Licencia;
use App\Models\Equipo;
use App\Models\Funcionario;
use App\Models\LicenciaHistorial;
use App\Http\Requests\StoreLicenciaAsignacionRequest;
use App\Http\Requests\UpdateLicenciaAsignacionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LicenciaAsignacionController extends Controller
{
    public function index(Request $request)
    {
        $query = LicenciaAsignacion::with(['licencia', 'equipo', 'funcionario']);

        if ($request->has('estado') && $request->estado != '') {
            $query->where('estado', $request->estado);
        }

        $asignaciones = $query->paginate(10)->withQueryString();
        
        return view('licencias_asignaciones.index', compact('asignaciones'));
    }

    public function create()
    {
        $licencias = Licencia::where('estado', 'Activa')->get();
        $equipos = Equipo::select('id', 'nombre_equipo', 'activo_fijo', 'serial')->get();
        $funcionarios = Funcionario::select('id', 'nombres', 'apellidos', 'identificacion')->get();
        // Nota: En la vista se usa $funcionario->cedula, pero el modelo usa identificacion.
        // Eloquent devolverá null para cedula, así que para no romper la vista inyectamos el alias o simplemente
        // dejamos que devuelva los datos requeridos.
        // Mejor añadir el alias a la consulta o iterar, pero select('id', 'nombres', 'apellidos', 'identificacion as cedula') no siempre funciona si la bd no lo permite fácil, sin embargo SQL lo soporta.
        $funcionarios = Funcionario::select('id', 'nombres', 'apellidos', 'identificacion', 'identificacion as cedula')->get();
        
        return view('licencias_asignaciones.create', compact('licencias', 'equipos', 'funcionarios'));
    }

    public function store(StoreLicenciaAsignacionRequest $request)
    {
        $asignacion = LicenciaAsignacion::create($request->validated() + ['created_by' => Auth::id()]);

        // Registrar historial
        $licenciaNombre = $asignacion->licencia ? $asignacion->licencia->nombre : 'Sin licencia';
        $equipoPlaca = $asignacion->equipo ? $asignacion->equipo->placa : 'N/A';
        $funcionarioNombre = $asignacion->funcionario ? $asignacion->funcionario->nombre_completo : 'N/A';

        LicenciaHistorial::create([
            'fecha' => now(),
            'usuario_id' => Auth::id(),
            'accion' => 'Asignación de licencia',
            'licencia_nombre' => $licenciaNombre,
            'funcionario_nombre' => $funcionarioNombre,
            'equipo_placa' => $equipoPlaca,
            'observacion' => 'Licencia asignada al equipo y funcionario',
        ]);

        return redirect()->route('licencia-asignaciones.index')->with('success', 'Asignación registrada exitosamente.');
    }

    public function show(LicenciaAsignacion $licencia_asignacion)
    {
        // En laravel los parametros de ruta resource pueden nombrarse 'licencia_asignacion' o 'licencia_asignacione'
        // El framework a veces quita la 's' final, así que 'licencia_asignacion' es el estándar.
        $licencia_asignacion->load(['licencia', 'equipo', 'funcionario']);
        return view('licencias_asignaciones.show', compact('licencia_asignacion'));
    }

    public function edit(LicenciaAsignacion $licencia_asignacion)
    {
        $licencias = Licencia::where('estado', 'Activa')->get();
        $equipos = Equipo::select('id', 'nombre_equipo', 'activo_fijo', 'serial')->get();
        $funcionarios = Funcionario::select('id', 'nombres', 'apellidos', 'identificacion', 'identificacion as cedula')->get();

        return view('licencias_asignaciones.edit', compact('licencia_asignacion', 'licencias', 'equipos', 'funcionarios'));
    }

    public function update(UpdateLicenciaAsignacionRequest $request, LicenciaAsignacion $licencia_asignacion)
    {
        $viejoEstado = $licencia_asignacion->estado;
        $licencia_asignacion->update($request->validated() + ['updated_by' => Auth::id()]);

        $accion = 'Actualización de asignación';
        if ($viejoEstado != $request->estado) {
            $accion = "Cambio de estado a " . $request->estado;
        }

        $licenciaNombre = $licencia_asignacion->licencia ? $licencia_asignacion->licencia->nombre : 'Sin licencia';
        $equipoPlaca = $licencia_asignacion->equipo ? $licencia_asignacion->equipo->placa : 'N/A';
        $funcionarioNombre = $licencia_asignacion->funcionario ? $licencia_asignacion->funcionario->nombre_completo : 'N/A';

        LicenciaHistorial::create([
            'fecha' => now(),
            'usuario_id' => Auth::id(),
            'accion' => $accion,
            'licencia_nombre' => $licenciaNombre,
            'funcionario_nombre' => $funcionarioNombre,
            'equipo_placa' => $equipoPlaca,
            'observacion' => 'Asignación actualizada',
        ]);

        return redirect()->route('licencia-asignaciones.index')->with('success', 'Asignación actualizada exitosamente.');
    }

    public function destroy(LicenciaAsignacion $licencia_asignacion)
    {
        $licenciaNombre = $licencia_asignacion->licencia ? $licencia_asignacion->licencia->nombre : 'Sin licencia';
        $equipoPlaca = $licencia_asignacion->equipo ? $licencia_asignacion->equipo->placa : 'N/A';
        $funcionarioNombre = $licencia_asignacion->funcionario ? $licencia_asignacion->funcionario->nombre_completo : 'N/A';

        LicenciaHistorial::create([
            'fecha' => now(),
            'usuario_id' => Auth::id(),
            'accion' => 'Retiro de licencia',
            'licencia_nombre' => $licenciaNombre,
            'funcionario_nombre' => $funcionarioNombre,
            'equipo_placa' => $equipoPlaca,
            'observacion' => 'La asignación de licencia fue eliminada',
        ]);

        $licencia_asignacion->delete();

        return redirect()->route('licencia-asignaciones.index')->with('success', 'Asignación eliminada exitosamente.');
    }
}
