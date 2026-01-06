<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pago extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 'pagos';

    protected $fillable = [
        'matricula_id',
        'tipo',
        'metodo_pago',
        'monto',
        'monto_pagado',
        'cambio',
        'mes_pagado',
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
        'fecha_pago' => 'date',
      
    ];

    /**
     * Relación con Matrícula
     */
    public function matricula(): BelongsTo
    {
        return $this->belongsTo(Matricula::class, 'matricula_id');
    }

    /**
     * Accesor para el estado formateado
     */
    public function getEstadoFormateadoAttribute(): string
    {
        $estados = [
            'pendiente' => 'Pendiente',
            'completado' => 'Completado',
            'anulado' => 'Anulado',
            'reembolsado' => 'Reembolsado'
        ];
        
        return $estados[$this->estado] ?? $this->estado;
    }

    /**
     * Accesor para el método de pago formateado
     */
    public function getMetodoPagoFormateadoAttribute(): string
    {
        $metodos = [
            'efectivo' => 'Efectivo',
            'tarjeta' => 'Tarjeta',
            'transferencia' => 'Transferencia',
            'deposito' => 'Depósito',
            'cheque' => 'Cheque'
        ];
        
        return $metodos[$this->metodo_pago] ?? $this->metodo_pago;
    }

    /**
     * Accesor para el tipo formateado
     */
    public function getTipoFormateadoAttribute(): string
    {
        $tipos = [
            'mensualidad' => 'Mensualidad',
            'matricula' => 'Matrícula',
            'pago_unico' => 'Pago Único',
            'adelanto' => 'Adelanto',
            'otros' => 'Otros'
        ];
        
        return $tipos[$this->tipo] ?? $this->tipo;
    }

    /**
     * Accesor para el monto formateado
     */
    public function getMontoFormateadoAttribute(): string
    {
        return 'L. ' . number_format($this->monto, 2);
    }

    /**
     * Accesor para el monto pagado formateado
     */
    public function getMontoPagadoFormateadoAttribute(): string
    {
        return 'L. ' . number_format($this->monto_pagado, 2);
    }
}