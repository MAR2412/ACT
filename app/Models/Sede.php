<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sede extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sedes';

    protected $fillable = [
        'nombre',
        'descripcion',
        'departamento',
        'municipio',
        'estado',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    protected $casts = [
        'estado' => 'boolean'
    ];
}