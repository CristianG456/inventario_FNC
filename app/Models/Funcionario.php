<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Funcionario extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'identificacion',
        'nombres',
        'apellidos',
        'cargo',
        'area',
        'departamento',
        'ciudad',
        'empresa_funcionario',
        'tipo_vinculacion',
        'estado',
    ];

    public function getNombreCompletoAttribute()
    {
        return trim("{$this->nombres} {$this->apellidos}");
    }

    public function licenciaAsignaciones()
    {
        return $this->hasMany(LicenciaAsignacion::class, 'funcionario_id');
    }

    public function equiposAsignados()
    {
        return $this->hasMany(UsuarioAsignado::class, 'cedula', 'identificacion');
    }

    public function autorizacionesActivos()
    {
        return $this->hasMany(AutorizacionActivo::class, 'funcionario_id');
    }
}
