<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MatriculaTutoria extends Model
{
    use SoftDeletes;
    
    protected $table = 'matricula_tutorias';
    
    protected $fillable = [
        'estudiante_id',
        'tutoria_id',
        'estado',
        'precio_hora_aplicado',
        'tutorias_registradas',
        'tutorias_pagadas',
        'saldo_pendiente',
        'fecha_inicio',
        'aprobado',
        'observaciones',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
    
    protected $casts = [
        'precio_hora_aplicado' => 'decimal:2',
        'saldo_pendiente' => 'decimal:2',
        'fecha_inicio' => 'date',
        'aprobado' => 'boolean'
    ];
    
    // Accessor para saldo formateado
    public function getSaldoPendienteFormateadoAttribute()
    {
        return 'L. ' . number_format($this->saldo_pendiente, 2);
    }
    
    // Accessor para precio por hora formateado
    public function getPrecioHoraAplicadoFormateadoAttribute()
    {
        return 'L. ' . number_format($this->precio_hora_aplicado, 2);
    }

   
    public function registrarPago($monto)
    {
        $this->decrement('saldo_pendiente', $monto);
        
        if ($this->precio_hora_aplicado > 0) {
            $cantidad = floor($monto / $this->precio_hora_aplicado);
            if ($cantidad > 0) {
                $this->increment('tutorias_pagadas', $cantidad);
            }
        }
        $this->save();
    }
 
    public function calcularSaldoPendiente()
    {
        $tutoriasNoPagadas = $this->tutorias_registradas - $this->tutorias_pagadas;
        return $tutoriasNoPagadas * $this->precio_hora_aplicado;
    }
    
    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class);
    }
    
    public function tutoria()
    {
        return $this->belongsTo(Tutoria::class);
    }
    
    public function pagos()
    {
        return $this->hasMany(PagoTutoria::class);
    }
    
    public function pagosCompletados()
    {
        return $this->hasMany(PagoTutoria::class)->where('estado', 'completado');
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
    public function registrarTutoria($fuePagada, $horas = 1)
    {
        $costoTotal = $this->precio_hora_aplicado * $horas;
        
        $this->tutorias_registradas += $horas; 

        if ($fuePagada) {
            $this->tutorias_pagadas += $horas;
        } else {
            $this->saldo_pendiente += $costoTotal;
        }

        $this->save();
    }
}