<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistorialAdministrativo extends Model
{
    use HasFactory;

    protected $table = 'historial_administrativos';

    // Sin SoftDeletes — auditoría inmutable

    protected $fillable = [
        'equipo_id',
        'user_id',
        'tipo_cambio',
        'campo_modificado',
        'valor_anterior',
        'valor_nuevo',
        'descripcion',
    ];

    // ── Constantes de tipos de cambio ─────────────────────────────────────────

    const TIPOS_CAMBIO = [
        'edicion'          => 'Edición de datos',
        'cambio_serial'    => 'Cambio de serial',
        'cambio_activo'    => 'Cambio de activo fijo',
        'cambio_estado'    => 'Cambio de estado operativo',
        'eliminacion'      => 'Eliminación lógica',
        'restauracion'     => 'Restauración',
        'creacion'         => 'Registro creado',
        'asignacion'       => 'Asignación de usuario',
        'retiro'           => 'Retiro de usuario',
        'otro'             => 'Otro cambio',
    ];

    // ── Relaciones ────────────────────────────────────────────────────────────

    public function equipo(): BelongsTo
    {
        return $this->belongsTo(Equipo::class);
    }

    public function realizadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    public function getTipoCambioLabelAttribute(): string
    {
        return self::TIPOS_CAMBIO[$this->tipo_cambio] ?? ucfirst($this->tipo_cambio);
    }

    public function getTipoCambioColorAttribute(): string
    {
        return match ($this->tipo_cambio) {
            'creacion'      => 'success',
            'edicion'       => 'primary',
            'cambio_serial' => 'warning',
            'cambio_activo' => 'warning',
            'cambio_estado' => 'info',
            'eliminacion'   => 'danger',
            'restauracion'  => 'success',
            'asignacion'    => 'primary',
            'retiro'        => 'secondary',
            default         => 'secondary',
        };
    }
}
