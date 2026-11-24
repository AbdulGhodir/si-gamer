@extends('layouts.admin')

@section('content')
@php
    $summary = $reportSummary ?? [
        'pendapatan' => 'Rp0',
        'sesi_lunas' => '0 Sesi Lunas',
        'operasional_harian' => 'Rp0',
        'utilisasi' => '0%',
        'sesi_aktif' => '0 sesi aktif',
    ];
    $breakdownRows = collect($breakdown ?? []);
@endphp

<header class="p-6 pb-4 border-b border-gray-800/50 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-white tracking-tight">Laporan Operasional</h1>
        <p class="text-xs text-gray-400 mt-0.5">Rekapitulasi pendapatan, jumlah sesi, dan performa operasional GameHub.</p>
    </div>
</header>

<div class="p-6 space-y-6">
    <!-- Card Ringkasan Kinerja -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-[#141221] border border-gray-800/80 p-5 rounded-2xl shadow-sm">
            <p class="text-xs text-gray-400">Total Pendapatan Lunas</p>
            <h2 class="text-2xl font-bold text-white mt-1">{{ $summary['pendapatan'] }}</h2>
            <p class="text-xs text-emerald-400 mt-2 font-medium">Berdasarkan booking lunas</p>
        </div>
        <div class="bg-[#141221] border border-gray-800/80 p-5 rounded-2xl shadow-sm">
            <p class="text-xs text-gray-400">Booking Lunas</p>
            <h2 class="text-2xl font-bold text-purple-400 mt-1">{{ $summary['sesi_lunas'] }}</h2>
            <p class="text-xs text-gray-400 mt-2">Operasional hari ini: {{ $summary['operasional_harian'] }}</p>
        </div>
        <div class="bg-[#141221] border border-gray-800/80 p-5 rounded-2xl shadow-sm">
            <p class="text-xs text-gray-400">Utilisasi Perangkat</p>
            <h2 class="text-2xl font-bold text-emerald-400 mt-1">{{ $summary['utilisasi'] }}</h2>
            <p class="text-xs text-gray-400 mt-2">Status saat ini: {{ $summary['sesi_aktif'] }}</p>
        </div>
    </div>

    <!-- Card Unduh Rekap Laporan -->
    <div class="bg-[#141221] border border-gray-800/80 rounded-2xl p-6 space-y-4 shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="space-y-1">
                <h3 class="font-semibold text-white">Unduh Rekap Laporan</h3>
                <p class="text-gray-400 text-sm">Data pendapatan, jumlah sesi, dan operasional GameHub siap diunduh dalam bentuk dokumen.</p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <select id="selectPeriodeLaporan" class="bg-[#0b0a12] border border-gray-800 rounded-xl px-3 py-2 text-xs text-gray-300 focus:outline-none focus:border-purple-600">
                    <option value="Rekap_Terkini">Rekap Terkini</option>
                    <option value="Harian_{{ now()->format('d_M_Y') }}">Harian ({{ now()->format('d/m/Y') }})</option>
                    <option value="Bulanan_{{ now()->format('M_Y') }}">Bulanan ({{ now()->format('M Y') }})</option>
                </select>

                <button type="button" id="btnDownloadCsv" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-xl text-sm font-medium transition shadow-lg shadow-purple-900/30 flex items-center gap-2 cursor-pointer">
                    <span>📥</span> Download Laporan (.CSV)
                </button>

                <button type="button" id="btnPrintPdf" class="bg-[#1b182e] hover:bg-[#25213d] border border-gray-700 text-gray-200 px-4 py-2 rounded-xl text-sm font-medium transition flex items-center gap-2 cursor-pointer">
                    <span>🖨️</span> Cetak / PDF
                </button>
            </div>
        </div>

        <p id="downloadFeedback" class="hidden text-xs text-emerald-400 pt-1"></p>
    </div>

    <!-- Tabel Ringkasan Data yang Akan Diunduh -->
    <div class="bg-[#141221] border border-gray-800/80 rounded-2xl overflow-hidden shadow-sm">
        <div class="p-4 border-b border-gray-800 flex justify-between items-center">
            <h3 class="font-semibold text-white">Pratinjau Rekap Operasional Per Kategori</h3>
            <span class="text-xs text-gray-400">GameHub</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse" id="reportTable">
                <thead>
                    <tr class="border-b border-gray-800 text-xs text-gray-400 bg-[#110f1c]">
                        <th class="p-4">Kategori Layanan</th>
                        <th class="p-4">Jumlah Sesi</th>
                        <th class="p-4">Utilisasi</th>
                        <th class="p-4">Total Pendapatan</th>
                        <th class="p-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800/60 text-sm">
                    @forelse($breakdownRows as $row)
                    <tr class="hover:bg-white/[0.02] transition">
                        <td class="p-4 font-medium text-white">{{ $row['kategori'] ?? '-' }}</td>
                        <td class="p-4 text-gray-300">{{ $row['sesi'] ?? '0 Sesi' }}</td>
                        <td class="p-4 text-gray-400 text-xs">{{ $row['utilisasi'] ?? '0%' }}</td>
                        <td class="p-4 text-purple-400 font-semibold">{{ $row['pendapatan'] ?? 'Rp0' }}</td>
                        <td class="p-4">
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-900/50 text-emerald-300">
                                {{ $row['status'] ?? 'Stabil' }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-6 text-center text-xs text-gray-500">
                            Belum ada data rekap kategori.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const btnDownloadCsv = document.getElementById('btnDownloadCsv');
    const btnPrintPdf = document.getElementById('btnPrintPdf');
    const selectPeriode = document.getElementById('selectPeriodeLaporan');
    const feedback = document.getElementById('downloadFeedback');

    btnDownloadCsv?.addEventListener('click', function () {
        const table = document.getElementById('reportTable');
        const rows = Array.from(table.querySelectorAll('tr'));
        const csvContent = rows.map(row => {
            const cols = Array.from(row.querySelectorAll('th, td'));
            return cols.map(col => `"${col.textContent.trim().replace(/"/g, '""')}"`).join(',');
        }).join('\n');

        const periode = selectPeriode.value;
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', `Laporan_Operasional_GameHub_${periode}.csv`);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);

        feedback.textContent = `✓ Berhasil mengunduh Laporan_Operasional_GameHub_${periode}.csv`;
        feedback.classList.remove('hidden');
    });

    btnPrintPdf?.addEventListener('click', function () {
        window.print();
    });
});
</script>
@endsection