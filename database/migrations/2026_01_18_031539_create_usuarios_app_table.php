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
        Schema::create('usuarios_app', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 50)->unique();
            $table->string('cedula_cliente', 13);
            $table->string('contrasena', 255); // Usaremos bcrypt (hash)
            $table->boolean('activo')->default(true);
            $table->timestamps();

            // Foreign key a clientes
            $table->foreign('cedula_cliente')
                ->references('cedula')
                ->on('clientes')
                ->onDelete('cascade');

            // Indices para búsquedas rápidas
            $table->index('cedula_cliente');
            $table->index('nombre');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios_app');
    }
};
