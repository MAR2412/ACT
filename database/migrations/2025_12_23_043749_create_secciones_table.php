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
        Schema::create('secciones', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->time('HoraInicio');
            $table->time('HoraFin');
            $table->enum('dia', [
                'Lunes', 
                'Martes', 
                'Miércoles', 
                'Jueves', 
                'Viernes', 
                'Sábado', 
                'Domingo'
            ]);
            $table->enum('diaF', [
                'Lunes', 
                'Martes', 
                'Miércoles', 
                'Jueves', 
                'Viernes', 
                'Sábado', 
                'Domingo'
            ])->nullable();
            $table->boolean( 'estado')->default(1);
            $table->softDeletes();
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('secciones');
    }
};
