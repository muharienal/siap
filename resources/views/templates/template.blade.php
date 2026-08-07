<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'SIAP - Sistem Administrasi Rapat')</title>

    @include('partials.style')
    @stack('custom-css')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

    <div class="app-wrapper">

        @include('partials.sidebar')

        <div class="app-content">

            @include('partials.header')

            <main style="flex:1; display:flex; flex-direction:column;">
                @yield('content')
            </main>

            @include('partials.footer')

        </div>

    </div>

    <div class="toast-container" id="toastContainer"></div>

    @include('partials.script')
    @stack('scripts')

</body>
</html>