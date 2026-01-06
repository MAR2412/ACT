<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Matricula extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 'matriculas';

    protected $fillable = [
        'estudiante_id',
        'modulo_id',
        'estado',
        'precio_total_modulo',
        'saldo_pendiente',
        'meses_pendientes',
        'meses_pagados',
        'fecha_matricula',
        'fecha_ultimo_pago',
        'fecha_proximo_pago',
        'aprobado',
        'observaciones',
        'matricula_anterior_id',
        'matricula_siguiente_id',
        'examen_suficiencia',
        'examen_suficiencia_fecha',
        'examen_suficiencia_nota',
        'descuento_aplicado',
        'porcentaje_descuento',
        'monto_descuento',
        'descuento_primer_mes',
        'pago_camiseta',
        'monto_camiseta',
        'pago_gastos_graduacion',
        'monto_gastos_graduacion',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    protected $casts = [
        'aprobado' => 'boolean',
        'examen_suficiencia' => 'boolean',
        'descuento_aplicado' => 'boolean',
        'descuento_primer_mes' => 'boolean',
        'pago_camiseta' => 'boolean',
        'pago_gastos_graduacion' => 'boolean',
        'fecha_matricula' => 'date',
        'fecha_ultimo_pago' => 'date',
        'fecha_proximo_pago' => 'date',
        'examen_suficiencia_fecha' => 'date',
        'precio_total_modulo' => 'decimal:2',
        'saldo_pendiente' => 'decimal:2',
        'examen_suficiencia_nota' => 'decimal:2',
        'porcentaje_descuento' => 'decimal:2',
        'monto_descuento' => 'decimal:2',
        'monto_camiseta' => 'decimal:2',
        'monto_gastos_graduacion' => 'decimal:2',
        'meses_pendientes' => 'integer',
        'meses_pagados' => 'integer'
    ];

    protected $appends = [
        'estado_formateado',
        'puede_avanzar',
        'total_pagado',
        'total_pagado_formateado',
        'saldo_pendiente_formateado',
        'con_descuento',
        'monto_primer_mes_con_descuento',
        'monto_mensual_sin_descuento',
        'precio_original',
        'monto_descuento_formateado',
        'monto_camiseta_formateado',
        'monto_gastos_graduacion_formateado'
    ];

    public function estudiante(): BelongsTo
    {
        return $this->belongsTo(Estudiante::class, 'estudiante_id');
    }

    public function modulo(): BelongsTo
    {
        return $this->belongsTo(Modulo::class, 'modulo_id');
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class, 'matricula_id');
    }

    public function anterior(): BelongsTo
    {
        return $this->belongsTo(Matricula::class, 'matricula_anterior_id');
    }

    public function siguiente(): HasOne
    {
        return $this->hasOne(Matricula::class, 'matricula_anterior_id');
    }

    public function getEstadoFormateadoAttribute(): string
    {
        $estados = [
            'activa' => 'Activa',
            'completada' => 'Completada',
            'cancelada' => 'Cancelada',
            'pendiente' => 'Pendiente'
        ];
        
        return $estados[$this->estado] ?? $this->estado;
    }

    public function getPuedeAvanzarAttribute(): bool
    {
        return $this->aprobado && 
               $this->estado === 'completada' &&
               $this->modulo->siguienteModulo !== null &&
               $this->siguiente === null;
    }

    public function getTotalPagadoAttribute(): float
    {
        return $this->precio_total_modulo - $this->saldo_pendiente;
    }

    public function getTotalPagadoFormateadoAttribute(): string
    {
        return 'L. ' . number_format($this->total_pagado, 2);
    }

    public function getSaldoPendienteFormateadoAttribute(): string
    {
        return 'L. ' . number_format($this->saldo_pendiente, 2);
    }

    public function getConDescuentoAttribute(): bool
    {
        return $this->porcentaje_descuento > 0;
    }

    public function getMontoDescuentoFormateadoAttribute(): string
    {
        return 'L. ' . number_format($this->monto_descuento, 2);
    }

    public function getMontoCamisetaFormateadoAttribute(): string
    {
        return 'L. ' . number_format($this->monto_camiseta, 2);
    }

    public function getMontoGastosGraduacionFormateadoAttribute(): string
    {
        return 'L. ' . number_format($this->monto_gastos_graduacion, 2);
    }

    public function getPrecioOriginalAttribute(): float
    {
        if ($this->descuento_aplicado && !$this->descuento_primer_mes) {
            return $this->precio_total_modulo + $this->monto_descuento;
        }
        
        if ($this->descuento_aplicado && $this->descuento_primer_mes) {
            $precioMensual = $this->modulo->precio_mensual;
            $mesesTotales = $this->modulo->duracion_meses;
            return $precioMensual * $mesesTotales;
        }
        
        return $this->precio_total_modulo;
    }

    public function registrarPago(array $datos): Pago
    {
        $monto = $datos['monto'] ?? $this->modulo->precio_mensual;
        
        $pago = Pago::create([
            'matricula_id' => $this->id,
            'tipo' => $datos['tipo'] ?? 'mensualidad',
            'metodo_pago' => $datos['metodo_pago'] ?? 'efectivo',
            'monto' => $monto,
            'monto_pagado' => $datos['monto_pagado'] ?? $monto,
            'cambio' => $datos['cambio'] ?? 0,
            'mes_pagado' => $datos['mes_pagado'] ?? null,
            'numero_transaccion' => $datos['numero_transaccion'] ?? null,
            'referencia_bancaria' => $datos['referencia_bancaria'] ?? null,
            'estado' => 'completado',
            'fecha_pago' => $datos['fecha_pago'] ?? now(),
            'observaciones' => $datos['observaciones'] ?? null,
            'created_by' => auth()->id(),
        ]);

        $this->update([
            'saldo_pendiente' => max(0, $this->saldo_pendiente - $monto),
            'meses_pagados' => $this->meses_pagados + 1,
            'meses_pendientes' => max(0, $this->meses_pendientes - 1),
            'fecha_ultimo_pago' => now(),
            'fecha_proximo_pago' => now()->addMonth(),
        ]);

        return $pago;
    }

    public function scopeSearch($query, $search)
    {
        if (!$search) {
            return $query;
        }

        return $query->where(function($q) use ($search) {
            $q->whereHas('estudiante', function($q2) use ($search) {
                $q2->where('nombre', 'like', "%{$search}%")
                    ->orWhere('apellido', 'like', "%{$search}%")
                    ->orWhere('dni', 'like', "%{$search}%");
            })
            ->orWhereHas('modulo', function($q2) use ($search) {
                $q2->where('nombre', 'like', "%{$search}%")
                    ->orWhere('codigo', 'like', "%{$search}%")
                    ->orWhere('nivel', 'like', "%{$search}%");
            })
            ->orWhere('estado', 'like', "%{$search}%")
            ->orWhere('id', 'like', "%{$search}%");
        });
    }

    public function marcarMesNoPagado(): void
    {
        $this->update([
            'saldo_pendiente' => $this->saldo_pendiente + $this->modulo->precio_mensual,
            'meses_pendientes' => $this->meses_pendientes + 1,
            'fecha_proximo_pago' => now()->addMonth(),
        ]);
    }

    public function completar(array $datos = []): bool
    {
        $this->update([
            'estado' => 'completada',
            'aprobado' => $datos['aprobado'] ?? false,
            'observaciones' => $datos['observaciones'] ?? null,
        ]);

        if ($this->aprobado && $this->modulo->siguienteModulo) {
            $this->crearMatriculaSiguiente();
        }

        return true;
    }

    private function crearMatriculaSiguiente(): Matricula
    {
        $siguienteModulo = $this->modulo->siguienteModulo;
        
        $matricula = Matricula::create([
            'estudiante_id' => $this->estudiante_id,
            'modulo_id' => $siguienteModulo->id,
            'estado' => 'pendiente',
            'precio_total_modulo' => $siguienteModulo->precio_total,
            'saldo_pendiente' => $siguienteModulo->precio_total,
            'meses_pendientes' => $siguienteModulo->duracion_meses,
            'fecha_matricula' => now(),
            'matricula_anterior_id' => $this->id,
            'created_by' => auth()->id(),
        ]);

        $this->update(['matricula_siguiente_id' => $matricula->id]);

        return $matricula;
    }

    public function aplicarDescuento($porcentaje, $observaciones = null, $soloPrimerMes = true): void
    {
        $precioMensual = $this->modulo->precio_mensual;
        
        if ($soloPrimerMes) {
            $montoDescuento = $precioMensual * ($porcentaje / 100);
            
            $this->update([
                'porcentaje_descuento' => $porcentaje,
                'monto_descuento' => $montoDescuento,
                'descuento_aplicado' => true,
                'descuento_primer_mes' => true,
                'observaciones' => $observaciones ? ($this->observaciones . ' | ' . $observaciones) : $this->observaciones,
            ]);
        } else {
            $precioTotal = $this->modulo->precio_total;
            $montoDescuento = $precioTotal * ($porcentaje / 100);
            $nuevoPrecio = $precioTotal - $montoDescuento;

            $this->update([
                'porcentaje_descuento' => $porcentaje,
                'monto_descuento' => $montoDescuento,
                'precio_total_modulo' => $nuevoPrecio,
                'saldo_pendiente' => $nuevoPrecio,
                'descuento_aplicado' => true,
                'descuento_primer_mes' => false,
                'observaciones' => $observaciones ? ($this->observaciones . ' | ' . $observaciones) : $this->observaciones,
            ]);
        }
    }

    public function registrarExamenSuficiencia($nota, $fecha = null): void
    {
        $this->update([
            'examen_suficiencia' => true,
            'examen_suficiencia_nota' => $nota,
            'examen_suficiencia_fecha' => $fecha ?? now(),
        ]);
    }

    public function registrarPagoCamiseta($monto): void
    {
        $this->update([
            'pago_camiseta' => true,
            'monto_camiseta' => $monto,
        ]);
    }

    public function registrarPagoGastosGraduacion($monto): void
    {
        $this->update([
            'pago_gastos_graduacion' => true,
            'monto_gastos_graduacion' => $monto,
        ]);
    }

    public function scopeActivas($query)
    {
        return $query->where('estado', 'activa');
    }

    public function scopeConDeuda($query)
    {
        return $query->where('saldo_pendiente', '>', 0);
    }

    public function scopeProximasAVencer($query, $dias = 7)
    {
        return $query->whereDate('fecha_proximo_pago', '<=', now()->addDays($dias))
                     ->where('saldo_pendiente', '>', 0);
    }

    public function scopeConDescuento($query)
    {
        return $query->where('descuento_aplicado', true)
                     ->orWhere('porcentaje_descuento', '>', 0);
    }

    public function getMontoMensualSinDescuentoAttribute()
    {
        if ($this->modulo) {
            return $this->modulo->precio_mensual;
        }
        return 0;
    }

    public function getMontoPrimerMesConDescuentoAttribute()
    {
        if ($this->descuento_aplicado && $this->descuento_primer_mes) {
            $precioMensual = $this->modulo->precio_mensual;
            return $precioMensual - ($precioMensual * ($this->porcentaje_descuento / 100));
        }
        return $this->modulo ? $this->modulo->precio_mensual : 0;
    }

    public function getMontoMensualConDescuentoAttribute()
    {
        if ($this->descuento_aplicado && !$this->descuento_primer_mes) {
            return $this->precio_total_modulo / $this->modulo->duracion_meses;
        }
        return $this->modulo ? $this->modulo->precio_mensual : 0;
    }
}