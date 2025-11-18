@extends('layouts.app')

@section('titolo', 'Notifiche')

@section('contenuto')
<div class="container mx-auto p-6">
    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-bell text-fucsia-magia mr-3"></i>
                Notifiche
            </h1>
            <p class="text-gray-600">
                <span class="font-semibold">{{ $notifiche->total() }}</span> notifiche totali
                @if($nonLette > 0)
                    · <span class="text-red-600 font-semibold">{{ $nonLette }}</span> non lette
                @endif
            </p>
        </div>

        @if($nonLette > 0)
            <form action="{{ route('notifiche.read-all') }}" method="POST">
                @csrf
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-check-double mr-2"></i>
                    Segna tutte come lette
                </button>
            </form>
        @endif
    </div>

    <!-- Lista Notifiche -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($notifiche->isEmpty())
            <div class="text-center py-16">
                <i class="fas fa-bell-slash text-gray-300 text-6xl mb-4"></i>
                <p class="text-gray-500 text-lg">Nessuna notifica</p>
                <p class="text-gray-400 text-sm mt-2">Le tue notifiche appariranno qui</p>
            </div>
        @else
            <div class="divide-y divide-gray-200">
                @foreach($notifiche as $notifica)
                    @php
                        $coloriMap = [
                            'blue' => 'bg-blue-100 text-blue-600',
                            'green' => 'bg-green-100 text-green-600',
                            'red' => 'bg-red-100 text-red-600',
                            'yellow' => 'bg-yellow-100 text-yellow-600',
                            'purple' => 'bg-purple-100 text-purple-600',
                            'pink' => 'bg-pink-100 text-pink-600',
                        ];
                        $coloreClasse = $coloriMap[$notifica->colore] ?? $coloriMap['blue'];
                    @endphp

                    <div class="p-4 hover:bg-gray-50 transition {{ !$notifica->letta ? 'bg-blue-50' : '' }}">
                        <div class="flex items-start gap-4">
                            <!-- Icona -->
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 rounded-full {{ $coloreClasse }} flex items-center justify-center">
                                    <i class="fas {{ $notifica->icona ?? 'fa-bell' }} text-xl"></i>
                                </div>
                            </div>

                            <!-- Contenuto -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-gray-800 {{ !$notifica->letta ? 'font-bold' : '' }}">
                                            {{ $notifica->titolo }}
                                            @if(!$notifica->letta)
                                                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-500 text-white">
                                                    Nuovo
                                                </span>
                                            @endif
                                        </h3>

                                        @if($notifica->corpo)
                                            <p class="text-sm text-gray-600 mt-1">
                                                {{ $notifica->corpo }}
                                            </p>
                                        @endif

                                        <div class="flex items-center gap-4 mt-2 text-xs text-gray-500">
                                            <span>
                                                <i class="fas fa-clock mr-1"></i>
                                                {{ $notifica->created_at->diffForHumans() }}
                                            </span>
                                            @if($notifica->letta)
                                                <span>
                                                    <i class="fas fa-check text-green-500 mr-1"></i>
                                                    Letta {{ $notifica->letta_at->diffForHumans() }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Timestamp formattato -->
                                    <div class="text-xs text-gray-400 flex-shrink-0">
                                        {{ $notifica->created_at->format('d/m/Y H:i') }}
                                    </div>
                                </div>

                                <!-- Azioni -->
                                <div class="flex items-center gap-3 mt-3">
                                    @if($notifica->link_url)
                                        <a href="{{ $notifica->link_url }}"
                                           class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 font-medium"
                                           onclick="segnaComeLetta({{ $notifica->id }})">
                                            <i class="fas fa-external-link-alt mr-1"></i>
                                            Vai
                                        </a>
                                    @endif

                                    @if(!$notifica->letta)
                                        <button type="button"
                                                onclick="segnaComeLetta({{ $notifica->id }})"
                                                class="inline-flex items-center text-sm text-gray-600 hover:text-gray-800">
                                            <i class="fas fa-check mr-1"></i>
                                            Segna come letta
                                        </button>
                                    @endif

                                    <form action="{{ route('notifiche.destroy', $notifica->id) }}"
                                          method="POST"
                                          class="inline"
                                          onsubmit="return confirm('Eliminare questa notifica?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center text-sm text-red-600 hover:text-red-800">
                                            <i class="fas fa-trash mr-1"></i>
                                            Elimina
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Paginazione -->
            <div class="p-4 bg-gray-50 border-t border-gray-200">
                {{ $notifiche->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function segnaComeLetta(id) {
    fetch(`/notifiche/${id}/read`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Ricarica la pagina per aggiornare l'UI
            location.reload();
        }
    })
    .catch(error => console.error('Errore:', error));
}
</script>
@endpush
@endsection
