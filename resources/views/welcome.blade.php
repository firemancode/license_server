<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

        <!-- Styles -->
        <style>
            body {
                font-family: 'Figtree', sans-serif;
                margin: 0;
                padding: 0;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .container {
                text-align: center;
                color: white;
            }
            h1 {
                font-size: 3rem;
                margin-bottom: 1rem;
            }
            p {
                font-size: 1.2rem;
                margin-bottom: 2rem;
            }
            .links a {
                color: white;
                text-decoration: none;
                margin: 0 1rem;
                padding: 0.5rem 1rem;
                border: 2px solid white;
                border-radius: 5px;
                transition: all 0.3s ease;
            }
            .links a:hover {
                background: white;
                color: #667eea;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>License Server</h1>
            <p>Welcome to your Laravel 11 License Server application!</p>
            
            @if (Route::has('login'))
                <div class="links">
                    @auth
                        <a href="{{ url('/dashboard') }}">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}">Log in</a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}">Register</a>
                        @endif
                    @endauth
                </div>
            @endif
        </div>
    </body>
</html>