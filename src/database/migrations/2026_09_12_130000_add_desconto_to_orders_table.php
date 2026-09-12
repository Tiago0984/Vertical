<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Fundação pra desconto de verdade (cupom, fase 2): valor do desconto em
 * reais, congelado no pedido -- nunca recalculado a partir do código do
 * cupom depois (o cupom pode ser editado/expirado, o pedido antigo tem
 * que continuar batendo). NOT NULL default 0: zero aqui é "sem desconto"
 * de verdade, diferente de products.custo (onde 0 seria uma afirmação
 * falsa sobre o custo).
 *
 * Novo invariante: subtotal - desconto + frete = total.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('desconto', 8, 2)->default(0)->after('subtotal');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('desconto');
        });
    }
};
