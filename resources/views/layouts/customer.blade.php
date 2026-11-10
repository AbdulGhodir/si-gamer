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
            
            <a href="{{ route('customer.dashboard') }}" class="flex items-center gap-3">
                <x-logo size="text-3xl" />
                <div class="flex flex-col">
                    <h1 class="text-xl font-bold text-white tracking-wide leading-none">SI-GAMER</h1>
                    <p class="text-xs font-semibold text-[#8B7EA1]">Gaming Center</p>
                </div>
            </a>

            <nav class="hidden md:flex items-center gap-8">
                <a href="{{ route('customer.dashboard') }}" 
                   class="text-sm font-semibold pb-1 border-b-2 {{ request()->routeIs('customer.dashboard') ? 'text-white border-purple-500' : 'text-[#8B7EA1] border-transparent hover:text-white' }} focus:outline-none transition-colors">
                    Beranda
                </a>
                <a href="{{ route('customer.riwayat') }}" 
                   class=" text-sm font-semibold pb-1 border-b-2 {{ request()->routeIs('customer.riwayat') ? 'text-white border-purple-500' : 'text-[#8B7EA1] border-transparent hover:text-white' }} focus:outline-none transition-colors">
                    Riwayat Booking
                </a>
                <a href="#" 
                   class="text-sm font-semibold pb-1 border-b-2 border-transparent text-[#8B7EA1] hover:text-white focus:outline-none transition-colors">
                    Membership
                </a>
            </nav>

            <div class="relative group">
                <div class="flex items-center gap-3 cursor-pointer py-2">
                    <div class="flex flex-col text-right">
                        <span class="text-sm font-semibold text-white leading-tight">
                            Customer
                        </span>
                        <span class="text-xs text-[#8B7EA1]">Customer</span>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-[#161124] border border-[#2D2442] flex items-center justify-center text-purple-400 group-hover:border-purple-500 transition-colors">
                        <i class="ph ph-user text-xl"></i>
                    </div>
                </div>

                <div class="absolute right-0 top-full pt-1 w-52 hidden group-hover:block z-50">
                    <div class="bg-[#161124] border border-[#2D2442] rounded-xl shadow-2xl p-2 flex flex-col gap-1">
                        
                        <div class="px-3 py-2 border-b border-[#2D2442] select-none">
                            <p class="text-xs font-semibold text-white truncate">
                                Customer
                            </p>
                            <p class="text-[11px] text-[#8B7EA1] truncate">
                                customer@gmail.com
                            </p>
                            <div class="mt-1 flex items-center gap-1.5 text-[10px] text-purple-400 font-medium">
                                <i class="ph ph-identification-badge"></i>
                                <span>Profil Pengguna</span>
                            </div>
                        </div>

                        <form action="{{ route('logout') }}" method="POST" class="w-full m-0">
                            @csrf
                            <button type="submit" 
                                    class="w-full flex items-center gap-2 px-3 py-2 text-xs font-semibold text-red-400 hover:bg-red-500/10 hover:text-red-300 rounded-lg transition-colors cursor-pointer text-left">
                                <i class="ph ph-sign-out text-base"></i>
                                <span>Keluar / Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="flex-1 max-w-7xl w-full mx-auto px-6 py-8">
        @yield('content')
    </main>

    <footer class="w-full border-t border-[#2D2442] bg-[#0B0812] py-8 text-center text-xs text-[#8B7EA1]">
        <div class="max-w-7xl mx-auto px-6">
            <p>© {{ date('Y') }} SI-GAMER Kelompok 2 - Pemrograman Web Lanjut.</p>
        </div>
    </footer>

    <x-lucide />
</body>
</html>
