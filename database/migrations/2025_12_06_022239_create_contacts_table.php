<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();

            // FK para users com ON DELETE CASCADE
            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade');

            $table->string('name');
            $table->string('cpf', 11); // apenas dígitos

            $table->string('phone', 20);

            // Endereço completo
            $table->string('cep', 9);
            $table->string('state', 2); // UF
            $table->string('city');
            $table->string('neighborhood');
            $table->string('street');
            $table->string('number', 20);
            $table->string('complement')->nullable(); // único campo opcional de endereço

            // Coordenadas geográficas
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            $table->timestamps();

            // CPF não pode repetir para o mesmo usuário
            $table->unique(['user_id', 'cpf']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
