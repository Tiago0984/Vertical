<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Limiares do painel de estoque
    |--------------------------------------------------------------------------
    |
    | multiplicador_atencao: um produto entra em "Atenção" quando o estoque
    | cai até este múltiplo do estoque_minimo (ex.: 1.5 = até 50% acima do
    | mínimo). Abaixo (ou igual) do próprio estoque_minimo, vira "Repor".
    |
    | janela_cobertura_dias: estoque é estado ATUAL, não métrica de período --
    | a cobertura em meses (estoque ÷ média vendida) usa sempre esta janela
    | fixa de dias corridos pra trás de "agora", INDEPENDENTE do filtro de
    | período do dashboard. Sem isso, filtrar "últimos 7 dias" extrapolaria
    | uma semana de venda pra uma média mensal, e o mesmo produto mudaria de
    | "4 meses de estoque" pra "0,5 mês" só por causa do filtro clicado, sem
    | nada na tela explicando a diferença.
    |
    */

    'estoque' => [
        'multiplicador_atencao' => 1.5,
        'janela_cobertura_dias' => 90,
    ],

    /*
    |--------------------------------------------------------------------------
    | Limiares do painel financeiro
    |--------------------------------------------------------------------------
    |
    | cobertura_custo_minima: fração (0 a 1) dos produtos vendidos no período
    | que precisa ter "custo" cadastrado pra margem/CMV serem exibidos como
    | número confiável. Abaixo disso, o service retorna cmv/lucro_bruto como
    | NULL e margem_confiavel=false -- em vez de, por exemplo, tratar custo
    | ausente como 0 e mostrar 100% de margem (que parece ótimo e ninguém
    | questiona, ao contrário de um alerta óbvio de estoque zerado).
    |
    */

    'financeiro' => [
        'cobertura_custo_minima' => 0.8,
    ],

];
