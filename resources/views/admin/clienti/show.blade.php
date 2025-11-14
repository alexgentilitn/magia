@extends('layouts.admin')

@section('titolo', 'Dettaglio Cliente')

@section('contenuto')
<div class="p-6">
    
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <a href="{{ route('admin.clienti.index') }}" class="text-fucsia-magia hover:text-viola-magia mb-2 inline-block">
                <i class="fas fa-arrow-left mr-2"></i> Torna alla lista
            </a>
            <h2 class="text-2xl font-bold text-gray-800">{{ $cliente->nomeCompleto }}</h2>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.clienti.edit', $cliente->id) }}" 
               class="px-4 py-2 bg-fucsia-magia text-white rounded-lg hover:bg-viola-magia transition">
                <i class="fas fa-edit mr-2"></i> Modifica
            </a>
            <form method="POST" action="{{ route('admin.clienti.destroy', $cliente->id) }}" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        onclick="return confirm('Sei sicuro di voler eliminare questa cliente?')"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                    <i class="fas fa-trash mr-2"></i> Elimina
                </button>
            </form>
        </div>
    </div>

    <!-- Info Rapide -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-600">Stato</p>
            <p class="text-lg font-bold text-gray-800">{{ ucfirst($cliente->stato_cliente) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-600">Età</p>
            <p class="text-lg font-bold text-gray-800">{{ $cliente->eta }} anni</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-600">Certificato Medico</p>
            <p class="text-lg font-bold text-gray-800">
                {{ $cliente->haCertificatoValido() ? 'Valido' : 'Scaduto' }}
            </p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-600">Iscrizione</p>
            <p class="text-lg font-bold text-gray-800">{{ $cliente->created_at->format('d/m/Y') }}</p>
        </div>
    </div>

    <!-- Tabs -->
    <div class="bg-white rounded-lg shadow" x-data="{ tab: 'anagrafica' }">
        
        <!-- Tab Headers -->
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <button @click="tab = 'anagrafica'" 
                        :class="tab === 'anagrafica' ? 'border-fucsia-magia text-fucsia-magia' : 'border-transparent text-gray-500 hover:text-gray-700'"
                        class="py-4 px-6 border-b-2 font-medium text-sm transition">
                    <i class="fas fa-user mr-2"></i> Anagrafica
                </button>
                <button @click="tab = 'parametri'" 
                        :class="tab === 'parametri' ? 'border-fucsia-magia text-fucsia-magia' : 'border-transparent text-gray-500 hover:text-gray-700'"
                        class="py-4 px-6 border-b-2 font-medium text-sm transition">
                    <i class="fas fa-weight mr-2"></i> Parametri Corporei
                </button>
                <button @click="tab = 'programmi'" 
                        :class="tab === 'programmi' ? 'border-fucsia-magia text-fucsia-magia' : 'border-transparent text-gray-500 hover:text-gray-700'"
                        class="py-4 px-6 border-b-2 font-medium text-sm transition">
                    <i class="fas fa-dumbbell mr-2"></i> Programmi
                </button>
                <button @click="tab = 'note'" 
                        :class="tab === 'note' ? 'border-fucsia-magia text-fucsia-magia' : 'border-transparent text-gray-500 hover:text-gray-700'"
                        class="py-4 px-6 border-b-2 font-medium text-sm transition">
                    <i class="fas fa-sticky-note mr-2"></i> Note
                </button>
            </nav>
        </div>

        <!-- Tab Content -->
        <div class="p-6">
            
            <!-- Tab Anagrafica -->
            <div x-show="tab === 'anagrafica'" x-cloak>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="font-bold text-gray-800 mb-4">Dati Personali</h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm text-gray-600">Nome Completo</dt>
                                <dd class="text-base font-medium text-gray-800">{{ $cliente->nomeCompleto }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm text-gray-600">Codice Fiscale</dt>
                                <dd class="text-base font-medium text-gray-800">{{ $cliente->codice_fiscale }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm text-gray-600">Data di Nascita</dt>
                                <dd class="text-base font-medium text-gray-800">{{ $cliente->data_nascita ? $cliente->data_nascita->format('d/m/Y') : '-' }}</dd>
                            </div>
                        </dl>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-800 mb-4">Contatti</h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm text-gray-600">Email</dt>
                                <dd class="text-base font-medium text-gray-800">{{ $cliente->email }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm text-gray-600">Telefono Mobile</dt>
                                <dd class="text-base font-medium text-gray-800">{{ $cliente->telefono_mobile ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm text-gray-600">Telefono Fisso</dt>
                                <dd class="text-base font-medium text-gray-800">{{ $cliente->telefono_fisso ?? '-' }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>

            <!-- Tab Parametri -->
            <div x-show="tab === 'parametri'" x-cloak>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-600 mb-1">Peso Attuale</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $cliente->peso_kg ?? '-' }} kg</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-600 mb-1">Altezza</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $cliente->altezza_cm ?? '-' }} cm</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-600 mb-1">IMC</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $cliente->calcolaImc() ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Tab Programmi -->
            <div x-show="tab === 'programmi'" x-cloak>
                <div class="space-y-4">
                    <div class="bg-purple-50 rounded-lg p-4">
                        <h4 class="font-bold text-gray-800 mb-2">Programma Attuale</h4>
                        <p class="text-lg">{{ $cliente->programma_attuale ? ucfirst(str_replace('_', ' ', $cliente->programma_attuale)) : 'Nessun programma attivo' }}</p>
                    </div>
                </div>
            </div>

            <!-- Tab Note -->
            <div x-show="tab === 'note'" x-cloak>
                <div class="prose max-w-none">
                    <p class="text-gray-600">{{ $cliente->note ?? 'Nessuna nota' }}</p>
                </div>
            </div>

        </div>

    </div>

</div>

@push('styles')
<style>
    [x-cloak] { display: none !important; }
</style>
@endpush

@endsection
