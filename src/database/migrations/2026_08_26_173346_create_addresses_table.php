<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Endereços salvos do cliente (área do cliente). O pedido em si guarda
     * uma cópia do endereço usado na hora da compra (orders.endereco_entrega/
     * endereco_faturamento), então editar ou apagar um endereço salvo aqui
     * não altera pedidos já feitos.
     */
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('nome');
            $table->string('sobrenome');
            $table->string('cep', 9);
            $table->string('endereco');
            $table->string('numero');
            $table->string('complemento')->nullable();
            $table->string('bairro');
            $table->string('cidade');
            $table->string('estado', 2);
            $table->string('telefone', 20)->nullable();
            $table->string('referencia')->nullable();
            $table->boolean('is_padrao')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
