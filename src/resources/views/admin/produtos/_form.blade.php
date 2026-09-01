<div class="row">
    <div class="col-md-8">
        <div class="mb-3">
            <label for="nome" class="form-label">Nome <span class="text-danger">*</span></label>
            <input type="text" name="nome" id="nome" class="form-control @error('nome') is-invalid @enderror"
                   value="{{ old('nome', $produto->nome ?? '') }}" required>
            @error('nome')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="descricao" class="form-label">Descrição</label>
            <textarea name="descricao" id="descricao" rows="4" class="form-control @error('descricao') is-invalid @enderror">{{ old('descricao', $produto->descricao ?? '') }}</textarea>
            @error('descricao')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="preco" class="form-label">Preço Atual (R$) <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="0" name="preco" id="preco"
                           class="form-control @error('preco') is-invalid @enderror"
                           value="{{ old('preco', $produto->preco ?? '') }}" required>
                    <div class="form-text">O valor que o cliente paga. Aparece em destaque na loja.</div>
                    @error('preco')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="preco_promocional" class="form-label">Preço Antes da Promoção (R$)</label>
                    <input type="number" step="0.01" min="0" name="preco_promocional" id="preco_promocional"
                           class="form-control @error('preco_promocional') is-invalid @enderror"
                           value="{{ old('preco_promocional', $produto->preco_promocional ?? '') }}">
                    <div class="form-text">
                        Só preencha se marcar "Promoção" abaixo. É o preço original, exibido riscado
                        — precisa ser maior que o Preço Atual.
                    </div>
                    @error('preco_promocional')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="mb-3">
            <div class="form-check">
                <input type="checkbox" name="is_novo" id="is_novo" class="form-check-input" value="1"
                       {{ old('is_novo', $produto->is_novo ?? false) ? 'checked' : '' }}>
                <label for="is_novo" class="form-check-label">Marcar como "Novo"</label>
            </div>
            <div class="form-check">
                <input type="checkbox" name="is_promocao" id="is_promocao" class="form-check-input" value="1"
                       {{ old('is_promocao', $produto->is_promocao ?? false) ? 'checked' : '' }}>
                <label for="is_promocao" class="form-check-label">Marcar como "Promoção"</label>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="mb-3">
            <label for="category_id" class="form-label">Categoria</label>
            <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror">
                <option value="">Sem categoria</option>
                @foreach ($categorias as $categoria)
                    <option value="{{ $categoria->id }}"
                        {{ (int) old('category_id', $produto->category_id ?? 0) === $categoria->id ? 'selected' : '' }}>
                        {{ $categoria->nome }}
                    </option>
                @endforeach
            </select>
            @error('category_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="imagem" class="form-label">
                Imagem {{ isset($produto) ? '' : '*' }}
            </label>
            @if (isset($produto) && $produto->imagem)
                <div class="mb-2">
                    <img src="{{ asset($produto->imagem) }}" alt="{{ $produto->nome }}" class="img-fluid rounded" style="max-height: 160px;">
                </div>
            @endif
            <input type="file" name="imagem" id="imagem" class="form-control @error('imagem') is-invalid @enderror" accept="image/png,image/jpeg,image/webp">
            @error('imagem')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            @if (isset($produto))
                <div class="form-text">Deixe em branco para manter a imagem atual.</div>
            @endif
        </div>
    </div>
</div>
