@extends('layout.site')

@section('content')

<div class="breadcumb_area">
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="bread_box">
                    <ul class="breadcumb">
                        <li><a href="{{ route('home') }}">Início <span>|</span></a></li>
                        <li><a href="{{ route('conta.index') }}">Minha Conta <span>|</span></a></li>
                        <li><a href="{{ route('conta.enderecos.index') }}">Endereços <span>|</span></a></li>
                        <li class="active"><a href="#">{{ $endereco ? 'Editar' : 'Novo' }}</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<section class="checkout-section">
    <div class="container">
        <div class="row">
            <div class="col-md-3 col-sm-4 col-xs-12">
                @include('partials.conta-sidebar')
            </div>

            <div class="col-md-9 col-sm-8 col-xs-12">

                <form method="POST" action="{{ $endereco ? route('conta.enderecos.update', $endereco) : route('conta.enderecos.store') }}">
                    @csrf
                    @if ($endereco) @method('PUT') @endif

                    <div class="checkout-card">
                        <div class="checkout-card-title">
                            <i class="fa fa-map-marker" style="color:#000;"></i> {{ $endereco ? 'Editar Endereço' : 'Novo Endereço' }}
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group-custom">
                                    <label>Nome <span class="obrig">*</span></label>
                                    <input type="text" name="nome" class="form-control-custom @error('nome') error @enderror"
                                           value="{{ old('nome', $endereco->nome ?? '') }}" />
                                    @error('nome') <small style="color:#e74c3c;">{{ $message }}</small> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-custom">
                                    <label>Sobrenome <span class="obrig">*</span></label>
                                    <input type="text" name="sobrenome" class="form-control-custom @error('sobrenome') error @enderror"
                                           value="{{ old('sobrenome', $endereco->sobrenome ?? '') }}" />
                                    @error('sobrenome') <small style="color:#e74c3c;">{{ $message }}</small> @enderror
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group-custom">
                                    <label>CEP <span class="obrig">*</span></label>
                                    <input type="text" name="cep" id="end-cep" class="form-control-custom @error('cep') error @enderror"
                                           value="{{ old('cep', $endereco->cep ?? '') }}" placeholder="00000-000" maxlength="9"
                                           oninput="mascaraCEP(this)" />
                                    @error('cep') <small style="color:#e74c3c;">{{ $message }}</small> @enderror
                                </div>
                            </div>
                            <div class="col-md-7">
                                <div class="form-group-custom">
                                    <label>Endereço <span class="obrig">*</span></label>
                                    <input type="text" name="endereco" class="form-control-custom @error('endereco') error @enderror"
                                           value="{{ old('endereco', $endereco->endereco ?? '') }}" placeholder="Rua, Avenida, etc." />
                                    @error('endereco') <small style="color:#e74c3c;">{{ $message }}</small> @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group-custom">
                                    <label>Número <span class="obrig">*</span></label>
                                    <input type="text" name="numero" class="form-control-custom @error('numero') error @enderror"
                                           value="{{ old('numero', $endereco->numero ?? '') }}" />
                                    @error('numero') <small style="color:#e74c3c;">{{ $message }}</small> @enderror
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group-custom">
                                    <label>Complemento</label>
                                    <input type="text" name="complemento" class="form-control-custom"
                                           value="{{ old('complemento', $endereco->complemento ?? '') }}" placeholder="Apto, Bloco, etc." />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group-custom">
                                    <label>Bairro <span class="obrig">*</span></label>
                                    <input type="text" name="bairro" class="form-control-custom @error('bairro') error @enderror"
                                           value="{{ old('bairro', $endereco->bairro ?? '') }}" />
                                    @error('bairro') <small style="color:#e74c3c;">{{ $message }}</small> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-custom">
                                    <label>Cidade <span class="obrig">*</span></label>
                                    <input type="text" name="cidade" class="form-control-custom @error('cidade') error @enderror"
                                           value="{{ old('cidade', $endereco->cidade ?? '') }}" />
                                    @error('cidade') <small style="color:#e74c3c;">{{ $message }}</small> @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group-custom">
                                    <label>Estado <span class="obrig">*</span></label>
                                    @php $ufAtual = old('estado', $endereco->estado ?? ''); @endphp
                                    <select name="estado" class="form-control-custom @error('estado') error @enderror">
                                        <option value="">UF</option>
                                        @foreach (['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'] as $uf)
                                            <option value="{{ $uf }}" {{ $ufAtual === $uf ? 'selected' : '' }}>{{ $uf }}</option>
                                        @endforeach
                                    </select>
                                    @error('estado') <small style="color:#e74c3c;">{{ $message }}</small> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-custom">
                                    <label>Telefone</label>
                                    <input type="tel" name="telefone" class="form-control-custom"
                                           value="{{ old('telefone', $endereco->telefone ?? '') }}" placeholder="(11) 99999-0000"
                                           oninput="mascaraTel(this)" maxlength="15" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-custom">
                                    <label>Referência</label>
                                    <input type="text" name="referencia" class="form-control-custom"
                                           value="{{ old('referencia', $endereco->referencia ?? '') }}" placeholder="Ponto de referência" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label class="checkout-optin">
                                    <input type="checkbox" name="is_padrao" value="1"
                                           {{ old('is_padrao', $endereco->is_padrao ?? false) ? 'checked' : '' }} />
                                    Definir como endereço padrão
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="checkout-nav">
                        <a href="{{ route('conta.enderecos.index') }}" class="btn-prev" style="text-decoration:none;">
                            <i class="fa fa-arrow-left" style="margin-right:6px;"></i> Cancelar
                        </a>
                        <button type="submit" class="btn-next">Salvar Endereço</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</section>

<script>
function mascaraCEP(el) {
    let v = el.value.replace(/\D/g, '').slice(0, 8);
    if (v.length > 5) v = v.slice(0,5) + '-' + v.slice(5);
    el.value = v;
}
function mascaraTel(el) {
    let v = el.value.replace(/\D/g, '');
    if (v.length > 11) v = v.slice(0, 11);
    if (v.length > 6)      v = '(' + v.slice(0,2) + ') ' + v.slice(2,7) + '-' + v.slice(7);
    else if (v.length > 2) v = '(' + v.slice(0,2) + ') ' + v.slice(2);
    else if (v.length > 0) v = '(' + v;
    el.value = v;
}
</script>

@endsection
