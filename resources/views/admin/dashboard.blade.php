<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameHub - Dashboard Admin</title>
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

                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl bg-purple-600 text-white font-medium text-sm transition">
                    <span>📊</span> Dashboard
                </a>
                <a href="{{ route('admin.users') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-400 hover:bg-white/5 hover:text-white text-sm transition">
                    <span>👥</span> Akun staff
                </a>
                <a href="{{ route('admin.devices') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-400 hover:bg-white/5 hover:text-white text-sm transition">
                    <span>🎮</span> Perangkat
                </a>
                <a href="{{ route('admin.bookings') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-400 hover:bg-white/5 hover:text-white text-sm transition">
                    <span>📅</span> Booking
                </a>
                <a href="{{ route('admin.maintenance') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-400 hover:bg-white/5 hover:text-white text-sm transition">
                    <span>🛠️</span> Maintenance
                </a>
                <a href="{{ route('admin.reports') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-400 hover:bg-white/5 hover:text-white text-sm transition">
                    <span>📈</span> Laporan operasional
                </a>
            </div>
        </div>

        <div class="p-4 border-t border-gray-800/60 flex items-center justify-between bg-[#110f1c]">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-purple-900 text-purple-200 font-bold flex items-center justify-center text-sm">
                    {{ substr($user->nama ?? 'A', 0, 1) }}
                </div>
                <div>
                    <p class="text-sm font-semibold text-white">{{ $user->nama ?? 'Admin' }}</p>
                    <p class="text-xs text-gray-400">Admin</p>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="text-gray-400 hover:text-red-400 transition p-1 cursor-pointer" title="Logout">
                    🚪
                </button>
            </form>
        </div>
    </aside>

    <main class="flex-1 flex flex-col h-screen overflow-y-auto bg-[#0b0a12]">
        <header class="p-6 pb-4 border-b border-gray-800/50 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-white tracking-tight">Ringkasan bisnis</h1>
                <p class="text-xs text-gray-400 mt-0.5">Kinerja rental dan operasional GameHub, dalam satu tempat.</p>
            </div>
            <div>
                <a href="{{ route('admin.reports') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-xl text-sm font-medium transition shadow-lg shadow-purple-900/30 flex items-center gap-2">
                    <span>📥</span> Unduh laporan
                </a>
            </div>
        </header>

        <div class="p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-[#141221] border border-gray-800/80 p-5 rounded-2xl shadow-sm">
                    <p class="text-xs text-gray-400">Total pendapatan lunas</p>
                    <h2 class="text-2xl font-bold text-white mt-1">{{ $stats['pendapatan'] ?? 'Rp0' }}</h2>
                    <p class="text-xs text-emerald-400 mt-2 font-medium">Data live database</p>
                </div>
                <div class="bg-[#141221] border border-gray-800/80 p-5 rounded-2xl shadow-sm">
                    <p class="text-xs text-gray-400">Booking lunas</p>
                    <h2 class="text-2xl font-bold text-white mt-1">{{ $stats['transaksi_lunas'] ?? 0 }}</h2>
                    <p class="text-xs text-emerald-400 mt-2 font-medium">Tercatat terbayar</p>
                </div>
                <div class="bg-[#141221] border border-gray-800/80 p-5 rounded-2xl shadow-sm">
                    <p class="text-xs text-gray-400">Booking hari ini</p>
                    <h2 class="text-2xl font-bold text-white mt-1">{{ $stats['booking_hari_ini'] ?? 0 }}</h2>
                    <p class="text-xs text-gray-400 mt-2">{{ $stats['booking_hari_ini_sub'] ?? '0 lunas · 0 belum' }}</p>
                </div>
                <div class="bg-[#141221] border border-gray-800/80 p-5 rounded-2xl shadow-sm">
                    <p class="text-xs text-gray-400">Utilisasi perangkat</p>
                    <h2 class="text-2xl font-bold text-white mt-1">{{ $stats['utilitas'] ?? '0%' }}</h2>
                    <p class="text-xs text-gray-400 mt-2">{{ $stats['sesi_aktif_sub'] ?? '0 sesi aktif' }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 bg-[#141221] border border-gray-800/80 p-5 rounded-2xl">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-semibold text-white">Utilisasi perangkat</h3>
                        <span class="text-xs text-gray-400">Saat ini {{ $stats['utilitas'] ?? '0%' }}</span>
                    </div>
                    <div class="h-48 flex items-center justify-center border border-dashed border-gray-800 rounded-xl text-gray-500 text-sm">
                        [ Area Grafik Batang Perangkat ]
                    </div>
                </div>

                <div class="bg-[#141221] border border-gray-800/80 p-5 rounded-2xl flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <h3 class="font-semibold text-white">Operasional hari ini</h3>
                            <a href="{{ route('admin.reports') }}" class="text-xs text-purple-400 hover:text-purple-300 hover:underline transition">
                                Detail →
                            </a>
                        </div>
                        <h2 class="text-xl font-bold text-white">{{ $stats['operasional_harian'] ?? 'Rp0' }}</h2>
                        <p class="text-xs text-gray-400 mt-1">{{ $stats['operasional_sub'] ?? '0 booking lunas' }}</p>
                    </div>
                    <div class="bg-[#1b182e] p-3 rounded-xl border border-gray-800 text-xs text-gray-300 mt-4">
                        @if($activeMaintenance)
                            <p class="font-semibold text-white mb-1">🛠️ {{ $activeMaintenance->device?->nama_device ?? 'Perangkat' }}</p>
                            <p class="text-gray-400">{{ $activeMaintenance->keterangan }}</p>
                        @else
                            <p class="font-semibold text-white mb-1">✅ Tidak ada maintenance aktif</p>
                            <p class="text-gray-400">Semua unit dalam kondisi normal</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </main>

</body>
</html>