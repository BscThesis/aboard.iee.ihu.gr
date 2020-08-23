<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="has-navbar-fixed-top">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title')</title>

    @include('feed::links')

    <!-- Imports -->
    <link rel="stylesheet" href="/css/app.css">
</head>

<body>
    <div id="app">
        <navbar-component-bulma></navbar-component-bulma>

        <div class="container block">
            @yield('content')
        </div>

        <footer-component></footer-component>

    </div>

    <!-- Imports -->
    <script src="/js/app.js"></script>
</body>

</html>