<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sedes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->text('descripcion')->nullable();
            $table->enum('departamento', [
                'Atlántida',
                'Colón',
                'Comayagua',
                'Copán',
                'Cortés',
                'Choluteca',
                'El Paraíso',
                'Francisco Morazán',
                'Gracias a Dios',
                'Intibucá',
                'Islas de la Bahía',
                'La Paz',
                'Lempira',
                'Ocotepeque',
                'Olancho',
                'Santa Bárbara',
                'Valle',
                'Yoro'
            ]);
            $table->string('municipio');
            $table->boolean('estado')->default(true);
            $table->softDeletes();
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            
            $table->index('departamento');
            $table->index('municipio');
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sedes');
    }
};