{{--
    Card de estatística neutro por padrão -- número em destaque por
    tipografia, não por fundo colorido. Cor (via $tone) fica reservada pra
    estado que exige ação; ver decisão da fase 8D.

    Parâmetros:
    $label     (string) rótulo do card
    $value     (string) valor já formatado
    $tone      ('neutro'|'danger'|'warning', opcional, default 'neutro')
    $sublabel  (string, opcional) texto pequeno abaixo do valor
    $href      (string, opcional) link "ver mais" cobrindo o card inteiro
--}}
@php
    $tone = $tone ?? 'neutro';
    $valorClass = match ($tone) {
        'danger' => 'text-danger',
        'warning' => 'text-warning-emphasis',
        default => '',
    };
    $borderClass = match ($tone) {
        'danger' => 'border-danger-subtle',
        'warning' => 'border-warning-subtle',
        default => '',
    };
@endphp
<div class="card h-100 {{ $borderClass }} position-relative">
    <div class="card-body">
        <div class="text-secondary text-uppercase small mb-1">{{ $label }}</div>
        <div class="fs-3 fw-bold {{ $valorClass }}">{{ $value }}</div>
        @isset($sublabel)
            <div class="text-secondary small">{{ $sublabel }}</div>
        @endisset
        @isset($href)
            <a href="{{ $href }}" class="stretched-link" aria-label="{{ $label }}"></a>
        @endisset
    </div>
</div>
