<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Modalidad extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 'modalidades';

    protected $fillable = [
        'nombre',
        'descripcion',
        'estado',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    protected $casts = [
        'estado' => 'boolean'
    ];
}