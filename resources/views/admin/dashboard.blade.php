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
                <button type="submit" class="rounded-lg border border-gray-700 p-2 text-gray-300 transition hover:border-red-500/50 hover:bg-red-500/10 hover:text-red-300 cursor-pointer" title="Logout" aria-label="Logout">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6A2.25 2.25 0 005.25 5.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M18 15l3-3m0 0l-3-3m3 3H9" />
                    </svg>
                </button>
            </form>
        </div>
    </aside>

    <main class="flex-1 flex flex-col h-screen overflow-y-auto bg-[#0b0a12]">
        <header class="p-6 pb-4 border-b border-gray-800/50">
            <div>
                <h1 class="text-2xl font-bold text-white tracking-tight">Ringkasan bisnis</h1>
                <p class="text-xs text-gray-400 mt-0.5">Kinerja rental dan operasional GameHub, dalam satu tempat.</p>
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
                    <div class="rounded-xl border border-gray-800 bg-[#110f1c] p-5" role="group" aria-label="Distribusi status perangkat">
                        <div class="flex items-center justify-between mb-5">
                            <p class="text-xs text-gray-400">Status seluruh perangkat</p>
                            <p class="text-xs text-gray-500">{{ $totalDeviceCount }} unit</p>
                        </div>
                        @if($totalDeviceCount > 0)
                            <div class="space-y-5">
                                @foreach($deviceChart as $item)
                                    <div class="grid grid-cols-[5.5rem_minmax(0,1fr)_5.5rem] items-center gap-3">
                                        <span class="text-xs text-gray-300">{{ $item['label'] }}</span>
                                        <div class="h-3 overflow-hidden rounded-full bg-gray-800" role="progressbar"
                                             aria-label="{{ $item['label'] }}"
                                             aria-valuemin="0"
                                             aria-valuemax="{{ $totalDeviceCount }}"
                                             aria-valuenow="{{ $item['count'] }}">
                                            <div class="h-full rounded-full {{ $item['color'] }} transition-all"
                                                 style="width: {{ $item['percentage'] }}%"></div>
                                        </div>
                                        <span class="text-right text-xs text-gray-400">
                                            {{ $item['count'] }} <span class="text-gray-600">({{ $item['percentage'] }}%)</span>
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="flex h-32 items-center justify-center text-sm text-gray-500">
                                Belum ada perangkat untuk ditampilkan.
                            </div>
                        @endif
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