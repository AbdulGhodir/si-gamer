@extends('layouts.admin')

@section('content')
<header class="p-6 pb-4 border-b border-gray-800/50 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-white tracking-tight">Kelola Transaksi</h1>
        <p class="text-xs text-gray-400 mt-0.5">Daftar transaksi pembayaran rental GameHub.</p>
    </div>
    <div>
        <button class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-xl text-sm font-medium transition shadow-lg shadow-purple-900/30 flex items-center gap-2">
            <span>➕</span> Tambah Transaksi
        </button>
    </div>
</header>

<div class="p-6 space-y-6">
    <div class="bg-[#141221] border border-gray-800/80 rounded-2xl overflow-hidden shadow-sm">
        <div class="p-4 border-b border-gray-800 flex justify-between items-center">
            <h3 class="font-semibold text-white">Riwayat Transaksi Terbaru</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-800 text-xs text-gray-400 bg-[#110f1c]">
                        <th class="p-4">Kode Booking</th>
                        <th class="p-4">Pelanggan</th>
                        <th class="p-4">Perangkat</th>
                        <th class="p-4">Total</th>
                        <th class="p-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800/60 text-sm">
                    @foreach($transactions as $t)
                    <tr class="hover:bg-white/[0.02] transition">
                        <td class="p-4 font-medium text-white">{{ $t['kode'] }}</td>
                        <td class="p-4 text-gray-300">{{ $t['pelanggan'] }}</td>
                        <td class="p-4 text-gray-400 text-xs">{{ $t['perangkat'] }}</td>
                        <td class="p-4 text-purple-400 font-semibold">{{ $t['total'] }}</td>
                        <td class="p-4">
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $t['status'] == 'Lunas' ? 'bg-emerald-900/50 text-emerald-300' : 'bg-amber-900/50 text-amber-300' }}">
                                {{ $t['status'] }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection