@props(['name', 'fill' => '#F2B63D', 'size' => 24])

<svg {{ $attributes->merge(['class' => 'shrink-0']) }}
     width="{{ $size }}" height="{{ $size }}" viewBox="0 0 24 24"
     fill="none" stroke="#2a1b6b" stroke-width="2"
     stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    @switch($name)
        @case('home')
            <path d="M3 11.5 12 4l9 7.5V20a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1z" fill="{{ $fill }}"/>
            @break
        @case('wallet')
            <rect x="3" y="6" width="18" height="14" rx="3" fill="{{ $fill }}"/>
            <path d="M3 10h18"/>
            <circle cx="16.5" cy="14.5" r="1.2" fill="#2a1b6b"/>
            @break
        @case('target')
            <path d="M6 21V4"/>
            <path d="M6 4h12l-2.5 4 2.5 4H6z" fill="{{ $fill }}"/>
            @break
        @case('receipt')
            <path d="M6 3h12v18l-3-2-3 2-3-2-3 2z" fill="{{ $fill }}"/>
            <path d="M9 8h6M9 12h6"/>
            @break
        @case('calendar')
            <rect x="3" y="5" width="18" height="16" rx="3" fill="{{ $fill }}"/>
            <path d="M3 10h18M8 3v4M16 3v4"/>
            @break
        @case('calculator')
            <rect x="5" y="3" width="14" height="18" rx="3" fill="{{ $fill }}"/>
            <rect x="8" y="6" width="8" height="4" rx="1" fill="#fff"/>
            <path d="M9 14h.01M12 14h.01M15 14h.01M9 17.5h.01M12 17.5h.01M15 17.5h.01"/>
            @break
        @case('chart')
            <rect x="4" y="12" width="4" height="8" rx="1" fill="{{ $fill }}"/>
            <rect x="10" y="5" width="4" height="15" rx="1" fill="{{ $fill }}"/>
            <rect x="16" y="9" width="4" height="11" rx="1" fill="{{ $fill }}"/>
            @break
        @case('mountain')
            <path d="M2 20 9 7l4 7 3-4 6 10z" fill="{{ $fill }}"/>
            @break
        @case('plus')
            <path d="M12 5v14M5 12h14"/>
            @break
        @case('minus')
            <path d="M5 12h14"/>
            @break
        @case('more')
            <circle cx="5" cy="12" r="1.6" fill="#2a1b6b"/>
            <circle cx="12" cy="12" r="1.6" fill="#2a1b6b"/>
            <circle cx="19" cy="12" r="1.6" fill="#2a1b6b"/>
            @break
    @endswitch
</svg>