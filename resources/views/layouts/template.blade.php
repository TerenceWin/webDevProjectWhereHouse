<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/template.css') }}">

    {{-- yield for the page title --}}
    <title>@yield('title', 'WhereHouse')</title>

    {{-- yield for the page head section (used for individual css files) --}}
    @yield('head')
</head>
<body>
    {{-- top border --}}
    <header class="header">
        <h1 class="title">WHEREHOUSE</h1>
        <nav class="pages">
            <ul>
                <li><a href="{{ route('home') }}">Home</a></li>
                <li><a href="{{ route('login') }}">Login</a></li>
                <li><a href="{{ route('contact') }}">Contact</a></li>
                <li><a href="{{ route('about') }}">About</a></li>
            </ul>
        </nav>
    </header>

    {{-- yield for the page content --}}
    <main class="content">
        @yield('content')
    </main>

    {{-- bottom border --}}
    <footer class="footer">
            <p>© {{ date('Y') }} WhereHouse. All rights reserved.</p>
    </footer>

</body>
</html>