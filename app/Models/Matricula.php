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
        'monto_gastos_graduacion_formateado',
        'monto_mensual_con_descuento'
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
        return $this->hasMany(Pago::class, 'matricula_id')->orderBy('fecha_pago', 'desc');
    }

    public function pagosMensualidad(): HasMany
    {
        return $this->hasMany(Pago::class, 'matricula_id')
            ->where('tipo', 'mensualidad')
            ->orderBy('mes_pagado', 'desc');
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
        return $this->pagosMensualidad->where('estado', 'completado')->sum('monto');
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
        return $this->descuento_aplicado && $this->porcentaje_descuento > 0;
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
        if (!$this->modulo) {
            return $this->precio_total_modulo;
        }

        $precioMensual = $this->modulo->precio_mensual;
        $duracion = $this->modulo->duracion_meses;
        
        return $precioMensual * $duracion;
    }

    public function getMontoMensualSinDescuentoAttribute(): float
    {
        if (!$this->modulo) {
            return 0;
        }
        return $this->modulo->precio_mensual;
    }

    public function getMontoPrimerMesConDescuentoAttribute(): float
    {
        if (!$this->modulo) {
            return 0;
        }
        
        if ($this->descuento_aplicado && $this->descuento_primer_mes && $this->porcentaje_descuento > 0) {
            $precioMensual = $this->modulo->precio_mensual;
            return round($precioMensual * (1 - $this->porcentaje_descuento / 100), 2);
        }
        
        return $this->modulo->precio_mensual;
    }

    public function getMontoMensualConDescuentoAttribute(): float
    {
        if (!$this->modulo) {
            return 0;
        }
        
        if ($this->descuento_aplicado && !$this->descuento_primer_mes && $this->porcentaje_descuento > 0) {
            $precioMensual = $this->modulo->precio_mensual;
            return round($precioMensual * (1 - $this->porcentaje_descuento / 100), 2);
        }
        
        return $this->modulo->precio_mensual;
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

        $this->recalcularSaldo();
        
        $this->update([
            'meses_pagados' => $this->pagosMensualidad->count(),
            'meses_pendientes' => max(0, $this->modulo->duracion_meses - $this->pagosMensualidad->count()),
            'fecha_ultimo_pago' => now(),
            'fecha_proximo_pago' => now()->addMonth(),
        ]);

        return $pago;
    }

    public function recalcularSaldo(): void
    {
        $totalPagado = $this->pagosMensualidad->where('estado', 'completado')->sum('monto');
        $nuevoSaldo = round($this->precio_total_modulo - $totalPagado, 2);
        
        $this->update(['saldo_pendiente' => max(0, $nuevoSaldo)]);
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
            'precio_total_modulo' => $siguienteModulo->precio_mensual * $siguienteModulo->duracion_meses,
            'saldo_pendiente' => $siguienteModulo->precio_mensual * $siguienteModulo->duracion_meses,
            'meses_pendientes' => $siguienteModulo->duracion_meses,
            'meses_pagados' => 0,
            'fecha_matricula' => now(),
            'matricula_anterior_id' => $this->id,
            'created_by' => auth()->id(),
        ]);

        $this->update(['matricula_siguiente_id' => $matricula->id]);

        return $matricula;
    }

    public function aplicarDescuento($porcentaje, $observaciones = null, $soloPrimerMes = true): void
    {
        if (!$this->modulo) {
            return;
        }
        
        $precioMensual = $this->modulo->precio_mensual;
        $duracion = $this->modulo->duracion_meses;
        
        if ($soloPrimerMes) {
            $montoDescuento = $precioMensual * ($porcentaje / 100);
            $primerMesConDescuento = $precioMensual - $montoDescuento;
            $nuevoPrecioTotal = $primerMesConDescuento + ($precioMensual * ($duracion - 1));
            
            $this->update([
                'porcentaje_descuento' => $porcentaje,
                'monto_descuento' => round($montoDescuento, 2),
                'precio_total_modulo' => round($nuevoPrecioTotal, 2),
                'saldo_pendiente' => round($nuevoPrecioTotal - $this->total_pagado, 2),
                'descuento_aplicado' => true,
                'descuento_primer_mes' => true,
                'observaciones' => $observaciones ? ($this->observaciones . ' | ' . $observaciones) : $this->observaciones,
            ]);
        } else {
            $precioTotal = $precioMensual * $duracion;
            $montoDescuento = $precioTotal * ($porcentaje / 100);
            $nuevoPrecioTotal = $precioTotal - $montoDescuento;

            $this->update([
                'porcentaje_descuento' => $porcentaje,
                'monto_descuento' => round($montoDescuento, 2),
                'precio_total_modulo' => round($nuevoPrecioTotal, 2),
                'saldo_pendiente' => round($nuevoPrecioTotal - $this->total_pagado, 2),
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

    public function cancelarPagoCamiseta(): void
    {
        $this->update([
            'pago_camiseta' => false,
            'monto_camiseta' => 0,
        ]);
    }

    public function cancelarPagoGastosGraduacion(): void
    {
        $this->update([
            'pago_gastos_graduacion' => false,
            'monto_gastos_graduacion' => 0,
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
        return $query->where('descuento_aplicado', true);
    }
}