<style>
.fullwidthbanner-container {
    background: linear-gradient(135deg, #fff5f7 0%, #ffffff 45%, #ffe8ec 100%) !important;
}
/* Botão do slider */
.slider-btn {
    display: inline-block;
    padding: 14px 40px;
    background: #e91e8c;
    color: #fff !important;
    font-family: 'Montserrat', sans-serif;
    font-size: 14px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 2px;
    text-decoration: none !important;
    border-radius: 3px;
    box-shadow: 0 4px 15px rgba(233, 30, 140, 0.4);
    transition: background 0.2s, transform 0.2s;
}
.slider-btn:hover {
    background: #c2185b;
    transform: translateY(-2px);
    color: #fff !important;
}
</style>

<div class="slider-wrap">
    <div class="fullwidthbanner-container">
        <div class="fullwidthbanner">
            <ul>

                {{-- =====================================================
                     SLIDE 1 — Nova Coleção Verão 2025
                     Substitua slider-temp.jpg por uma foto de camiseta
                     em alta resolução (ideal: 1920x650px)
                     ===================================================== --}}
                <li data-transition="random" data-slotamount="7" data-masterspeed="1000">
                    <img src="{{ asset('vertical/images/slider-temp.jpg') }}" alt="Nova Coleção Verão 2025">

                    {{-- Overlay gradiente branco → rosa --}}
                    <div class="tp-caption" data-x="0" data-y="0" data-speed="200" data-start="0"
                        style="width:1920px; height:650px; background: linear-gradient(110deg, rgba(255,255,255,0.92) 0%, rgba(255,255,255,0.75) 55%, rgba(233,30,140,0.18) 100%); z-index:1; pointer-events:none;">
                    </div>

                    <div class="tp-caption large_black sfr" data-x="120" data-y="130" data-speed="1100" data-start="1100" data-easing="easeInOutBack"
                        style="font-size:85px; font-weight:normal; text-transform:inherit; color:#e91e8c; font-family:manustrialmanustrial; font-style:italic; text-shadow:0px 0px 4px rgba(255,255,255,0.8); z-index:5;">
                        Nova Coleção
                    </div>

                    <div class="tp-caption large_black sfr" data-x="120" data-y="220" data-speed="1100" data-start="1400" data-easing="easeInOutBack"
                        style="font-family:'Montserrat',sans-serif; font-size:62px; font-weight:700; line-height:75px; text-transform:uppercase; color:#232323; text-shadow:0 0 6px rgba(255,255,255,0.9); z-index:5;">
                        Verão 2025
                    </div>

                    <div class="tp-caption lfb" data-x="122" data-y="320" data-speed="1100" data-start="1600" data-easing="easeInOutBack"
                        style="font-family:'Cabin',sans-serif; font-size:18px; font-weight:600; color:#555; text-shadow:0 0 4px rgba(255,255,255,0.9); z-index:5;">
                        Camisetas 100% algodão com estampas exclusivas
                    </div>

                    <div class="tp-caption lfb" data-x="120" data-y="380" data-speed="1300" data-start="1900" data-easing="easeInOutBack" style="z-index:5;">
                        <a href="{{ route('loja') }}" class="slider-btn">VER COLEÇÃO</a>
                    </div>
                </li>

                {{-- =====================================================
                     SLIDE 2 — Camisetas Estampadas
                     Substitua slider-temp2.jpg por uma foto de camiseta
                     estampada em alta resolução (ideal: 1920x650px)
                     ===================================================== --}}
                <li data-transition="random" data-slotamount="7" data-masterspeed="1000">
                    <img src="{{ asset('vertical/images/slider-temp2.jpg') }}" alt="Camisetas Estampadas">

                    <div class="tp-caption" data-x="0" data-y="0" data-speed="200" data-start="0"
                        style="width:1920px; height:650px; background: linear-gradient(110deg, rgba(255,255,255,0.92) 0%, rgba(255,255,255,0.75) 55%, rgba(233,30,140,0.18) 100%); z-index:1; pointer-events:none;">
                    </div>

                    <div class="tp-caption large_black sfr" data-x="120" data-y="130" data-speed="1100" data-start="1100" data-easing="easeInOutBack"
                        style="font-size:85px; font-weight:normal; text-transform:inherit; color:#e91e8c; font-family:manustrialmanustrial; font-style:italic; text-shadow:0px 0px 4px rgba(255,255,255,0.8); z-index:5;">
                        Estampadas
                    </div>

                    <div class="tp-caption large_black sfr" data-x="120" data-y="220" data-speed="1100" data-start="1400" data-easing="easeInOutBack"
                        style="font-family:'Montserrat',sans-serif; font-size:62px; font-weight:700; line-height:75px; text-transform:uppercase; color:#232323; text-shadow:0 0 6px rgba(255,255,255,0.9); z-index:5;">
                        Camisetas
                    </div>

                    <div class="tp-caption lfb" data-x="122" data-y="320" data-speed="1100" data-start="1600" data-easing="easeInOutBack"
                        style="font-family:'Cabin',sans-serif; font-size:18px; font-weight:600; color:#555; text-shadow:0 0 4px rgba(255,255,255,0.9); z-index:5;">
                        Mais de 50 modelos diferentes — do básico ao criativo
                    </div>

                    <div class="tp-caption lfb" data-x="120" data-y="380" data-speed="1300" data-start="1900" data-easing="easeInOutBack" style="z-index:5;">
                        <a href="{{ route('loja') }}" class="slider-btn">COMPRAR AGORA</a>
                    </div>
                </li>

                {{-- =====================================================
                     SLIDE 3 — Frete Grátis + 10% OFF
                     Substitua slider-temp.jpg por uma foto de promoção /
                     vitrine de camisetas (ideal: 1920x650px)
                     ===================================================== --}}
                <li data-transition="random" data-slotamount="7" data-masterspeed="1000">
                    <img src="{{ asset('vertical/images/slider-temp.jpg') }}" alt="Frete Grátis + 10% OFF">

                    <div class="tp-caption" data-x="0" data-y="0" data-speed="200" data-start="0"
                        style="width:1920px; height:650px; background: linear-gradient(110deg, rgba(255,255,255,0.92) 0%, rgba(255,255,255,0.75) 55%, rgba(233,30,140,0.18) 100%); z-index:1; pointer-events:none;">
                    </div>

                    <div class="tp-caption large_black sfr" data-x="120" data-y="130" data-speed="1100" data-start="1100" data-easing="easeInOutBack"
                        style="font-size:85px; font-weight:normal; text-transform:inherit; color:#e91e8c; font-family:manustrialmanustrial; font-style:italic; text-shadow:0px 0px 4px rgba(255,255,255,0.8); z-index:5;">
                        Oferta Especial
                    </div>

                    <div class="tp-caption large_black sfr" data-x="120" data-y="220" data-speed="1100" data-start="1400" data-easing="easeInOutBack"
                        style="font-family:'Montserrat',sans-serif; font-size:52px; font-weight:700; line-height:65px; text-transform:uppercase; color:#232323; text-shadow:0 0 6px rgba(255,255,255,0.9); z-index:5;">
                        Frete Grátis + 10% OFF
                    </div>

                    <div class="tp-caption lfb" data-x="122" data-y="315" data-speed="1100" data-start="1600" data-easing="easeInOutBack"
                        style="font-family:'Cabin',sans-serif; font-size:18px; font-weight:600; color:#555; text-shadow:0 0 4px rgba(255,255,255,0.9); z-index:5;">
                        Na primeira compra use o cupom: <strong style="color:#e91e8c; font-size:20px;">BEMVINDO10</strong>
                    </div>

                    <div class="tp-caption lfb" data-x="120" data-y="375" data-speed="1300" data-start="1900" data-easing="easeInOutBack" style="z-index:5;">
                        <a href="{{ route('loja') }}" class="slider-btn">APROVEITAR</a>
                    </div>
                </li>

            </ul>
        </div>
    </div>
</div>
