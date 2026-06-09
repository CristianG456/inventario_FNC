<?php

namespace App\Http\Controllers;

use App\Models\Funcionario;
use Illuminate\Http\Request;

class FuncionarioController extends Controller
{
    public function index(Request $request)
    {
        $query = Funcionario::query();

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where('nombres', 'like', "%{$buscar}%")
                  ->orWhere('identificacion', 'like', "%{$buscar}%")
                  ->orWhere('cargo', 'like', "%{$buscar}%");
        }

        $funcionarios = $query->paginate(15);
        
        return view('funcionarios.index', compact('funcionarios'));
    }

    public function create()
    {
        return view('funcionarios.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'identificacion' => 'required|string|unique:funcionarios,identificacion',
            'nombres' => 'required|string|max:100',
            'apellidos' => 'nullable|string|max:100',
            'cargo' => 'nullable|string|max:100',
            'area' => 'nullable|string|max:100',
            'departamento' => 'nullable|string|max:100',
            'ciudad' => 'nullable|string|max:100',
            'estado' => 'required|in:Activo,Inactivo',
        ]);

        Funcionario::create($validated);

        return redirect()->route('funcionarios.index')->with('success', 'Funcionario registrado exitosamente.');
    }
}
