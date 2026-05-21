<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EquipoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $equipoId = $this->route('equipo')?->id;

        return [
            // --- Datos del equipo ---
            'tipo_recurso_id'  => ['required', 'exists:tipo_recursos,id'],
            'serial'           => ['required', 'string', 'max:100', Rule::unique('equipos', 'serial')->ignore($equipoId)->whereNull('deleted_at')],
            'placa'            => ['nullable', 'string', 'max:100'],
            'marca'            => ['required', 'string', 'max:100'],
            'modelo'           => ['required', 'string', 'max:100'],
            'nombre_equipo'    => ['required', 'string', 'max:150'],
            'estado_operativo' => ['required', Rule::in(['activo', 'mantenimiento', 'baja'])],
            'razon_estado'     => ['nullable', 'string', 'max:500'],
            'procesador'       => ['nullable', 'string', 'max:150'],
            'ram'              => ['nullable', 'string', 'max:50'],
            'disco'            => ['nullable', 'string', 'max:50'],
            'sistema_operativo'=> ['nullable', 'string', 'max:100'],
            'fecha_compra'     => ['nullable', 'date'],
            'fin_garantia'     => ['nullable', 'date', 'after_or_equal:fecha_compra'],
            'tiempo_uso'       => ['nullable', 'string', 'max:100'],

            // --- Usuario asignado ---
            'usuario_nombre'       => ['required', 'string', 'max:150'],
            'usuario_cedula'       => ['required', 'string', 'max:20'],
            'usuario_empresa_propietaria' => ['nullable', 'string', 'max:150'],
            'usuario_dependencia' => ['nullable', 'string', 'max:150'],
            'usuario_fuente_recurso' => ['nullable', 'string', 'max:150'],
            'usuario_empresa_funcionario' => ['nullable', 'string', 'max:150'],
            'usuario_tipo_vinculacion' => ['nullable', 'string', 'max:100'],
            'usuario_shortname' => ['nullable', 'string', 'max:100'],
            'usuario_departamento' => ['nullable', 'string', 'max:100'],
            'usuario_ciudad'       => ['nullable', 'string', 'max:100'],
            'usuario_cargo'        => ['nullable', 'string', 'max:100'],
            'usuario_area'         => ['nullable', 'string', 'max:100'],
            'usuario_piso'         => ['nullable', 'string', 'max:20'],

            // --- Periféricos ---
            'periferico_telefono' => ['nullable', 'string', 'max:100'],
            'periferico_teclado'  => ['nullable', 'string', 'max:100'],
            'periferico_mouse'    => ['nullable', 'string', 'max:100'],
            'periferico_camara'   => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'tipo_recurso_id.required'  => 'El tipo de recurso es obligatorio.',
            'tipo_recurso_id.exists'    => 'El tipo de recurso seleccionado no existe.',
            'serial.required'           => 'El número de serial es obligatorio.',
            'serial.unique'             => 'Ya existe un equipo con este serial.',
            'marca.required'            => 'La marca es obligatoria.',
            'modelo.required'           => 'El modelo es obligatorio.',
            'nombre_equipo.required'    => 'El nombre del equipo es obligatorio.',
            'estado_operativo.required' => 'El estado operativo es obligatorio.',
            'estado_operativo.in'       => 'El estado seleccionado no es válido.',
            'fin_garantia.after_or_equal' => 'La fecha de fin de garantía debe ser igual o posterior a la fecha de compra.',
            'usuario_nombre.required'   => 'El nombre del usuario asignado es obligatorio.',
            'usuario_cedula.required'   => 'La cédula del usuario es obligatoria.',
            'usuario_empresa_propietaria.max' => 'La empresa propietaria no puede superar los 150 caracteres.',
            'usuario_dependencia.max' => 'La dependencia no puede superar los 150 caracteres.',
            'usuario_fuente_recurso.max' => 'La fuente de recurso no puede superar los 150 caracteres.',
            'usuario_empresa_funcionario.max' => 'La empresa del funcionario no puede superar los 150 caracteres.',
            'usuario_tipo_vinculacion.max' => 'El tipo de vinculación no puede superar los 100 caracteres.',
            'usuario_shortname.max' => 'El shortname no puede superar los 100 caracteres.',
        ];
    }

    public function attributes(): array
    {
        return [
            'tipo_recurso_id'  => 'tipo de recurso',
            'serial'           => 'serial',
            'marca'            => 'marca',
            'modelo'           => 'modelo',
            'nombre_equipo'    => 'nombre del equipo',
            'estado_operativo' => 'estado operativo',
            'usuario_empresa_propietaria' => 'empresa propietaria',
            'usuario_dependencia' => 'dependencia',
            'usuario_fuente_recurso' => 'fuente de recurso',
            'usuario_empresa_funcionario' => 'empresa del funcionario',
            'usuario_tipo_vinculacion' => 'empleado o contratista',
            'usuario_shortname' => 'shortname',
        ];
    }
}
