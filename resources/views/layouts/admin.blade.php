<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameHub - Admin Workspace</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#0f0e17] text-gray-200 font-sans antialiased flex h-screen overflow-hidden">

    <aside class="w-64 bg-[#141221] border-r border-gray-800 flex flex-col justify-between hidden md:flex">
        <div>
            <div class="p-5 flex items-center gap-3 border-b border-gray-800/60">
                <div class="bg-purple-600 p-2 rounded-lg text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                    </svg>
                </div>
                <div>
                    <h1 class="font-bold text-white text-lg tracking-wide">GameHub</h1>
                    <p class="text-xs text-purple-400 font-medium">SI-GAMER / RENTAL SYSTEM</p>
                </div>
            </div>

            <div class="px-4 py-6 space-y-1">
                <p class="px-3 text-[10px] font-semibold tracking-wider text-gray-500 uppercase mb-2">Workspace Admin</p>
                
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.dashboard') ? 'bg-purple-600 text-white font-medium' : 'text-gray-400 hover:bg-white/5 hover:text-white' }} text-sm transition">
                    <span>📊</span> Dashboard
                </a>
                <a href="{{ route('admin.users') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.users') ? 'bg-purple-600 text-white font-medium' : 'text-gray-400 hover:bg-white/5 hover:text-white' }} text-sm transition">
                    <span>👥</span> Akun pengguna
                </a>
                <a href="{{ route('admin.devices') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.devices') ? 'bg-purple-600 text-white font-medium' : 'text-gray-400 hover:bg-white/5 hover:text-white' }} text-sm transition">
                    <span>🎮</span> Perangkat
                </a>
                <a href="{{ route('admin.tariffs') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.tariffs') ? 'bg-purple-600 text-white font-medium' : 'text-gray-400 hover:bg-white/5 hover:text-white' }} text-sm transition">
                    <span>🏷️</span> Tarif & paket
                </a>
                <a href="{{ route('admin.promos') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.promos') ? 'bg-purple-600 text-white font-medium' : 'text-gray-400 hover:bg-white/5 hover:text-white' }} text-sm transition">
                    <span>🎫</span> Promo
                </a>
                <a href="{{ route('admin.transactions') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.transactions') ? 'bg-purple-600 text-white font-medium' : 'text-gray-400 hover:bg-white/5 hover:text-white' }} text-sm transition">
                    <span>💳</span> Transaksi
                </a>
                <a href="{{ route('admin.bookings') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.bookings') ? 'bg-purple-600 text-white font-medium' : 'text-gray-400 hover:bg-white/5 hover:text-white' }} text-sm transition">
                    <span>📅</span> Booking
                </a>
                <a href="{{ route('admin.maintenance') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.maintenance') ? 'bg-purple-600 text-white font-medium' : 'text-gray-400 hover:bg-white/5 hover:text-white' }} text-sm transition">
                    <span>🛠️</span> Maintenance
                </a>
                <a href="{{ route('admin.reports') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.reports') ? 'bg-purple-600 text-white font-medium' : 'text-gray-400 hover:bg-white/5 hover:text-white' }} text-sm transition">
                    <span>📈</span> Laporan operasional
                </a>
            </div>
        </div>

        <div class="p-4 border-t border-gray-800/60 flex items-center justify-between bg-[#110f1c]">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-purple-900 text-purple-200 font-bold flex items-center justify-center text-sm">
                    {{ substr(Auth::user()->nama ?? 'A', 0, 1) }}
                </div>
                <div>
                    <p class="text-sm font-semibold text-white">{{ Auth::user()->nama ?? 'Admin' }}</p>
                    <p class="text-xs text-gray-400">Admin</p>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="text-gray-400 hover:text-red-400 transition p-1" title="Logout">🚪</button>
            </form>
        </div>
    </aside>

    <main class="flex-1 flex flex-col h-screen overflow-y-auto bg-[#0b0a12]">
        @yield('content')
    </main>

    <x-lucide />
</body>
</html>