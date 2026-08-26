<!DOCTYPE html>
<html lang="pt-BR">

<head>
    @include('partials.head')
</head>

<body data-logged-in="{{ auth()->check() ? '1' : '0' }}" data-just-logged-in="{{ session('just_logged_in') ? '1' : '0' }}">
    <div class="page-wrapper">
        @include('partials.header')

        <main>
            @yield('content')
        </main>

        @include('partials.footer')

        @include('partials.cart-drawer')
    </div>

    @include('partials.script')
</body>

</html>