@php
    $bloqueado = isset($cupom) && $cupom->usos > 0;
@endphp

<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="codigo" class="form-label">Código <span class="text-danger">*</span></label>
            <input type="text" name="codigo" id="codigo"
                   class="form-control @error('codigo') is-invalid @enderror text-uppercase"
                   value="{{ old('codigo', $cupom->codigo ?? '') }}"
                   {{ $bloqueado ? 'readonly' : '' }} required>
            @error('codigo')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            @if ($bloqueado)
                <div class="form-text text-warning">
                    Não pode ser alterado -- este cupom já foi usado {{ $cupom->usos }}
                    {{ $cupom->usos === 1 ? 'vez' : 'vezes' }}.
                </div>
            @endif
        </div>
    </div>

    <div class="col-md-3">
        <div class="mb-3">
            <label for="tipo" class="form-label">Tipo <span class="text-danger">*</span></label>
            <select name="tipo" id="tipo" class="form-select @error('tipo') is-invalid @enderror" required>
                @foreach (\App\Models\Coupon::TIPOS as $tipoOpcao)
                    <option value="{{ $tipoOpcao }}" {{ old('tipo', $cupom->tipo ?? '') === $tipoOpcao ? 'selected' : '' }}>
                        {{ ucfirst($tipoOpcao) }}
                    </option>
                @endforeach
            </select>
            @error('tipo')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-3">
        <div class="mb-3">
            <label for="valor" class="form-label">Valor <span class="text-danger">*</span></label>
            <input type="number" step="0.01" min="0.01" name="valor" id="valor"
                   class="form-control @error('valor') is-invalid @enderror"
                   value="{{ old('valor', $cupom->valor ?? '') }}" required>
            <div class="form-text">Percentual: 1 a 100. Fixo: valor em R$.</div>
            @error('valor')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="inicio_em" class="form-label">Início da validade</label>
            <input type="datetime-local" name="inicio_em" id="inicio_em"
                   class="form-control @error('inicio_em') is-invalid @enderror"
                   value="{{ old('inicio_em', isset($cupom->inicio_em) ? $cupom->inicio_em->format('Y-m-d\TH:i') : '') }}">
            <div class="form-text">Em branco = vale desde já.</div>
            @error('inicio_em')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            <label for="fim_em" class="form-label">Fim da validade</label>
            <input type="datetime-local" name="fim_em" id="fim_em"
                   class="form-control @error('fim_em') is-invalid @enderror"
                   value="{{ old('fim_em', isset($cupom->fim_em) ? $cupom->fim_em->format('Y-m-d\TH:i') : '') }}">
            <div class="form-text">Em branco = sem data de expiração.</div>
            @error('fim_em')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="minimo_compra" class="form-label">Compra mínima (R$)</label>
            <input type="number" step="0.01" min="0" name="minimo_compra" id="minimo_compra"
                   class="form-control @error('minimo_compra') is-invalid @enderror"
                   value="{{ old('minimo_compra', $cupom->minimo_compra ?? '') }}">
            <div class="form-text">
                Comparado ao subtotal ANTES do desconto. Em branco = sem mínimo.
            </div>
            @error('minimo_compra')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            <label for="uso_maximo" class="form-label">Limite de usos</label>
            <input type="number" step="1" min="1" name="uso_maximo" id="uso_maximo"
                   class="form-control @error('uso_maximo') is-invalid @enderror"
                   value="{{ old('uso_maximo', $cupom->uso_maximo ?? '') }}">
            <div class="form-text">
                Em branco = uso ilimitado.
                @if (isset($cupom))
                    Já usado {{ $cupom->usos }} {{ $cupom->usos === 1 ? 'vez' : 'vezes' }}.
                @endif
            </div>
            @error('uso_maximo')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="mb-3">
    <div class="form-check form-switch">
        <input type="checkbox" name="ativo" id="ativo" class="form-check-input" value="1"
               {{ old('ativo', $cupom->ativo ?? true) ? 'checked' : '' }}>
        <label for="ativo" class="form-check-label">Ativo</label>
    </div>
</div>
