<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * 'confirmado' foi um status de rascunho que nunca teve uso definido no
 * fluxo real (nada no código escrevia esse valor, só existia como opção
 * manual no dropdown do admin) e foi removido de Order::STATUSES. Essa
 * migration só normaliza dado que já exista nesse estado -- produção tem
 * zero pedidos hoje, mas dev/staging podem ter linhas.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('orders')
            ->where('status', 'confirmado')
            ->update(['status' => 'pago']);
    }

    public function down(): void
    {
        // Não há como reverter: depois do up(), não dá mais pra distinguir
        // um pedido que já era 'pago' de um que era 'confirmado' e virou
        // 'pago' por esta migration. Fingir um rollback aqui reverteria
        // pedidos que nunca foram 'confirmado' -- pior que não reverter nada.
    }
};
