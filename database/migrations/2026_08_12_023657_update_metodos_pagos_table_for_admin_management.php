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
        Schema::table('metodos_pagos', function (Blueprint $table) {
            $table->string('nombre', 100)->after('id')->nullable();
            $table->text('instrucciones')->after('titular')->nullable();
            $table->boolean('estado')->after('instrucciones')->default(1);
            $table->unsignedBigInteger('cliente_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('metodos_pagos', function (Blueprint $table) {
            $table->dropColumn(['nombre', 'instrucciones', 'estado']);
        });
    }
};
