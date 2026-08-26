@extends('layout.site')

@section('content')

{{-- Breadcrumb --}}
<div class="breadcumb_area">
    <div class="container">
        <div class="bread_box">
            <ul class="breadcumb">
                <li><a href="{{ route('home') }}">Início <span>|</span></a></li>
                <li class="active"><a href="#">Criar conta</a></li>
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
                        <i class="fa fa-user-plus" style="color:#000;"></i> Criar conta
                    </div>

                    @if ($errors->any())
                        <div style="background:#fdecea; border:1px solid #f5c6cb; color:#c0392b; border-radius:4px; padding:12px 16px; margin-bottom:18px; font-size:13px;">
                            <ul style="margin:0; padding-left:18px;">
                                @foreach ($errors->all() as $erro)
                                    <li>{{ $erro }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('register.attempt') }}">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group-custom">
                                    <label>Nome <span class="obrig">*</span></label>
                                    <input type="text" name="name" class="form-control-custom @error('name') error @enderror"
                                           value="{{ old('name') }}" placeholder="Seu nome" required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-custom">
                                    <label>Sobrenome <span class="obrig">*</span></label>
                                    <input type="text" name="sobrenome" class="form-control-custom @error('sobrenome') error @enderror"
                                           value="{{ old('sobrenome') }}" placeholder="Seu sobrenome" required />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group-custom">
                                    <label>E-mail <span class="obrig">*</span></label>
                                    <input type="email" name="email" class="form-control-custom @error('email') error @enderror"
                                           value="{{ old('email') }}" placeholder="seu@email.com.br" required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-custom">
                                    <label>Senha <span class="obrig">*</span></label>
                                    <input type="password" name="password" class="form-control-custom @error('password') error @enderror"
                                           placeholder="Mínimo 8 caracteres" required />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-custom">
                                    <label>Confirmar senha <span class="obrig">*</span></label>
                                    <input type="password" name="password_confirmation" class="form-control-custom"
                                           placeholder="Repita a senha" required />
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn-next" style="width:100%; justify-content:center; margin-top:10px;">
                            Criar conta <i class="fa fa-arrow-right" style="margin-left:6px;"></i>
                        </button>
                    </form>

                    <p style="margin-top:20px; font-size:13px; text-align:center; color:#777;">
                        Já tem conta? <a href="{{ route('login') }}" style="color:#000; font-weight:600;">Entrar</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
