<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Carrinho persistido no banco só pra usuário logado (sincroniza com o
     * localStorage do cart.js). Não guarda nome/preço — isso é sempre lido
     * ao vivo do produto, diferente de order_items, que é um snapshot
     * histórico do pedido já finalizado.
     */
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            // cor/tamanho usam string vazia (não null) como "sem variação", pra manter
            // o índice único funcionando de verdade — MySQL trata NULL como valor
            // distinto em toda linha, então múltiplos NULLs não seriam bloqueados
            $table->string('cor')->default('');
            $table->string('tamanho')->default('');
            $table->unsignedInteger('quantidade')->default(1);
            $table->timestamps();

            $table->unique(['user_id', 'product_id', 'cor', 'tamanho']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
