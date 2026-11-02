<x-layouts.app title="Design Preview">

    {{-- WELCOME --}}
    <header class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <p class="font-semibold text-forest">Selamat datang kembali 👋</p>
            <h1 class="text-3xl md:text-4xl">
                Satu puncak demi satu puncak.
            </h1>
        </div>
        <div class="flex gap-3">
            <a href="#" class="btn-sugar btn-primary">
                <x-icon name="plus" :size="20" class="!stroke-white" /> Add Money
            </a>
            <a href="#" class="btn-sugar btn-danger">
                <x-icon name="minus" :size="20" /> Withdraw
            </a>
        </div>
    </header>

    {{-- KAD RINGKASAN (data contoh) --}}
    <section class="mt-8 grid gap-5 lg:grid-cols-5">
        <div class="card-sugar card-sugar-hover p-6 lg:col-span-3">
            <p class="font-semibold opacity-70">Current Savings</p>
            <p class="mt-2 text-5xl font-extrabold">RM 1,000.00</p>
            <p class="mt-3 inline-block rounded-full border-[3px] border-ink bg-lime px-3 py-1 text-sm font-bold">
                Boleh digunakan sekarang
            </p>
        </div>

        <div class="card-sugar card-sugar-hover relative z-10 bg-forest p-6 text-ivory lg:col-span-2 lg:-ml-8 lg:mt-6 lg:rotate-1"
             style="background-color: var(--color-forest)">
            <p class="font-semibold opacity-80">Total Savings</p>
            <p class="mt-2 text-4xl font-extrabold">RM 1,500.00</p>
            <div class="mt-4 space-y-1 text-sm">
                <p class="flex justify-between"><span>Dalam goals</span><b>RM 500.00</b></p>
                <p class="flex justify-between"><span>Goals aktif</span><b>1</b></p>
            </div>
        </div>
    </section>

    {{-- GOALS (data contoh) --}}
    <section class="mt-10">
        <h2 class="mb-4 text-2xl">My Savings Goals</h2>
        <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ([
                ['Majlis Perkahwinan', 500, 10000, '#D9663A', '#F2B63D'],
                ['Cuti ke Jepun', 2400, 6000, '#4FA6A0', '#BFE87A'],
                ['Dana Kecemasan', 4500, 5000, '#1F4D3A', '#A9C4A0'],
            ] as [$name, $saved, $target, $color, $bg])
                @php $pct = round($saved / $target * 100); @endphp
                <article class="card-sugar card-sugar-hover overflow-hidden">
                    <div class="flex h-28 items-center justify-center border-b-[3px] border-ink" style="background-color: {{ $bg }}">
                        <x-icon name="mountain" :fill="$color" :size="64" class="float-soft" />
                    </div>
                    <div class="p-5">
                        <h3 class="text-xl">{{ $name }}</h3>
                        <p class="mt-1 text-sm opacity-70">
                            RM {{ number_format($saved, 2) }} / RM {{ number_format($target, 2) }}
                        </p>
                        <div class="progress-track mt-4">
                            <div class="progress-fill" style="width: {{ $pct }}%; background-color: {{ $color }}"></div>
                        </div>
                        <p class="mt-2 text-right text-sm font-bold">{{ $pct }}%</p>
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    {{-- PALET WARNA --}}
    <section class="mt-10">
        <h2 class="mb-4 text-2xl">Palet Warna</h2>
        <div class="flex flex-wrap gap-3">
            @foreach ([
                'cream' => '#FFF4DC', 'ivory' => '#FFFBF0', 'ink' => '#2A1B6B', 'forest' => '#1F4D3A',
                'sage' => '#A9C4A0', 'terracotta' => '#D9663A', 'coral' => '#FF7A6B',
                'mustard' => '#F2B63D', 'lagoon' => '#4FA6A0', 'lime' => '#BFE87A',
            ] as $name => $hex)
                <div class="text-center text-xs font-semibold">
                    <div class="h-14 w-14 rounded-2xl border-[3px] border-ink" style="background-color: {{ $hex }}"></div>
                    {{ $name }}
                </div>
            @endforeach
        </div>
    </section>

</x-layouts.app>