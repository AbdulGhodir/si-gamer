
@extends('layouts.customer')

@section('title', 'SI Gamer - Beranda')

@section('content')
<div class="flex flex-col gap-8">

    <div class="relative overflow-hidden rounded-2xl bg-linear-to-br from-[#0B0812] via-[#130B29] to-[#2A0845] border border-purple-500/30 p-8 md:p-10">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8">

            <div class="flex flex-col gap-3 max-w-2xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-500/10 border border-purple-500/30 text-purple-300 text-xs font-semibold w-fit">
                    <i class="ph-fill ph-game-controller text-purple-400"></i>
                    <span>Gaming Center</span>
                </div>

                <h1 class="text-3xl md:text-4xl font-extrabold text-white tracking-wide leading-tight">
                    Selamat Datang di SI-GAMER!
                </h1>

                <p class="text-sm md:text-base font-medium text-[#8B7EA1] leading-relaxed">
                    Saatnya push rank, mabar, dan seru-seruan bareng teman! Nikmati fasilitas gaming terbaik dengan pilihan PC dan PlayStation.
                </p>
            </div>

            <div class="flex items-center justify-center">
                <img
                    src="{{ asset('images/PC-Image.jpg') }}"
                    alt="PC Gaming"
                    class="max-w-md h-auto rounded-2xl border-2 border-purple-500/20"
                >
            </div>

        </div>
    </div>
    <div class="flex flex-col gap-6">

        <div>
            <h2 class="text-2xl font-bold text-white tracking-wide">
                Devices
            </h2>
            <p class="text-sm text-[#8B7EA1] mt-1">
                Pilih perangkat gaming sesuai kebutuhanmu.
            </p>
        </div>

        <div class="flex items-center gap-2 overflow-x-auto ">
            <div class="flex items-center gap-2 overflow-x-auto">
                <button type="button"
                        onclick="filterType('all')"
                        id="btn-all"
                        class="filter-btn px-4 py-2 rounded-lg text-xs font-semibold bg-purple-600 text-white border border-white focus:outline-none transition-colors cursor-pointer">
                    Semua tipe
                </button>
                <button type="button"
                        onclick="filterType('PC')"
                        id="btn-PC"
                        class="filter-btn px-4 py-2 rounded-lg text-xs font-semibold bg-[#161124] text-[#8B7EA1] hover:text-white border border-[#2D2442] focus:outline-none transition-colors cursor-pointer">
                    PC Gaming
                </button>
                <button type="button"
                        onclick="filterType('PS4')"
                        id="btn-PS4"
                        class="filter-btn px-4 py-2 rounded-lg text-xs font-semibold bg-[#161124] text-[#8B7EA1] hover:text-white border border-[#2D2442] focus:outline-none transition-colors cursor-pointer">
                    PlayStation 4
                </button>
                <button type="button"
                        onclick="filterType('PS5')"
                        id="btn-PS5"
                        class="filter-btn px-4 py-2 rounded-lg text-xs font-semibold bg-[#161124] text-[#8B7EA1] hover:text-white border border-[#2D2442] focus:outline-none transition-colors cursor-pointer">
                    PlayStation 5
                </button>
            </div>

            <div class="h-6 w-px bg-white/50"></div>

            <div>
                <button type="button"
                        onclick="filterCategory('all')"
                        id="btn-category-all"
                        class="filter-btn-category px-4 py-2 rounded-lg text-xs font-semibold bg-purple-600 text-white border border-white focus:outline-none transition-colors cursor-pointer">
                    Semua kategori
                </button>
                @foreach($categories as $category)
                    <button type="button"
                            onclick="filterCategory(@js($category))"
                            id="btn-category-{{ $category }}"
                            class="filter-btn-category px-4 py-2 rounded-lg text-xs font-semibold bg-[#161124] text-[#8B7EA1] hover:text-white border border-[#2D2442] focus:outline-none transition-colors cursor-pointer">
                        {{ $category }}
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6" id="device-grid">
        @foreach($devices as $device)
            <div class="device-card h-full min-h-[34rem] bg-[#130B29] border border-[#2D2442] hover:border-purple-500/60 rounded-2xl p-5 flex flex-col transition-colors shadow-lg"
                 data-tipe="{{ $device->tipe }}" data-category="{{ $device->kategori }}">

                <div class="w-full h-44 rounded-xl bg-[#0B0812] border border-[#2D2442] flex items-center justify-center mb-5">
                    @if($device->tipe === 'PC')
                        <div class="flex flex-col items-center justify-center gap-2">
                            <div class="w-20 h-16 rounded-lg bg-linear-to-b from-[#38bdf8]/30 to-[#0284c7]/20 border border-sky-400/40 flex items-center justify-center shadow-md">
                                <i class="ph ph-desktop text-4xl text-sky-400"></i>
                            </div>
                            <div class="w-16 h-2 bg-[#2D2442] rounded-full border border-sky-400/20"></div>
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center">
                            <div class="w-20 h-16 rounded-2xl bg-purple-600/20 border border-purple-500/40 flex items-center justify-center shadow-lg shadow-purple-500/10">
                                <i class="ph-fill ph-game-controller text-4xl text-purple-400"></i>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="flex flex-1 flex-col gap-2 mb-6">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold tracking-wider uppercase text-purple-400">
                            {{ $device->tipe }}
                        </span>


                        <span class="text-[14px] font-bold tracking-normal {{ $device->kategori === 'Reguler' ? 'text-purple-400' : 'text-amber-400' }}">
                            {{ $device->kategori }}
                        </span>
                    </div>

                    <h3 class="min-h-12 text-lg font-bold text-white tracking-wide leading-tight">
                        {{ $device->nama_device }}
                    </h3>

                    <p class="min-h-8 text-xs text-[#8B7EA1] leading-relaxed line-clamp-2">
                        {{ $device->spesifikasi ?: 'Spesifikasi belum tersedia.' }}
                    </p>

                    <div class="mt-2 flex items-baseline gap-1">
                        <span class="text-xl font-bold text-white">
                            Rp{{ number_format($device->harga_per_jam, 0, ',', '.') }}
                        </span>
                        <span class="text-xs text-[#8B7EA1]">/ jam</span>
                    </div>
                </div>

                <div class="flex min-h-16 items-center justify-between gap-3 pt-4 border-t border-[#2D2442]">

                    <div>
                        @if($device->status === 'tersedia')
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                                <span class="text-xs font-semibold text-emerald-400">Tersedia</span>
                            </div>
                        @elseif($device->status === 'dipesan')
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-blue-400"></span>
                                <span class="text-xs font-semibold text-blue-400">Dipesan</span>
                            </div>
                        @elseif($device->status === 'bermain')
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-purple-400"></span>
                                <span class="text-xs font-semibold text-purple-400">Sedang digunakan</span>
                            </div>
                        @elseif(in_array($device->status, ['penuh', 'offline']))
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-red-400"></span>
                                <span class="text-xs font-semibold text-red-400">{{ $device->status === 'penuh' ? 'Penuh' : 'Offline' }}</span>
                            </div>
                        @elseif(in_array($device->status, ['maintenance', 'perbaikan']))
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                                <span class="text-xs font-semibold text-amber-400">Maintenance</span>
                            </div>
                        @else
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                                <span class="text-xs font-semibold text-slate-400">{{ ucfirst($device->status) }}</span>
                            </div>
                        @endif
                    </div>

                    <div>
                        @if(in_array($device->status, ['tersedia', 'dipesan', 'bermain']))
                            <a href="{{ route('customer.booking', ['device' => $device->id]) }}"
                               class="inline-block px-5 py-2 rounded-lg bg-purple-600 hover:bg-purple-500 text-white font-semibold text-xs tracking-wide transition-colors cursor-pointer shadow-md shadow-purple-600/20 text-center">
                                Booking
                            </a>
                        @else
                            <button type="button"
                                    disabled
                                    class="px-5 py-2 rounded-lg bg-[#161124] text-[#8B7EA1] border border-[#2D2442] font-semibold text-xs tracking-wide cursor-not-allowed">
                                {{ in_array($device->status, ['maintenance', 'perbaikan']) ? 'Maintenance' : ($device->status === 'penuh' ? 'Penuh' : 'Offline') }}
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach

    </div>
    @if($devices->isEmpty())
        <div class="rounded-xl border border-[#2D2442] bg-[#130B29] px-4 py-8 text-center">
            <p class="text-sm font-semibold text-white">Belum ada perangkat tersedia di katalog.</p>
            <p class="text-xs text-[#8B7EA1] mt-1">Silakan hubungi admin untuk informasi lebih lanjut.</p>
        </div>
    @else
        <div id="device-filter-empty" class="hidden rounded-xl border border-[#2D2442] bg-[#130B29] px-4 py-8 text-center">
            <p class="text-sm font-semibold text-white">Tidak ada perangkat yang cocok dengan filter ini.</p>
            <button type="button" onclick="filterType('all'); filterCategory('all')"
                    class="mt-3 text-xs font-semibold text-purple-400 hover:text-purple-300">
                Tampilkan semua perangkat
            </button>
        </div>
    @endif
</div>

<script>
    let selectedType = 'all';
    let selectedCategory = 'all';

    function normalizeFilterValue(value) {
        return (value ?? '').trim().toLowerCase();
    }

    function filterType(type='all') {
        selectedType = normalizeFilterValue(type);

        const buttons = document.querySelectorAll('.filter-btn');
        buttons.forEach(btn => {
            btn.classList.remove('bg-purple-600', 'text-white', 'border-white');
            btn.classList.add('bg-[#161124]', 'text-[#8B7EA1]', 'border-[#2D2442]');
        });

        const activeBtn = document.getElementById('btn-' + type);
        if (activeBtn) {
            activeBtn.classList.remove('bg-[#161124]', 'text-[#8B7EA1]', 'border-[#2D2442]');
            activeBtn.classList.add('bg-purple-600', 'text-white', 'border-white');
        }

        applyFilters();
    }

    function filterCategory(category) {
        selectedCategory = normalizeFilterValue(category);

        const buttons = document.querySelectorAll('.filter-btn-category');
        buttons.forEach(btn => {
            btn.classList.remove('bg-purple-600', 'text-white', 'border-white');
            btn.classList.add('bg-[#161124]', 'text-[#8B7EA1]', 'border-[#2D2442]');
        });

        const activeBtn = document.getElementById('btn-category-' + category);
        if (activeBtn) {
            activeBtn.classList.remove('bg-[#161124]', 'text-[#8B7EA1]', 'border-[#2D2442]');
            activeBtn.classList.add('bg-purple-600', 'text-white', 'border-white');
        }

        applyFilters();
    }

    function applyFilters() {
        const cards = document.querySelectorAll('.device-card');
        const emptyState = document.getElementById('device-filter-empty');
        let visibleCount = 0;

        cards.forEach(card => {
            const cardType = normalizeFilterValue(card.getAttribute('data-tipe'));
            const cardCategory = normalizeFilterValue(card.getAttribute('data-category'));

            const typeMatch = (selectedType === 'all' || cardType === selectedType);
            const categoryMatch = (selectedCategory === 'all' || cardCategory === selectedCategory);

            if (typeMatch && categoryMatch) {
                card.style.display = 'flex';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        if (emptyState) {
            emptyState.classList.toggle('hidden', visibleCount > 0);
        }
    }
</script>
@endsection
