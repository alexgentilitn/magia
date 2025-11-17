@extends('layouts.admin')

@section('titolo', 'Gestione Professionisti')

@section('contenuto')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">👔 Gestione Professionisti</h1>
        <a href="{{ route('admin.professionisti.create') }}" class="px-4 py-2 bg-fucsia-magia text-white rounded-lg hover:bg-viola-magia transition">
            <i class="fas fa-plus mr-2"></i> Nuovo Professionista
        </a>
    </div>

    <!-- Statistiche -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-600">Totale Professionisti</p>
            <p class="text-2xl font-bold text-gray-800">{{ $statistiche['totale'] }}</p>
        </div>
        @if($statistiche['pending'] > 0)
        <div class="bg-orange-50 rounded-lg shadow p-4 border-2 border-orange-200">
            <p class="text-sm text-orange-600 font-semibold">Da Approvare</p>
            <p class="text-2xl font-bold text-orange-600">{{ $statistiche['pending'] }}</p>
        </div>
        @endif
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-600">Attivi</p>
            <p class="text-2xl font-bold text-green-600">{{ $statistiche['attivi'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-600">Visibili al Pubblico</p>
            <p class="text-2xl font-bold text-blue-600">{{ $statistiche['visibili'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-600">Con Certificazioni</p>
            <p class="text-2xl font-bold text-purple-600">{{ $statistiche['con_certificazioni'] }}</p>
        </div>
    </div>

    <!-- Lista Professionisti -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-4 border-b">
            <h2 class="text-lg font-semibold">Elenco Professionisti</h2>
        </div>

        @if($professionisti->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nome</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Titolo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contatti</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lezioni</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stato</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Azioni</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($professionisti as $prof)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="h-10 w-10 rounded-full bg-gradient-to-br from-fucsia-magia to-viola-magia flex items-center justify-center text-white font-bold">
                                    {{ substr($prof->nome, 0, 1) }}{{ substr($prof->cognome, 0, 1) }}
                                </div>
                                <div class="ml-3">
                                    <p class="font-medium text-gray-800">{{ $prof->nome }} {{ $prof->cognome }}</p>
                                    <p class="text-xs text-gray-500">{{ $prof->codice_professionista }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm text-gray-800">{{ $prof->titolo_professionale ?? 'Non specificato' }}</p>
                            @if($prof->anni_esperienza)
                            <p class="text-xs text-gray-500">{{ $prof->anni_esperienza }} anni esperienza</p>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($prof->telefono_mobile)
                            <p class="text-sm text-gray-800"><i class="fas fa-phone mr-1"></i> {{ $prof->telefono_mobile }}</p>
                            @endif
                            @if($prof->email)
                            <p class="text-sm text-gray-600"><i class="fas fa-envelope mr-1"></i> {{ $prof->email }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-center">
                                <p class="text-lg font-bold text-gray-800">{{ $prof->lezioni_count }}</p>
                                <p class="text-xs text-gray-500">lezioni</p>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full {{ $prof->badge_stato }}">
                                {{ ucfirst($prof->stato) }}
                            </span>
                            @if($prof->visibile_pubblico)
                            <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800 ml-1">
                                <i class="fas fa-eye"></i>
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex gap-2">
                                <a href="{{ route('admin.professionisti.show', $prof->id) }}" class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.professionisti.edit', $prof->id) }}" class="text-green-600 hover:text-green-800">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.professionisti.destroy', $prof->id) }}" class="inline" id="delete-form-{{ $prof->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" onclick="confermaEliminazione('delete-form-{{ $prof->id }}', 'Eliminare il professionista?', 'Il professionista {{ $prof->nome }} {{ $prof->cognome }} sarà eliminato definitivamente. Le lezioni esistenti non saranno eliminate.')" class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t">
            {{ $professionisti->links() }}
        </div>
        @else
        <div class="p-12 text-center">
            <i class="fas fa-user-tie text-gray-400 text-5xl mb-4"></i>
            <p class="text-gray-500 font-medium">Nessun professionista registrato</p>
            <a href="{{ route('admin.professionisti.create') }}" class="text-fucsia-magia hover:text-viola-magia mt-2 inline-block">
                <i class="fas fa-plus-circle mr-1"></i> Aggiungi primo professionista
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
