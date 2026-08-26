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
                        <li class="active"><a href="#">Dados Pessoais</a></li>
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

                @if (session('status'))
                    <div class="conta-alert-sucesso">{{ session('status') }}</div>
                @endif

                <form method="POST" action="{{ route('conta.dados-pessoais.atualizar') }}">
                    @csrf
                    @method('PUT')

                    <div class="checkout-card">
                        <div class="checkout-card-title">
                            <i class="fa fa-id-card-o" style="color:#000;"></i> Dados Pessoais
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group-custom">
                                    <label>Nome <span class="obrig">*</span></label>
                                    <input type="text" name="name" class="form-control-custom @error('name') error @enderror"
                                           value="{{ old('name', $user->name) }}" />
                                    @error('name') <small style="color:#e74c3c;">{{ $message }}</small> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-custom">
                                    <label>Sobrenome <span class="obrig">*</span></label>
                                    <input type="text" name="sobrenome" class="form-control-custom @error('sobrenome') error @enderror"
                                           value="{{ old('sobrenome', $user->sobrenome) }}" />
                                    @error('sobrenome') <small style="color:#e74c3c;">{{ $message }}</small> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-custom">
                                    <label>E-mail <span class="obrig">*</span></label>
                                    <input type="email" name="email" class="form-control-custom @error('email') error @enderror"
                                           value="{{ old('email', $user->email) }}" />
                                    @error('email') <small style="color:#e74c3c;">{{ $message }}</small> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-custom">
                                    <label>Telefone / WhatsApp</label>
                                    <input type="tel" name="telefone" class="form-control-custom"
                                           value="{{ old('telefone', $user->telefone) }}" placeholder="(11) 99999-0000" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-custom">
                                    <label>Data de nascimento</label>
                                    <input type="date" name="data_nascimento" class="form-control-custom"
                                           value="{{ old('data_nascimento', $user->data_nascimento?->format('Y-m-d')) }}" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label class="checkout-optin">
                                    <input type="checkbox" name="newsletter_email" value="1" {{ old('newsletter_email', $user->newsletter_email) ? 'checked' : '' }} />
                                    Receber novidades e ofertas por e-mail
                                </label>
                                <label class="checkout-optin">
                                    <input type="checkbox" name="newsletter_sms" value="1" {{ old('newsletter_sms', $user->newsletter_sms) ? 'checked' : '' }} />
                                    Receber novidades e ofertas por SMS
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="checkout-nav" style="justify-content:flex-end;">
                        <button type="submit" class="btn-next">Salvar alterações</button>
                    </div>
                </form>

                <form method="POST" action="{{ route('conta.senha.atualizar') }}">
                    @csrf
                    @method('PUT')

                    <div class="checkout-card">
                        <div class="checkout-card-title">
                            <i class="fa fa-lock" style="color:#000;"></i> Alterar Senha
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group-custom">
                                    <label>Senha atual <span class="obrig">*</span></label>
                                    <input type="password" name="senha_atual" class="form-control-custom @error('senha_atual') error @enderror" />
                                    @error('senha_atual') <small style="color:#e74c3c;">{{ $message }}</small> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group-custom">
                                    <label>Nova senha <span class="obrig">*</span></label>
                                    <input type="password" name="senha" class="form-control-custom @error('senha') error @enderror" />
                                    @error('senha') <small style="color:#e74c3c;">{{ $message }}</small> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group-custom">
                                    <label>Confirmar nova senha <span class="obrig">*</span></label>
                                    <input type="password" name="senha_confirmation" class="form-control-custom" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="checkout-nav" style="justify-content:flex-end;">
                        <button type="submit" class="btn-next">Alterar senha</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</section>

@endsection
