<?php

namespace App\Http\Controllers;

use App\Http\Requests\TipoRecursoRequest;
use App\Models\TipoRecurso;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TipoRecursoController extends Controller
{
    public function index(): View
    {
        $tipoRecursos = TipoRecurso::withCount('equipos')->orderBy('nombre')->paginate(20);
        return view('tipo_recursos.index', compact('tipoRecursos'));
    }

    public function create(): View
    {
        return view('tipo_recursos.create');
    }

    public function store(TipoRecursoRequest $request): RedirectResponse
    {
        TipoRecurso::create($request->validated());

        return redirect()->route('tipo-recursos.index')
            ->with('success', 'Tipo de recurso creado correctamente.');
    }

    public function edit(TipoRecurso $tipoRecurso): View
    {
        return view('tipo_recursos.edit', compact('tipoRecurso'));
    }

    public function update(TipoRecursoRequest $request, TipoRecurso $tipoRecurso): RedirectResponse
    {
        $tipoRecurso->update($request->validated());

        return redirect()->route('tipo-recursos.index')
            ->with('success', 'Tipo de recurso actualizado correctamente.');
    }

    public function destroy(TipoRecurso $tipoRecurso): RedirectResponse
    {
        // Evitar eliminar si tiene equipos asociados
        if ($tipoRecurso->equipos()->count() > 0) {
            return redirect()->route('tipo-recursos.index')
                ->with('error', 'No se puede eliminar: tiene equipos asociados.');
        }

        $tipoRecurso->delete();

        return redirect()->route('tipo-recursos.index')
            ->with('success', 'Tipo de recurso eliminado correctamente.');
    }
}
