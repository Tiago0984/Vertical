<?php

namespace App\Support;

use App\Services\DashboardService;

/**
 * Mesmo padrão do OrderStatus: rótulo e cor do status de estoque
 * centralizados aqui, não hardcoded na view.
 */
class EstoqueStatus
{
    /**
     * Regra única de "qual status esse produto tem", usada tanto pelo
     * DashboardService::estoque() quanto pela listagem de Produtos do admin
     * -- centralizada aqui pra não correr o risco das duas telas divergirem
     * um dia por alguém mexer só numa delas.
     */
    public static function calcular(int $estoque, int $estoqueMinimo): string
    {
        $multiplicadorAtencao = (float) config('dashboard.estoque.multiplicador_atencao');

        // Checado ANTES da regra do mínimo: estoque zero é estoque zero,
        // vendendo ou não o mínimo estar configurado. "Não configurado"
        // existe pra não alarmar em massa no dia do deploy (estoque_minimo
        // default 0), mas isso não pode engolir um produto que já esgotou.
        if ($estoque === 0) {
            return DashboardService::ESTOQUE_ESGOTADO;
        }

        if ($estoqueMinimo === 0) {
            return DashboardService::ESTOQUE_NAO_CONFIGURADO;
        }

        if ($estoque <= $estoqueMinimo) {
            return DashboardService::ESTOQUE_REPOR;
        }

        if ($estoque <= $estoqueMinimo * $multiplicadorAtencao) {
            return DashboardService::ESTOQUE_ATENCAO;
        }

        return DashboardService::ESTOQUE_OK;
    }

    public static function label(string $status): string
    {
        return match ($status) {
            DashboardService::ESTOQUE_ESGOTADO => 'Esgotado',
            DashboardService::ESTOQUE_REPOR => 'Repor',
            DashboardService::ESTOQUE_ATENCAO => 'Atenção',
            DashboardService::ESTOQUE_OK => 'OK',
            DashboardService::ESTOQUE_NAO_CONFIGURADO => 'Não configurado',
            default => ucfirst($status),
        };
    }

    /**
     * Cor reservada pra quem exige ação (Esgotado/Repor = vermelho, Atenção
     * = âmbar). OK e Não configurado ficam neutros de propósito -- "todo o
     * resto, inclusive os números bons" (decisão da fase 8D), pra cor
     * continuar sendo um sinal escasso. Esgotado é o alerta mais forte do
     * painel (venda perdida agora, não "abaixo do mínimo"), mas usa a mesma
     * cor de Repor -- não existe um "mais vermelho que vermelho" no Bootstrap.
     */
    public static function badgeClass(string $status): string
    {
        return match ($status) {
            DashboardService::ESTOQUE_ESGOTADO, DashboardService::ESTOQUE_REPOR => 'text-bg-danger',
            DashboardService::ESTOQUE_ATENCAO => 'text-bg-warning',
            default => 'text-bg-secondary',
        };
    }
}
