<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       Schema::create('matricula_tutorias', function (Blueprint $table) {
        $table->id();
        $table->foreignId('estudiante_id')->constrained('estudiantes')->onDelete('cascade');
        $table->foreignId('tutoria_id')->constrained('tutorias')->onDelete('restrict');
        $table->enum('estado', ['activa', 'completada', 'cancelada', 'pendiente'])->default('activa');
        
        // Información financiera personalizada
        $table->decimal('precio_hora_aplicado', 10, 2)->default(0); // Precio por hora para este estudiante
        $table->integer('tutorias_registradas')->default(0); // Contador de tutorías registradas
        $table->integer('tutorias_pagadas')->default(0); // Contador de tutorías pagadas
        $table->decimal('saldo_pendiente', 10, 2)->default(0); // Saldo acumulado
        
        
        $table->date('fecha_inicio');
        $table->boolean('aprobado')->default(false);
        $table->text('observaciones')->nullable();
        
        $table->softDeletes();
        $table->timestamps();
        $table->unsignedBigInteger('created_by')->nullable();
        $table->unsignedBigInteger('updated_by')->nullable();
        $table->unsignedBigInteger('deleted_by')->nullable();
        
        // Índices
        $table->index('estudiante_id');
        $table->index('tutoria_id');
        $table->index('estado');
        $table->index('fecha_inicio');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matricula_tutorias');
    }
};
