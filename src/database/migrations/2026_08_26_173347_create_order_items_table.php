<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * nome/preco ficam duplicados do produto (snapshot) de propósito: se o
     * preço do produto mudar depois, o histórico do pedido continua mostrando
     * o valor pago na época. cor/tamanho ficam como texto livre porque o site
     * ainda não gerencia estoque por variação (cor/tamanho são só informativos
     * hoje, os mesmos 6 tamanhos aparecem em todo produto).
     */
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();

            $table->string('nome');
            $table->decimal('preco', 8, 2);
            $table->string('cor')->nullable();
            $table->string('tamanho')->nullable();
            $table->unsignedInteger('quantidade');
            $table->decimal('subtotal', 8, 2);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
