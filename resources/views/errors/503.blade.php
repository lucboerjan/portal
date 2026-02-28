<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Even geduld...</title>
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
    </style>
</head>
<body>
    <div class="card">
        <h1>🔧 Even geduld</h1>
        <p>We voeren momenteel onderhoud uit aan het portaal.<br>
        We zijn zo snel mogelijk terug.</p>
        @if(isset($exception) && $exception->getMessage())
            <p><em>{{ $exception->getMessage() }}</em></p>
        @endif
    </div>
</body>
</html>