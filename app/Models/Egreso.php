<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Egreso extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'monto_utilizado',
        'fecha_egreso',
        'descripcion',
        'estado',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'monto_utilizado' => 'decimal:2',
        'fecha_egreso' => 'date',
        'estado' => 'boolean',
    ];

    /**
     * Get the user who created the egreso.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the egreso.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the user who deleted the egreso.
     */
    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /**
     * Scope a query to only include active egresos.
     */
    public function scopeActivo($query)
    {
        return $query->where('estado', true);
    }

    /**
     * Scope a query to only include inactive egresos.
     */
    public function scopeInactivo($query)
    {
        return $query->where('estado', false);
    }

    /**
     * Scope a query by date range.
     */
    public function scopePorRangoFecha($query, $inicio, $fin)
    {
        return $query->whereBetween('fecha_egreso', [$inicio, $fin]);
    }
}