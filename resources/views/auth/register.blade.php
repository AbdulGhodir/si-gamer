@extends('layouts.auth')

@section('left-content')
<h1 class="text-4xl font-bold text-white leading-norma text-balance">
    Selamat Datang di SI-GAMER
</h1>
<p class="text-md font-medium text-[#8B7EA1] text-balance">
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
            <i data-lucide="user" stroke-width="1.5" class="w-5 h-5 top-1/2 -translate-y-1/2 absolute left-4 text-[#8B7EA1]"></i>
            <input type="text"
                   name="nama"
                   value="{{ old('nama') }}"
                   placeholder="Masukkan nama"
                   class="w-full bg-[#161124] border border-[#2D2442] text-white rounded-lg pl-11 pr-4 py-4 focus:outline-none focus:border-purple-500/50 focus:ring-1 focus:ring-purple-500/50 transition-colors placeholder-[#8B7EA1]/50 text-sm"
                   required>
        </div>
    </div>

    <div class="flex flex-col gap-2">
        <label for="email" class="text-white font-semibold">Email</label>
        <div class="relative">
            <i data-lucide="mail" stroke-width="1.5" class="w-5 h-5 top-1/2 -translate-y-1/2 absolute left-4 text-[#8B7EA1]"></i>
            <input type="email"
                   name="email"
                   value="{{ old('email') }}"
                   placeholder="nama@gmail.com"
                   class="w-full bg-[#161124] border border-[#2D2442] text-white rounded-lg pl-11 pr-4 py-4 focus:outline-none focus:border-purple-500/50 focus:ring-1 focus:ring-purple-500/50 transition-colors placeholder-[#8B7EA1]/50 text-sm"
                   required>
        </div>
    </div>

    <div class="flex flex-col gap-2">
        <label for="password" class="text-white font-semibold">Password</label>
        <div class="relative">
            <i data-lucide="lock" stroke-width="1.5" class="w-5 h-5 top-1/2 -translate-y-1/2 absolute left-4 text-[#8B7EA1]"></i>
            <input id="password"
                   type="password"
                   name="password"
                   placeholder="•••••••••••"
                   class="w-full bg-[#161124] border border-[#2D2442] text-white rounded-lg pl-11 pr-4 py-4 focus:outline-none focus:border-purple-500/50 focus:ring-1 focus:ring-purple-500/50 transition-colors placeholder-[#8B7EA1]/50 text-sm"
                   required>
            <i id="eye-password" class="top-1/2 -translate-y-1/2 ph ph-eye absolute right-4 text-[#8B7EA1] text-lg cursor-pointer"></i>
        </div>
    </div>

    <div class="flex flex-col gap-2">
        <label for="password" class="text-white font-semibold">Konfirmasi Password</label>
        <div class="relative">
            <i data-lucide="lock" stroke-width="1.5" class="w-5 h-5 top-1/2 -translate-y-1/2 absolute left-4 text-[#8B7EA1]"></i>
            <input id="password-confirmation"
                   type="password"
                   name="password_confirmation"
                   placeholder="•••••••••••"
                   class="w-full bg-[#161124] border border-[#2D2442] text-white rounded-lg pl-11 pr-4 py-4 focus:outline-none focus:border-purple-500/50 focus:ring-1 focus:ring-purple-500/50 transition-colors placeholder-[#8B7EA1]/50 text-sm"
                   required>
            <i id="eye-password-confirmation" class="top-1/2 -translate-y-1/2 ph ph-eye absolute right-4 text-[#8B7EA1] text-lg cursor-pointer"></i>
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

<script>
    const eyePassword = document.getElementById('eye-password');
    const password = document.getElementById('password');

    const eyePasswordConfirmation = document.getElementById('eye-password-confirmation');
    const passwordConfirmation = document.getElementById('password-confirmation');

    eyePassword.addEventListener('click', () => {
        if (password.type === 'password') {
            password.type = 'text';
            eyePassword.classList.remove('ph-eye');
            eyePassword.classList.add('ph-eye-slash');
        } else {
            password.type = 'password';
            eyePassword.classList.remove('ph-eye-slash');
            eyePassword.classList.add('ph-eye');
        }
    });

    eyePasswordConfirmation.addEventListener('click', () => {
        if (passwordConfirmation.type === 'password') {
            passwordConfirmation.type = 'text';
            eyePasswordConfirmation.classList.remove('ph-eye');
            eyePasswordConfirmation.classList.add('ph-eye-slash');
        } else {
            passwordConfirmation.type = 'password';
            eyePasswordConfirmation.classList.remove('ph-eye-slash');
            eyePasswordConfirmation.classList.add('ph-eye');
        }
    });
</script>
@endsection