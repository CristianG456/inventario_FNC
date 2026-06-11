<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLicenciaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:255|unique:licencias,nombre,' . $this->licencia->id,
            'descripcion' => 'nullable|string',
            'tipo_licencia' => 'required|in:Suscripción,Vitalicia',
            'cantidad_maxima' => 'required|integer|min:1',
            'fecha_inicio' => 'nullable|date',
            'fecha_vencimiento' => 'nullable|date|after_or_equal:fecha_inicio',
            'estado' => 'required|in:Activa,Suspendida,Vencida',
            'observaciones' => 'nullable|string',
        ];
    }
}
