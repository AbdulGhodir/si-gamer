@extends('layouts.admin')

@section('content')
<header class="p-6 pb-4 border-b border-gray-800/50 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-white tracking-tight">Manajemen Promo</h1>
        <p class="text-xs text-gray-400 mt-0.5">Kelola kupon diskon dan penawaran spesial untuk pelanggan.</p>
    </div>
    <div>
        <button class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-xl text-sm font-medium transition shadow-lg shadow-purple-900/30 flex items-center gap-2">
            <span>➕</span> Tambah Promo
        </button>
    </div>
</header>

<div class="p-6 space-y-6">
    <div class="bg-[#141221] border border-gray-800/80 rounded-2xl overflow-hidden shadow-sm">
        <div class="p-4 border-b border-gray-800 flex justify-between items-center">
            <h3 class="font-semibold text-white">Daftar Promo Aktif</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-800 text-xs text-gray-400 bg-[#110f1c]">
                        <th class="p-4">Kode Promo</th>
                        <th class="p-4">Diskon</th>
                        <th class="p-4">Keterangan</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800/60 text-sm">
                    @foreach($promos as $p)
                    <tr class="hover:bg-white/[0.02] transition">
                        <td class="p-4 font-medium text-white">{{ $p['kode'] }}</td>
                        <td class="p-4 text-purple-400 font-semibold">{{ $p['diskon'] }}</td>
                        <td class="p-4 text-gray-400 text-xs">{{ $p['ket'] }}</td>
                        <td class="p-4">
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $p['status'] == 'Aktif' ? 'bg-emerald-900/50 text-emerald-300' : 'bg-gray-800 text-gray-400' }}">
                                {{ $p['status'] }}
                            </span>
                        </td>
                        <td class="p-4 text-center space-x-2">
                            <button class="text-xs text-purple-400 hover:text-purple-300">Edit</button>
                            <button class="text-xs text-red-400 hover:text-red-300">Hapus</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection