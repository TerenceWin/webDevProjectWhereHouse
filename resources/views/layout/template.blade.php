<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    {{-- yield for title set on different pages --}}
    <title>@yield('title', 'WhereHouse')</title>

    {{-- link to css --}}
    <link rel="stylesheet" href="{{ asset('css/template.css') }}">
</head>
<body>

    {{-- top border --}}
    <header class="site-header">
        <div class="container">
            <h1 class="site-title">WhereHouse</h1>
            <nav class="navbar">
                <ul>
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li><a href="{{ route('login') }}">Login</a></li>
                    <li><a href="{{ route('contact') }}">Contact</a></li>
                    <li><a href="{{ route('about') }}">About</a></li>
                </ul>
            </nav>
        </div>
    </header>

    {{-- yield page content --}}
    <main class="site-content container">
        @yield('content')
    </main>

    {{-- bottom border --}}
    <footer class="site-footer">
        <div class="container">
            &copy; {{ date('Y') }} WhereHouse. All rights reserved.
        </div>
    </footer>

</body>
</html>
