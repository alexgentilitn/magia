@extends('layouts.app')

@section('titolo', 'Chat')

@section('contenuto')
<div class="container mx-auto p-6">
    <div class="bg-white rounded-lg shadow h-[calc(100vh-12rem)] flex flex-col">

        <!-- Header Conversazione -->
        <div class="p-4 border-b border-gray-200 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('messaging.index') }}" class="text-gray-600 hover:text-gray-800">
                    <i class="fas fa-arrow-left"></i>
                </a>

                @if($conversazione->tipo === 'privata' && $altriUtenti->count() === 1)
                    @php $altroUtente = $altriUtenti->first(); @endphp
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-fucsia-magia to-purple-600 flex items-center justify-center text-white font-bold">
                        {{ strtoupper(substr($altroUtente->nome ?? 'U', 0, 1)) }}
                    </div>
                    <div>
                        <h2 class="font-semibold text-gray-800">{{ $altroUtente->nome_completo ?? 'Utente' }}</h2>
                        <p class="text-xs text-gray-500">
                            @if($altroUtente->tipo === 'admin')
                                <i class="fas fa-crown text-yellow-500"></i> Amministratore
                            @elseif($altroUtente->tipo === 'professionista')
                                <i class="fas fa-user-md text-blue-500"></i> Professionista
                            @else
                                <i class="fas fa-user text-gray-500"></i> Cliente
                            @endif
                        </p>
                    </div>
                @else
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center text-white">
                        <i class="fas fa-users"></i>
                    </div>
                    <h2 class="font-semibold text-gray-800">{{ $conversazione->titolo ?? 'Gruppo' }}</h2>
                @endif
            </div>

            <div class="text-sm text-gray-500">
                <i class="fas fa-users mr-1"></i>
                {{ $conversazione->utenti->count() }} partecipanti
            </div>
        </div>

        <!-- Area Messaggi (scrollabile) -->
        <div id="messaggi-container" class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50">
            @forelse($conversazione->messaggi as $msg)
                @php
                    $isMio = $msg->mittente_id === Auth::id();
                @endphp

                <div class="flex {{ $isMio ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-[70%]">
                        @if(!$isMio)
                            <div class="text-xs text-gray-600 mb-1 ml-2">
                                {{ $msg->mittente->nome_completo ?? 'Utente' }}
                            </div>
                        @endif

                        <div class="rounded-lg px-4 py-2 {{ $isMio ? 'bg-fucsia-magia text-white' : 'bg-white border border-gray-200 text-gray-800' }}">
                            @if($msg->tipo === 'immagine')
                                <img src="{{ Storage::url($msg->allegato_path) }}"
                                     alt="Immagine"
                                     class="rounded max-w-full mb-2 cursor-pointer"
                                     onclick="window.open(this.src, '_blank')">
                            @elseif($msg->tipo === 'file')
                                <a href="{{ route('messaging.download', $msg->id) }}"
                                   class="flex items-center gap-2 {{ $isMio ? 'text-white hover:text-gray-100' : 'text-blue-600 hover:text-blue-800' }}">
                                    <i class="fas fa-file text-2xl"></i>
                                    <div>
                                        <div class="font-semibold">{{ $msg->allegato_nome }}</div>
                                        <div class="text-xs opacity-75">Clicca per scaricare</div>
                                    </div>
                                </a>
                            @endif

                            <p class="whitespace-pre-wrap break-words">{{ $msg->corpo }}</p>
                        </div>

                        <div class="text-xs {{ $isMio ? 'text-right' : 'text-left' }} mt-1 px-2">
                            <span class="text-gray-500">{{ $msg->created_at->format('H:i') }}</span>
                            @if($isMio && $msg->letto)
                                <i class="fas fa-check-double text-blue-500 ml-1"></i>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12 text-gray-400">
                    <i class="fas fa-comment-slash text-5xl mb-3"></i>
                    <p>Nessun messaggio. Inizia la conversazione!</p>
                </div>
            @endforelse
        </div>

        <!-- Form Invio Messaggio -->
        <div class="p-4 border-t border-gray-200 bg-white">
            <form action="{{ route('messaging.send', $conversazione->id) }}"
                  method="POST"
                  enctype="multipart/form-data"
                  id="form-messaggio"
                  class="flex gap-3">
                @csrf

                <div class="flex-1 relative">
                    <textarea name="messaggio"
                              id="input-messaggio"
                              rows="1"
                              placeholder="Scrivi un messaggio..."
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg resize-none focus:ring-2 focus:ring-fucsia-magia focus:border-transparent"
                              required></textarea>
                </div>

                <label for="allegato" class="flex items-center justify-center w-10 h-10 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50 cursor-pointer">
                    <i class="fas fa-paperclip"></i>
                    <input type="file"
                           name="allegato"
                           id="allegato"
                           class="hidden"
                           accept="image/*,.pdf,.doc,.docx"
                           onchange="mostraAllegato(this)">
                </label>

                <button type="submit" class="flex items-center justify-center w-10 h-10 rounded-lg bg-fucsia-magia text-white hover:bg-opacity-90">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </form>

            <!-- Preview allegato -->
            <div id="preview-allegato" class="mt-2 hidden">
                <div class="flex items-center gap-2 text-sm text-gray-600 bg-gray-100 rounded px-3 py-2">
                    <i class="fas fa-paperclip"></i>
                    <span id="nome-allegato"></span>
                    <button type="button" onclick="rimuoviAllegato()" class="ml-auto text-red-500 hover:text-red-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Scroll automatico ai messaggi più recenti
const container = document.getElementById('messaggi-container');
if (container) {
    container.scrollTop = container.scrollHeight;
}

// Auto-resize textarea
const textarea = document.getElementById('input-messaggio');
textarea.addEventListener('input', function() {
    this.style.height = 'auto';
    this.style.height = Math.min(this.scrollHeight, 150) + 'px';
});

// Gestione allegato
function mostraAllegato(input) {
    const preview = document.getElementById('preview-allegato');
    const nomeAllegato = document.getElementById('nome-allegato');

    if (input.files && input.files[0]) {
        nomeAllegato.textContent = input.files[0].name;
        preview.classList.remove('hidden');
    }
}

function rimuoviAllegato() {
    document.getElementById('allegato').value = '';
    document.getElementById('preview-allegato').classList.add('hidden');
}

// Polling nuovi messaggi ogni 3 secondi
let ultimoId = {{ $conversazione->messaggi->last()->id ?? 0 }};

function caricaNuoviMessaggi() {
    fetch(`{{ route('messaging.messages', $conversazione->id) }}?ultimo_id=${ultimoId}`)
        .then(res => res.json())
        .then(data => {
            if (data.count > 0) {
                data.messaggi.forEach(msg => {
                    aggiungiMessaggio(msg);
                    ultimoId = Math.max(ultimoId, msg.id);
                });
                container.scrollTop = container.scrollHeight;
            }
        })
        .catch(err => console.error('Errore polling:', err));
}

function aggiungiMessaggio(msg) {
    const isMio = msg.mittente_id === {{ Auth::id() }};
    const html = `
        <div class="flex ${isMio ? 'justify-end' : 'justify-start'}">
            <div class="max-w-[70%]">
                ${!isMio ? `<div class="text-xs text-gray-600 mb-1 ml-2">${msg.mittente.nome_completo}</div>` : ''}
                <div class="rounded-lg px-4 py-2 ${isMio ? 'bg-fucsia-magia text-white' : 'bg-white border border-gray-200 text-gray-800'}">
                    <p class="whitespace-pre-wrap break-words">${msg.corpo}</p>
                </div>
                <div class="text-xs ${isMio ? 'text-right' : 'text-left'} mt-1 px-2">
                    <span class="text-gray-500">${new Date(msg.created_at).toLocaleTimeString('it-IT', {hour: '2-digit', minute: '2-digit'})}</span>
                </div>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
}

// Avvia polling
setInterval(caricaNuoviMessaggi, 3000);

// Enter per inviare (Shift+Enter per andare a capo)
textarea.addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        document.getElementById('form-messaggio').submit();
    }
});
</script>
@endpush
@endsection
