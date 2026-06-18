<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CampoPersonalizado extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'campos_personalizados';

    protected $fillable = [
        'modulo',
        'nombre',
        'descripcion',
        'tipo',
        'obligatorio',
        'editable',
        'visible',
        'importable',
        'exportable',
        'exportar_por_defecto',
        'orden',
        'activo'
    ];

    protected $casts = [
        'obligatorio' => 'boolean',
        'editable' => 'boolean',
        'visible' => 'boolean',
        'importable' => 'boolean',
        'exportable' => 'boolean',
        'exportar_por_defecto' => 'boolean',
        'activo' => 'boolean',
    ];

    public function opciones(): HasMany
    {
        return $this->hasMany(CampoPersonalizadoOpcion::class)->orderBy('orden');
    }

    public function valores(): HasMany
    {
        return $this->hasMany(CampoPersonalizadoValor::class);
    }
}
