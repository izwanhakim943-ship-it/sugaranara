@props(['title' => 'Sugaranara'])

@php
    $nav = [
        ['label' => 'Dashboard',       'icon' => 'home',       'fill' => '#BFE87A', 'href' => route('design.preview'), 'active' => request()->routeIs('design.preview')],
        ['label' => 'Current Savings', 'icon' => 'wallet',     'fill' => '#F2B63D', 'href' => '#', 'active' => false],
        ['label' => 'Savings Goals',   'icon' => 'target',     'fill' => '#FF7A6B', 'href' => '#', 'active' => false],
        ['label' => 'Transactions',    'icon' => 'receipt',    'fill' => '#4FA6A0', 'href' => '#', 'active' => false],
        ['label' => 'Calendar',        'icon' => 'calendar',   'fill' => '#F2B63D', 'href' => '#', 'active' => false],
        ['label' => 'Calculator',      'icon' => 'calculator', 'fill' => '#A9C4A0', 'href' => '#', 'active' => false],
        ['label' => 'Reports',         'icon' => 'chart',      'fill' => '#D9663A', 'href' => '#', 'active' => false],
    ];
@endphp

<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} · Sugaranara</title>
    <style>[x-cloak]{display:none !important}</style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="relative isolate overflow-x-hidden">

    {{-- Hiasan bukit berlapis (kanan atas) --}}
    <svg class="pointer-events-none fixed right-0 top-0 -z-10 w-[520px] max-w-full" viewBox="0 0 520 140" fill="none" aria-hidden="true">
        <path d="M520 0H120c60 40 90 90 170 100s150-20 230 20z" fill="#A9C4A0" opacity=".7"/>
        <path d="M520 0H260c40 30 70 60 130 60s90-10 130 30z" fill="#4FA6A0" opacity=".7"/>
        <path d="M520 0H400c30 20 60 30 120 35z" fill="#F2B63D"/>
    </svg>

    {{-- SIDEBAR (tablet dan desktop) --}}
    <aside class="fixed inset-y-0 left-0 z-20 hidden w-60 flex-col border-r-[3px] border-ink bg-ivory p-5 md:flex">
        <a href="{{ route('design.preview') }}" class="mb-8 flex items-center gap-2">
            <span class="flex h-11 w-11 items-center justify-center rounded-2xl border-[3px] border-ink bg-sage shadow-solid-sm">
                <x-icon name="mountain" fill="#F2B63D" :size="26" />
            </span>
            <span class="font-display text-2xl font-bold">Sugaranara</span>
        </a>

        <nav class="flex flex-col gap-2">
            @foreach ($nav as $item)
                <a href="{{ $item['href'] }}"
                   class="flex items-center gap-3 rounded-2xl border-[3px] px-3 py-2 font-semibold transition
                          {{ $item['active'] ? 'border-ink bg-cream shadow-solid-sm' : 'border-transparent hover:bg-cream' }}">
                    <x-icon :name="$item['icon']" :fill="$item['fill']" :size="24" />
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>

        <p class="mt-auto text-xs opacity-60">Semua kiraan adalah anggaran.<br>Mata wang: RM (MYR)</p>
    </aside>

    {{-- KANDUNGAN UTAMA --}}
    <main class="relative md:pl-60">
        <div class="mx-auto max-w-6xl px-4 pb-28 pt-6 md:px-8 md:pb-10">
            {{ $slot }}
        </div>
    </main>

    {{-- BAR TAB BAWAH (telefon) --}}
    <nav class="fixed inset-x-0 bottom-0 z-30 md:hidden" x-data="{ more: false }">
        <div x-show="more" x-cloak x-transition class="card-sugar mx-4 mb-3 grid grid-cols-3 gap-3 p-4">
            @foreach ([1, 4, 5, 6] as $i)
                <a href="{{ $nav[$i]['href'] }}" class="flex flex-col items-center gap-1 text-xs font-semibold">
                    <x-icon :name="$nav[$i]['icon']" :fill="$nav[$i]['fill']" :size="28" />
                    {{ $nav[$i]['label'] }}
                </a>
            @endforeach
        </div>

        <div class="flex items-end justify-around border-t-[3px] border-ink bg-ivory px-2 pb-3 pt-2">
            @foreach ([0, 2] as $i)
                <a href="{{ $nav[$i]['href'] }}" class="flex w-16 flex-col items-center text-[11px] font-semibold">
                    <x-icon :name="$nav[$i]['icon']" :fill="$nav[$i]['fill']" :size="26" />
                    {{ explode(' ', $nav[$i]['label'])[0] }}
                </a>
            @endforeach

            <a href="#" aria-label="Add Money"
               class="-mt-8 flex h-14 w-14 items-center justify-center rounded-full border-[3px] border-ink bg-terracotta shadow-solid-sm">
                <x-icon name="plus" :size="28" class="!stroke-white" />
            </a>

            <a href="{{ $nav[3]['href'] }}" class="flex w-16 flex-col items-center text-[11px] font-semibold">
                <x-icon :name="$nav[3]['icon']" :fill="$nav[3]['fill']" :size="26" />
                Transaksi
            </a>
            <button type="button" @click="more = !more" class="flex w-16 flex-col items-center text-[11px] font-semibold">
                <x-icon name="more" :size="26" />
                Lagi
            </button>
        </div>
    </nav>

</body>
</html>