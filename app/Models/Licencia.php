<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Licencia extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nombre',
        'descripcion',
        'tipo_licencia',
        'cantidad_maxima',
        'fecha_inicio',
        'fecha_vencimiento',
        'estado',
        'observaciones',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_vencimiento' => 'date',
        'cantidad_maxima' => 'integer',
    ];

    public function asignaciones(): HasMany
    {
        return $this->hasMany(LicenciaAsignacion::class, 'licencia_id');
    }

    public function historial(): HasMany
    {
        return $this->hasMany(LicenciaHistorial::class, 'licencia_nombre', 'nombre'); // Vinculo lógico por nombre
    }

    /**
     * Devuelve la cantidad de cupos actualmente asignados de forma activa.
     */
    public function getCuposAsignadosAttribute(): int
    {
        return $this->asignaciones()->where('estado', 'Activa')->count();
    }

    /**
     * Devuelve la cantidad de cupos disponibles.
     */
    public function getCuposDisponiblesAttribute(): int
    {
        return max(0, $this->cantidad_maxima - $this->cupos_asignados);
    }

    /**
     * Indica si quedan cupos disponibles para esta licencia.
     */
    public function getTieneCuposAttribute(): bool
    {
        return $this->cupos_disponibles > 0;
    }
}
