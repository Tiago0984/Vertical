@extends('layout.site')

@section('content')

{{-- ══════════════════════════════════════════
     TOPO
══════════════════════════════════════════ --}}
<section class="contact_banner_area">
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="contact_banner">
                    <h2>Contato</h2>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════
     BREADCRUMB
══════════════════════════════════════════ --}}
<div class="breadcumb_area">
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="bread_box">
                    <ul class="breadcumb">
                        <li><a href="{{ route('home') }}">Início <span>|</span></a></li>
                        <li class="active"><a href="#">Contato</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════
     FORMULÁRIO + INFORMAÇÕES
══════════════════════════════════════════ --}}
<section class="contact_p_area">
    <div class="container">
        <div class="row">

            <div class="col-md-8 col-sm-8 col-xs-12">
                <div class="contact_box_left">
                    <h4>Deixe sua mensagem</h4>
                    <div class="multi_line"></div>
                </div>

                <form id="form-contato">
                    <div class="input_form">
                        <div class="input_left">
                            <input type="text" id="contato-nome" placeholder="Nome *" required />
                            <input type="email" id="contato-email" placeholder="Seu e-mail *" required />
                            <input type="tel" id="contato-telefone" placeholder="Telefone *" required
                                   oninput="mascaraTelContato(this)" maxlength="15" />
                        </div>
                        <div class="input_right">
                            <textarea id="contato-mensagem" placeholder="Mensagem" required></textarea>
                        </div>
                    </div>
                    <div class="submit_btn form-group submitRow">
                        <input type="submit" value="Enviar mensagem" />
                    </div>
                    <p id="contato-sucesso" style="display:none; clear:both; padding-top:16px; font-size:13px; color:#2e7d32; font-weight:600;">
                        <i class="fa fa-check-circle"></i> Mensagem enviada! Em breve entraremos em contato.
                    </p>
                </form>
            </div>

            <div class="col-md-4 col-sm-4 col-xs-12">
                <div class="contact_box_right">
                    <h5>Endereço</h5>
                    <p>Rua das Camisetas, 42<br>São Paulo, SP — 01310-100</p>
                    <div class="dotted_line"></div>

                    <h5>Informações de contato</h5>
                    <p>contato@camisetashop.com.br</p>
                    <a href="tel:+5511999990000">(11) 99999-0000</a>
                    <div class="dotted_line"></div>

                    <h5>Horário de atendimento</h5>
                    <p>Segunda a sexta, das 9h às 18h</p>
                </div>
            </div>

        </div>
    </div>
</section>

<script>
document.getElementById('form-contato').addEventListener('submit', function (e) {
    e.preventDefault();
    document.getElementById('contato-sucesso').style.display = 'block';
    this.reset();
});

function mascaraTelContato(el) {
    let v = el.value.replace(/\D/g, '').slice(0, 11);
    if (v.length > 10) v = v.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
    else if (v.length > 6) v = v.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3');
    else if (v.length > 2) v = v.replace(/(\d{2})(\d{0,5})/, '($1) $2');
    el.value = v;
}
</script>

@endsection
