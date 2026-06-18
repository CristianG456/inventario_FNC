<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCampoPersonalizadoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('campos_personalizados.crear');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'modulo'               => 'required|string|max:50',
            'nombre'               => 'required|string|max:150|unique:campos_personalizados,nombre',
            'descripcion'          => 'nullable|string',
            'tipo'                 => 'required|string|in:texto,textarea,numero,fecha,correo,telefono,boolean,select,multiselect,url,archivo',
            'obligatorio'          => 'boolean',
            'editable'             => 'boolean',
            'visible'              => 'boolean',
            'importable'           => 'boolean',
            'exportable'           => 'boolean',
            'exportar_por_defecto' => 'boolean',
            'activo'               => 'boolean',
            'opciones'             => 'nullable|string', // Se enviará como cadena separada por comas desde la vista y se guardará en la tabla opciones
        ];
    }
}
