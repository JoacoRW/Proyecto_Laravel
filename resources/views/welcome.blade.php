<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MediTrack</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Estilos personalizados -->
    <style>
        body {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            color: #0c4a6e;
        }

        .container {
            text-align: center;
            padding: 2rem;
            background-color: white;
            border-radius: 1rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            max-width: 400px;
            width: 100%;
        }

        .logo {
            font-size: 2.5rem;
            font-weight: 600;
            margin-bottom: 2rem;
            color: #0284c7;
        }

        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            margin: 0.5rem;
            border-radius: 0.5rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s ease;
            border: 1px solid transparent;
        }

        .btn-login {
            background-color: #dbeafe;
            color: #1d4ed8;
        }

        .btn-login:hover {
            background-color: #bfdbfe;
            border-color: #93c5fd;
        }

        .btn-register {
            background-color: #0ea5e9;
            color: white;
        }

        .btn-register:hover {
            background-color: #0284c7;
            border-color: #0369a1;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">MediTrack</div>

        @if (Route::has('login'))
            <a href="{{ route('login') }}" class="btn btn-login">Iniciar sesión</a>
        @endif

        @if (Route::has('register'))
            <a href="{{ route('register') }}" class="btn btn-register">Registrarse</a>
        @endif
    </div>
</body>
</html>