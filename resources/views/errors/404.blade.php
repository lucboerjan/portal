<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagina niet gevonden</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background: #f3f4f6;
            color: #374151;
        }
        .card {
            background: white;
            padding: 3rem;
            border-radius: 1rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 500px;
        }
        h1 { font-size: 2rem; margin-bottom: 0.5rem; }
        p  { color: #6b7280; line-height: 1.6; }
        img {
            max-width: 180px;
            margin-bottom: 1rem;
            opacity: 0.9;
        }
        a.button {
            display: inline-block;
            margin-top: 1.5rem;
            background: #2563eb;
            color: white;
            padding: 0.6rem 1.2rem;
            border-radius: 0.5rem;
            text-decoration: none;
            font-weight: 600;
        }
        a.button:hover {
            background: #1d4ed8;
        }
    </style>
</head>
<body>
    <div class="card">

        {{-- Optionele afbeelding --}}
        <img src="{{ asset('afbeeldingen/app/404.png') }}" alt="404">

        <h1>❌ Pagina niet gevonden</h1>

        <p>
            De pagina die je probeert te openen bestaat niet (meer).<br>
            Misschien is de link verouderd of verkeerd getypt.
        </p>

        <a href="{{ route('filament.admin.pages.dashboard') }}" class="button">
            Terug naar het dashboard
        </a>

        @if(isset($exception) && $exception->getMessage())
            <p><em>{{ $exception->getMessage() }}</em></p>
        @endif
    </div>
</body>
</html>