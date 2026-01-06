<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Seccion extends BaseModel
{
    use HasFactory, SoftDeletes;
    protected $table = 'secciones';
    protected $fillable = [
        'nombre',
        'HoraInicio',
        'HoraFin',
        'dia',
        'diaF',
        'estado',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'estado' => 'boolean',
        
    ];
// En tu modelo Seccion.php


    // Relación con el usuario que creó la sección
    public function creador()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relación con el usuario que actualizó la sección
    public function actualizador()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Relación con el usuario que eliminó la sección
    public function eliminador()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    // Accesor para el estado como texto
    public function getEstadoTextoAttribute()
    {
        return $this->estado ? 'Activo' : 'Inactivo';
    }

    // Accesor para el horario completo
    public function getHorarioCompletoAttribute()
    {
        return "{$this->HoraInicio} - {$this->HoraFin}";
    }
}