<div class="mb-3">
    <label for="nome" class="form-label">Nome <span class="text-danger">*</span></label>
    <input type="text" name="nome" id="nome" class="form-control @error('nome') is-invalid @enderror"
           value="{{ old('nome', $categoria->nome ?? '') }}" required>
    @error('nome')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
