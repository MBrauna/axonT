<!doctype html>
<html lang="pt-br" class="no-js">
    <head>
        <!-- Mobile Specific Meta -->
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <!-- Favicon-->
        <link rel="shortcut icon" href="/favico.ico">
        <!-- Author Meta -->
        <meta name="author" content="MBrauna - 1nesstech <michel.brauna@1nesstech.com.br>">
        <!-- Meta Description -->
        <meta name="description" content="AxionT - Sistema de gestão empresarial">
        <!-- Meta Keyword -->
        <meta name="keywords" content="AxionT, Tarefas, Administração, Aplicação">
        <!-- meta character set -->
        <meta charset="UTF-8">
        <!-- Token de sessão -->
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="useToken" content="{{ generateTokenOaut() }}"

        <!-- Site Title -->
        <title>@yield('title') - {{ config('app.name', 'AxionT*') }}</title>
        <!-- Arquivos principais -->
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    </head>
    <body class="corpo-bg">
        <div class="container-fluid" id="app">
            <menu-app csrf="{{ csrf_token() }}" user="{{ json_encode(Auth::user()) }}"></menu-app>

            <div class="body" style="padding-top: 4.5em">
                @yield('body')
            </div>
        </div>

        <script type="text/javascript" src="{{ asset('js/app.js') }}"></script>
    </body>
</html>