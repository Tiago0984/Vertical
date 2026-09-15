<?php

namespace Tests\Feature;

use App\Services\DashboardService;
use App\Support\EstoqueStatus;
use Tests\TestCase;

/**
 * estoque === 0 é checado ANTES da regra do mínimo -- um produto esgotado
 * não pode ficar escondido atrás de "Não configurado" só porque ninguém
 * preencheu estoque_minimo. A regra do mínimo continua valendo quando ainda
 * HÁ estoque (é o que evita os 16 produtos virarem "Repor" no dia do deploy).
 */
class EstoqueStatusTest extends TestCase
{
    public function test_estoque_zero_minimo_zero_e_esgotado(): void
    {
        $this->assertSame(DashboardService::ESTOQUE_ESGOTADO, EstoqueStatus::calcular(0, 0));
    }

    public function test_estoque_zero_minimo_dez_e_esgotado(): void
    {
        $this->assertSame(DashboardService::ESTOQUE_ESGOTADO, EstoqueStatus::calcular(0, 10));
    }

    public function test_estoque_tres_minimo_dez_e_repor(): void
    {
        $this->assertSame(DashboardService::ESTOQUE_REPOR, EstoqueStatus::calcular(3, 10));
    }

    public function test_estoque_doze_minimo_dez_e_atencao(): void
    {
        // multiplicador_atencao = 1.5 (config/dashboard.php) -- 12 <= 10*1.5.
        $this->assertSame(DashboardService::ESTOQUE_ATENCAO, EstoqueStatus::calcular(12, 10));
    }

    public function test_estoque_nove_minimo_zero_e_nao_configurado(): void
    {
        // com estoque > 0, a regra do mínimo continua valendo -- só o caso
        // estoque === 0 é que pula na frente dela.
        $this->assertSame(DashboardService::ESTOQUE_NAO_CONFIGURADO, EstoqueStatus::calcular(9, 0));
    }

    public function test_label_e_badge_do_esgotado(): void
    {
        $this->assertSame('Esgotado', EstoqueStatus::label(DashboardService::ESTOQUE_ESGOTADO));
        $this->assertSame('text-bg-danger', EstoqueStatus::badgeClass(DashboardService::ESTOQUE_ESGOTADO));
    }
}
