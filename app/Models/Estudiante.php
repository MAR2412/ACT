<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Estudiante extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'estudiantes';

    protected $fillable = [
        'nombre',
        'apellido',
        'dni',
        'sexo',
        'fecha_nacimiento',
        'telefono',
        'email',
        'direccion',
        'nombre_tutor',
        'telefono_tutor',
        'email_tutor',
        'estado',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    protected $casts = [
        'estado' => 'boolean',
        'fecha_nacimiento' => 'date'
    ];

    /**
     * Accesor para el nombre completo
     */
    public function getNombreCompletoAttribute(): string
    {
        return "{$this->apellido}, {$this->nombre}";
    }

    /**
     * Accesor para la edad calculada
     */
    public function getEdadAttribute(): ?int
    {
        if (!$this->fecha_nacimiento) {
            return null;
        }
        
        return now()->diffInYears($this->fecha_nacimiento);
    }
 // Relación con matrículas de módulos
    public function matriculas()
    {
        return $this->hasMany(Matricula::class);
    }

    // Relación con matrículas de tutorías
    public function matriculasTutorias()
    {
        return $this->hasMany(MatriculaTutoria::class);
    }

    // Matrículas activas de módulos
    public function matriculasActivas()
    {
        return $this->hasMany(Matricula::class)->where('estado', 'activa');
    }

    // Matrículas activas de tutorías
    public function tutoriasActivas()
    {
        return $this->hasMany(MatriculaTutoria::class)->where('estado', 'activa');
    }

    // Matrículas pendientes de pago (módulos)
    public function matriculasPendientes()
    {
        return $this->hasMany(Matricula::class)
            ->where('estado', 'activa')
            ->where('saldo_pendiente', '>', 0);
    }

    // Última matrícula (módulo)
    public function ultimaMatricula()
    {
        return $this->hasOne(Matricula::class)->latestOfMany();
    }

    // Ámbito para estudiantes activos
    public function scopeActivos($query)
    {
        return $query->where('estado', true);
    }

    // Ámbito para estudiantes inactivos
    public function scopeInactivos($query)
    {
        return $query->where('estado', false);
    }

    // Ámbito para búsqueda por DNI, nombre o apellido
    public function scopeBuscar($query, $termino)
    {
        return $query->where('dni', 'like', "%{$termino}%")
                    ->orWhere('nombre', 'like', "%{$termino}%")
                    ->orWhere('apellido', 'like', "%{$termino}%")
                    ->orWhere('email', 'like', "%{$termino}%");
    }

  
    public function setNombreAttribute($value)
    {
        $this->attributes['nombre'] = ucwords(strtolower($value));
    }

    // Mutador para apellido
    public function setApellidoAttribute($value)
    {
        $this->attributes['apellido'] = ucwords(strtolower($value));
    }

    // Mutador para email
    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = strtolower($value);
    }
    
    public function getSexoFormateadoAttribute(): string
    {
        return $this->sexo === 'M' ? 'Masculino' : 'Femenino';
    }

    /**
     * Accesor para la fecha de nacimiento formateada
     */
    public function getFechaNacimientoFormateadaAttribute(): ?string
    {
        if (!$this->fecha_nacimiento) {
            return null;
        }
        
        return $this->fecha_nacimiento->format('d/m/Y');
    }

    /**
     * Mutator para el DNI (sin guiones)
     */
    public function setDniAttribute($value)
    {
        $this->attributes['dni'] = preg_replace('/[^0-9]/', '', $value);
    }


    /**
     * Accesor para el estado formateado
     */
    public function getEstadoFormateadoAttribute(): string
    {
        return $this->estado ? 'Activo' : 'Inactivo';
    }

   
}