<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estudiantes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('apellido');
            $table->string('dni')->unique();
            $table->enum('sexo', ['M', 'F']);
            $table->date('fecha_nacimiento')->nullable();
            $table->string('telefono')->nullable();
            $table->string('email')->nullable();
            $table->text('direccion')->nullable();
            $table->string('nombre_tutor')->nullable();
            $table->string('telefono_tutor')->nullable();
            $table->string('email_tutor')->nullable();
            $table->boolean('estado')->default(true);
            
            $table->softDeletes();
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            
            // Índices
            $table->index('dni');
            $table->index('apellido');
            $table->index('nombre');
            $table->index(['apellido', 'nombre']);
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estudiantes');
    }
};