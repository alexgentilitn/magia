<!DOCTYPE html>
<html>
<head>
    <title>TEST SEMPLICISSIMO</title>
    <style>
        body { background: yellow; color: red; font-size: 30px; padding: 50px; }
        h1 { background: red; color: white; padding: 20px; }
    </style>
</head>
<body>
    <h1>QUESTA È UNA PAGINA DI TEST</h1>
    <p>Se vedi questo testo, Laravel funziona correttamente.</p>
    <p>Data: {{ now()->format('Y-m-d H:i:s') }}</p>
    <p>Utente autenticato: {{ auth()->check() ? 'SÌ' : 'NO' }}</p>
    @if(auth()->check())
        <p>Nome: {{ auth()->user()->nome }}</p>
        <p>Email: {{ auth()->user()->email }}</p>
    @endif
</body>
</html>
