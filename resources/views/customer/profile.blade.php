@extends('layouts.customer')

@section('title', 'Profile - SI-GAMER')

@section('content')
<div class="max-w-3xl mx-auto flex flex-col gap-6">

    <div>
        <h1 class="text-2xl font-bold text-white">Profile Saya</h1>
        <p class="text-sm text-[#8B7EA1] mt-1">
            Kelola informasi akun SI-GAMER kamu.
        </p>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-xl bg-red-500/10 border border-red-500/30 text-red-400 text-sm">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form action="{{ route('customer.profile.update') }}"
          method="POST"
          enctype="multipart/form-data"
          class="bg-[#130B29] border border-[#2D2442] rounded-2xl p-7 flex flex-col gap-6">

        @csrf
        @method('PUT')

        <div class="flex flex-col items-center gap-3">
            @if($user->foto_profil)
                <img
                    src="{{ asset('storage/' . $user->foto_profil) }}"
                    class="w-28 h-28 rounded-full object-cover border-2 border-purple-500"
                    alt="Foto Profil">
            @else
                <div class="w-28 h-28 rounded-full bg-[#211638] border-2 border-purple-500 flex items-center justify-center">
                    <i class="ph ph-user text-5xl text-purple-400"></i>
                </div>
            @endif

            <label class="text-sm text-purple-400 font-semibold cursor-pointer">
                Ganti Foto Profil
                <input type="file" name="foto_profil" accept="image/*" class="hidden">
            </label>

            <p class="text-xs text-[#8B7EA1]">JPG, PNG, WEBP maksimal 2 MB.</p>
        </div>

        <div>
            <label class="block text-sm text-[#B8A9CC] mb-2">Nama Lengkap</label>
            <input type="text" name="nama"
                value="{{ old('nama', $user->nama) }}"
                required
                class="w-full bg-[#0B0812] border border-[#2D2442] rounded-xl px-4 py-3 text-white focus:border-purple-500 outline-none">
        </div>

        <div>
            <label class="block text-sm text-[#B8A9CC] mb-2">Email</label>
            <input type="email" name="email"
                value="{{ old('email', $user->email) }}"
                required
                class="w-full bg-[#0B0812] border border-[#2D2442] rounded-xl px-4 py-3 text-white focus:border-purple-500 outline-none">
        </div>

        <div class="border-t border-[#2D2442] pt-5">
            <h2 class="text-lg font-bold text-white mb-1">Ubah Password</h2>
            <p class="text-xs text-[#8B7EA1] mb-4">
                Kosongkan jika tidak ingin mengubah password.
            </p>

            <div class="flex flex-col gap-4">
                <div>
                    <label class="block text-sm text-[#B8A9CC] mb-2">Password Baru</label>
                    <input type="password" name="password"
                        class="w-full bg-[#0B0812] border border-[#2D2442] rounded-xl px-4 py-3 text-white focus:border-purple-500 outline-none">
                </div>

                <div>
                    <label class="block text-sm text-[#B8A9CC] mb-2">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation"
                        class="w-full bg-[#0B0812] border border-[#2D2442] rounded-xl px-4 py-3 text-white focus:border-purple-500 outline-none">
                </div>
            </div>
        </div>

        <button type="submit"
            class="w-full py-3 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-bold transition">
            Simpan Perubahan
        </button>

    </form>
</div>
@endsection