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
        Schema::create('metodos_pagos', function (Blueprint $table) {
            $table->id();
            $table->string('tipo', 50)->nullable();
            $table->string('numero', 100)->nullable();
            $table->string('titular', 100)->nullable();
            $table->timestamps();

            $table->foreignId('cliente_id')->constrained('clientes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('metodos_pagos');
    }
};
