<?php

namespace App\Services;

use App\Models\Coupon;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * Uma razão de recusa por caso -- "cupom expirado" e "cupom inválido" não
 * são a mesma mensagem pra quem está comprando. Não confia em nada vindo
 * do cliente além do código: quem calcula o desconto é sempre o servidor.
 */
class CupomValidador
{
    public const VALIDO = 'valido';
    public const NAO_EXISTE = 'nao_existe';
    public const INATIVO = 'inativo';
    public const FORA_DA_VALIDADE = 'fora_da_validade';
    public const ABAIXO_DO_MINIMO = 'abaixo_do_minimo';
    public const ESGOTADO = 'esgotado';

    /**
     * NAO_EXISTE e INATIVO deliberadamente compartilham a mesma mensagem:
     * diferenciar "esse código não existe" de "esse código existe mas está
     * desativado" ajudaria quem está tentando adivinhar código por força
     * bruta a mapear quais strings são cupons reais. Isso é defesa em
     * profundidade / UX, não a barreira principal -- FORA_DA_VALIDADE e
     * ABAIXO_DO_MINIMO continuam revelando que o código existe, porque
     * "cupom expirado" e "faltam R$ X pro mínimo" são informação que o
     * cliente legítimo precisa, e enumerar esses dois tem valor perto de
     * zero pra um atacante. A defesa real contra força bruta é o rate
     * limit da rota de validação (fase 2b). O motivo interno continua
     * distinto em todo caso -- é o que os testes verificam.
     */
    private const MENSAGENS = [
        self::NAO_EXISTE => 'Cupom inválido.',
        self::INATIVO => 'Cupom inválido.',
        self::FORA_DA_VALIDADE => 'Cupom expirado.',
        self::ESGOTADO => 'Cupom esgotado.',
    ];

    /**
     * @return array{status: string, mensagem: ?string, cupom: ?Coupon}
     */
    public function validar(string $codigo, float $subtotalAntesDoDesconto): array
    {
        $codigoNormalizado = Str::upper(trim($codigo));

        $cupom = Coupon::where('codigo', $codigoNormalizado)->first();

        if (! $cupom) {
            return $this->recusa(self::NAO_EXISTE);
        }

        if (! $cupom->ativo) {
            return $this->recusa(self::INATIVO);
        }

        $agora = Carbon::now();
        $antesDoInicio = $cupom->inicio_em && $agora->lt($cupom->inicio_em);
        $depoisDoFim = $cupom->fim_em && $agora->gt($cupom->fim_em);
        if ($antesDoInicio || $depoisDoFim) {
            return $this->recusa(self::FORA_DA_VALIDADE);
        }

        // minimo_compra é comparado ao subtotal ANTES do desconto -- decisão
        // do projeto, evita um cupom "se qualificar" usando um subtotal que
        // ele mesmo já reduziu. Mensagem diz quanto falta, não só que não
        // atinge -- empurra o cliente a aumentar o carrinho em vez de só
        // fechar a porta.
        if ($cupom->minimo_compra !== null && $subtotalAntesDoDesconto < (float) $cupom->minimo_compra) {
            $faltam = number_format((float) $cupom->minimo_compra - $subtotalAntesDoDesconto, 2, ',', '.');

            return [
                'status' => self::ABAIXO_DO_MINIMO,
                'mensagem' => "Faltam R$ {$faltam} para usar este cupom.",
                'cupom' => null,
            ];
        }

        if ($cupom->uso_maximo !== null && $cupom->usos >= $cupom->uso_maximo) {
            return $this->recusa(self::ESGOTADO);
        }

        return ['status' => self::VALIDO, 'mensagem' => null, 'cupom' => $cupom];
    }

    /**
     * @return array{status: string, mensagem: string, cupom: null}
     */
    private function recusa(string $status): array
    {
        return ['status' => $status, 'mensagem' => self::MENSAGENS[$status], 'cupom' => null];
    }
}
