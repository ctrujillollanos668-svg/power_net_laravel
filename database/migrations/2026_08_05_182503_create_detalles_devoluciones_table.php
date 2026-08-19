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
        Schema::create('detalles_devoluciones', function (Blueprint $table) {
            $table->id();
            $table->integer('cantidad')->nullable();
            $table->text('motivo')->nullable();
            $table->timestamps();

            $table->foreignId('devolucione_id')->constrained('devoluciones');
            $table->foreignId('producto_id')->constrained('productos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalles_devoluciones');
    }
};
