@extends('layout.site')

@section('content')

{{-- Breadcrumb --}}
<div class="breadcumb_area">
    <div class="container">
        <div class="bread_box">
            <ul class="breadcumb">
                <li><a href="{{ route('home') }}">Início <span>|</span></a></li>
                <li class="active"><a href="#">Entrar</a></li>
            </ul>
        </div>
    </div>
</div>

<section class="checkout-section">
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
                <div class="checkout-card">
                    <div class="checkout-card-title">
                        <i class="fa fa-user-circle-o" style="color:#000;"></i> Entrar
                    </div>

                    @if ($errors->any())
                        <div style="background:#fdecea; border:1px solid #f5c6cb; color:#c0392b; border-radius:4px; padding:12px 16px; margin-bottom:18px; font-size:13px;">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login.attempt') }}">
                        @csrf

                        <div class="form-group-custom">
                            <label>E-mail <span class="obrig">*</span></label>
                            <input type="email" name="email" class="form-control-custom @error('email') error @enderror"
                                   value="{{ old('email') }}" placeholder="seu@email.com.br" required />
                        </div>

                        <div class="form-group-custom">
                            <label>Senha <span class="obrig">*</span></label>
                            <input type="password" name="password" class="form-control-custom" placeholder="Sua senha" required />
                        </div>

                        <label class="checkout-optin">
                            <input type="checkbox" name="lembrar" />
                            Lembrar de mim
                        </label>

                        <button type="submit" class="btn-next" style="width:100%; justify-content:center; margin-top:10px;">
                            Entrar <i class="fa fa-arrow-right" style="margin-left:6px;"></i>
                        </button>
                    </form>

                    <p style="margin-top:20px; font-size:13px; text-align:center; color:#777;">
                        Ainda não tem conta? <a href="{{ route('register') }}" style="color:#000; font-weight:600;">Cadastre-se</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
