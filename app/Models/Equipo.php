<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Equipo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'equipos';

    protected $fillable = [
        'tipo_recurso_id',
        'serial',
        'activo_fijo',
        'placa',
        'marca',
        'modelo',
        'nombre_equipo',
        'estado_operativo',
        'razon_estado',
        'procesador',
        'ram',
        'disco',
        'sistema_operativo',
        'fecha_compra',
        'fin_garantia',
        'tiempo_uso',
    ];

    protected $casts = [
        'fecha_compra' => 'date',
        'fin_garantia' => 'date',
    ];

    public function tipoRecurso(): BelongsTo
    {
        return $this->belongsTo(TipoRecurso::class);
    }

    public function usuarioAsignado(): HasOne
    {
        return $this->hasOne(UsuarioAsignado::class);
    }

    public function periferico(): HasOne
    {
        return $this->hasOne(Periferico::class);
    }

    public function checklists(): HasMany
    {
        return $this->hasMany(Checklist::class);
    }

    public function asignaciones(): HasMany
    {
        return $this->hasMany(Asignacion::class)->orderByDesc('fecha_accion');
    }

    public function historialTecnico(): HasMany
    {
        return $this->hasMany(HistorialTecnico::class)->orderByDesc('fecha_evento');
    }

    public function historialAdministrativo(): HasMany
    {
        return $this->hasMany(HistorialAdministrativo::class)->orderByDesc('created_at');
    }

    public function licenciaAsignaciones(): HasMany
    {
        return $this->hasMany(LicenciaAsignacion::class, 'equipo_id');
    }

    /**
     * Indica si el equipo tiene un usuario asignado actualmente.
     */
    public function estaAsignado(): bool
    {
        return $this->usuarioAsignado()->exists();
    }

    /**
     * Etiqueta legible del estado operativo.
     */
    public function getEstadoLabelAttribute(): string
    {
        return match ($this->estado_operativo) {
            'activo'        => 'Activo',
            'mantenimiento' => 'Mantenimiento',
            'baja'          => 'Baja',
            default         => $this->estado_operativo,
        };
    }

    /**
     * Clase Bootstrap para el badge de estado.
     */
    public function getEstadoBadgeAttribute(): string
    {
        return match ($this->estado_operativo) {
            'activo'        => 'success',
            'mantenimiento' => 'warning',
            'baja'          => 'danger',
            default         => 'secondary',
        };
    }
}
