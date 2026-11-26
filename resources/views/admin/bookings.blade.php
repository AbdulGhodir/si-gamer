@extends('layouts.admin')

@section('content')
@php
    $bookingList = collect($bookings ?? []);
    $totalBooking = $bookingList->count();
    $dikonfirmasiCount = $bookingList->where('status', 'Dikonfirmasi')->count();
    $menungguCount = $bookingList->where('status', '!=', 'Dikonfirmasi')->count();
@endphp

<header class="p-6 pb-4 border-b border-gray-800/50">
    <div>
        <h1 class="text-2xl font-bold text-white tracking-tight">Daftar Booking</h1>
        <p class="text-xs text-gray-400 mt-0.5">Pantau jadwal pemesanan perangkat dan status pembayaran.</p>
    </div>
</header>

<div class="p-6 space-y-6">
    @if(session('success'))
        <div class="bg-emerald-950/60 border border-emerald-700/60 text-emerald-300 px-4 py-3 rounded-xl text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-rose-950/60 border border-rose-700/60 text-rose-300 px-4 py-3 rounded-xl text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Card Ringkasan Booking -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-[#141221] border border-gray-800/80 p-5 rounded-2xl shadow-sm">
            <p class="text-xs text-gray-400">Total Booking</p>
            <h2 class="text-2xl font-bold text-white mt-1">{{ $totalBooking }}</h2>
            <p class="text-xs text-gray-400 mt-2">Jadwal terdaftar</p>
        </div>
        <div class="bg-[#141221] border border-gray-800/80 p-5 rounded-2xl shadow-sm">
            <p class="text-xs text-gray-400">Dikonfirmasi</p>
            <h2 class="text-2xl font-bold text-emerald-400 mt-1">{{ $dikonfirmasiCount }}</h2>
            <p class="text-xs text-gray-400 mt-2">Dikonfirmasi staff</p>
        </div>
        <div class="bg-[#141221] border border-gray-800/80 p-5 rounded-2xl shadow-sm">
            <p class="text-xs text-gray-400">Menunggu</p>
            <h2 class="text-2xl font-bold text-amber-400 mt-1">{{ $menungguCount }}</h2>
            <p class="text-xs text-gray-400 mt-2">Menunggu check-in staff</p>
        </div>
    </div>

    <!-- Tabel Jadwal Booking -->
    <div class="bg-[#141221] border border-gray-800/80 rounded-2xl overflow-hidden shadow-sm">
        <div class="p-4 border-b border-gray-800 flex justify-between items-center">
            <h3 class="font-semibold text-white">Jadwal Booking</h3>
            <span class="text-xs text-gray-400">Informasi booking dan pembayaran</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse" id="bookingsTable">
                <thead>
                    <tr class="border-b border-gray-800 text-xs text-gray-400 bg-[#110f1c]">
                        <th class="p-4">ID Booking</th>
                        <th class="p-4">Pelanggan</th>
                        <th class="p-4">Jadwal Main</th>
                        <th class="p-4">Durasi</th>
                        <th class="p-4">Total / Harga</th>
                        <th class="p-4">Pembayaran</th>
                        <th class="p-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800/60 text-sm" id="bookingsTableBody">
                    @forelse($bookingList as $b)
                    @php
                        $bookingStatus = $b['status'] ?? 'Menunggu';
                        $isConfirmed = $bookingStatus === 'Dikonfirmasi';
                        $paymentStatus = $b['status_bayar'] ?? 'Belum bayar';
                        $isPaid = $paymentStatus === 'Lunas';
                        $totalHarga = $b['total'] ?? 'Rp0';
                    @endphp
                    <tr class="booking-row hover:bg-white/[0.02] transition">
                        <td class="p-4 font-medium text-white">{{ $b['id'] ?? '-' }}</td>
                        <td class="p-4 text-gray-300">{{ $b['pelanggan'] ?? '-' }}</td>
                        <td class="p-4 text-gray-400 text-xs">{{ $b['jadwal'] ?? '-' }}</td>
                        <td class="p-4 text-purple-400">{{ $b['durasi'] ?? '-' }}</td>
                        <td class="p-4 font-semibold text-white">{{ $totalHarga }}</td>
                        <td class="p-4">
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $isPaid ? 'bg-emerald-900/50 text-emerald-300' : 'bg-rose-900/50 text-rose-300' }}">
                                {{ $paymentStatus }}
                            </span>
                        </td>
                        <td class="p-4">
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $isConfirmed ? 'bg-emerald-900/50 text-emerald-300' : 'bg-amber-900/50 text-amber-300' }}">
                                {{ $bookingStatus }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-6 text-center text-xs text-gray-500">
                            Belum ada data booking di database.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection