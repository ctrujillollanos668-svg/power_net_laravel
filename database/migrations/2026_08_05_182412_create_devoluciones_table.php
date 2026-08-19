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
        Schema::create('devoluciones', function (Blueprint $table) {
            $table->id();
            $table->dateTime('fecha_devolucion');
            $table->decimal('monto_devolucion', 10, 2);
            $table->text('motivo');
            $table->string('estado');
            $table->text('motivo_rechazo');
            $table->timestamps();

            $table->foreignId('pedido_id')->constrained('pedidos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devoluciones');
    }
};
