<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string|null $responsable_cedula
 * @property string|null $responsable_nombre
 * @property string|null $responsable_cargo
 * @property string|null $responsable_ciudad
 * @property string|null $responsable_area
 * @property string|null $responsable_tipo_recurso
 * @property \Illuminate\Support\Carbon|null $fecha_inicio_responsable
 * @property \Illuminate\Support\Carbon|null $fecha_fin_responsable
 */
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
        'responsable_cedula',
        'responsable_nombre',
        'responsable_cargo',
        'responsable_ciudad',
        'responsable_area',
        'responsable_tipo_recurso',
        'fecha_inicio_responsable',
        'fecha_fin_responsable',
    ];

    protected $casts = [
        'fecha_compra' => 'date',
        'fin_garantia' => 'date',
        'fecha_inicio_responsable' => 'date',
        'fecha_fin_responsable' => 'date',
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

    public function asignaciones(): HasMany
    {
        return $this->hasMany(Asignacion::class);
    }

    public function complementos(): HasMany
    {
        return $this->hasMany(ActivoComplemento::class, 'equipo_id');
    }

    public function checklists(): HasMany
    {
        return $this->hasMany(Checklist::class);
    }

    public function latestChecklist(): HasOne
    {
        return $this->hasOne(Checklist::class)->latestOfMany();
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

    public function camposPersonalizadosValores(): HasMany
    {
        return $this->hasMany(CampoPersonalizadoValor::class, 'entidad_id');
    }

    /**
     * Etiqueta legible del estado operativo.
     */
    public function getEstadoLabelAttribute(): string
    {
        return match ($this->estado_operativo) {
            'activo'        => 'Asignado',
            'disponible'    => 'Disponible',
            'asignado'      => 'Asignado',
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
            'disponible'    => 'primary',
            'asignado'      => 'success',
            'mantenimiento' => 'warning',
            'baja'          => 'danger',
            default         => 'secondary',
        };
    }

    /**
     * Identificador interno estandarizado generado dinámicamente.
     */
    public function getIdentificadorInternoAttribute(): string
    {
        $prefijo = $this->tipoRecurso ? $this->tipoRecurso->prefijo : 'ACT';
        // Formatea el ID rellenando con ceros a la izquierda hasta 4 dígitos (Ej. LAP-0015)
        return $prefijo . '-' . str_pad((string) $this->id, 4, '0', STR_PAD_LEFT);
    }

    public function getSerialVisualAttribute(): string
    {
        $serialReal = trim((string) $this->serial);
        $invalidos = ['PENDIENTE', 'N/A', 'NA', 'NO TIENE', 'SIN SERIAL', 'SIN REGISTRO'];
        
        if ($serialReal === '' || str_starts_with(strtoupper($serialReal), 'SIN_SERIAL_') || in_array(strtoupper($serialReal), $invalidos, true)) {
            $prefijo = $this->tipoRecurso ? $this->tipoRecurso->prefijo : 'ACT';
            $codigo = str_pad((string) $this->id, 4, '0', STR_PAD_LEFT);
            return "{$prefijo}-S{$codigo}";
        }
        return $serialReal;
    }

    /**
     * Identificador visual inteligente para la placa.
     */
    public function getPlacaVisualAttribute(): string
    {
        $placaReal = trim((string) $this->placa);
        $invalidos = ['PENDIENTE', 'N/A', 'NA', 'NO TIENE', 'SIN PLACA', 'SIN REGISTRO'];
        
        if ($placaReal === '' || in_array(strtoupper($placaReal), $invalidos, true)) {
            $prefijo = $this->tipoRecurso ? $this->tipoRecurso->prefijo : 'ACT';
            $codigo = str_pad((string) $this->id, 4, '0', STR_PAD_LEFT);
            return "{$prefijo}-P{$codigo}";
        }
        return $placaReal;
    }
}
