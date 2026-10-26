@extends('layouts.admin')

@section('content')
<header class="p-6 pb-4 border-b border-gray-800/50 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-white tracking-tight">Daftar Booking</h1>
        <p class="text-xs text-gray-400 mt-0.5">Kelola jadwal pemesanan perangkat rental.</p>
    </div>
</header>

<div class="p-6 space-y-6">
    <div class="bg-[#141221] border border-gray-800/80 rounded-2xl overflow-hidden shadow-sm">
        <div class="p-4 border-b border-gray-800 flex justify-between items-center">
            <h3 class="font-semibold text-white">Jadwal Booking Aktif</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-800 text-xs text-gray-400 bg-[#110f1c]">
                        <th class="p-4">ID Booking</th>
                        <th class="p-4">Pelanggan</th>
                        <th class="p-4">Jadwal Main</th>
                        <th class="p-4">Durasi</th>
                        <th class="p-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800/60 text-sm">
                    @foreach($bookings as $b)
                    <tr class="hover:bg-white/[0.02] transition">
                        <td class="p-4 font-medium text-white">{{ $b['id'] }}</td>
                        <td class="p-4 text-gray-300">{{ $b['pelanggan'] }}</td>
                        <td class="p-4 text-gray-400 text-xs">{{ $b['jadwal'] }}</td>
                        <td class="p-4 text-purple-400">{{ $b['durasi'] }}</td>
                        <td class="p-4">
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-900/50 text-emerald-300">
                                {{ $b['status'] }}
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