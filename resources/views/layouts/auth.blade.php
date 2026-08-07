<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'SIAP - Login')</title>

    <!-- Bootstrap 5 + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Google Font: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">

    <!-- Global Design System -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <style>
        /* Reset & centering */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body.auth-page {
            background: var(--ui-bg, #f1f5f9);
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            min-height: 100vh;
            min-height: 100dvh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            margin: 0;
        }

        .auth-wrapper {
            width: 100%;
            max-width: 1024px;
            margin: 0 auto;
        }

        @media (max-width: 576px) {
            body.auth-page {
                padding: 0.75rem;
                align-items: flex-start;
                padding-top: 1.5rem;
            }
        }
    </style>

    @stack('styles')
</head>
<body class="auth-page">
    <div class="auth-wrapper">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>