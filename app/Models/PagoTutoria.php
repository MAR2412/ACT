<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PagoTutoria extends Model
{
    use SoftDeletes;
    
    protected $table = 'pago_tutorias';
    
    protected $fillable = [
        'matricula_tutoria_id',
        'tipo',
        'metodo_pago',
        'monto',
        'monto_pagado',
        'cambio',
        'numero_transaccion',
        'referencia_bancaria',
        'estado',
        'fecha_pago',
        'observaciones',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
    
    protected $casts = [
        'monto' => 'decimal:2',
        'monto_pagado' => 'decimal:2',
        'cambio' => 'decimal:2',
        'fecha_pago' => 'date'
    ];
    
    public function matriculaTutoria()
    {
        return $this->belongsTo(MatriculaTutoria::class);
    }
    
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    
    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}