<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCampoPersonalizadoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('campos_personalizados.editar');
    }

    public function rules(): array
    {
        $id = $this->route('campos_personalizado') ?? $this->route('campo_personalizado');
        return [
            'modulo'               => 'required|string|max:50',
            'nombre'               => 'required|string|max:150|unique:campos_personalizados,nombre,' . $id,
            'descripcion'          => 'nullable|string',
            'tipo'                 => 'required|string|in:texto,textarea,numero,fecha,correo,telefono,boolean,select,multiselect,url,archivo',
            'obligatorio'          => 'boolean',
            'editable'             => 'boolean',
            'visible'              => 'boolean',
            'importable'           => 'boolean',
            'exportable'           => 'boolean',
            'exportar_por_defecto' => 'boolean',
            'activo'               => 'boolean',
            'opciones'             => 'nullable|string',
        ];
    }
}
