<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'titulo',
        'tipo',
        'prioridad',
        'descripcion',
        'estado',
        'funcionario_id',
        'equipo_id',
        'user_id',
        'archivos',
    ];

    protected $casts = [
        'archivos' => 'array',
    ];

    public function funcionario()
    {
        return $this->belongsTo(Funcionario::class);
    }

    public function equipo()
    {
        return $this->belongsTo(Equipo::class);
    }

    public function responsable()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
