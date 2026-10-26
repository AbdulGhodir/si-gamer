@extends('layouts.admin')

@section('content')
<header class="p-6 pb-4 border-b border-gray-800/50 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-white tracking-tight">Laporan Operasional</h1>
        <p class="text-xs text-gray-400 mt-0.5">Rekapitulasi performa dan unduh laporan bulanan.</p>
    </div>
</header>

<div class="p-6 space-y-6">
    <div class="bg-[#141221] border border-gray-800/80 rounded-2xl p-6 space-y-4 shadow-sm">
        <h3 class="font-semibold text-white">Unduh Rekap Laporan</h3>
        <p class="text-gray-400 text-sm">Data kinerja rental dan operasional GameHub Depok siap diunduh dalam bentuk dokumen.</p>
        <button class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-xl text-sm font-medium transition shadow-lg shadow-purple-900/30">
            📥 Download Laporan
        </button>
    </div>
</div>
@endsection