@extends('layouts.staff')

@section('title', 'Booking Walk-in - GameHub')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-white tracking-tight">Booking walk-in</h2>
    <p class="text-xs text-slate-400 mt-1">Pilih pelanggan dan perangkat tersedia untuk langsung memulai sesi.</p>
</div>

<section class="max-w-3xl bg-[#12131a] border border-slate-800/80 rounded-xl p-5">
    <form action="{{ route('staff.booking.walkin.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @csrf
        <label class="text-xs text-slate-400">
            Pelanggan
            <select name="id_user" required class="mt-1 w-full bg-[#181924] border border-slate-800 rounded-lg px-3 py-2.5 text-xs text-white focus:outline-none focus:border-purple-600">
                <option value="">Pilih pelanggan</option>
                @foreach($customers as $customer)
                    <option value="{{ $customer->id }}" {{ old('id_user') == $customer->id ? 'selected' : '' }}>{{ $customer->nama }}</option>
                @endforeach
            </select>
        </label>
        <label class="text-xs text-slate-400">
            Perangkat tersedia
            <select name="id_device" required class="mt-1 w-full bg-[#181924] border border-slate-800 rounded-lg px-3 py-2.5 text-xs text-white focus:outline-none focus:border-purple-600">
                <option value="">Pilih perangkat</option>
                @foreach($devicesTersedia as $device)
                    <option value="{{ $device->id }}" {{ old('id_device') == $device->id ? 'selected' : '' }}>{{ $device->nama_device }} — Rp{{ number_format($device->harga_per_jam, 0, ',', '.') }}/jam</option>
                @endforeach
            </select>
        </label>
        <label class="text-xs text-slate-400">
            Durasi
            <select name="durasi" required class="mt-1 w-full bg-[#181924] border border-slate-800 rounded-lg px-3 py-2.5 text-xs text-white focus:outline-none focus:border-purple-600">
                @for($hours = 1; $hours <= 12; $hours++)
                    <option value="{{ $hours }}" {{ old('durasi', 1) == $hours ? 'selected' : '' }}>{{ $hours }} jam</option>
                @endfor
            </select>
        </label>
        <div class="md:col-span-2 flex flex-wrap items-center justify-between gap-3">
            <p class="text-[11px] text-slate-500">
                @if($customers->isEmpty()) Belum ada pelanggan terdaftar. @elseif($devicesTersedia->isEmpty()) Tidak ada perangkat yang tersedia. @else Sesi dimulai setelah booking walk-in dibuat. @endif
            </p>
            <button type="submit" @if($customers->isEmpty() || $devicesTersedia->isEmpty()) disabled @endif
                    class="bg-purple-600 hover:bg-purple-500 disabled:opacity-50 disabled:cursor-not-allowed text-white text-xs font-semibold px-4 py-2.5 rounded-lg transition"
                    onclick="return confirm('Buat booking walk-in dan mulai sesi sekarang?')">
                Mulai sesi
            </button>
        </div>
    </form>
</section>
@endsection
