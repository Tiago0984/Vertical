@extends('layout.site')

@section('content')

<style>
/* ══ Indicador de etapas ══ */
.steps-bar { padding: 36px 0 28px; background: #fafafa; border-bottom: 1px solid #f0f0f0; }
.steps-list { display: flex; align-items: center; justify-content: center; gap: 0; list-style: none; margin: 0; padding: 0; }
.step-item { display: flex; align-items: center; gap: 0; }
.step-circle {
    width: 40px; height: 40px; border-radius: 50%;
    border: 2px solid #ddd; background: #fff;
    display: flex; align-items: center; justify-content: center;
    font-family: 'Montserrat', sans-serif; font-size: 14px; font-weight: 700;
    color: #aaa; transition: all 0.3s; position: relative;
}
.step-circle.done   { background: #4caf50; border-color: #4caf50; color: #fff; }
.step-circle.active { background: #e91e8c; border-color: #e91e8c; color: #fff; }
.step-label {
    font-family: 'Cabin', sans-serif; font-size: 11px;
    font-weight: 600; color: #aaa; text-align: center;
    margin-top: 6px; white-space: nowrap;
    text-transform: uppercase; letter-spacing: 0.5px;
}
.step-label.active { color: #e91e8c; }
.step-label.done   { color: #4caf50; }
.step-wrap { display: flex; flex-direction: column; align-items: center; }
.step-line { height: 2px; width: 80px; background: #eee; margin: 0 8px; transition: background 0.3s; }
.step-line.done { background: #4caf50; }

/* ══ Container principal ══ */
.checkout-section { padding: 40px 0 70px; }
.step-content { display: none; }
.step-content.active { display: block; }

/* ══ Card de etapa ══ */
.checkout-card {
    background: #fff; border: 1px solid #f0f0f0;
    border-radius: 6px; padding: 32px 28px; margin-bottom: 20px;
}
.checkout-card-title {
    font-family: 'Montserrat', sans-serif; font-size: 15px;
    font-weight: 700; text-transform: uppercase; letter-spacing: 1px;
    color: #232323; margin-bottom: 20px; padding-bottom: 12px;
    border-bottom: 2px solid #e91e8c; display: flex; align-items: center; gap: 8px;
}

/* ══ Campos do formulário ══ */
.form-group-custom { margin-bottom: 18px; }
.form-group-custom label {
    display: block; font-family: 'Montserrat', sans-serif;
    font-size: 11px; font-weight: 700; text-transform: uppercase;
    letter-spacing: 0.5px; color: #555; margin-bottom: 6px;
}
.form-group-custom label .obrig { color: #e91e8c; margin-left: 2px; }
.form-control-custom {
    width: 100%; padding: 11px 14px; border: 2px solid #e0e0e0;
    border-radius: 3px; font-family: 'Cabin', sans-serif; font-size: 14px;
    color: #333; outline: none; transition: border 0.15s;
    background: #fff; box-sizing: border-box;
}
.form-control-custom:focus { border-color: #e91e8c; }
.form-control-custom.error { border-color: #e53935; }

/* ══ Botões de navegação ══ */
.checkout-nav { display: flex; justify-content: space-between; align-items: center; margin-top: 28px; flex-wrap: wrap; gap: 12px; }
.btn-prev {
    padding: 12px 24px; border: 2px solid #ddd; background: #fff;
    color: #555; font-family: 'Montserrat', sans-serif; font-size: 12px;
    font-weight: 700; text-transform: uppercase; letter-spacing: 1px;
    cursor: pointer; border-radius: 3px; transition: all 0.15s;
}
.btn-prev:hover { border-color: #555; }
.btn-next {
    padding: 12px 32px; background: #e91e8c; color: #fff; border: none;
    font-family: 'Montserrat', sans-serif; font-size: 12px; font-weight: 700;
    text-transform: uppercase; letter-spacing: 1px; cursor: pointer;
    border-radius: 3px; transition: background 0.2s;
    box-shadow: 0 3px 12px rgba(233,30,140,0.35);
}
.btn-next:hover { background: #c2185b; }

/* ══ CEP ══ */
.cep-wrap { display: flex; gap: 10px; align-items: flex-start; }
.cep-wrap input { flex: 1; }
.btn-cep {
    padding: 11px 18px; background: #333; color: #fff; border: none;
    font-family: 'Montserrat', sans-serif; font-size: 11px; font-weight: 700;
    text-transform: uppercase; letter-spacing: 1px; cursor: pointer;
    border-radius: 3px; white-space: nowrap; transition: background 0.2s; flex-shrink: 0;
}
.btn-cep:hover { background: #e91e8c; }
.cep-spinner { display: none; font-size: 13px; color: #e91e8c; margin-top: 6px; }
.cep-error   { display: none; font-size: 12px; color: #e53935; margin-top: 4px; font-family: 'Cabin', sans-serif; }

/* ══ Seletor de pagamento ══ */
.payment-options { display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 24px; }
.payment-opt {
    flex: 1; min-width: 120px; border: 2px solid #e0e0e0; border-radius: 6px;
    padding: 16px 12px; text-align: center; cursor: pointer;
    transition: all 0.2s; background: #fff;
}
.payment-opt:hover { border-color: #e91e8c; }
.payment-opt.selected { border-color: #e91e8c; background: #fff0f7; }
.payment-opt i { font-size: 24px; color: #aaa; display: block; margin-bottom: 8px; }
.payment-opt.selected i { color: #e91e8c; }
.payment-opt-label { font-family: 'Montserrat', sans-serif; font-size: 11px; font-weight: 700; color: #555; text-transform: uppercase; }
.payment-opt.selected .payment-opt-label { color: #e91e8c; }
.payment-panel { display: none; }
.payment-panel.active { display: block; }

/* ══ Card de crédito ══ */
.card-preview {
    background: linear-gradient(135deg, #e91e8c, #880e4f);
    border-radius: 12px; padding: 22px 24px; color: #fff;
    font-family: 'Montserrat', sans-serif; margin-bottom: 20px;
    box-shadow: 0 8px 24px rgba(233,30,140,0.3); position: relative;
}
.card-preview .card-number { font-size: 18px; letter-spacing: 4px; margin: 14px 0 10px; }
.card-preview .card-info   { display: flex; justify-content: space-between; font-size: 11px; opacity: 0.85; }
.card-preview .card-logo   { position: absolute; top: 16px; right: 20px; font-size: 26px; opacity: 0.9; }

/* ══ PIX ══ */
.pix-box {
    text-align: center; padding: 28px 20px;
    border: 2px dashed #32bcad; border-radius: 8px;
    background: #f0faf9;
}
.pix-qr {
    width: 160px; height: 160px; margin: 0 auto 14px;
    background: #fff; border: 4px solid #32bcad;
    border-radius: 8px; display: flex; align-items: center;
    justify-content: center; padding: 10px;
}
.pix-qr svg { width: 100%; height: 100%; }
.pix-code {
    background: #e0f7fa; border: 1px solid #b2ebf2; border-radius: 4px;
    padding: 10px 16px; font-family: 'Courier New', monospace; font-size: 11px;
    color: #00796b; word-break: break-all; margin: 12px auto; max-width: 360px;
}
.btn-copy {
    padding: 9px 20px; background: #32bcad; color: #fff; border: none;
    font-family: 'Montserrat', sans-serif; font-size: 11px; font-weight: 700;
    text-transform: uppercase; border-radius: 3px; cursor: pointer;
    letter-spacing: 1px; transition: background 0.2s;
}
.btn-copy:hover { background: #00897b; }
.pix-timer {
    font-family: 'Montserrat', sans-serif; font-size: 20px;
    font-weight: 700; color: #e91e8c; margin-top: 10px;
}
.pix-timer small { font-size: 12px; color: #aaa; font-weight: 400; font-family: 'Cabin', sans-serif; display: block; }

/* ══ Boleto ══ */
.boleto-box {
    text-align: center; padding: 28px 20px;
    border: 2px dashed #ddd; border-radius: 8px;
    background: #fafafa;
}
.barcode {
    display: flex; justify-content: center; gap: 2px;
    margin: 16px auto; height: 50px; max-width: 300px;
}
.barcode span {
    display: inline-block; height: 100%;
    background: #333; border-radius: 1px;
}
.btn-boleto {
    padding: 12px 28px; background: #555; color: #fff; border: none;
    font-family: 'Montserrat', sans-serif; font-size: 13px; font-weight: 700;
    text-transform: uppercase; border-radius: 3px; cursor: pointer;
    letter-spacing: 1px; transition: background 0.2s; margin-top: 12px;
}
.btn-boleto:hover { background: #333; }

/* ══ Confirmação ══ */
.confirm-box { text-align: center; padding: 40px 20px; }
.confirm-icon { font-size: 72px; color: #4caf50; display: block; margin-bottom: 16px; animation: popIn 0.5s ease; }
@keyframes popIn { 0%{transform:scale(0)} 80%{transform:scale(1.1)} 100%{transform:scale(1)} }
.confirm-title { font-family: 'Montserrat', sans-serif; font-size: 26px; font-weight: 700; color: #232323; margin-bottom: 8px; }
.confirm-sub   { font-family: 'Cabin', sans-serif; font-size: 15px; color: #666; margin-bottom: 24px; }
.confirm-num   { background: #fff0f7; border: 2px dashed #e91e8c; border-radius: 6px; padding: 16px 28px; display: inline-block; margin-bottom: 28px; }
.confirm-num strong { font-family: 'Montserrat', sans-serif; font-size: 22px; font-weight: 700; color: #e91e8c; }

/* ══ Resumo lateral ══ */
.side-summary { background: #fafafa; border: 1px solid #f0f0f0; border-radius: 6px; padding: 24px 20px; position: sticky; top: 20px; }
.side-summary h5 { font-family: 'Montserrat', sans-serif; font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #232323; margin-bottom: 16px; padding-bottom: 10px; border-bottom: 2px solid #e91e8c; }
.side-item { display: flex; gap: 10px; margin-bottom: 12px; align-items: flex-start; }
.side-item img { width: 52px; height: 52px; object-fit: cover; border-radius: 3px; flex-shrink: 0; }
.side-item-info { flex: 1; }
.side-item-name { font-family: 'Cabin', sans-serif; font-size: 12px; font-weight: 600; color: #333; margin-bottom: 2px; }
.side-item-meta { font-size: 11px; color: #aaa; font-family: 'Cabin', sans-serif; }
.side-item-price { font-family: 'Montserrat', sans-serif; font-size: 13px; font-weight: 700; color: #e91e8c; white-space: nowrap; }
.side-divider { border: none; border-top: 1px solid #f0f0f0; margin: 12px 0; }
.side-total-row { display: flex; justify-content: space-between; font-family: 'Cabin', sans-serif; font-size: 13px; color: #555; margin-bottom: 6px; }
.side-total-row.total { font-family: 'Montserrat', sans-serif; font-size: 16px; font-weight: 700; color: #232323; margin-top: 8px; border-top: 2px solid #e91e8c; padding-top: 12px; }
.side-total-row.total span:last-child { color: #e91e8c; }
.side-total-row .green { color: #4caf50; font-weight: 600; }
</style>

{{-- Breadcrumb --}}
<div class="breadcumb_area">
    <div class="container">
        <div class="bread_box">
            <ul class="breadcumb">
                <li><a href="{{ route('home') }}">Início <span>|</span></a></li>
                <li><a href="{{ route('carrinho') }}">Carrinho <span>|</span></a></li>
                <li class="active"><a href="#">Checkout</a></li>
            </ul>
        </div>
    </div>
</div>

{{-- Indicador de etapas --}}
<div class="steps-bar">
    <div class="container">
        <ul class="steps-list" id="steps-list">
            <li class="step-item">
                <div class="step-wrap">
                    <div class="step-circle active" id="sc-1">1</div>
                    <div class="step-label active" id="sl-1">Identificação</div>
                </div>
            </li>
            <li class="step-line" id="line-1"></li>
            <li class="step-item">
                <div class="step-wrap">
                    <div class="step-circle" id="sc-2">2</div>
                    <div class="step-label" id="sl-2">Endereço</div>
                </div>
            </li>
            <li class="step-line" id="line-2"></li>
            <li class="step-item">
                <div class="step-wrap">
                    <div class="step-circle" id="sc-3">3</div>
                    <div class="step-label" id="sl-3">Pagamento</div>
                </div>
            </li>
            <li class="step-line" id="line-3"></li>
            <li class="step-item">
                <div class="step-wrap">
                    <div class="step-circle" id="sc-4">4</div>
                    <div class="step-label" id="sl-4">Confirmação</div>
                </div>
            </li>
        </ul>
    </div>
</div>

{{-- Conteúdo principal --}}
<section class="checkout-section">
    <div class="container">
        <div class="row">

            {{-- Formulário (esquerda) --}}
            <div class="col-md-8 col-sm-12 col-xs-12">

                {{-- ══ ETAPA 1: Identificação ══ --}}
                <div class="step-content active" id="step-1">
                    <div class="checkout-card">
                        <div class="checkout-card-title">
                            <i class="fa fa-user-circle-o" style="color:#e91e8c;"></i> Identificação
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group-custom">
                                    <label>Nome <span class="obrig">*</span></label>
                                    <input type="text" id="id-nome" class="form-control-custom" placeholder="Seu nome completo" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-custom">
                                    <label>Sobrenome <span class="obrig">*</span></label>
                                    <input type="text" id="id-sobrenome" class="form-control-custom" placeholder="Seu sobrenome" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-custom">
                                    <label>E-mail <span class="obrig">*</span></label>
                                    <input type="email" id="id-email" class="form-control-custom" placeholder="seu@email.com.br" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-custom">
                                    <label>Telefone / WhatsApp <span class="obrig">*</span></label>
                                    <input type="tel" id="id-tel" class="form-control-custom" placeholder="(11) 99999-0000"
                                           oninput="mascaraTel(this)" maxlength="15" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-custom">
                                    <label>CPF <span class="obrig">*</span></label>
                                    <input type="text" id="id-cpf" class="form-control-custom" placeholder="000.000.000-00"
                                           oninput="mascaraCPF(this)" maxlength="14" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-custom">
                                    <label>Data de nascimento</label>
                                    <input type="date" id="id-nasc" class="form-control-custom" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div style="background:#f5f5f5; border-radius:4px; padding:14px 16px; margin-top:4px;">
                                    <label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-family:'Cabin',sans-serif; font-size:13px; color:#555; margin:0; font-weight:normal; text-transform:none; letter-spacing:0;">
                                        <input type="checkbox" id="criar-conta" style="width:16px; height:16px; accent-color:#e91e8c;" />
                                        Criar conta para compras futuras mais rápidas
                                    </label>
                                    <div id="senha-wrap" style="display:none; margin-top:12px;">
                                        <div class="form-group-custom" style="margin-bottom:0;">
                                            <label>Senha</label>
                                            <input type="password" class="form-control-custom" placeholder="Mínimo 8 caracteres" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="checkout-nav">
                        <a href="{{ route('carrinho') }}" style="font-family:'Cabin',sans-serif; font-size:13px; color:#aaa; text-decoration:none;">
                            <i class="fa fa-arrow-left" style="margin-right:4px;"></i> Voltar ao carrinho
                        </a>
                        <button class="btn-next" onclick="irParaEtapa(2)">
                            Continuar <i class="fa fa-arrow-right" style="margin-left:6px;"></i>
                        </button>
                    </div>
                </div>

                {{-- ══ ETAPA 2: Endereço ══ --}}
                <div class="step-content" id="step-2">
                    <div class="checkout-card">
                        <div class="checkout-card-title">
                            <i class="fa fa-map-marker" style="color:#e91e8c;"></i> Endereço de Entrega
                        </div>
                        <div class="row">
                            <div class="col-md-5">
                                <div class="form-group-custom">
                                    <label>CEP <span class="obrig">*</span></label>
                                    <div class="cep-wrap">
                                        <input type="text" id="end-cep" class="form-control-custom"
                                               placeholder="00000-000" maxlength="9"
                                               oninput="mascaraCEP(this)" />
                                        <button class="btn-cep" onclick="buscarCEP()">
                                            <i class="fa fa-search"></i> Buscar
                                        </button>
                                    </div>
                                    <div class="cep-spinner" id="cep-spinner">
                                        <i class="fa fa-spinner fa-spin"></i> Buscando endereço...
                                    </div>
                                    <div class="cep-error" id="cep-error">CEP não encontrado. Verifique e tente novamente.</div>
                                    <a href="https://buscacepinter.correios.com.br" target="_blank"
                                       style="font-size:11px; color:#e91e8c; margin-top:4px; display:inline-block;">
                                       Não sei meu CEP
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-7">
                                <div class="form-group-custom">
                                    <label>Logradouro <span class="obrig">*</span></label>
                                    <input type="text" id="end-rua" class="form-control-custom" placeholder="Rua, Avenida, etc." />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group-custom">
                                    <label>Número <span class="obrig">*</span></label>
                                    <input type="text" id="end-num" class="form-control-custom" placeholder="Nº" />
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group-custom">
                                    <label>Complemento</label>
                                    <input type="text" id="end-comp" class="form-control-custom" placeholder="Apto, Bloco, etc." />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group-custom">
                                    <label>Bairro <span class="obrig">*</span></label>
                                    <input type="text" id="end-bairro" class="form-control-custom" placeholder="Bairro" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-custom">
                                    <label>Cidade <span class="obrig">*</span></label>
                                    <input type="text" id="end-cidade" class="form-control-custom" placeholder="Cidade" />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group-custom">
                                    <label>Estado <span class="obrig">*</span></label>
                                    <select id="end-uf" class="form-control-custom">
                                        <option value="">UF</option>
                                        <option>AC</option><option>AL</option><option>AP</option><option>AM</option>
                                        <option>BA</option><option>CE</option><option>DF</option><option>ES</option>
                                        <option>GO</option><option>MA</option><option>MT</option><option>MS</option>
                                        <option>MG</option><option>PA</option><option>PB</option><option>PR</option>
                                        <option>PE</option><option>PI</option><option>RJ</option><option>RN</option>
                                        <option>RS</option><option>RO</option><option>RR</option><option>SC</option>
                                        <option selected>SP</option><option>SE</option><option>TO</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group-custom">
                                    <label>Referência</label>
                                    <input type="text" id="end-ref" class="form-control-custom" placeholder="Ponto de referência" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="checkout-nav">
                        <button class="btn-prev" onclick="irParaEtapa(1)">
                            <i class="fa fa-arrow-left" style="margin-right:6px;"></i> Voltar
                        </button>
                        <button class="btn-next" onclick="irParaEtapa(3)">
                            Continuar <i class="fa fa-arrow-right" style="margin-left:6px;"></i>
                        </button>
                    </div>
                </div>

                {{-- ══ ETAPA 3: Pagamento ══ --}}
                <div class="step-content" id="step-3">
                    <div class="checkout-card">
                        <div class="checkout-card-title">
                            <i class="fa fa-credit-card" style="color:#e91e8c;"></i> Forma de Pagamento
                        </div>

                        <div class="payment-options">
                            <div class="payment-opt selected" onclick="selecionarPagamento('cartao', this)">
                                <i class="fa fa-credit-card"></i>
                                <div class="payment-opt-label">Cartão de Crédito</div>
                            </div>
                            <div class="payment-opt" onclick="selecionarPagamento('pix', this)">
                                <i class="fa fa-qrcode"></i>
                                <div class="payment-opt-label">PIX</div>
                            </div>
                            <div class="payment-opt" onclick="selecionarPagamento('boleto', this)">
                                <i class="fa fa-barcode"></i>
                                <div class="payment-opt-label">Boleto Bancário</div>
                            </div>
                        </div>

                        {{-- Painel: Cartão de Crédito --}}
                        <div class="payment-panel active" id="panel-cartao">
                            <div class="card-preview">
                                <span class="card-logo"><i class="fa fa-cc-visa"></i></span>
                                <div style="font-size:11px; opacity:0.7; text-transform:uppercase; letter-spacing:1px;">CamisetaShop</div>
                                <div class="card-number" id="prev-num">•••• •••• •••• ••••</div>
                                <div class="card-info">
                                    <div>
                                        <div style="font-size:9px; opacity:0.6; text-transform:uppercase; letter-spacing:1px;">Titular</div>
                                        <div id="prev-nome" style="font-size:13px; font-weight:600;">NOME DO TITULAR</div>
                                    </div>
                                    <div style="text-align:right;">
                                        <div style="font-size:9px; opacity:0.6; text-transform:uppercase; letter-spacing:1px;">Validade</div>
                                        <div id="prev-val" style="font-size:13px; font-weight:600;">MM/AA</div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group-custom">
                                        <label>Número do Cartão <span class="obrig">*</span></label>
                                        <input type="text" id="cc-num" class="form-control-custom"
                                               placeholder="0000 0000 0000 0000" maxlength="19"
                                               oninput="mascaraCartao(this)" />
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group-custom">
                                        <label>Nome como no Cartão <span class="obrig">*</span></label>
                                        <input type="text" id="cc-nome" class="form-control-custom"
                                               placeholder="NOME IMPRESSO NO CARTÃO"
                                               oninput="document.getElementById('prev-nome').textContent=this.value.toUpperCase()||'NOME DO TITULAR'"
                                               style="text-transform:uppercase;" />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group-custom">
                                        <label>Validade <span class="obrig">*</span></label>
                                        <input type="text" id="cc-val" class="form-control-custom"
                                               placeholder="MM/AA" maxlength="5"
                                               oninput="mascaraVal(this)" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group-custom">
                                        <label>CVV <span class="obrig">*</span>
                                            <i class="fa fa-question-circle" style="color:#aaa; cursor:help;" title="3 dígitos no verso do cartão"></i>
                                        </label>
                                        <input type="text" id="cc-cvv" class="form-control-custom"
                                               placeholder="•••" maxlength="4"
                                               oninput="this.value=this.value.replace(/\D/g,'')" />
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group-custom">
                                        <label>Parcelas <span class="obrig">*</span></label>
                                        <select id="cc-parcelas" class="form-control-custom">
                                            <option value="1">1x de R$229,60 (sem juros)</option>
                                            <option value="2">2x de R$114,80 (sem juros)</option>
                                            <option value="3">3x de R$76,54 (sem juros)</option>
                                            <option value="4">4x de R$59,11 (sem juros)</option>
                                            <option value="5">5x de R$47,72 (sem juros)</option>
                                            <option value="6">6x de R$39,77 (sem juros)</option>
                                            <option value="7">7x de R$35,51 (c/ juros)</option>
                                            <option value="8">8x de R$31,58 (c/ juros)</option>
                                            <option value="9">9x de R$28,44 (c/ juros)</option>
                                            <option value="10">10x de R$26,00 (c/ juros)</option>
                                            <option value="11">11x de R$24,03 (c/ juros)</option>
                                            <option value="12">12x de R$22,42 (c/ juros)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Painel: PIX --}}
                        <div class="payment-panel" id="panel-pix">
                            <div class="pix-box">
                                <div class="pix-qr">
                                    {{-- QR Code placeholder em SVG --}}
                                    <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                                        <rect width="100" height="100" fill="#fff"/>
                                        <!-- Marcadores de canto --}}
                                        <rect x="5" y="5" width="25" height="25" fill="none" stroke="#000" stroke-width="3"/>
                                        <rect x="9" y="9" width="17" height="17" fill="#000"/>
                                        <rect x="70" y="5" width="25" height="25" fill="none" stroke="#000" stroke-width="3"/>
                                        <rect x="74" y="9" width="17" height="17" fill="#000"/>
                                        <rect x="5" y="70" width="25" height="25" fill="none" stroke="#000" stroke-width="3"/>
                                        <rect x="9" y="74" width="17" height="17" fill="#000"/>
                                        {{-- Módulos centrais (simulados) --}}
                                        <rect x="38" y="5" width="4" height="4" fill="#000"/>
                                        <rect x="44" y="5" width="4" height="4" fill="#000"/>
                                        <rect x="50" y="5" width="4" height="4" fill="#000"/>
                                        <rect x="56" y="5" width="4" height="4" fill="#000"/>
                                        <rect x="62" y="5" width="4" height="4" fill="#000"/>
                                        <rect x="5" y="38" width="4" height="4" fill="#000"/>
                                        <rect x="5" y="44" width="4" height="4" fill="#000"/>
                                        <rect x="5" y="50" width="4" height="4" fill="#000"/>
                                        <rect x="5" y="56" width="4" height="4" fill="#000"/>
                                        <rect x="5" y="62" width="4" height="4" fill="#000"/>
                                        <rect x="35" y="35" width="30" height="30" rx="2" fill="none" stroke="#32bcad" stroke-width="3"/>
                                        <rect x="41" y="41" width="18" height="18" rx="1" fill="#32bcad"/>
                                        <rect x="38" y="90" width="4" height="4" fill="#000"/>
                                        <rect x="44" y="90" width="4" height="4" fill="#000"/>
                                        <rect x="56" y="90" width="4" height="4" fill="#000"/>
                                        <rect x="90" y="38" width="4" height="4" fill="#000"/>
                                        <rect x="90" y="50" width="4" height="4" fill="#000"/>
                                        <rect x="90" y="62" width="4" height="4" fill="#000"/>
                                    </svg>
                                </div>
                                <p style="font-family:'Montserrat',sans-serif; font-size:14px; font-weight:700; color:#00897b; margin-bottom:6px;">
                                    Escaneie o QR Code com seu banco
                                </p>
                                <p style="font-family:'Cabin',sans-serif; font-size:13px; color:#666; margin-bottom:12px;">
                                    Ou copie o código PIX abaixo:
                                </p>
                                <div class="pix-code" id="pix-code-text">
                                    00020126580014br.gov.bcb.pix0136a1b2c3d4-e5f6-7890-abcd-ef1234567890520400005303986540529.605802BR5913CamisetaShop6009SAO PAULO62070503***6304ABCD
                                </div>
                                <button class="btn-copy" onclick="copiarPIX()">
                                    <i class="fa fa-copy" style="margin-right:6px;"></i>Copiar código PIX
                                </button>
                                <div class="pix-timer">
                                    <span id="pix-countdown">14:59</span>
                                    <small>O QR Code expira em</small>
                                </div>
                                <p style="font-family:'Cabin',sans-serif; font-size:12px; color:#aaa; margin-top:10px;">
                                    <i class="fa fa-bolt" style="color:#32bcad;"></i> Pagamento confirmado em segundos!
                                </p>
                            </div>
                        </div>

                        {{-- Painel: Boleto --}}
                        <div class="payment-panel" id="panel-boleto">
                            <div class="boleto-box">
                                <i class="fa fa-barcode" style="font-size:48px; color:#555; margin-bottom:12px;"></i>
                                <p style="font-family:'Montserrat',sans-serif; font-size:15px; font-weight:700; color:#333; margin-bottom:6px;">
                                    Boleto Bancário
                                </p>
                                <p style="font-family:'Cabin',sans-serif; font-size:13px; color:#666; margin-bottom:4px;">
                                    Vencimento: <strong>{{ \Carbon\Carbon::now()->addDays(3)->format('d/m/Y') }}</strong>
                                </p>
                                <p style="font-family:'Cabin',sans-serif; font-size:13px; color:#666; margin-bottom:16px;">
                                    O pedido será confirmado após a compensação do boleto (1-2 dias úteis).
                                </p>
                                <div class="barcode" aria-label="Código de barras simulado">
                                    @php
                                        $bars = [3,1,2,3,1,4,2,1,3,2,1,2,3,1,2,4,1,3,2,1,2,3,2,1,3,4,1,2,3,1,2,3,1,4,2,1,2,3];
                                    @endphp
                                    @foreach($bars as $w)
                                        <span style="width:{{ $w }}px; margin: 0 1px;"></span>
                                    @endforeach
                                </div>
                                <p style="font-family:'Courier New',monospace; font-size:11px; color:#555; margin-bottom:12px; word-break:break-all;">
                                    1234.56789 01234.567890 12345.678901 2 12340000022960
                                </p>
                                <button class="btn-boleto">
                                    <i class="fa fa-download" style="margin-right:8px;"></i>Gerar Boleto PDF
                                </button>
                                <p style="font-family:'Cabin',sans-serif; font-size:11px; color:#aaa; margin-top:12px;">
                                    Você também receberá o boleto por e-mail após confirmar o pedido.
                                </p>
                            </div>
                        </div>

                    </div>
                    <div class="checkout-nav">
                        <button class="btn-prev" onclick="irParaEtapa(2)">
                            <i class="fa fa-arrow-left" style="margin-right:6px;"></i> Voltar
                        </button>
                        <button class="btn-next" onclick="irParaEtapa(4)" style="padding:12px 36px; font-size:13px;">
                            <i class="fa fa-lock" style="margin-right:8px;"></i>CONFIRMAR PEDIDO
                        </button>
                    </div>
                </div>

                {{-- ══ ETAPA 4: Confirmação ══ --}}
                <div class="step-content" id="step-4">
                    <div class="checkout-card">
                        <div class="confirm-box">
                            <i class="fa fa-check-circle confirm-icon"></i>
                            <div class="confirm-title">Pedido Realizado com Sucesso!</div>
                            <div class="confirm-sub">
                                Obrigado pela sua compra! Você receberá um e-mail de confirmação em instantes.
                            </div>
                            <div class="confirm-num">
                                Número do pedido: <strong>#CS-{{ rand(10000, 99999) }}</strong>
                            </div>
                            <div style="background:#f9f9f9; border-radius:6px; padding:20px 24px; text-align:left; margin-bottom:24px;">
                                <h5 style="font-family:'Montserrat',sans-serif; font-size:13px; font-weight:700; text-transform:uppercase; color:#333; margin-bottom:14px;">
                                    Próximos passos:
                                </h5>
                                <div style="display:flex; flex-direction:column; gap:10px;">
                                    <div style="display:flex; gap:12px; align-items:flex-start;">
                                        <span style="width:28px; height:28px; background:#e91e8c; border-radius:50%; color:#fff; display:flex; align-items:center; justify-content:center; font-family:'Montserrat',sans-serif; font-size:12px; font-weight:700; flex-shrink:0;">1</span>
                                        <div><strong style="font-family:'Cabin',sans-serif; font-size:13px;">Confirmação por e-mail</strong><br><span style="font-size:12px; color:#aaa;">Você receberá o comprovante em até 5 minutos</span></div>
                                    </div>
                                    <div style="display:flex; gap:12px; align-items:flex-start;">
                                        <span style="width:28px; height:28px; background:#e91e8c; border-radius:50%; color:#fff; display:flex; align-items:center; justify-content:center; font-family:'Montserrat',sans-serif; font-size:12px; font-weight:700; flex-shrink:0;">2</span>
                                        <div><strong style="font-family:'Cabin',sans-serif; font-size:13px;">Separação e embalagem</strong><br><span style="font-size:12px; color:#aaa;">Seu pedido será separado em até 1 dia útil</span></div>
                                    </div>
                                    <div style="display:flex; gap:12px; align-items:flex-start;">
                                        <span style="width:28px; height:28px; background:#e91e8c; border-radius:50%; color:#fff; display:flex; align-items:center; justify-content:center; font-family:'Montserrat',sans-serif; font-size:12px; font-weight:700; flex-shrink:0;">3</span>
                                        <div><strong style="font-family:'Cabin',sans-serif; font-size:13px;">Envio pelos Correios</strong><br><span style="font-size:12px; color:#aaa;">Prazo de entrega: 5–10 dias úteis</span></div>
                                    </div>
                                </div>
                            </div>
                            <div style="display:flex; gap:12px; justify-content:center; flex-wrap:wrap;">
                                <a href="{{ route('home') }}" class="btn-next" style="text-decoration:none; padding:12px 28px;">
                                    <i class="fa fa-home" style="margin-right:8px;"></i>Voltar à Loja
                                </a>
                                <button class="btn-prev" onclick="window.print()">
                                    <i class="fa fa-print" style="margin-right:6px;"></i>Imprimir
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>{{-- /col formulário --}}

            {{-- Resumo lateral --}}
            <div class="col-md-4 col-sm-12 col-xs-12" id="side-summary-col">
                <div class="side-summary">
                    <h5><i class="fa fa-shopping-bag" style="color:#e91e8c; margin-right:6px;"></i>Resumo do Pedido</h5>

                    <div class="side-item">
                        <img src="{{ asset('vertical/images/t_item2.jpg') }}" alt="" />
                        <div class="side-item-info">
                            <div class="side-item-name">Camiseta Estampada Street Art</div>
                            <div class="side-item-meta">Tam: M · Cor: Preta · Qty: 1</div>
                        </div>
                        <div class="side-item-price">R$69,90</div>
                    </div>

                    <div class="side-item">
                        <img src="{{ asset('vertical/images/t_item1.jpg') }}" alt="" />
                        <div class="side-item-info">
                            <div class="side-item-name">Camiseta Básica Branca</div>
                            <div class="side-item-meta">Tam: G · Cor: Branca · Qty: 2</div>
                        </div>
                        <div class="side-item-price">R$99,80</div>
                    </div>

                    <div class="side-item">
                        <img src="{{ asset('vertical/images/t_item4.jpg') }}" alt="" />
                        <div class="side-item-info">
                            <div class="side-item-name">Camiseta Dry-Fit Sport</div>
                            <div class="side-item-meta">Tam: P · Cor: Azul · Qty: 1</div>
                        </div>
                        <div class="side-item-price">R$59,90</div>
                    </div>

                    <hr class="side-divider">

                    <div class="side-total-row">
                        <span>Subtotal</span>
                        <span>R$229,60</span>
                    </div>
                    <div class="side-total-row">
                        <span><i class="fa fa-truck" style="color:#4caf50; margin-right:4px;"></i>Frete</span>
                        <span class="green">GRÁTIS</span>
                    </div>
                    <div class="side-total-row total">
                        <span>Total</span>
                        <span>R$229,60</span>
                    </div>

                    <div style="margin-top:16px; padding:12px; background:#f0faf9; border-radius:4px; text-align:center;">
                        <i class="fa fa-shield" style="color:#4caf50; margin-right:6px;"></i>
                        <span style="font-family:'Cabin',sans-serif; font-size:12px; color:#555;">Compra 100% segura e protegida</span>
                    </div>
                </div>
            </div>

        </div>{{-- /row --}}
    </div>
</section>

<script>
let etapaAtual = 1;
let pixInterval = null;
let pixSecs = 14 * 60 + 59;

/* ── Navegação entre etapas ── */
function irParaEtapa(n) {
    document.getElementById('step-' + etapaAtual).classList.remove('active');
    atualizarIndicador(etapaAtual, n);
    etapaAtual = n;
    document.getElementById('step-' + n).classList.add('active');
    window.scrollTo({ top: 0, behavior: 'smooth' });

    if (n === 3) iniciarPixTimer();
    if (n === 4) document.getElementById('side-summary-col').style.display = 'none';
}

function atualizarIndicador(de, para) {
    for (let i = 1; i <= 4; i++) {
        const sc = document.getElementById('sc-' + i);
        const sl = document.getElementById('sl-' + i);
        if (i < para) {
            sc.className = 'step-circle done';
            sc.innerHTML = '<i class="fa fa-check"></i>';
            sl.className = 'step-label done';
        } else if (i === para) {
            sc.className = 'step-circle active';
            sc.innerHTML = i;
            sl.className = 'step-label active';
        } else {
            sc.className = 'step-circle';
            sc.innerHTML = i;
            sl.className = 'step-label';
        }
        if (i < 4) {
            document.getElementById('line-' + i).className = 'step-line' + (i < para ? ' done' : '');
        }
    }
}

/* ── Busca CEP via ViaCEP ── */
function buscarCEP() {
    const cep = document.getElementById('end-cep').value.replace(/\D/g, '');
    const spinner = document.getElementById('cep-spinner');
    const errEl   = document.getElementById('cep-error');

    if (cep.length !== 8) { errEl.style.display = 'block'; errEl.textContent = 'CEP inválido — informe 8 dígitos.'; return; }
    spinner.style.display = 'block';
    errEl.style.display   = 'none';

    fetch('https://viacep.com.br/ws/' + cep + '/json/')
        .then(r => r.json())
        .then(data => {
            spinner.style.display = 'none';
            if (data.erro) { errEl.style.display = 'block'; errEl.textContent = 'CEP não encontrado.'; return; }
            document.getElementById('end-rua').value    = data.logradouro || '';
            document.getElementById('end-bairro').value = data.bairro     || '';
            document.getElementById('end-cidade').value = data.localidade  || '';
            const uf = document.getElementById('end-uf');
            for (let i = 0; i < uf.options.length; i++) {
                if (uf.options[i].value === data.uf) { uf.selectedIndex = i; break; }
            }
            document.getElementById('end-num').focus();
        })
        .catch(() => {
            spinner.style.display = 'none';
            errEl.style.display   = 'block';
            errEl.textContent = 'Erro ao buscar CEP. Verifique sua conexão.';
        });
}

/* ── Formas de pagamento ── */
function selecionarPagamento(tipo, el) {
    document.querySelectorAll('.payment-opt').forEach(o => o.classList.remove('selected'));
    el.classList.add('selected');
    document.querySelectorAll('.payment-panel').forEach(p => p.classList.remove('active'));
    document.getElementById('panel-' + tipo).classList.add('active');
    if (tipo === 'pix') iniciarPixTimer();
    else pararPixTimer();
}

/* ── Timer PIX ── */
function iniciarPixTimer() {
    pararPixTimer();
    pixInterval = setInterval(() => {
        if (pixSecs <= 0) { pararPixTimer(); document.getElementById('pix-countdown').textContent = 'Expirado'; return; }
        pixSecs--;
        const m = String(Math.floor(pixSecs / 60)).padStart(2, '0');
        const s = String(pixSecs % 60).padStart(2, '0');
        const el = document.getElementById('pix-countdown');
        if (el) el.textContent = m + ':' + s;
    }, 1000);
}
function pararPixTimer() { if (pixInterval) { clearInterval(pixInterval); pixInterval = null; } }

/* ── Copiar PIX ── */
function copiarPIX() {
    const txt = document.getElementById('pix-code-text').textContent.trim();
    navigator.clipboard.writeText(txt).then(() => alert('Código PIX copiado!')).catch(() => {
        const ta = document.createElement('textarea');
        ta.value = txt; document.body.appendChild(ta); ta.select(); document.execCommand('copy'); document.body.removeChild(ta);
        alert('Código PIX copiado!');
    });
}

/* ── Máscaras ── */
function mascaraTel(el) {
    let v = el.value.replace(/\D/g, '');
    if (v.length > 11) v = v.slice(0, 11);
    if (v.length > 6)      v = '(' + v.slice(0,2) + ') ' + v.slice(2,7) + '-' + v.slice(7);
    else if (v.length > 2) v = '(' + v.slice(0,2) + ') ' + v.slice(2);
    else if (v.length > 0) v = '(' + v;
    el.value = v;
}
function mascaraCPF(el) {
    let v = el.value.replace(/\D/g, '').slice(0, 11);
    if (v.length > 9)      v = v.slice(0,3) + '.' + v.slice(3,6) + '.' + v.slice(6,9) + '-' + v.slice(9);
    else if (v.length > 6) v = v.slice(0,3) + '.' + v.slice(3,6) + '.' + v.slice(6);
    else if (v.length > 3) v = v.slice(0,3) + '.' + v.slice(3);
    el.value = v;
}
function mascaraCEP(el) {
    let v = el.value.replace(/\D/g, '').slice(0, 8);
    if (v.length > 5) v = v.slice(0,5) + '-' + v.slice(5);
    el.value = v;
}
function mascaraCartao(el) {
    let v = el.value.replace(/\D/g, '').slice(0, 16);
    v = v.replace(/(.{4})/g, '$1 ').trim();
    el.value = v;
    document.getElementById('prev-num').textContent = v || '•••• •••• •••• ••••';
}
function mascaraVal(el) {
    let v = el.value.replace(/\D/g, '').slice(0, 4);
    if (v.length > 2) v = v.slice(0,2) + '/' + v.slice(2);
    el.value = v;
    document.getElementById('prev-val').textContent = v || 'MM/AA';
}

/* ── Mostrar/ocultar campo de senha ── */
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('criar-conta').addEventListener('change', function() {
        document.getElementById('senha-wrap').style.display = this.checked ? 'block' : 'none';
    });
});
</script>

@endsection
