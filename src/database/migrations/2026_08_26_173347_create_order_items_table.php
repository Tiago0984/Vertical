<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * nome/preco ficam duplicados do produto (snapshot) de propósito: se o
     * preço do produto mudar depois, o histórico do pedido continua mostrando
     * o valor pago na época.
     *
     * cor é texto livre só por retrocompatibilidade: pedidos antigos vinham
     * de um seletor de cor solto do produto (cor era "variação"). Isso mudou
     * -- cor agora é atributo do PRODUTO (cada cor é um item próprio do
     * catálogo, ex. "Camiseta Gola V Azul" e "Camiseta Gola V Preta" são dois
     * produtos), não algo que se escolhe no checkout. Pedido novo grava cor
     * sempre null; a coluna continua nullable só pra pedido antigo exibir o
     * que foi vendido de fato.
     *
     * tamanho continua sendo escolha real do cliente, texto livre porque os
     * mesmos 6 tamanhos aparecem em todo produto e o site não gerencia
     * estoque por variação: quem baixa é o estoque TOTAL do produto, não por
     * tamanho -- dá pra vender um G com o produto anunciando estoque só
     * porque ainda sobra M, P etc. Limitação conhecida, não corrigida aqui.
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
