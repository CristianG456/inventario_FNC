<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCampoPersonalizadoRequest;
use App\Http\Requests\UpdateCampoPersonalizadoRequest;
use App\Models\CampoPersonalizado;
use App\Models\CampoPersonalizadoOpcion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class CampoPersonalizadoController extends Controller
{
    public function index()
    {
        $campos = CampoPersonalizado::with('opciones')->orderBy('modulo')->orderBy('orden')->get();
        return view('campos_personalizados.index', compact('campos'));
    }

    public function store(StoreCampoPersonalizadoRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            $data['obligatorio'] = $request->boolean('obligatorio');
            $data['editable'] = $request->boolean('editable', true);
            $data['visible'] = $request->boolean('visible', true);
            $data['importable'] = $request->boolean('importable', true);
            $data['exportable'] = $request->boolean('exportable', true);
            $data['exportar_por_defecto'] = $request->boolean('exportar_por_defecto');
            $data['activo'] = $request->boolean('activo', true);

            $maxOrden = CampoPersonalizado::where('modulo', $data['modulo'])->max('orden');
            $data['orden'] = $maxOrden !== null ? $maxOrden + 1 : 0;

            $campo = CampoPersonalizado::create($data);

            if (in_array($campo->tipo, ['select', 'multiselect']) && !empty($data['opciones'])) {
                $opciones = array_map('trim', explode(',', $data['opciones']));
                foreach ($opciones as $index => $valor) {
                    if ($valor !== '') {
                        $campo->opciones()->create([
                            'valor' => $valor,
                            'orden' => $index
                        ]);
                    }
                }
            }

            AuditLog::create([
                'user_id' => Auth::id(),
                'user_name' => Auth::user()->name ?? 'System',
                'action' => 'CREAR_CAMPO_PERSONALIZADO',
                'details' => json_encode(['nombre' => $campo->nombre, 'modulo' => $campo->modulo])
            ]);

            DB::commit();
            return redirect()->route('campos-personalizados.index')->with('success', 'Campo personalizado creado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al crear el campo: ' . $e->getMessage())->withInput();
        }
    }

    public function update(UpdateCampoPersonalizadoRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $campo = CampoPersonalizado::findOrFail($id);
            $data = $request->validated();
            $data['obligatorio'] = $request->boolean('obligatorio');
            $data['editable'] = $request->boolean('editable', true);
            $data['visible'] = $request->boolean('visible', true);
            $data['importable'] = $request->boolean('importable', true);
            $data['exportable'] = $request->boolean('exportable', true);
            $data['exportar_por_defecto'] = $request->boolean('exportar_por_defecto');
            $data['activo'] = $request->boolean('activo', true);

            $campo->update($data);

            if (in_array($campo->tipo, ['select', 'multiselect'])) {
                $campo->opciones()->delete();
                if (!empty($data['opciones'])) {
                    $opciones = array_map('trim', explode(',', $data['opciones']));
                    foreach ($opciones as $index => $valor) {
                        if ($valor !== '') {
                            $campo->opciones()->create([
                                'valor' => $valor,
                                'orden' => $index
                            ]);
                        }
                    }
                }
            }

            AuditLog::create([
                'user_id' => Auth::id(),
                'user_name' => Auth::user()->name ?? 'System',
                'action' => 'ACTUALIZAR_CAMPO_PERSONALIZADO',
                'details' => json_encode(['nombre' => $campo->nombre, 'modulo' => $campo->modulo])
            ]);

            DB::commit();
            return redirect()->route('campos-personalizados.index')->with('success', 'Campo personalizado actualizado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al actualizar el campo: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        $campo = CampoPersonalizado::findOrFail($id);
        $campo->delete();

        AuditLog::create([
            'user_id' => Auth::id(),
            'user_name' => Auth::user()->name ?? 'System',
            'action' => 'ELIMINAR_CAMPO_PERSONALIZADO',
            'details' => json_encode(['nombre' => $campo->nombre, 'modulo' => $campo->modulo])
        ]);

        return redirect()->route('campos-personalizados.index')->with('success', 'Campo personalizado eliminado exitosamente.');
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*.id' => 'required|exists:campos_personalizados,id',
            'order.*.orden' => 'required|integer',
        ]);

        foreach ($request->order as $item) {
            CampoPersonalizado::where('id', $item['id'])->update(['orden' => $item['orden']]);
        }

        return response()->json(['success' => true]);
    }
}
