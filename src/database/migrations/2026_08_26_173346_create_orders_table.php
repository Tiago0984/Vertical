<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * user_id fica nullable pra manter o checkout como convidado (guest)
     * funcionando igual hoje, mesmo depois da autenticação existir — quem
     * estiver logado tem o pedido associado à conta, quem não estiver ainda
     * consegue comprar normalmente.
     *
     * endereco_entrega/endereco_faturamento guardam uma cópia (snapshot) do
     * endereço no momento da compra, não uma referência a addresses — assim
     * o pedido não muda se o cliente editar ou apagar o endereço salvo depois.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('numero_pedido')->unique();

            $table->string('nome');
            $table->string('sobrenome');
            $table->string('email');
            $table->string('telefone', 20);

            $table->decimal('subtotal', 8, 2);
            $table->decimal('frete', 8, 2);
            $table->decimal('total', 8, 2);
            $table->string('cupom')->nullable();

            $table->string('forma_pagamento'); // cartao | pix | boleto
            $table->string('status')->default('pendente_pagamento'); // pendente_pagamento | pago | cancelado

            $table->json('endereco_entrega');
            $table->json('endereco_faturamento')->nullable(); // null = mesmo da entrega

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
