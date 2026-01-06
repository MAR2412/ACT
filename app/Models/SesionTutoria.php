<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SesionTutoria extends Model
{
    use SoftDeletes;
    
    protected $table = 'sesiones_tutoria';
    
    protected $fillable = [
        'matricula_tutoria_id',
        'fecha_sesion',
        'hora_inicio',
        'hora_fin',
        'contenido',
        'monto_sesion',
        'asistio',
        'pagado',
        'observaciones',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
    
    protected $casts = [
        'fecha_sesion' => 'date',
        'monto_sesion' => 'decimal:2',
        'asistio' => 'boolean',
        'pagado' => 'boolean'
    ];
    
    public function matriculaTutoria()
    {
        return $this->belongsTo(MatriculaTutoria::class);
    }
    
    public function pago()
    {
        return $this->hasOne(PagoTutoria::class, 'sesion_tutoria_id');
    }
}