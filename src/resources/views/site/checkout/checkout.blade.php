@extends('layout.site')

@section('content')

{{-- Breadcrumb --}}
<div class="breadcumb_area">
    <div class="container">
        <div class="bread_box">
            <ul class="breadcumb">
                <li><a href="{{ route('home') }}">Início <span>|</span></a></li>
                <li><a href="#" class="js-cart-open">Carrinho <span>|</span></a></li>
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
                            <i class="fa fa-user-circle-o" style="color:#000;"></i> Identificação
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group-custom">
                                    <label>Nome <span class="obrig">*</span></label>
                                    <input type="text" id="id-nome" class="form-control-custom" placeholder="Nome" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-custom">
                                    <label>Sobrenome <span class="obrig">*</span></label>
                                    <input type="text" id="id-sobrenome" class="form-control-custom" placeholder="Sobrenome" />
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
                                    <label>Data de nascimento</label>
                                    <input type="date" id="id-nasc" class="form-control-custom" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label class="checkout-optin">
                                    <input type="checkbox" id="opt-novidades-email" checked />
                                    Enviar novidades e ofertas para mim por e-mail
                                </label>
                            </div>
                            <div class="col-md-12">
                                <div style="background:#f5f5f5; border-radius:4px; padding:14px 16px; margin-top:4px;">
                                    <label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-size:13px; color:#555; margin:0; font-weight:normal; text-transform:none; letter-spacing:0;">
                                        <input type="checkbox" id="criar-conta" style="width:16px; height:16px; accent-color:#000;" />
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
                        <a href="#" class="js-cart-open" style="font-size:13px; color:#aaa; text-decoration:none;">
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
                            <i class="fa fa-map-marker" style="color:#000;"></i> Endereço de Entrega
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
                                       style="font-size:11px; color:#000; margin-top:4px; display:inline-block;">
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
                            <div class="col-md-12">
                                <label class="checkout-optin">
                                    <input type="checkbox" id="opt-salvar-info" />
                                    Salvar minhas informações para a próxima vez
                                </label>
                                <label class="checkout-optin">
                                    <input type="checkbox" id="opt-novidades-sms" />
                                    Enviar novidades e ofertas para mim por SMS
                                </label>
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
                            <i class="fa fa-credit-card" style="color:#000;"></i> Forma de Pagamento
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
                                        {{-- Marcadores de canto --}}
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
                                <p style="font-size:14px; font-weight:700; color:#00897b; margin-bottom:6px;">
                                    Escaneie o QR Code com seu banco
                                </p>
                                <p style="font-size:13px; color:#666; margin-bottom:12px;">
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
                                <p style="font-size:12px; color:#aaa; margin-top:10px;">
                                    <i class="fa fa-bolt" style="color:#32bcad;"></i> Pagamento confirmado em segundos!
                                </p>
                            </div>
                        </div>

                        {{-- Painel: Boleto --}}
                        <div class="payment-panel" id="panel-boleto">
                            <div class="boleto-box">
                                <i class="fa fa-barcode" style="font-size:48px; color:#555; margin-bottom:12px;"></i>
                                <p style="font-size:15px; font-weight:700; color:#333; margin-bottom:6px;">
                                    Boleto Bancário
                                </p>
                                <p style="font-size:13px; color:#666; margin-bottom:4px;">
                                    Vencimento: <strong>{{ \Carbon\Carbon::now()->addDays(3)->format('d/m/Y') }}</strong>
                                </p>
                                <p style="font-size:13px; color:#666; margin-bottom:16px;">
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
                                <p style="font-size:11px; color:#aaa; margin-top:12px;">
                                    Você também receberá o boleto por e-mail após confirmar o pedido.
                                </p>
                            </div>
                        </div>

                    </div>

                    {{-- Endereço de faturamento --}}
                    <div class="checkout-card">
                        <div class="checkout-card-title">
                            <i class="fa fa-file-text-o" style="color:#000;"></i> Endereço de Faturamento
                        </div>

                        <div class="billing-toggle">
                            <div class="billing-opt selected" id="billing-opt-mesmo" onclick="selecionarFaturamento('mesmo', this)">
                                <span class="billing-radio"></span>
                                Usar o endereço de entrega
                            </div>
                            <div class="billing-opt" id="billing-opt-diferente" onclick="selecionarFaturamento('diferente', this)">
                                <span class="billing-radio"></span>
                                Usar um endereço de faturamento diferente
                            </div>
                        </div>

                        <div class="billing-address-form" id="billing-address-form">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group-custom">
                                        <label>Nome <span class="obrig">*</span></label>
                                        <input type="text" id="fat-nome" class="form-control-custom" placeholder="Nome" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group-custom">
                                        <label>Sobrenome <span class="obrig">*</span></label>
                                        <input type="text" id="fat-sobrenome" class="form-control-custom" placeholder="Sobrenome" />
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group-custom">
                                        <label>CEP <span class="obrig">*</span></label>
                                        <input type="text" id="fat-cep" class="form-control-custom" placeholder="00000-000" maxlength="9" oninput="mascaraCEP(this)" />
                                    </div>
                                </div>
                                <div class="col-md-7">
                                    <div class="form-group-custom">
                                        <label>Endereço <span class="obrig">*</span></label>
                                        <input type="text" id="fat-rua" class="form-control-custom" placeholder="Rua, Avenida, etc." />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group-custom">
                                        <label>Número <span class="obrig">*</span></label>
                                        <input type="text" id="fat-num" class="form-control-custom" placeholder="Nº" />
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group-custom">
                                        <label>Apartamento, bloco etc. <span style="color:#aaa; font-weight:400;">(opcional)</span></label>
                                        <input type="text" id="fat-comp" class="form-control-custom" placeholder="Apto, Bloco, etc." />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group-custom">
                                        <label>Bairro <span class="obrig">*</span></label>
                                        <input type="text" id="fat-bairro" class="form-control-custom" placeholder="Bairro" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group-custom">
                                        <label>Cidade <span class="obrig">*</span></label>
                                        <input type="text" id="fat-cidade" class="form-control-custom" placeholder="Cidade" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group-custom">
                                        <label>Estado <span class="obrig">*</span></label>
                                        <select id="fat-uf" class="form-control-custom">
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
                                <div class="col-md-12">
                                    <div class="form-group-custom" style="margin-bottom:0;">
                                        <label>Telefone <span style="color:#aaa; font-weight:400;">(opcional)</span></label>
                                        <input type="tel" id="fat-tel" class="form-control-custom" placeholder="(11) 99999-0000" oninput="mascaraTel(this)" maxlength="15" />
                                    </div>
                                </div>
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
                            <i class="fa fa-check-circle confirm-icon" id="confirm-icon" style="color:#4caf50;"></i>
                            <div class="confirm-title" id="confirm-title">Pedido Confirmado!</div>
                            <div class="confirm-sub" id="confirm-sub">
                                Pagamento aprovado! Você receberá um e-mail de confirmação em instantes.
                            </div>
                            <div class="confirm-num">
                                Número do pedido: <strong id="confirm-num-value">—</strong>
                            </div>
                            <div id="confirm-status-box" style="display:none; background:#fff8e1; border:1px solid #ffe082; border-radius:6px; padding:14px 18px; text-align:left; margin-bottom:20px;">
                                <i class="fa fa-clock-o" style="color:#f5a623; margin-right:8px;"></i>
                                <span id="confirm-status-text" style="font-size:13px; color:#8a6d00;"></span>
                            </div>
                            <div style="background:#f9f9f9; border-radius:6px; padding:20px 24px; text-align:left; margin-bottom:24px;">
                                <h5 style="font-size:13px; font-weight:700; text-transform:uppercase; color:#333; margin-bottom:14px;">
                                    Próximos passos:
                                </h5>
                                <div style="display:flex; flex-direction:column; gap:10px;" id="confirm-steps">
                                    {{-- Preenchido dinamicamente conforme o método de pagamento --}}
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
                    <h5><i class="fa fa-shopping-bag" style="color:#000; margin-right:6px;"></i>Resumo do Pedido</h5>

                    <div id="checkout-side-items">
                        {{-- Preenchido dinamicamente via JS a partir do carrinho (cart.js) --}}
                    </div>

                    <div id="checkout-side-empty" style="display:none; padding: 16px 0; text-align:center; color:#aaa; font-size:13px;">
                        Seu carrinho está vazio.
                    </div>

                    <hr class="side-divider">

                    @if ($cupomUtilizavel ?? false)
                    <div style="margin-bottom:12px;">
                        <div style="display:flex; gap:6px;">
                            <input type="text" id="cupom-input" placeholder="Código do cupom"
                                   style="flex:1; padding:8px; border:1px solid #ddd; border-radius:4px; text-transform:uppercase; font-size:13px;">
                            <button type="button" id="cupom-aplicar-btn"
                                    style="padding:8px 14px; border:none; border-radius:4px; background:#000; color:#fff; cursor:pointer; font-size:13px;">
                                Aplicar
                            </button>
                        </div>
                        <div id="cupom-mensagem" style="font-size:12px; margin-top:6px; display:none;"></div>
                    </div>
                    @endif

                    <div class="side-total-row">
                        <span>Subtotal</span>
                        <span id="checkout-side-subtotal">R$ 0,00</span>
                    </div>
                    <div class="side-total-row" id="checkout-side-frete-row">
                        <span><i class="fa fa-truck" style="color:#4caf50; margin-right:4px;"></i>Frete</span>
                        <span class="green" id="checkout-side-frete">GRÁTIS</span>
                    </div>
                    <div class="side-total-row" id="checkout-side-desconto-row" style="display:none;">
                        <span>Desconto</span>
                        <span class="green" id="checkout-side-desconto">-R$ 0,00</span>
                    </div>
                    <div class="side-total-row total">
                        <span>Total</span>
                        <span id="checkout-side-total">R$ 0,00</span>
                    </div>

                    <div style="margin-top:16px; padding:12px; background:#f0faf9; border-radius:4px; text-align:center;">
                        <i class="fa fa-shield" style="color:#4caf50; margin-right:6px;"></i>
                        <span style="font-size:12px; color:#555;">Compra 100% segura e protegida</span>
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
let metodoPagamento = 'cartao';
let faturamentoDiferente = false;

/* ── Validação de campos obrigatórios por etapa ── */
function validarEtapa(etapa) {
    const camposPorEtapa = {
        1: ['id-nome', 'id-sobrenome', 'id-email', 'id-tel'],
        2: ['end-cep', 'end-rua', 'end-num', 'end-bairro', 'end-cidade', 'end-uf'],
    };
    const ids = camposPorEtapa[etapa] || [];

    let valido = true;
    let primeiroInvalido = null;

    ids.forEach(id => {
        const campo = document.getElementById(id);
        if (!campo) return;
        const vazio = !campo.value || !campo.value.trim();
        campo.classList.toggle('error', vazio);
        if (vazio) {
            valido = false;
            if (!primeiroInvalido) primeiroInvalido = campo;
        }
    });

    if (etapa === 3) {
        const pagamentoSelecionado = document.querySelector('.payment-opt.selected');
        if (!pagamentoSelecionado) {
            valido = false;
        } else {
            const tipo = pagamentoSelecionado.getAttribute('onclick').match(/'([a-z]+)'/)[1];
            if (tipo === 'cartao') {
                ['cc-num', 'cc-nome', 'cc-val', 'cc-cvv'].forEach(id => {
                    const campo = document.getElementById(id);
                    if (!campo) return;
                    const vazio = !campo.value || !campo.value.trim();
                    campo.classList.toggle('error', vazio);
                    if (vazio) {
                        valido = false;
                        if (!primeiroInvalido) primeiroInvalido = campo;
                    }
                });
            }
        }

        if (faturamentoDiferente) {
            ['fat-nome', 'fat-sobrenome', 'fat-cep', 'fat-rua', 'fat-num', 'fat-bairro', 'fat-cidade', 'fat-uf'].forEach(id => {
                const campo = document.getElementById(id);
                if (!campo) return;
                const vazio = !campo.value || !campo.value.trim();
                campo.classList.toggle('error', vazio);
                if (vazio) {
                    valido = false;
                    if (!primeiroInvalido) primeiroInvalido = campo;
                }
            });
        }
    }

    if (!valido) {
        if (primeiroInvalido) primeiroInvalido.focus();
        alert('Preencha todos os campos obrigatórios antes de continuar.');
    }

    return valido;
}

/* ── Navegação entre etapas ── */
function irParaEtapa(n) {
    if (n > etapaAtual && !validarEtapa(etapaAtual)) return;

    if (n === 4) {
        finalizarPedido();
        return;
    }

    document.getElementById('step-' + etapaAtual).classList.remove('active');
    atualizarIndicador(etapaAtual, n);
    etapaAtual = n;
    document.getElementById('step-' + n).classList.add('active');
    window.scrollTo({ top: 0, behavior: 'smooth' });

    if (n === 3) iniciarPixTimer();
}

/* ── Envia o pedido pro servidor (grava Order/OrderItem de verdade) ── */
let ultimoPedido = null;

function csrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
}

function coletarDadosPedido() {
    const itens = window.VerticalCart ? window.VerticalCart.load() : [];

    const enderecoEntrega = {
        cep: document.getElementById('end-cep').value,
        rua: document.getElementById('end-rua').value,
        numero: document.getElementById('end-num').value,
        complemento: document.getElementById('end-comp').value,
        bairro: document.getElementById('end-bairro').value,
        cidade: document.getElementById('end-cidade').value,
        uf: document.getElementById('end-uf').value,
        referencia: document.getElementById('end-ref').value,
    };

    let enderecoFaturamento = null;
    if (faturamentoDiferente) {
        enderecoFaturamento = {
            nome: document.getElementById('fat-nome').value,
            sobrenome: document.getElementById('fat-sobrenome').value,
            cep: document.getElementById('fat-cep').value,
            rua: document.getElementById('fat-rua').value,
            numero: document.getElementById('fat-num').value,
            complemento: document.getElementById('fat-comp').value,
            bairro: document.getElementById('fat-bairro').value,
            cidade: document.getElementById('fat-cidade').value,
            uf: document.getElementById('fat-uf').value,
            telefone: document.getElementById('fat-tel').value,
        };
    }

    return {
        nome: document.getElementById('id-nome').value,
        sobrenome: document.getElementById('id-sobrenome').value,
        email: document.getElementById('id-email').value,
        telefone: document.getElementById('id-tel').value,
        forma_pagamento: metodoPagamento,
        endereco_entrega: enderecoEntrega,
        endereco_faturamento: enderecoFaturamento,
        items: itens.map(item => ({ id: item.id, cor: item.cor, tamanho: item.tamanho, qty: item.qty })),
        // Manda só o CÓDIGO -- quem calcula o desconto é sempre o servidor,
        // que revalida do zero (o cupom pode ter expirado nos últimos
        // minutos do checkout).
        cupom: cupomAplicado ? cupomAplicado.codigo : null,
    };
}

function finalizarPedido() {
    const itens = window.VerticalCart ? window.VerticalCart.load() : [];
    if (itens.length === 0) {
        alert('Seu carrinho está vazio.');
        return;
    }

    const btn = document.querySelector('#step-3 .btn-next');
    if (btn) { btn.disabled = true; btn.style.opacity = '0.6'; }

    fetch('{{ route('checkout.finalizar') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken(),
        },
        body: JSON.stringify(coletarDadosPedido()),
    })
        .then(r => r.ok ? r.json() : r.json().then(err => { throw err; }))
        .then(resultado => {
            ultimoPedido = resultado;
            if (window.VerticalCart) window.VerticalCart.clear();

            document.getElementById('step-3').classList.remove('active');
            atualizarIndicador(3, 4);
            etapaAtual = 4;
            document.getElementById('step-4').classList.add('active');
            window.scrollTo({ top: 0, behavior: 'smooth' });
            document.getElementById('side-summary-col').style.display = 'none';
            renderConfirmacao();
        })
        .catch(err => {
            alert((err && err.message) || 'Não foi possível finalizar o pedido. Verifique os dados e tente novamente.');
        })
        .finally(() => {
            if (btn) { btn.disabled = false; btn.style.opacity = ''; }
        });
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
    metodoPagamento = tipo;
    document.querySelectorAll('.payment-opt').forEach(o => o.classList.remove('selected'));
    el.classList.add('selected');
    document.querySelectorAll('.payment-panel').forEach(p => p.classList.remove('active'));
    document.getElementById('panel-' + tipo).classList.add('active');
    if (tipo === 'pix') iniciarPixTimer();
    else pararPixTimer();
}

/* ── Endereço de faturamento: mesmo endereço da entrega, ou um diferente ── */
function selecionarFaturamento(tipo, el) {
    faturamentoDiferente = tipo === 'diferente';
    document.querySelectorAll('.billing-opt').forEach(o => o.classList.remove('selected'));
    el.classList.add('selected');
    document.getElementById('billing-address-form').classList.toggle('open', faturamentoDiferente);
}

/* ── Tela de confirmação — muda conforme o método de pagamento ──
   Cartão: aprovação imediata, pedido confirmado.
   PIX / Boleto: pagamento assíncrono, pedido fica pendente até a confirmação. */
function renderConfirmacao() {
    const icon = document.getElementById('confirm-icon');
    const title = document.getElementById('confirm-title');
    const sub = document.getElementById('confirm-sub');
    const statusBox = document.getElementById('confirm-status-box');
    const statusText = document.getElementById('confirm-status-text');
    const stepsEl = document.getElementById('confirm-steps');
    const numEl = document.getElementById('confirm-num-value');
    if (numEl) numEl.textContent = '#' + (ultimoPedido ? ultimoPedido.numero_pedido : '—');

    if (metodoPagamento === 'cartao') {
        icon.style.color = '#4caf50';
        icon.className = 'fa fa-check-circle confirm-icon';
        title.textContent = 'Pedido Confirmado!';
        sub.textContent = 'Pagamento aprovado! Você receberá um e-mail de confirmação em instantes.';
        statusBox.style.display = 'none';
        stepsEl.innerHTML = passoConfirmacao(1, 'Confirmação por e-mail', 'Você receberá o comprovante em até 5 minutos')
            + passoConfirmacao(2, 'Separação e embalagem', 'Seu pedido será separado em até 1 dia útil')
            + passoConfirmacao(3, 'Envio pelos Correios', 'Prazo de entrega: 5–10 dias úteis');
    } else {
        icon.style.color = '#f5a623';
        icon.className = 'fa fa-clock-o confirm-icon';
        title.textContent = 'Pedido Recebido — Pagamento Pendente';
        sub.textContent = 'Falta só o pagamento para confirmarmos seu pedido.';
        statusBox.style.display = 'block';
        statusText.textContent = metodoPagamento === 'pix'
            ? 'Aguardando pagamento via PIX. Assim que identificarmos o pagamento, seu pedido será confirmado automaticamente.'
            : 'Aguardando compensação do boleto (1–2 dias úteis). Seu pedido só será separado após a confirmação do pagamento.';
        stepsEl.innerHTML = passoConfirmacao(1, 'Aguardando pagamento', metodoPagamento === 'pix' ? 'Confirmação em poucos minutos após o pagamento' : 'Compensação em até 2 dias úteis')
            + passoConfirmacao(2, 'Separação e embalagem', 'Iniciada somente após confirmação do pagamento')
            + passoConfirmacao(3, 'Envio pelos Correios', 'Prazo de entrega: 5–10 dias úteis após a separação');
    }
}

function passoConfirmacao(numero, titulo, texto) {
    return '<div style="display:flex; gap:12px; align-items:flex-start;">'
        + '<span style="width:28px; height:28px; background:#000; border-radius:50%; color:#fff; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:700; flex-shrink:0;">' + numero + '</span>'
        + '<div><strong style="font-size:13px;">' + titulo + '</strong><br><span style="font-size:12px; color:#aaa;">' + texto + '</span></div>'
        + '</div>';
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

/* ── Resumo do pedido — lê os itens reais do carrinho (VerticalCart / localStorage) ── */
const FRETE_GRATIS_MINIMO = 150;

// { codigo, desconto } do último cupom validado com sucesso, ou null. Só
// preview -- o servidor revalida do zero em /checkout/finalizar, nunca
// confia neste valor.
let cupomAplicado = null;
let cupomValidando = false;

function mostrarMensagemCupom(texto, cor) {
    const msgEl = document.getElementById('cupom-mensagem');
    msgEl.textContent = texto;
    msgEl.style.color = cor;
    msgEl.style.display = 'block';
}

async function aplicarCupom() {
    const input = document.getElementById('cupom-input');
    const codigo = input.value.trim();
    if (!codigo || cupomValidando) return;

    const itens = window.VerticalCart ? window.VerticalCart.load() : [];
    if (itens.length === 0) return;

    cupomValidando = true;
    const btn = document.getElementById('cupom-aplicar-btn');
    if (btn) btn.disabled = true;
    document.getElementById('cupom-mensagem').style.display = 'none';

    try {
        const resp = await fetch('{{ route('checkout.cupom') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken(),
            },
            body: JSON.stringify({
                codigo,
                items: itens.map(item => ({ id: item.id, cor: item.cor, tamanho: item.tamanho, qty: item.qty })),
            }),
        });

        if (resp.status === 429) {
            cupomAplicado = null;
            mostrarMensagemCupom('Muitas tentativas. Aguarde um instante e tente de novo.', '#c0392b');
        } else {
            const resultado = await resp.json();
            if (resp.ok && resultado.valido) {
                cupomAplicado = { codigo, desconto: resultado.desconto };
                mostrarMensagemCupom('Cupom aplicado!', '#2e7d32');
            } else {
                cupomAplicado = null;
                mostrarMensagemCupom(resultado.mensagem || 'Não foi possível aplicar o cupom.', '#c0392b');
            }
        }
    } catch (e) {
        cupomAplicado = null;
        mostrarMensagemCupom('Erro ao validar o cupom. Tente novamente.', '#c0392b');
    } finally {
        cupomValidando = false;
        if (btn) btn.disabled = false;
        renderResumoCheckout();
    }
}

function renderResumoCheckout() {
    if (!window.VerticalCart) return;

    const itens = window.VerticalCart.load();
    const itemsEl = document.getElementById('checkout-side-items');
    const emptyEl = document.getElementById('checkout-side-empty');
    const freteRowEl = document.getElementById('checkout-side-frete-row');

    if (itens.length === 0) {
        itemsEl.innerHTML = '';
        emptyEl.style.display = 'block';
        freteRowEl.style.display = 'none';
    } else {
        emptyEl.style.display = 'none';
        freteRowEl.style.display = 'flex';
        itemsEl.innerHTML = itens.map(item => {
            const meta = [];
            if (item.tamanho) meta.push('Tam: ' + item.tamanho);
            if (item.cor) meta.push('Cor: ' + item.cor);
            meta.push('Qty: ' + item.qty);
            return `
                <div class="side-item">
                    <img src="${item.imagem}" alt="" />
                    <div class="side-item-info">
                        <div class="side-item-name">${item.nome}</div>
                        <div class="side-item-meta">${meta.join(' · ')}</div>
                    </div>
                    <div class="side-item-price">${window.VerticalCart.formatBRL(item.preco * item.qty)}</div>
                </div>
            `;
        }).join('');
    }

    const subtotal = window.VerticalCart.subtotal(itens);
    // Frete decidido pelo subtotal CHEIO, antes do desconto -- mesma regra
    // do servidor (PedidoCalculoService), preview tem que bater com o real.
    const freteGratis = itens.length === 0 || subtotal >= FRETE_GRATIS_MINIMO;
    const frete = freteGratis ? 0 : 19.90;
    const desconto = cupomAplicado ? cupomAplicado.desconto : 0;
    const total = Math.max(0, subtotal - desconto + frete);

    document.getElementById('checkout-side-subtotal').textContent = window.VerticalCart.formatBRL(subtotal);
    document.getElementById('checkout-side-frete').textContent = freteGratis ? 'GRÁTIS' : window.VerticalCart.formatBRL(frete);

    const descontoRowEl = document.getElementById('checkout-side-desconto-row');
    if (desconto > 0) {
        descontoRowEl.style.display = 'flex';
        document.getElementById('checkout-side-desconto').textContent = '-' + window.VerticalCart.formatBRL(desconto);
    } else {
        descontoRowEl.style.display = 'none';
    }

    document.getElementById('checkout-side-total').textContent = window.VerticalCart.formatBRL(total);
}

document.addEventListener('DOMContentLoaded', function() {
    renderResumoCheckout();
    // Bloco de cupom pode não existir no DOM (nenhum cupom utilizável agora
    // -- ver CheckoutController::checkout()); os elementos abaixo saem null
    // nesse caso, então o listener só é preso quando eles realmente existem.
    const cupomBtn = document.getElementById('cupom-aplicar-btn');
    const cupomInput = document.getElementById('cupom-input');
    if (cupomBtn) cupomBtn.addEventListener('click', aplicarCupom);
    if (cupomInput) cupomInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') { e.preventDefault(); aplicarCupom(); }
    });
});
</script>

@endsection
