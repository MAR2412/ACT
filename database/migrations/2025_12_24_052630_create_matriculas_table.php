<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('matriculas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estudiante_id')->constrained('estudiantes')->onDelete('cascade');
            $table->foreignId('modulo_id')->constrained('modulos')->onDelete('restrict');
            
            $table->enum('estado', ['activa', 'completada', 'cancelada', 'pendiente'])->default('activa');
            $table->boolean('aprobado')->default(false);
            
            $table->decimal('precio_total_modulo', 10, 2)->default(0);
            $table->decimal('saldo_pendiente', 10, 2)->default(0);
            $table->integer('meses_pendientes')->default(0);
            $table->integer('meses_pagados')->default(0);
            
            $table->boolean('descuento_aplicado')->default(false);
            $table->decimal('porcentaje_descuento', 5, 2)->default(0);
            $table->decimal('monto_descuento', 10, 2)->default(0);
            $table->boolean('descuento_primer_mes')->default(true);
            
            $table->boolean('examen_suficiencia')->default(false);
            $table->date('examen_suficiencia_fecha')->nullable();
            $table->decimal('examen_suficiencia_nota', 5, 2)->nullable();
            
            $table->date('fecha_matricula');
            $table->date('fecha_ultimo_pago')->nullable();
            $table->date('fecha_proximo_pago')->nullable();
            
            $table->text('observaciones')->nullable();
            
            $table->foreignId('matricula_anterior_id')->nullable()->constrained('matriculas')->onDelete('set null');
            $table->foreignId('matricula_siguiente_id')->nullable()->constrained('matriculas')->onDelete('set null');
            
            $table->softDeletes();
            $table->timestamps();
            
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            
            $table->index('estudiante_id');
            $table->index('modulo_id');
            $table->index('estado');
            $table->index('aprobado');
            $table->index('saldo_pendiente');
            $table->index('fecha_proximo_pago');
            $table->index('examen_suficiencia');
            $table->index('descuento_aplicado');
            $table->index('descuento_primer_mes');
             $table->boolean('pago_camiseta')->default(false);
            $table->decimal('monto_camiseta', 10, 2)->default(0);
            $table->boolean('pago_gastos_graduacion')->default(false);
            $table->decimal('monto_gastos_graduacion', 10, 2)->default(0);
            $table->index(['estudiante_id', 'modulo_id']);
            $table->index(['estudiante_id', 'estado']);
            $table->index(['modulo_id', 'estado']);
            $table->index('created_by');
            $table->index('updated_by');
             $table->index('pago_camiseta');
            $table->index('pago_gastos_graduacion');
            $table->unique(['estudiante_id', 'modulo_id'], 'matriculas_unique_estudiante_modulo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matriculas');
    }
};