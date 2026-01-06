<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tutoria extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 'tutorias';

    protected $fillable = [
        'nombre',
        'descripcion',
        'materia',
        'precio_hora',
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
        'duracion_horas' => 'integer',
        'precio_hora' => 'decimal:2'
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
     * Relación con MatriculasTutoria
     */
    public function matriculas(): HasMany
    {
        return $this->hasMany(MatriculaTutoria::class, 'tutoria_id');
    }

    /**
     * Accesor para el nombre completo de la tutoría
     */
    public function getNombreCompletoAttribute(): string
    {
        return "{$this->nombre}" . ($this->materia ? " - {$this->materia}" : "");
    }

    /**
     * Accesor para el precio por hora formateado
     */
    public function getPrecioHoraFormateadoAttribute(): string
    {
        return 'L. ' . number_format($this->precio_hora, 2, '.', ',');
    }

   
    public function getDuracionFormateadaAttribute(): string
    {
        return $this->duracion_horas . ' ' . ($this->duracion_horas === 1 ? 'hora' : 'horas');
    }
}

    
  