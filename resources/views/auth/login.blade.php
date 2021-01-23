<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Tela de login para AxionT - Sistema de tarefas" />
        <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1" />
        <meta name="keywords" content="AxionT, Tarefas, Administração, Aplicação">
        <meta name="author" content="MBrauna<eu@mbrauna.com>">


        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'AxionT*') }}</title>

        <!-- Scripts -->
        <script src="{{ asset('js/app.js') }}" defer></script>

        <!-- Fonts -->
        <link rel="dns-prefetch" href="//fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

        <!-- Styles -->
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    </head>
    <body id="corpo" style="min-height: 100vh; min-width: 100vw;">
        <div id="app">
            <div class="container">
                <div class="row d-flex justify-content-center">
                    <div class="col-12 col-sm-12 col-md-5" style="margin-top: 20vh;">
                        <login axiontoken="{{ csrf_token() }}"></login>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>