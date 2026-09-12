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

];
