@extends('layouts.customer')

@section('title', 'Membership - SI-GAMER')

@section('content')
<div class="max-w-4xl mx-auto flex flex-col gap-6">

    <div class="rounded-2xl bg-linear-to-br from-[#130B29] to-[#2A0845] border border-purple-500/30 p-8">
        <div class="flex items-center gap-3 mb-4">
            <i class="ph-fill ph-crown text-4xl text-amber-400"></i>
            <div>
                <h1 class="text-2xl font-bold text-white">Membership SI-GAMER</h1>
                <p class="text-sm text-[#B8A9CC]">Jadilah bagian dari member SI-GAMER.</p>
            </div>
        </div>

        <p class="text-sm text-[#B8A9CC] leading-relaxed">
            Daftarkan akunmu menjadi member SI-GAMER dan nikmati keuntungan khusus
            yang tersedia bagi anggota.
        </p>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 rounded-xl bg-red-500/10 border border-red-500/30 text-red-400 text-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-[#130B29] border border-[#2D2442] rounded-2xl p-7">
        <h2 class="text-xl font-bold text-white mb-5">Keuntungan Membership</h2>

        <div class="grid md:grid-cols-2 gap-4">
            <div class="flex gap-3 items-center">
                <i class="ph-fill ph-check-circle text-xl text-purple-400"></i>
                <span class="text-sm text-[#D8CDE8]">Status member permanen</span>
            </div>

            <div class="flex gap-3 items-center">
                <i class="ph-fill ph-check-circle text-xl text-purple-400"></i>
                <span class="text-sm text-[#D8CDE8]">Akses informasi promo member</span>
            </div>

            <div class="flex gap-3 items-center">
                <i class="ph-fill ph-check-circle text-xl text-purple-400"></i>
                <span class="text-sm text-[#D8CDE8]">Keuntungan khusus member</span>
            </div>

            <div class="flex gap-3 items-center">
                <i class="ph-fill ph-check-circle text-xl text-purple-400"></i>
                <span class="text-sm text-[#D8CDE8]">Identitas member pada akun</span>
            </div>
        </div>
    </div>

    <div class="bg-[#130B29] border border-[#2D2442] rounded-2xl p-7">
        @if($membership && $membership->status === 'aktif')

            <div class="text-center py-5">
                <i class="ph-fill ph-crown text-5xl text-amber-400"></i>

                <h2 class="text-2xl font-bold text-white mt-3">
                    Kamu Sudah Menjadi Member!
                </h2>

                <p class="text-sm text-emerald-400 mt-2">
                    Status Membership: Aktif
                </p>

                <p class="text-sm text-[#8B7EA1] mt-2">
                    Terdaftar sejak
                    {{ \Carbon\Carbon::parse($membership->tanggal_mulai)->format('d F Y') }}
                </p>

                <p class="text-xs text-amber-400 mt-3">
                    Membership berlaku selamanya.
                </p>
            </div>

        @else

            <h2 class="text-xl font-bold text-white">Daftar Membership</h2>

            <p class="text-sm text-[#8B7EA1] mt-2 mb-6">
                Membership bersifat opsional. Kamu tetap bisa menggunakan layanan
                SI-GAMER tanpa mendaftar sebagai member.
            </p>

            <form action="{{ route('customer.membership.aktifkan') }}" method="POST">
                @csrf

                <button type="submit"
                    onclick="return confirm('Apakah kamu yakin ingin mendaftar menjadi member SI-GAMER?')"
                    class="w-full py-3 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-bold transition">
                    <i class="ph ph-crown"></i>
                    Daftar Membership Sekarang
                </button>
            </form>

        @endif
    </div>

</div>
@endsection