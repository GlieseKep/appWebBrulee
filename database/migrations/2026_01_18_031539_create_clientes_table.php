<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->string('cedula', 13)->primary();
            $table->string('correo', 320)->unique();
            $table->string('telefono', 14)->nullable();
            $table->timestamps();

            // Index para búsquedas rápidas por correo
            $table->index('correo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
