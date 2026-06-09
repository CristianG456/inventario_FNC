<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Funcionario;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $query = Ticket::with(['funcionario', 'responsable']);

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where('titulo', 'like', "%{$buscar}%")
                  ->orWhere('id', $buscar)
                  ->orWhereHas('funcionario', function($q) use ($buscar) {
                      $q->where('nombres', 'like', "%{$buscar}%")
                        ->orWhere('identificacion', 'like', "%{$buscar}%");
                  });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $tickets = $query->orderByDesc('created_at')->paginate(15);
        $totalTickets = Ticket::count();

        return view('tickets.index', compact('tickets', 'totalTickets'));
    }

    public function create()
    {
        $funcionarios = Funcionario::orderBy('nombres')->get();
        return view('tickets.create', compact('funcionarios'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'tipo' => 'required|string',
            'prioridad' => 'required|string',
            'descripcion' => 'required|string',
            'funcionario_id' => 'required|exists:funcionarios,id',
            'equipo_id' => 'nullable|exists:equipos,id',
        ]);

        Ticket::create([
            ...$validated,
            'estado' => 'Abierto'
        ]);

        return redirect()->route('tickets.index')->with('success', 'Ticket creado correctamente');
    }
}
