<?php

namespace App\Http\Controllers;

use App\Models\AutorizacionActivo;
use App\Models\Funcionario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class FuncionarioController extends Controller
{
    public function index(Request $request)
    {
        $query = Funcionario::withCount([
            'equiposAsignados',
            'autorizacionesActivos as autorizaciones_disponibles_count' => fn ($q) => $q->disponibles(),
            'autorizacionesActivos as autorizaciones_total_count',
        ]);

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where('nombres', 'like', "%{$buscar}%")
                  ->orWhere('identificacion', 'like', "%{$buscar}%")
                  ->orWhere('cargo', 'like', "%{$buscar}%");
        }

        $funcionarios = $query->paginate(15);
        
        return view('funcionarios.index', compact('funcionarios'));
    }

    public function storeAutorizacion(Request $request, Funcionario $funcionario)
    {
        $request->validate([
            'archivo_autorizacion' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:10240'],
        ]);

        $archivo = $request->file('archivo_autorizacion');
        $ruta = $archivo->store('autorizaciones_activos', 'local');

        AutorizacionActivo::create([
            'funcionario_id' => $funcionario->id,
            'cedula' => $funcionario->identificacion,
            'nombre_funcionario' => $funcionario->nombre_completo,
            'archivo' => $ruta,
            'mime_type' => $archivo->getMimeType(),
            'tamano_bytes' => $archivo->getSize(),
            'estado' => AutorizacionActivo::ESTADO_CARGADA,
            'user_id' => Auth::id(),
        ]);

        return redirect()
            ->route('funcionarios.index')
            ->with('success', 'Autorización cargada correctamente para ' . $funcionario->nombre_completo . '.');
    }

    public function anularAutorizacion(Request $request, Funcionario $funcionario, AutorizacionActivo $autorizacion)
    {
        if ((int) $autorizacion->funcionario_id !== (int) $funcionario->id) {
            throw ValidationException::withMessages([
                'autorizacion' => 'La autorización no corresponde al funcionario seleccionado.',
            ]);
        }

        if ($autorizacion->estado !== AutorizacionActivo::ESTADO_CARGADA) {
            throw ValidationException::withMessages([
                'autorizacion' => 'Solo se pueden anular autorizaciones en estado cargada.',
            ]);
        }

        $validated = $request->validate([
            'motivo_anulacion' => ['nullable', 'string', 'max:500'],
        ]);

        $autorizacion->update([
            'estado' => AutorizacionActivo::ESTADO_ANULADA,
            'anulada_en' => now(),
            'anulada_por_user_id' => Auth::id(),
            'motivo_anulacion' => trim((string) ($validated['motivo_anulacion'] ?? '')) ?: 'Anulación manual',
        ]);

        return redirect()
            ->route('funcionarios.show', $funcionario)
            ->with('success', 'Autorización anulada correctamente.');
    }

    public function create()
    {
        return view('funcionarios.create');
    }

    public function show(Funcionario $funcionario)
    {
        $funcionario->load([
            'equiposAsignados' => function ($q) {
                $q->with('equipo:id,nombre_equipo,serial,estado_operativo');
            },
            'autorizacionesActivos' => function ($q) {
                $q->latest();
            },
        ])->loadCount(['equiposAsignados', 'autorizacionesActivos']);

        return view('funcionarios.show', compact('funcionario'));
    }

    public function edit(Funcionario $funcionario)
    {
        return view('funcionarios.edit', compact('funcionario'));
    }

    public function store(Request $request)
    {
        $validated = $this->validarFuncionario($request);

        Funcionario::create($validated);

        return redirect()->route('funcionarios.index')->with('success', 'Funcionario registrado exitosamente.');
    }

    public function update(Request $request, Funcionario $funcionario)
    {
        $validated = $this->validarFuncionario($request, $funcionario->id);
        $funcionario->update($validated);

        return redirect()
            ->route('funcionarios.show', $funcionario)
            ->with('success', 'Funcionario actualizado exitosamente.');
    }

    private function validarFuncionario(Request $request, ?int $funcionarioId = null): array
    {
        $identificacionRule = 'required|string|unique:funcionarios,identificacion';
        if ($funcionarioId) {
            $identificacionRule .= ',' . $funcionarioId;
        }

        return $request->validate([
            'identificacion' => $identificacionRule,
            'nombres' => 'required|string|max:100',
            'apellidos' => 'nullable|string|max:100',
            'cargo' => 'nullable|string|max:100',
            'area' => 'nullable|string|max:100',
            'departamento' => 'nullable|string|max:100',
            'ciudad' => 'nullable|string|max:100',
            'empresa_funcionario' => 'nullable|string|max:150',
            'tipo_vinculacion' => 'nullable|string|max:100',
            'estado' => 'required|in:Activo,Inactivo',
        ]);
    }
}
