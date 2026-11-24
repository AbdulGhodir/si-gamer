@extends('layouts.admin')

@section('content')
<header class="p-6 pb-4 border-b border-gray-800/50 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-white tracking-tight">Transaksi Digabung ke Booking</h1>
        <p class="text-xs text-gray-400 mt-0.5">Status pembayaran dan total biaya kini dikelola langsung di menu Booking, sedangkan rekap pendapatan ada di Laporan Operasional.</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.bookings') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-xl text-sm font-medium transition">
            Buka Booking
        </a>
        <a href="{{ route('admin.reports') }}" class="bg-[#1b182e] hover:bg-[#25213d] border border-gray-700 text-gray-200 px-4 py-2 rounded-xl text-sm font-medium transition">
            Buka Laporan
        </a>
    </div>
</header>

<div class="p-6">
    <div class="bg-[#141221] border border-gray-800/80 rounded-2xl p-6 text-sm text-gray-300 space-y-2">
        <p class="font-semibold text-white">Alur Terbaru GameHub:</p>
        <p>• <span class="text-purple-400 font-medium">Booking:</span> Memuat ID booking, pelanggan, jadwal main, durasi, total/harga, status pembayaran (Lunas / Belum bayar), dan status booking.</p>
        <p>• <span class="text-purple-400 font-medium">Laporan Operasional:</span> Memuat total pendapatan, jumlah sesi, utilisasi, dan ekspor laporan.</p>
    </div>
</div>
@endsection