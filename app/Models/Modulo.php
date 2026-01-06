<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Modulo extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 'modulos';

    protected $fillable = [
        'nombre',
        'codigo',
        'descripcion',
        'duracion_meses',
        'precio_mensual',
        'fecha_inicio',
        'fecha_fin',
        'nivel',
        'orden',
        'modulo_requerido_id',
        'es_ultimo_modulo',
        'estado',
        'sede_id',
        'modalidad_id',
        'seccion_id',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    protected $casts = [
        'estado' => 'boolean',
        'es_ultimo_modulo' => 'boolean',
        'duracion_meses' => 'integer',
        'precio_mensual' => 'decimal:2',
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date'
    ];

    /**
     * Relación con Sede
     */
    public function sede(): BelongsTo
    {
        return $this->belongsTo(Sede::class, 'sede_id');
    }

    /**
     * Relación con Modalidad
     */
    public function modalidad(): BelongsTo
    {
        return $this->belongsTo(Modalidad::class, 'modalidad_id');
    }

    /**
     * Relación con Sección
     */
    public function seccion(): BelongsTo
    {
        return $this->belongsTo(Seccion::class, 'seccion_id');
    }

    /**
     * Relación con módulo requerido (anterior)
     */
    public function moduloRequerido(): BelongsTo
    {
        return $this->belongsTo(Modulo::class, 'modulo_requerido_id');
    }

    /**
     * Relación con módulos que requieren este módulo (siguientes)
     */
    public function modulosSiguientes(): HasMany
    {
        return $this->hasMany(Modulo::class, 'modulo_requerido_id');
    }

    /**
     * Relación con matrículas
     */
    public function matriculas(): HasMany
    {
        return $this->hasMany(Matricula::class, 'modulo_id');
    }

    /**
     * Accesor para el siguiente módulo
     */
    public function getSiguienteModuloAttribute(): ?Modulo
    {
        return $this->modulosSiguientes()->first();
    }

    /**
     * Accesor para saber si es el primer módulo
     */
    public function getEsPrimerModuloAttribute(): bool
    {
        return $this->moduloRequerido === null;
    }

    /**
     * Accesor para el precio total del módulo
     */
    public function getPrecioTotalAttribute(): float
    {
        return $this->precio_mensual * $this->duracion_meses;
    }

    /**
     * Accesor para el precio total formateado
     */
    public function getPrecioTotalFormateadoAttribute(): string
    {
        return 'L. ' . number_format($this->precio_total, 2);
    }

    /**
     * Accesor para el precio mensual formateado
     */
    public function getPrecioMensualFormateadoAttribute(): string
    {
        return 'L. ' . number_format($this->precio_mensual, 2);
    }

    /**
     * Método para configurar la secuencia
     */
    public static function configurarSecuencia(array $modulosIds): void
    {
        $orden = 1;
        $anteriorId = null;
        
        foreach ($modulosIds as $moduloId) {
            $modulo = self::find($moduloId);
            
            if ($modulo) {
                $modulo->update([
                    'orden' => $orden,
                    'nivel' => $this->convertirNumeroARomano($orden),
                    'modulo_requerido_id' => $anteriorId,
                    'es_ultimo_modulo' => ($orden === count($modulosIds))
                ]);
                
                $anteriorId = $modulo->id;
                $orden++;
            }
        }
    }

    /**
     * Convertir número a romano
     */
    private function convertirNumeroARomano($num): string
    {
        $romanos = [
            1 => 'I',
            2 => 'II', 
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X'
        ];
        
        return $romanos[$num] ?? (string)$num;
    }
}