@extends('layouts.admin')

@section('content')
<header class="p-6 pb-4 border-b border-gray-800/50 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-white tracking-tight">Manajemen Perangkat</h1>
        <p class="text-xs text-gray-400 mt-0.5">Kelola status konsol PlayStation, PC Gaming, dan pemeliharaan unit.</p>
    </div>
    <div>
        <button class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-xl text-sm font-medium transition shadow-lg shadow-purple-900/30 flex items-center gap-2">
            <span>➕</span> Tambah Unit
        </button>
    </div>
</header>

<div class="p-6 space-y-6">
    <div class="bg-[#141221] border border-gray-800/80 rounded-2xl overflow-hidden shadow-sm">
        <div class="p-4 border-b border-gray-800 flex justify-between items-center">
            <h3 class="font-semibold text-white">Daftar Unit Perangkat Rental</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-800 text-xs text-gray-400 bg-[#110f1c]">
                        <th class="p-4">Kode Unit</th>
                        <th class="p-4">Tipe Perangkat</th>
                        <th class="p-4">Status</th>
                        <th class="p-4">Keterangan</th>
                        <th class="p-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800/60 text-sm">
                    @foreach($devices as $d)
                    <tr class="hover:bg-white/[0.02] transition">
                        <td class="p-4 font-medium text-white">{{ $d['kode'] }}</td>
                        <td class="p-4 text-gray-300">{{ $d['tipe'] }}</td>
                        <td class="p-4">
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $d['status'] == 'Tersedia' ? 'bg-emerald-900/50 text-emerald-300' : ($d['status'] == 'Digunakan' ? 'bg-blue-900/50 text-blue-300' : 'bg-amber-900/50 text-amber-300') }}">
                                {{ $d['status'] }}
                            </span>
                        </td>
                        <td class="p-4 text-gray-400 text-xs">{{ $d['keterangan'] }}</td>
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