<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('matricula_id')->constrained('matriculas')->onDelete('cascade');
            $table->enum('tipo', ['mensualidad', 'matricula', 'pago_unico', 'adelanto', 'otros'])->default('mensualidad');
            $table->enum('metodo_pago', ['efectivo', 'tarjeta', 'transferencia', 'deposito', 'cheque'])->default('efectivo');
            $table->decimal('monto', 10, 2);
            $table->decimal('monto_pagado', 10, 2);
            $table->decimal('cambio', 10, 2)->default(0);
            $table->string('mes_pagado')->nullable();            
            $table->string('numero_transaccion')->nullable();
            $table->string('referencia_bancaria')->nullable();
            
            
            
            $table->enum('estado', ['pendiente', 'completado', 'anulado', 'reembolsado'])->default('completado');
            $table->date('fecha_pago');
               
            
            $table->text('observaciones')->nullable();
            
            $table->softDeletes();
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            
           
            $table->index('matricula_id');
            $table->index('tipo');
            $table->index('estado');
            $table->index('fecha_pago');
            $table->index('numero_transaccion');
          
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};