@extends('layouts.staff')

@section('content')
<!-- Header Utama -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h2 class="text-2xl font-bold text-white tracking-tight">Operasional hari ini</h2>
        <p class="text-xs text-slate-400 mt-1">Pantau booking, kelola sesi, dan pastikan semua perangkat siap.</p>
    </div>
    <button @click="walkinModal = true" class="bg-purple-600 hover:bg-purple-500 text-white text-xs font-semibold px-4 py-2.5 rounded-lg flex items-center space-x-2 transition shadow-lg shadow-purple-600/20">
        <i class="fa-solid fa-plus text-xs"></i>
        <span>Booking walk-in</span>
    </button>
</div>

<!-- 4 KARTU STATISTIK RINGKASAN -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-[#12131a] border border-slate-800/80 rounded-xl p-4 flex flex-col justify-between">
        <div class="flex justify-between items-start text-slate-400 mb-2">
            <span class="text-xs font-medium">Booking hari ini</span>
            <i class="fa-regular fa-calendar text-xs"></i>
        </div>
        <div>
            <h3 class="text-2xl font-bold text-white">{{ $bookingHariIni }}</h3>
            <p class="text-[11px] text-slate-400 mt-1">{{ $bookingAkanDatang }} akan datang</p>
        </div>
    </div>

    <div class="bg-[#12131a] border border-slate-800/80 rounded-xl p-4 flex flex-col justify-between">
        <div class="flex justify-between items-start text-slate-400 mb-2">
            <span class="text-xs font-medium">Sesi aktif</span>
            <i class="fa-regular fa-clock text-xs"></i>
        </div>
        <div>
            <h3 class="text-2xl font-bold text-white">{{ $sesiAktifCount }}</h3>
            <p class="text-[11px] text-slate-400 mt-1">dari {{ $totalPerangkat }} perangkat</p>
        </div>
    </div>

    <div class="bg-[#12131a] border border-slate-800/80 rounded-xl p-4 flex flex-col justify-between">
        <div class="flex justify-between items-start text-slate-400 mb-2">
            <span class="text-xs font-medium">Perangkat siap</span>
            <i class="fa-regular fa-square-check text-xs"></i>
        </div>
        <div>
            <h3 class="text-2xl font-bold text-white">{{ $perangkatSiap }}</h3>
            <p class="text-[11px] text-slate-400 mt-1">siap digunakan</p>
        </div>
    </div>

    <div class="bg-[#12131a] border border-slate-800/80 rounded-xl p-4 flex flex-col justify-between">
        <div class="flex justify-between items-start text-slate-400 mb-2">
            <span class="text-xs font-medium">Perlu maintenance</span>
            <i class="fa-solid fa-wrench text-xs"></i>
        </div>
        <div>
            <h3 class="text-2xl font-bold text-white">{{ $perluMaintenance }}</h3>
            <span class="inline-block text-[10px] text-slate-400 bg-slate-800/80 px-2 py-0.5 rounded mt-1">
                {{ $perangkatMaintenance->nama_device ?? 'Tidak ada unit' }}
            </span>
        </div>
    </div>
</div>

<!-- BARIS TENGAH: TABEL BOOKING & PANEL SESI AKTIF -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <!-- Tabel Booking Hari Ini -->
    <div id="table-booking" class="lg:col-span-2 bg-[#12131a] border border-slate-800/80 rounded-xl p-5">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-4">
            <div class="flex items-baseline space-x-2">
                <h3 class="text-sm font-bold text-white">Daftar booking</h3>
                <span class="text-xs text-slate-400">{{ now()->translatedFormat('d F Y') }}</span>
            </div>
            <a href="{{ route('staff.dashboard') }}" class="text-xs text-purple-400 hover:text-purple-300 font-medium">Semua booking &rarr;</a>
        </div>

        <!-- Filter Pencarian & Status Form -->
        <form method="GET" action="{{ route('staff.dashboard') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-4">
            <div class="sm:col-span-2 relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-slate-500 text-xs"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode booking atau nama pelanggan" class="w-full bg-[#181924] border border-slate-800 rounded-lg pl-8 pr-3 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-purple-600">
            </div>
            <div>
                <select name="status" onchange="this.form.submit()" class="w-full bg-[#181924] border border-slate-800 rounded-lg px-3 py-2 text-xs text-slate-300 focus:outline-none focus:border-purple-600">
                    <option value="Semua" {{ request('status') == 'Semua' ? 'selected' : '' }}>Semua status</option>
                    <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                    <option value="bermain" {{ request('status') == 'bermain' ? 'selected' : '' }}>Bermain</option>
                    <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="dibatalkan" {{ request('status') == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>
        </form>

        <!-- Tabel Data -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="text-slate-400 border-b border-slate-800/80 pb-2">
                        <th class="pb-3 font-medium">Kode / pelanggan</th>
                        <th class="pb-3 font-medium">Perangkat</th>
                        <th class="pb-3 font-medium">Jadwal</th>
                        <th class="pb-3 font-medium">Status</th>
                        <th class="pb-3 font-medium text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/40">
                    @forelse($daftarBooking as $booking)
                        <tr>
                            <td class="py-3">
                                <p class="font-semibold text-white">{{ $booking->kode_booking }}</p>
                                <p class="text-[11px] text-slate-400">
                                    {{ $booking->user->nama ?? 'Pelanggan' }}
                                    @if($booking->jenis_booking === 'langsung')
                                        &middot; <span class="italic text-purple-400">walk-in</span>
                                    @endif
                                </p>
                            </td>
                            <td class="py-3 text-slate-300">{{ $booking->device->nama_device ?? '-' }}</td>
                            <td class="py-3 text-slate-400">{{ \Carbon\Carbon::parse($booking->waktu_mulai)->format('H.i') }}–{{ \Carbon\Carbon::parse($booking->waktu_selesai)->format('H.i') }}</td>
                            <td class="py-3">
                                @if($booking->status === 'menunggu')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-emerald-500/10 text-emerald-400">
                                        <span class="w-1 h-1 rounded-full bg-emerald-400 mr-1.5"></span> {{ ucfirst($booking->status_konfirmasi == 'dikonfirmasi' ? 'Terkonfirmasi' : 'Menunggu') }}
                                    </span>
                                @elseif($booking->status === 'bermain')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-purple-500/10 text-purple-400">
                                        <span class="w-1 h-1 rounded-full bg-purple-400 mr-1.5"></span> Bermain
                                    </span>
                                @elseif($booking->status === 'selesai')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-slate-500/10 text-slate-400">
                                        <span class="w-1 h-1 rounded-full bg-slate-400 mr-1.5"></span> Selesai
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-rose-500/10 text-rose-400">
                                        <span class="w-1 h-1 rounded-full bg-rose-400 mr-1.5"></span> Dibatalkan
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 text-right">
                                @if($booking->status === 'menunggu')
                                    <form action="{{ route('staff.booking.checkin', $booking->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="bg-purple-600 hover:bg-purple-500 text-white text-[11px] font-medium px-3 py-1 rounded transition">Check-in</button>
                                    </form>
                                @elseif($booking->status === 'bermain')
                                    <form action="{{ route('staff.booking.checkout', $booking->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="bg-[#1e202e] hover:bg-slate-700 text-white text-[11px] px-3 py-1 rounded transition">Check-out</button>
                                    </form>
                                @else
                                    <span class="text-slate-500 text-[11px]">Selesai</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-6 text-center text-slate-500">Tidak ada data booking hari ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Footer Pagination -->
        <div class="flex justify-between items-center text-[11px] text-slate-400 pt-4 mt-2 border-t border-slate-800/80">
            <span>{{ $daftarBooking->count() }} dari {{ $daftarBooking->total() }} booking &middot; {{ $totalSelesaiHariIni }} sesi selesai</span>
            <div>
                {{ $daftarBooking->links() }}
            </div>
        </div>
    </div>

    <!-- Sesi Aktif (Panel Kanan) -->
    <div id="sesi-aktif" class="bg-[#12131a] border border-slate-800/80 rounded-xl p-5 flex flex-col justify-between">
        <div>
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-sm font-bold text-white">Sesi aktif</h3>
                <span class="text-xs text-slate-400 font-medium">{{ $sesiAktif->count() }} sesi</span>
            </div>

            <div class="space-y-3">
                @forelse($sesiAktif as $sesi)
                    @php
                        $waktuSelesai = \Carbon\Carbon::parse($sesi->waktu_selesai);
                        $sisaMenit = max(0, now()->diffInMinutes($waktuSelesai, false));
                        $sisaJam = floor($sisaMenit / 60);
                        $sisaMenitSisa = $sisaMenit % 60;
                    @endphp
                    <div class="flex items-center justify-between p-3 rounded-lg bg-[#181924] border border-slate-800/60">
                        <div>
                            <p class="text-xs font-semibold text-white">
                                {{ $sesi->device->nama_device ?? '-' }}
                                <span class="font-normal text-slate-400">{{ $sesi->user->nama ?? 'Pelanggan' }}</span>
                            </p>
                            <p class="text-[10px] text-slate-500">{{ \Carbon\Carbon::parse($sesi->waktu_mulai)->format('H.i') }}–{{ $waktuSelesai->format('H.i') }} WIB</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="text-right">
                                <p class="text-xs font-bold text-white leading-none">
                                    {{ sprintf('%02d:%02d', $sisaJam, $sisaMenitSisa) }}
                                </p>
                                <p class="text-[9px] text-slate-400">sisa waktu</p>
                            </div>
                            <form action="{{ route('staff.booking.add_duration', $sesi->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-6 h-6 rounded bg-[#232534] hover:bg-purple-600 text-slate-300 hover:text-white text-xs flex items-center justify-center transition" title="Tambah 1 Jam">+</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-500 py-4 text-center">Belum ada sesi bermain yang aktif.</p>
                @endforelse
            </div>
        </div>

        <p class="text-[11px] text-slate-500 mt-4 flex items-center space-x-1.5">
            <i class="fa-regular fa-circle-question text-[10px]"></i>
            <span>Tambah durasi lewat tombol + pada sesi.</span>
        </p>
    </div>
</div>

<!-- BARIS BAWAH: STATUS PERANGKAT & MAINTENANCE -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Grid Status Perangkat -->
    <div id="status-perangkat" class="lg:col-span-2 bg-[#12131a] border border-slate-800/80 rounded-xl p-5">
        <div class="flex justify-between items-center mb-4">
            <div class="flex items-baseline space-x-2">
                <h3 class="text-sm font-bold text-white">Status perangkat</h3>
                <span class="text-xs text-slate-400">{{ $totalPerangkat }} unit</span>
            </div>
        </div>

        <div class="space-y-4">
            @foreach($devices as $kategori => $items)
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">{{ $kategori }}</p>
                    <div class="grid grid-cols-4 sm:grid-cols-6 gap-2">
                        @foreach($items as $dev)
                            @php
                                $badgeStyle = match($dev->status) {
                                    'tersedia' => 'bg-emerald-500/15 border border-emerald-500/30 text-emerald-400',
                                    'bermain'  => 'bg-purple-600/20 border border-purple-500/40 text-purple-300',
                                    'perbaikan'=> 'bg-amber-500/15 border border-amber-500/30 text-amber-400',
                                    default    => 'bg-slate-800 border border-slate-700 text-slate-400'
                                };
                            @endphp
                            <div class="py-2.5 rounded-lg text-center font-bold text-xs {{ $badgeStyle }}" title="{{ $dev->nama_device }} ({{ $dev->status }})">
                                {{ preg_replace('/[^0-9]/', '', $dev->nama_device) ?: $dev->nama_device }}
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <div class="flex flex-wrap items-center justify-between gap-4 pt-4 mt-4 border-t border-slate-800/80 text-[11px]">
            <div class="flex items-center space-x-4">
                <span class="flex items-center text-slate-300">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 mr-2"></span> Siap &middot; {{ $perangkatSiap }}
                </span>
                <span class="flex items-center text-slate-300">
                    <span class="w-2 h-2 rounded-full bg-purple-400 mr-2"></span> Digunakan &middot; {{ $sesiAktifCount }}
                </span>
                <span class="flex items-center text-slate-300">
                    <span class="w-2 h-2 rounded-full bg-amber-400 mr-2"></span> Maintenance &middot; {{ $perluMaintenance }}
                </span>
            </div>
            <span class="text-slate-500">Diperbarui {{ now()->format('H.i') }} WIB</span>
        </div>
    </div>

    <!-- Panel Status Maintenance -->
    <div class="bg-[#12131a] border border-slate-800/80 rounded-xl p-5 flex flex-col justify-between">
        <div>
            <h3 class="text-sm font-bold text-white mb-4">Laporan maintenance</h3>
            
            @if($perangkatMaintenance)
                <div class="p-4 rounded-lg bg-[#181924] border border-slate-800/60 mb-4">
                    <div class="flex justify-between items-start mb-2">
                        <span class="text-xs font-bold text-white">{{ $perangkatMaintenance->nama_device }}</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-amber-500/10 text-amber-400 border border-amber-500/20">
                            <span class="w-1 h-1 rounded-full bg-amber-400 mr-1.5"></span> Dalam perbaikan
                        </span>
                    </div>
                    <p class="text-xs text-slate-300 leading-relaxed mb-3">
                        Perangkat dalam status penanganan teknis. Unit dinonaktifkan sementara dari antrean sewa.
                    </p>
                    <p class="text-[10px] text-slate-500 font-mono">
                        {{ $perangkatMaintenance->tipe }} &middot; Diperbarui {{ $perangkatMaintenance->updated_at->diffForHumans() }}
                    </p>
                </div>
            @else
                <div class="p-4 rounded-lg bg-[#181924]/50 border border-slate-800/40 text-center py-6 mb-4">
                    <i class="fa-solid fa-circle-check text-emerald-400 text-lg mb-2"></i>
                    <p class="text-xs text-slate-400">Semua perangkat dalam kondisi optimal.</p>
                </div>
            @endif
        </div>

        <button @click="maintenanceModal = true" class="w-full bg-[#1e202e] hover:bg-slate-800 border border-slate-700/60 text-white text-xs font-medium py-2.5 rounded-lg flex items-center justify-center space-x-2 transition">
            <i class="fa-solid fa-plus text-xs"></i>
            <span>Set unit ke perbaikan</span>
        </button>
    </div>
</div>
@endsection