@extends('layouts.admin')

@section('titolo', 'Gestione Sedi')

@section('contenuto')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">📍 Gestione Sedi</h1>
        <a href="{{ route('admin.sedi.create') }}" class="px-4 py-2 bg-fucsia-magia text-white rounded-lg hover:bg-viola-magia transition">
            <i class="fas fa-plus mr-2"></i> Nuova Sede
        </a>
    </div>

    <!-- Statistiche -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-600">Totale Sedi</p>
            <p class="text-2xl font-bold text-gray-800">{{ $statistiche['totale'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-600">Sedi Attive</p>
            <p class="text-2xl font-bold text-green-600">{{ $statistiche['attive'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-600">Visibili al Pubblico</p>
            <p class="text-2xl font-bold text-blue-600">{{ $statistiche['visibili'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-600">Sede Principale</p>
            <p class="text-2xl font-bold text-purple-600">{{ $statistiche['principale'] }}</p>
        </div>
    </div>

    <!-- Lista Sedi -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-4 border-b">
            <h2 class="text-lg font-semibold">Elenco Sedi</h2>
        </div>

        @if($sedi->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 p-4">
            @foreach($sedi as $sede)
            <div class="border rounded-lg p-4 hover:shadow-lg transition">
                <!-- Header con badge -->
                <div class="flex justify-between items-start mb-3">
                    <h3 class="text-lg font-bold text-gray-800">{{ $sede->nome }}</h3>
                    <div class="flex flex-col gap-1">
                        @if($sede->sede_principale)
                        <span class="px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-800">
                            <i class="fas fa-star"></i> Principale
                        </span>
                        @endif
                        <span class="px-2 py-1 text-xs rounded-full {{ $sede->attiva ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $sede->attiva ? 'Attiva' : 'Non Attiva' }}
                        </span>
                    </div>
                </div>

                <!-- Indirizzo -->
                <div class="mb-3">
                    <p class="text-sm text-gray-600">
                        <i class="fas fa-map-marker-alt mr-1 text-fucsia-magia"></i>
                        {{ $sede->indirizzo_via }}
                    </p>
                    <p class="text-sm text-gray-600">
                        {{ $sede->indirizzo_cap }} {{ $sede->indirizzo_citta }} ({{ $sede->indirizzo_provincia }})
                    </p>
                </div>

                <!-- Statistiche -->
                <div class="grid grid-cols-2 gap-2 mb-3 py-3 border-t border-b">
                    <div class="text-center">
                        <p class="text-xs text-gray-600">Programmi</p>
                        <p class="text-lg font-bold text-gray-800">{{ $sede->programmi_count }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-xs text-gray-600">Lezioni</p>
                        <p class="text-lg font-bold text-gray-800">{{ $sede->lezioni_count }}</p>
                    </div>
                </div>

                <!-- Contatti -->
                @if($sede->telefono || $sede->email)
                <div class="mb-3">
                    @if($sede->telefono)
                    <p class="text-sm text-gray-600">
                        <i class="fas fa-phone mr-1"></i> {{ $sede->telefono }}
                    </p>
                    @endif
                    @if($sede->email)
                    <p class="text-sm text-gray-600">
                        <i class="fas fa-envelope mr-1"></i> {{ $sede->email }}
                    </p>
                    @endif
                </div>
                @endif

                <!-- Azioni -->
                <div class="flex gap-2">
                    <a href="{{ route('admin.sedi.show', $sede->id) }}" class="flex-1 px-3 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 text-center">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="{{ route('admin.sedi.edit', $sede->id) }}" class="flex-1 px-3 py-2 bg-green-600 text-white text-sm rounded hover:bg-green-700 text-center">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form method="POST" action="{{ route('admin.sedi.destroy', $sede->id) }}" class="flex-1" id="delete-form-{{ $sede->id }}">
                        @csrf
                        @method('DELETE')
                        <button type="button" onclick="confermaEliminazione('delete-form-{{ $sede->id }}', 'Eliminare la sede?', 'La sede {{ $sede->nome }} sarà eliminata definitivamente. Le lezioni esistenti non saranno eliminate.')" class="w-full px-3 py-2 bg-red-600 text-white text-sm rounded hover:bg-red-700">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>

        <div class="p-4 border-t">
            {{ $sedi->links() }}
        </div>
        @else
        <div class="p-12 text-center">
            <i class="fas fa-map-marker-alt text-gray-400 text-5xl mb-4"></i>
            <p class="text-gray-500 font-medium">Nessuna sede registrata</p>
            <a href="{{ route('admin.sedi.create') }}" class="text-fucsia-magia hover:text-viola-magia mt-2 inline-block">
                <i class="fas fa-plus-circle mr-1"></i> Crea prima sede
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
