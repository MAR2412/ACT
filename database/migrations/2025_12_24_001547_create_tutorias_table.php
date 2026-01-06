<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tutorias', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->string('materia')->nullable();
            $table->decimal('precio_hora', 10, 2)->default(0);
            $table->boolean('estado')->default(true);
            
            
            $table->foreignId('sede_id')->constrained('sedes')->onDelete('restrict');
            $table->foreignId('modalidad_id')->constrained('modalidades')->onDelete('restrict');
            $table->foreignId('seccion_id')->constrained('secciones')->onDelete('restrict');
            
            $table->softDeletes();
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            
          
            $table->index('nombre');
            $table->index('materia');
            $table->index('estado');
            $table->index('sede_id');
            $table->index('modalidad_id');
            $table->index('seccion_id');
            $table->unique(['nombre', 'sede_id', 'modalidad_id', 'seccion_id'], 'tutorias_unique_composite');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tutorias');
    }
};