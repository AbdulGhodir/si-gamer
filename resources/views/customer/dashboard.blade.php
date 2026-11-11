
@extends('layouts.customer')

@section('title', 'SI Gamer - Beranda')

@section('content')
<div class="flex flex-col gap-8">

    <!-- HERO SECTION -->
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

    <!-- DEVICES SECTION -->
    <div class="flex flex-col gap-6">

        <div>
            <h2 class="text-2xl font-bold text-white tracking-wide">
                Devices
            </h2>
            <p class="text-sm text-[#8B7EA1] mt-1">
                Pilih perangkat gaming sesuai kebutuhanmu.
            </p>
        </div>

        <!-- ================================= -->
        <!-- REGULER -->
        <!-- ================================= -->

        <div class="flex flex-col gap-4">

            <div class="flex items-center gap-3">
                <h3 class="text-lg font-bold text-white">Reguler</h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

                @foreach($dummyDevices as $device)

                    @if($device['kelas'] === 'Reguler')

                        <div class="bg-[#130B29] border border-[#2D2442] hover:border-purple-500/60 rounded-2xl p-5 flex flex-col justify-between transition-colors shadow-lg">

                            <!-- GAMBAR PERANGKAT -->
                            <div class="w-full h-44 rounded-xl bg-[#0B0812] border border-[#2D2442] flex items-center justify-center mb-5">

                                @if($device['tipe'] === 'PC')

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

                            <!-- INFORMASI PERANGKAT -->
                            <div class="flex flex-col gap-2 mb-6">

                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] font-bold tracking-wider uppercase text-purple-400">
                                        {{ $device['tipe'] }}
                                    </span>

                                    <span class="px-2.5 py-1 rounded-md bg-[#211638] border border-purple-500/30 text-purple-300 text-[10px] font-bold">
                                        REGULER
                                    </span>
                                </div>

                                <h3 class="text-lg font-bold text-white tracking-wide leading-tight">
                                    {{ $device['nama_device'] }}
                                </h3>

                                <p class="text-xs text-[#8B7EA1] leading-relaxed line-clamp-2">
                                    {{ $device['spesifikasi'] }}
                                </p>

                                <div class="mt-2 flex items-baseline gap-1">
                                    <span class="text-xl font-bold text-white">
                                        Rp{{ number_format($device['harga_per_jam'], 0, ',', '.') }}
                                    </span>

                                    <span class="text-xs text-[#8B7EA1]">
                                        / jam
                                    </span>
                                </div>

                            </div>

                            <!-- STATUS DAN BOOKING -->
                            <div class="flex items-center justify-between pt-4 border-t border-[#2D2442]">

                                <div>
                                    @if($device['status'] === 'tersedia')

                                        <div class="flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                                            <span class="text-xs font-semibold text-emerald-400">Tersedia</span>
                                        </div>

                                    @elseif($device['status'] === 'penuh')

                                        <div class="flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full bg-red-400"></span>
                                            <span class="text-xs font-semibold text-red-400">Penuh</span>
                                        </div>

                                    @elseif($device['status'] === 'maintenance')

                                        <div class="flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                                            <span class="text-xs font-semibold text-amber-400">Maintenance</span>
                                        </div>

                                    @endif
                                </div>

                                @if($device['status'] === 'tersedia')

                                    <a
                                        href="{{ route('customer.booking') }}"
                                        class="inline-block px-5 py-2 rounded-lg bg-purple-600 hover:bg-purple-500 text-white font-semibold text-xs tracking-wide transition-colors cursor-pointer shadow-md shadow-purple-600/20 text-center"
                                    >
                                        Booking
                                    </a>

                                @else

                                    <button
                                        type="button"
                                        disabled
                                        class="px-5 py-2 rounded-lg bg-[#161124] text-[#8B7EA1] border border-[#2D2442] font-semibold text-xs tracking-wide cursor-not-allowed"
                                    >
                                        {{ $device['status'] === 'penuh' ? 'Penuh' : 'Maintenance' }}
                                    </button>

                                @endif

                            </div>

                        </div>

                    @endif

                @endforeach

            </div>
        </div>

        <!-- ================================= -->
        <!-- VIP -->
        <!-- ================================= -->

        <div class="flex flex-col gap-4 mt-4">

            <div class="flex items-center gap-3">
                <h3 class="text-lg font-bold text-white">VIP</h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

                @foreach($dummyDevices as $device)

                    @if($device['kelas'] === 'VIP')

                        <div class="bg-[#130B29] border border-purple-500/20 hover:border-purple-500/60 rounded-2xl p-5 flex flex-col justify-between transition-colors shadow-lg">

                            <!-- GAMBAR PERANGKAT -->
                            <div class="w-full h-44 rounded-xl bg-[#0B0812] border border-[#2D2442] flex items-center justify-center mb-5">

                                @if($device['tipe'] === 'PC')

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

                            <!-- INFORMASI PERANGKAT -->
                            <div class="flex flex-col gap-2 mb-6">

                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] font-bold tracking-wider uppercase text-purple-400">
                                        {{ $device['tipe'] }}
                                    </span>

                                    <span class="px-2.5 py-1 rounded-md bg-purple-500/15 border border-purple-500/40 text-purple-300 text-[10px] font-bold">
                                        VIP
                                    </span>
                                </div>

                                <h3 class="text-lg font-bold text-white tracking-wide leading-tight">
                                    {{ $device['nama_device'] }}
                                </h3>

                                <p class="text-xs text-[#8B7EA1] leading-relaxed line-clamp-2">
                                    {{ $device['spesifikasi'] }}
                                </p>

                                <div class="mt-2 flex items-baseline gap-1">
                                    <span class="text-xl font-bold text-white">
                                        Rp{{ number_format($device['harga_per_jam'], 0, ',', '.') }}
                                    </span>

                                    <span class="text-xs text-[#8B7EA1]">
                                        / jam
                                    </span>
                                </div>

                            </div>

                            <!-- STATUS DAN BOOKING -->
                            <div class="flex items-center justify-between pt-4 border-t border-[#2D2442]">

                                <div>
                                    @if($device['status'] === 'tersedia')

                                        <div class="flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                                            <span class="text-xs font-semibold text-emerald-400">Tersedia</span>
                                        </div>

                                    @elseif($device['status'] === 'penuh')

                                        <div class="flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full bg-red-400"></span>
                                            <span class="text-xs font-semibold text-red-400">Penuh</span>
                                        </div>

                                    @elseif($device['status'] === 'maintenance')

                                        <div class="flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                                            <span class="text-xs font-semibold text-amber-400">Maintenance</span>
                                        </div>

                                    @endif
                                </div>

                                @if($device['status'] === 'tersedia')

                                    <a
                                        href="{{ route('customer.booking') }}"
                                        class="inline-block px-5 py-2 rounded-lg bg-purple-600 hover:bg-purple-500 text-white font-semibold text-xs tracking-wide transition-colors cursor-pointer shadow-md shadow-purple-600/20 text-center"
                                    >
                                        Booking
                                    </a>

                                @else

                                    <button
                                        type="button"
                                        disabled
                                        class="px-5 py-2 rounded-lg bg-[#161124] text-[#8B7EA1] border border-[#2D2442] font-semibold text-xs tracking-wide cursor-not-allowed"
                                    >
                                        {{ $device['status'] === 'penuh' ? 'Penuh' : 'Maintenance' }}
                                    </button>

                                @endif

                            </div>

                        </div>

                    @endif

                @endforeach

            </div>
        </div>

    </div>
</div>
@endsection
