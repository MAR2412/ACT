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
        Schema::create('pago_tutorias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('matricula_tutoria_id')->constrained('matricula_tutorias')->onDelete('cascade');
            $table->enum('tipo', ['pago_unico', 'adelanto', 'mensual', 'otros'])->default('pago_unico');
            $table->enum('metodo_pago', ['efectivo', 'tarjeta', 'transferencia', 'deposito', 'cheque'])->default('efectivo');
            $table->decimal('monto', 10, 2);
            $table->decimal('monto_pagado', 10, 2);
            $table->decimal('cambio', 10, 2)->default(0);
            $table->string('numero_transaccion')->nullable();
            $table->string('referencia_bancaria')->nullable();
            $table->enum('estado', ['pendiente', 'completado', 'anulado', 'reembolsado'])->default('pendiente');
            $table->date('fecha_pago');
            $table->text('observaciones')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
          
            $table->index('matricula_tutoria_id');
            $table->index('tipo');
            $table->index('metodo_pago');
            $table->index('numero_transaccion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pago_tutorias');
    }
};
