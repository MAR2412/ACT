<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('modulos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('codigo')->nullable(); // Ej: MOD-I, MOD-II, MOD-III
            $table->text('descripcion')->nullable();
            $table->integer('duracion_meses')->default(1);
            $table->decimal('precio_mensual', 10, 2)->default(0); // Cambié de 'precio' a 'precio_mensual'
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->enum('nivel', ['I', 'II', 'III', 'IV', 'V', 'VI'])->default('I');
            $table->integer('orden')->default(1); // Para secuencia 1, 2, 3...
            $table->foreignId('modulo_requerido_id')->nullable()->constrained('modulos')->onDelete('restrict');
            $table->boolean('es_ultimo_modulo')->default(false);
            $table->boolean('estado')->default(true);
            
            // Claves foráneas
            $table->foreignId('sede_id')->constrained('sedes')->onDelete('restrict');
            $table->foreignId('modalidad_id')->constrained('modalidades')->onDelete('restrict');
            $table->foreignId('seccion_id')->constrained('secciones')->onDelete('restrict');
            
            $table->softDeletes();
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            
            // Índices
            $table->index('nombre');
            $table->index('codigo');
            $table->index('nivel');
            $table->index('orden');
            $table->index('estado');
            $table->index('sede_id');
            $table->index('modalidad_id');
            $table->index('seccion_id');
            $table->index('modulo_requerido_id');
            $table->unique(['nombre', 'sede_id', 'modalidad_id', 'seccion_id'], 'modulos_unique_composite');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modulos');
    }
};