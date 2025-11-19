@extends('layouts.admin')

@section('titolo', 'Impostazioni')

@section('contenuto')
<div class="p-6" x-data="{ activeTab: '{{ session('active_tab', 'email') }}' }">

    <!-- Header Compatto -->
    <div class="mb-4">
        <h1 class="text-2xl font-bold text-gray-800 mb-1">
            <i class="fas fa-cog text-fucsia-magia mr-2"></i>
            Impostazioni
        </h1>
        <p class="text-sm text-gray-500">Gestisci le configurazioni del sistema</p>
    </div>

    <!-- Alert Compatti -->
    @if(session('success'))
        <div class="mb-4 px-4 py-2.5 bg-green-50 border-l-4 border-green-500 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-600 text-sm mr-2"></i>
                <p class="text-sm text-green-800 font-medium">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 px-4 py-2.5 bg-red-50 border-l-4 border-red-500 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle text-red-600 text-sm mr-2"></i>
                <p class="text-sm text-red-800 font-medium">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 px-4 py-2.5 bg-red-50 border-l-4 border-red-500 rounded-lg">
            <div class="flex items-start">
                <i class="fas fa-exclamation-triangle text-red-600 text-sm mr-2 mt-0.5"></i>
                <div class="flex-1">
                    <p class="text-sm text-red-800 font-medium mb-1">Errori di validazione:</p>
                    <ul class="list-disc list-inside text-xs text-red-700 space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- Tab Navigation -->
    <div class="mb-6">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8">
                <!-- Tab Email -->
                <button
                    @click="activeTab = 'email'"
                    :class="activeTab === 'email' ? 'border-fucsia-magia text-fucsia-magia' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors"
                >
                    <i class="fas fa-envelope mr-2"></i>
                    Email e Notifiche
                </button>

                <!-- Tab PayPal -->
                <button
                    @click="activeTab = 'paypal'"
                    :class="activeTab === 'paypal' ? 'border-fucsia-magia text-fucsia-magia' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors"
                >
                    <i class="fab fa-paypal mr-2"></i>
                    Pagamenti
                </button>

                <!-- Tab Valori Sistema -->
                <button
                    @click="activeTab = 'sistema'"
                    :class="activeTab === 'sistema' ? 'border-fucsia-magia text-fucsia-magia' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors"
                >
                    <i class="fas fa-cogs mr-2"></i>
                    Valori Sistema
                </button>

                <!-- Tab Importa Pesate -->
                <button
                    @click="activeTab = 'pesate'"
                    :class="activeTab === 'pesate' ? 'border-fucsia-magia text-fucsia-magia' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors"
                >
                    <i class="fas fa-file-excel mr-2"></i>
                    Importa Pesate
                </button>

                <!-- Tab Segnalazioni -->
                <button
                    @click="activeTab = 'segnalazioni'"
                    :class="activeTab === 'segnalazioni' ? 'border-fucsia-magia text-fucsia-magia' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors"
                >
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    Segnalazioni
                    @if($statsSegnalazioni['aperte'] > 0)
                    <span class="ml-2 px-2 py-0.5 bg-red-500 text-white text-xs font-bold rounded-full">
                        {{ $statsSegnalazioni['aperte'] }}
                    </span>
                    @endif
                </button>
            </nav>
        </div>
    </div>

    <!-- Tab Content: Email e Notifiche -->
    <div x-show="activeTab === 'email'" x-cloak>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Form Configurazione SMTP -->
            <div class="lg:col-span-2">
                <form method="POST" action="{{ route('admin.impostazioni.smtp.salva') }}" class="bg-white rounded-lg shadow">
                    @csrf

                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-xl font-bold text-gray-800 mb-1">
                            <i class="fas fa-server text-fucsia-magia mr-2"></i>
                            Impostazioni Server SMTP
                        </h2>
                        <p class="text-sm text-gray-600">Configura i parametri di connessione al server email</p>
                    </div>

                    <div class="p-6 space-y-6">
                        <!-- Host SMTP -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Host SMTP *
                                <span class="text-gray-500 font-normal">(es: smtp.gmail.com, smtp.office365.com)</span>
                            </label>
                            <input
                                type="text"
                                name="smtp_host"
                                required
                                value="{{ old('smtp_host', $impostazioniSmtp['smtp_host']['valore'] ?? '') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent"
                                placeholder="smtp.gmail.com"
                            >
                        </div>

                        <!-- Porta e Encryption -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Porta *</label>
                                <input
                                    type="number"
                                    name="smtp_porta"
                                    required
                                    min="1"
                                    max="65535"
                                    value="{{ old('smtp_porta', $impostazioniSmtp['smtp_porta']['valore'] ?? '587') }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent"
                                >
                                <p class="mt-1 text-xs text-gray-500">TLS: 587, SSL: 465</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Encryption *</label>
                                <select
                                    name="smtp_encryption"
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent"
                                >
                                    <option value="tls" {{ (old('smtp_encryption', $impostazioniSmtp['smtp_encryption']['valore'] ?? 'tls') == 'tls') ? 'selected' : '' }}>TLS</option>
                                    <option value="ssl" {{ (old('smtp_encryption', $impostazioniSmtp['smtp_encryption']['valore'] ?? '') == 'ssl') ? 'selected' : '' }}>SSL</option>
                                    <option value="none" {{ (old('smtp_encryption', $impostazioniSmtp['smtp_encryption']['valore'] ?? '') == 'none') ? 'selected' : '' }}>Nessuna</option>
                                </select>
                            </div>
                        </div>

                        <!-- Username -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Username / Email SMTP *
                            </label>
                            <input
                                type="email"
                                name="smtp_username"
                                required
                                value="{{ old('smtp_username', $impostazioniSmtp['smtp_username']['valore'] ?? '') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent"
                                placeholder="tua-email@gmail.com"
                            >
                        </div>

                        <!-- Password -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Password SMTP
                                <span class="text-gray-500 font-normal">(lascia vuoto per mantenere quella esistente)</span>
                            </label>
                            <input
                                type="password"
                                name="smtp_password"
                                value=""
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent"
                                placeholder="••••••••"
                            >
                            <p class="mt-1 text-xs text-gray-500">
                                <i class="fas fa-shield-alt mr-1"></i>
                                La password viene salvata in modo criptato nel database
                            </p>
                        </div>
                    </div>

                    <!-- Mittente Email -->
                    <div class="p-6 border-t border-gray-200">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">
                            <i class="fas fa-user-circle text-fucsia-magia mr-2"></i>
                            Mittente Email
                        </h3>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email Mittente *</label>
                                <input
                                    type="email"
                                    name="mail_from_address"
                                    required
                                    value="{{ old('mail_from_address', $impostazioniSmtp['mail_from_address']['valore'] ?? '') }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent"
                                    placeholder="noreply@magiadonna.it"
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nome Mittente *</label>
                                <input
                                    type="text"
                                    name="mail_from_name"
                                    required
                                    value="{{ old('mail_from_name', $impostazioniSmtp['mail_from_name']['valore'] ?? '') }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent"
                                    placeholder="MA.GIA DONNA"
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Pulsante Salva -->
                    <div class="p-6 bg-gray-50 rounded-b-lg flex justify-end">
                        <button type="submit" class="px-6 py-3 bg-fucsia-magia text-white font-medium rounded-lg hover:bg-viola-magia transition-colors">
                            <i class="fas fa-save mr-2"></i>
                            Salva Configurazione
                        </button>
                    </div>
                </form>
            </div>

            <!-- Sidebar: Test Email e Info -->
            <div class="lg:col-span-1">
                <!-- Box Test Email -->
                <div class="bg-white rounded-lg shadow mb-6">
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-xl font-bold text-gray-800 mb-1">
                            <i class="fas fa-paper-plane text-fucsia-magia mr-2"></i>
                            Test Invio Email
                        </h2>
                        <p class="text-sm text-gray-600">Verifica la configurazione SMTP</p>
                    </div>

                    <form method="POST" action="{{ route('admin.impostazioni.smtp.test') }}" class="p-6">
                        @csrf

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Email destinatario
                            </label>
                            <input
                                type="email"
                                name="email_test"
                                required
                                value="{{ old('email_test', auth()->user()->email) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-fucsia-magia focus:border-transparent"
                                placeholder="test@example.com"
                            >
                        </div>

                        <button type="submit" class="w-full px-4 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors">
                            <i class="fas fa-vial mr-2"></i>
                            Invia Email di Test
                        </button>

                        <p class="mt-3 text-xs text-gray-500">
                            <i class="fas fa-info-circle mr-1"></i>
                            Verrà inviata un'email di prova all'indirizzo specificato utilizzando la configurazione salvata.
                        </p>
                    </form>
                </div>

                <!-- Box Configurazione Corrente - Compatto -->
                <div class="bg-white rounded-lg shadow">
                    <div class="p-4 border-b border-gray-200">
                        <h3 class="text-sm font-bold text-gray-800">
                            <i class="fas fa-cog text-fucsia-magia mr-2"></i>
                            Configurazione Attiva
                        </h3>
                    </div>

                    <div class="p-4">
                        <dl class="space-y-2 text-xs">
                            <div class="flex justify-between py-1.5 border-b border-gray-100">
                                <dt class="font-medium text-gray-600">Host:</dt>
                                <dd class="text-gray-900 font-mono">{{ Str::limit($impostazioniSmtp['smtp_host']['valore'] ?? 'Non configurato', 25) }}</dd>
                            </div>
                            <div class="flex justify-between py-1.5 border-b border-gray-100">
                                <dt class="font-medium text-gray-600">Porta:</dt>
                                <dd class="text-gray-900 font-mono">{{ $impostazioniSmtp['smtp_porta']['valore'] ?? '-' }}</dd>
                            </div>
                            <div class="flex justify-between py-1.5 border-b border-gray-100">
                                <dt class="font-medium text-gray-600">Encryption:</dt>
                                <dd class="text-gray-900 font-mono uppercase">{{ $impostazioniSmtp['smtp_encryption']['valore'] ?? '-' }}</dd>
                            </div>
                            <div class="flex justify-between py-1.5 border-b border-gray-100">
                                <dt class="font-medium text-gray-600">Username:</dt>
                                <dd class="text-gray-900 font-mono text-[10px] truncate max-w-[140px]">{{ $impostazioniSmtp['smtp_username']['valore'] ?? '-' }}</dd>
                            </div>
                            <div class="flex justify-between py-1.5">
                                <dt class="font-medium text-gray-600">Password:</dt>
                                <dd class="text-gray-500">
                                    @if(!empty($impostazioniSmtp['smtp_password']['valore']))
                                        <i class="fas fa-check-circle text-green-600"></i>
                                    @else
                                        <i class="fas fa-times-circle text-red-600"></i>
                                    @endif
                                </dd>
                            </div>
                        </dl>

                        @php
                            $hasPassword = !empty($impostazioniSmtp['smtp_password']['valore']);
                            $hasUsername = !empty($impostazioniSmtp['smtp_username']['valore']);
                            $hasHost = !empty($impostazioniSmtp['smtp_host']['valore']);
                            $isConfigured = $hasPassword && $hasUsername && $hasHost;
                        @endphp

                        @if($isConfigured)
                            <div class="mt-3 px-3 py-2 bg-green-50 border border-green-200 rounded">
                                <p class="text-xs text-green-800 text-center">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    <strong>Configurato</strong>
                                </p>
                            </div>
                        @else
                            <div class="mt-3 px-3 py-2 bg-red-50 border border-red-200 rounded">
                                <p class="text-xs text-red-800 text-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    <strong>Incompleto</strong>
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab Content: PayPal e Pagamenti -->
    @include('admin.impostazioni._paypal')

    <!-- Tab Content: Valori Sistema -->
    <div x-show="activeTab === 'sistema'" x-cloak class="max-w-7xl mx-auto">

        <!-- Categorie -->
        @foreach($categorieSistema as $catKey => $catInfo)
            @if(isset($impostazioniSistema[$catKey]))
            <div class="bg-white rounded-lg shadow-lg mb-6 overflow-hidden">

                <!-- Header Categoria -->
                <div class="bg-gradient-to-r from-viola-magia to-fucsia-magia p-4 flex items-center justify-between">
                    <div class="flex items-center text-white">
                        <i class="fas {{ $catInfo['icona'] }} text-2xl mr-3"></i>
                        <div>
                            <h2 class="text-xl font-bold">{{ $catInfo['nome'] }}</h2>
                            <p class="text-sm opacity-90">{{ $catInfo['descrizione'] }}</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.impostazioni-sistema.create', ['categoria' => $catKey]) }}"
                       class="px-4 py-2 bg-white text-viola-magia rounded-lg hover:bg-gray-100 transition font-medium">
                        <i class="fas fa-plus mr-2"></i>Aggiungi
                    </a>
                </div>

                <!-- Tabella Impostazioni -->
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Chiave</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Etichetta</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Colore/Icona</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Ordine</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Stato</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Sistema</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Azioni</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($impostazioniSistema[$catKey] as $imp)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 text-sm font-mono text-gray-700">{{ $imp->chiave }}</td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $imp->etichetta }}</div>
                                    @if($imp->descrizione)
                                    <div class="text-xs text-gray-500 mt-1">{{ Str::limit($imp->descrizione, 50) }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-2">
                                        @if($imp->colore)
                                        <div class="w-6 h-6 rounded border border-gray-300" style="background-color: {{ $imp->colore }}"></div>
                                        @endif
                                        @if($imp->icona)
                                        <i class="fas {{ $imp->icona }} text-gray-600"></i>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center text-sm text-gray-700">{{ $imp->ordinamento }}</td>
                                <td class="px-6 py-4 text-center">
                                    @if($imp->attivo)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        Attivo
                                    </span>
                                    @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                        Disattivo
                                    </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($imp->di_sistema)
                                    <i class="fas fa-lock text-gray-400" title="Impostazione di sistema"></i>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <form action="{{ route('admin.impostazioni-sistema.toggle', $imp->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-gray-600 hover:text-gray-900" title="{{ $imp->attivo ? 'Disattiva' : 'Attiva' }}">
                                            <i class="fas fa-{{ $imp->attivo ? 'eye-slash' : 'eye' }}"></i>
                                        </button>
                                    </form>
                                    <a href="{{ route('admin.impostazioni-sistema.edit', $imp->id) }}" class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if(!$imp->di_sistema)
                                    <form action="{{ route('admin.impostazioni-sistema.destroy', $imp->id) }}" method="POST" class="inline" id="delete-form-{{ $imp->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="text-red-600 hover:text-red-900"
                                                onclick="confermaEliminazione('delete-form-{{ $imp->id }}', 'Eliminare l\'impostazione?', 'L\'impostazione {{ $imp->chiave }} sarà eliminata definitivamente.')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
            @endif
        @endforeach

    </div>

    <!-- Tab Content: Importa Pesate -->
    <div x-show="activeTab === 'pesate'" x-cloak>
        @include('admin.pesate.import-content')
    </div>

    <!-- Tab Content: Segnalazioni -->
    @include('admin.segnalazioni._tab-content')

</div>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection
