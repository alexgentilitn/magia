{{--
    Component: Breadcrumb Navigation

    Uso:
    <x-breadcrumb :items="[
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'Clienti', 'url' => route('admin.clienti.index')],
        ['label' => 'Maria Rossi'] // ultimo senza URL
    ]" />
--}}

@props(['items' => []])

@if(count($items) > 0)
<nav class="mb-6" aria-label="Breadcrumb">
    <ol class="flex items-center gap-2 text-sm">
        {{-- Home Icon --}}
        <li>
            <a href="{{ route('admin.dashboard') }}"
               class="text-gray-500 hover:text-fucsia-magia transition-colors"
               title="Dashboard">
                <i class="fas fa-home"></i>
            </a>
        </li>

        {{-- Breadcrumb Items --}}
        @foreach($items as $index => $item)
            <li class="flex items-center gap-2">
                {{-- Separator --}}
                <i class="fas fa-chevron-right text-xs text-gray-400"></i>

                {{-- Link o Testo --}}
                @if(isset($item['url']))
                    <a href="{{ $item['url'] }}"
                       class="text-gray-600 hover:text-fucsia-magia transition-colors font-medium">
                        @if(isset($item['icon']))
                            <i class="{{ $item['icon'] }} mr-1"></i>
                        @endif
                        {{ $item['label'] }}
                    </a>
                @else
                    <span class="text-fucsia-magia font-semibold">
                        @if(isset($item['icon']))
                            <i class="{{ $item['icon'] }} mr-1"></i>
                        @endif
                        {{ $item['label'] }}
                    </span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
@endif
