<!DOCTYPE html>
<html lang="pt-BR">

<head>
    @include('partials.head')
</head>

<body>
    <div class="page-wrapper">
        @include('partials.header')

        <main>
            @yield('content')
            @include('site.home.banner')
            @include('site.home.cta')
            @include('site.home.promocao')
            @include('site.home.tendencias')
            @include('site.home.produtos')
            @include('site.home.novidades')
            @include('site.home.depoimentos')
            @include('site.home.marcas')
        </main>

        @include('partials.footer')
    </div>

    @include('partials.script')
</body>

</html>