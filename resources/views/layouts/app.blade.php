@php
    // Layout dinamico in base al tipo di utente
    $layout = match(auth()->user()->tipo ?? 'guest') {
        'admin' => 'layouts.admin',
        'professionista' => 'layouts.professionista',
        'cliente' => 'layouts.cliente',
        default => 'layouts.pubblico'
    };
@endphp

@extends($layout)
