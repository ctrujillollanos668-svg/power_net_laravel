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
        Schema::create('envios', function (Blueprint $table) {
            $table->id();
            $table->string('empresa_envios', 100);
            $table->string('estado', 50);
            $table->decimal('costo', 10, 2);
            $table->dateTime('fecha_hora');
            $table->string('direccion_envio', 255);
            $table->timestamps();

            $table->foreignId('pedido_id')->constrained('pedidos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('envios');
    }
};
