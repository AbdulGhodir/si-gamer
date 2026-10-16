@extends('layouts.auth')

@section('left-content')
<h1 class="text-4xl font-bold text-white leading-normal">
    Selamat Datang di SI-GAMER
</h1>
<p class="text-md font-medium text-[#8B7EA1]">
    Daftarkan Akun dan mulai booking PlayStation & PC Gaming dengan mudah, cepat, dan aman.
</p>
@endsection

@section('right-content')
<div class="flex flex-col gap-1 mb-6">
    <h1 class="text-2xl font-bold text-white text-center">Daftar Akun</h1>
    <p class="text-sm font-medium text-[#8B7EA1] text-center">Selamat datang Gamer</p>
</div>

<form action="{{ route('register.post') }}" method="POST" class="flex flex-col gap-4">
    @csrf
    <div class="flex flex-col gap-2">
        <label for="name" class="text-white font-semibold">Nama Lengkap</label>
        <div class="relative">
            <i class="top-1/2 -translate-y-1/2 ph ph-user absolute left-4 text-[#8B7EA1] text-lg"></i>
            <input type="text" name="nama" value="{{ old('nama') }}" class="w-full bg-[#161124] border border-[#2D2442] text-white rounded-lg pl-11 pr-4 py-4 focus:outline-none focus:border-purple-500/50 focus:ring-1 focus:ring-purple-500/50 transition-colors placeholder-[#8B7EA1]/50 text-sm" placeholder="Masukkan nama" required>
        </div>
    </div>

    <div class="flex flex-col gap-2">
        <label for="email" class="text-white font-semibold">Email</label>
        <div class="relative">
            <i class="top-1/2 -translate-y-1/2 ph ph-envelope absolute left-4 text-[#8B7EA1] text-lg"></i>
            <input type="email" name="email" value="{{ old('email') }}" class="w-full bg-[#161124] border border-[#2D2442] text-white rounded-lg pl-11 pr-4 py-4 focus:outline-none focus:border-purple-500/50 focus:ring-1 focus:ring-purple-500/50 transition-colors placeholder-[#8B7EA1]/50 text-sm" placeholder="nama@gmail.com" required>
        </div>
    </div>

    <div class="flex flex-col gap-2">
        <label for="password" class="text-white font-semibold">Password</label>
        <div class="relative">
            <i class="top-1/2 -translate-y-1/2 ph ph-lock absolute left-4 text-[#8B7EA1] text-lg"></i>
            <input type="password" name="password" class="w-full bg-[#161124] border border-[#2D2442] text-white rounded-lg pl-11 pr-4 py-4 focus:outline-none focus:border-purple-500/50 focus:ring-1 focus:ring-purple-500/50 transition-colors placeholder-[#8B7EA1]/50 text-sm" placeholder="•••••••••••" required>
            <i class="top-1/2 -translate-y-1/2 ph ph-eye absolute right-4 text-[#8B7EA1] text-lg cursor-pointer"></i>
        </div>
    </div>

    <div class="flex flex-col gap-2">
        <label for="password" class="text-white font-semibold">Konfirmasi Password</label>
        <div class="relative">
            <i class="top-1/2 -translate-y-1/2 ph ph-lock absolute left-4 text-[#8B7EA1] text-lg"></i>
            <input type="password" name="password_confirmation" class="w-full bg-[#161124] border border-[#2D2442] text-white rounded-lg pl-11 pr-4 py-4 focus:outline-none focus:border-purple-500/50 focus:ring-1 focus:ring-purple-500/50 transition-colors placeholder-[#8B7EA1]/50 text-sm" placeholder="•••••••••••" required>
            <i class="top-1/2 -translate-y-1/2 ph ph-eye absolute right-4 text-[#8B7EA1] text-lg cursor-pointer"></i>
        </div>
    </div>

    @if($errors->any())
        <div class="flex items-center gap-2">
            <i class="ph text-red-500 ph-warning-circle"></i>
            <p class="text-red-500 text-sm">{{ $errors->first() }}</p>
        </div>
    @endif

    <button type="submit" class="w-full mt-6 bg-[#9333EA] text-white font-bold py-3 rounded-lg hover:bg-purple-500 transition-colors cursor-pointer">Daftar</button>
</form>

<div class="flex items-center gap-4 my-6">
    <div class="flex-1 h-px bg-[#2D2442]"></div>
    <span class="text-xs text-[#8B7EA1] uppercase tracking-wider">atau</span>
    <div class="flex-1 h-px bg-[#2D2442]"></div>
</div>

<div class="flex flex-col gap-6">
    <button type="button" class="cursor-pointer w-full bg-[#161124] border border-[#2D2442] hover:border-purple-500 text-white font-medium py-3 rounded-lg transition-colors flex justify-center items-center gap-3">
        <svg class="w-6 h-6" viewBox="0 0 24 24">
            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
        </svg>
        <span>Login dengan Google</span>
    </button>

    <p class="text-center text-sm text-[#8B7EA1]">
        Sudah punya akun? <a href="/" class="cursor-pointer text-purple-400 font-semibold hover:text-purple-500 hover:underline transition-colors">Login</a>
    </p>
</div>
@endsection