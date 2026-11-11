
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SI Gamer - Gaming Center')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>

<body class="bg-[#0B0812] text-white min-h-screen flex flex-col font-sans antialiased">

    <header class="w-full bg-[#0B0812] border-b border-[#2D2442] sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">

            <!-- LOGO -->
            <a href="{{ route('customer.dashboard') }}" class="flex items-center gap-3">
                <x-logo size="text-3xl" />

                <div class="flex flex-col">
                    <h1 class="text-xl font-bold text-white tracking-wide leading-none">
                        SI-GAMER
                    </h1>
                    <p class="text-xs font-semibold text-[#8B7EA1]">
                        Gaming Center
                    </p>
                </div>
            </a>

            <!-- NAVBAR -->
            <nav class="hidden md:flex items-center gap-8">

                <a href="{{ route('customer.dashboard') }}"
                   class="text-sm font-semibold pb-1 border-b-2 {{ request()->routeIs('customer.dashboard') ? 'text-white border-purple-500' : 'text-[#8B7EA1] border-transparent hover:text-white' }}">
                    Beranda
                </a>

                <a href="{{ route('customer.riwayat') }}"
                   class="text-sm font-semibold pb-1 border-b-2 {{ request()->routeIs('customer.riwayat') ? 'text-white border-purple-500' : 'text-[#8B7EA1] border-transparent hover:text-white' }}">
                    Riwayat Booking
                </a>

                <a href="{{ route('customer.membership') }}"
                   class="text-sm font-semibold pb-1 border-b-2 {{ request()->routeIs('customer.membership') ? 'text-white border-purple-500' : 'text-[#8B7EA1] border-transparent hover:text-white' }}">
                    Membership
                </a>

            </nav>

            <!-- DROPDOWN PROFILE -->
            <div class="relative">

                <details class="relative group">

                    <!-- TOMBOL AVATAR -->
                    <summary class="list-none cursor-pointer flex items-center gap-3 py-2 outline-none">

                        <div class="flex flex-col text-right">
                            <span class="text-sm font-semibold text-white leading-tight">
                                {{ Auth::user()->nama }}
                            </span>

                            <span class="text-xs text-[#8B7EA1]">
                                Customer
                            </span>
                        </div>

                        @if(Auth::user()->foto_profil)
                            <img
                                src="{{ asset('storage/' . Auth::user()->foto_profil) }}"
                                class="w-10 h-10 rounded-full object-cover border border-purple-500 group-hover:border-purple-300 transition-colors"
                                alt="Foto Profil"
                            >
                        @else
                            <div class="w-10 h-10 rounded-full bg-[#161124] border border-[#2D2442] flex items-center justify-center text-purple-400 group-hover:border-purple-500 transition-colors">
                                <i class="ph ph-user text-xl"></i>
                            </div>
                        @endif

                        <i class="ph ph-caret-down text-[#8B7EA1] text-sm transition-transform group-open:rotate-180"></i>

                    </summary>

                    <!-- ISI DROPDOWN -->
                    <div class="absolute right-0 top-full mt-3 w-52 bg-[#161124] border border-[#2D2442] rounded-xl shadow-xl overflow-hidden z-50">

                        <!-- LIHAT PROFIL -->
                        <a href="{{ route('customer.profile') }}"
                           class="flex items-center gap-3 px-4 py-3 text-sm text-white hover:bg-[#241938] transition-colors">

                            <i class="ph ph-user-circle text-lg text-purple-400"></i>

                            <span>Lihat Profil</span>
                        </a>

                        <div class="border-t border-[#2D2442]"></div>

                        <!-- LOGOUT -->
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf

                            <button type="submit"
                                    onclick="return confirm('Apakah kamu yakin ingin keluar dari SI-GAMER?')"
                                    class="w-full flex items-center gap-3 px-4 py-3 text-sm text-red-400 hover:bg-red-500/10 transition-colors">

                                <i class="ph ph-sign-out text-lg"></i>

                                <span>Keluar</span>
                            </button>
                        </form>

                    </div>

                </details>

            </div>

        </div>
    </header>

    <!-- KONTEN -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-6 py-8">
        @yield('content')
    </main>

    <!-- FOOTER -->
    <footer class="w-full border-t border-[#2D2442] bg-[#0B0812] py-8 text-center text-xs text-[#8B7EA1]">
        <div class="max-w-7xl mx-auto px-6">
            <p>
                © {{ date('Y') }} SI-GAMER Kelompok 2 - Pemrograman Web Lanjut.
            </p>
        </div>
    </footer>

    <x-lucide />

</body>
</html>
