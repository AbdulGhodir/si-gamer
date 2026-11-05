<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'GameHub - Workspace Staff')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Alpine.js untuk kontrol modal dan dropdown secara reaktif -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-[#0b0c10] text-slate-200 font-sans min-h-screen flex" x-data="{ walkinModal: false, maintenanceModal: false }">

    <!-- SIDEBAR KIRI -->
    <aside class="w-64 bg-[#12131a] border-r border-slate-800/80 flex flex-col justify-between shrink-0 min-h-screen sticky top-0 h-screen">
        <div>
            <!-- Header Brand -->
            <div class="p-6 flex items-center space-x-3 border-b border-slate-800/60">
                <div class="w-9 h-9 rounded-xl bg-purple-600 flex items-center justify-center text-white shadow-lg shadow-purple-600/30">
                    <i class="fa-solid fa-gamepad text-lg"></i>
                </div>
                <div>
                    <h1 class="font-bold text-white tracking-wide text-base leading-tight">GameHub</h1>
                    <p class="text-[10px] text-slate-400 font-medium tracking-wider uppercase">SI-GAMER / RENTAL SYSTEM</p>
                </div>
            </div>

            <!-- Navigasi Menu Workspace Staff -->
            <div class="px-4 py-6">
                <p class="text-[10px] uppercase font-bold text-slate-500 tracking-wider mb-3 px-3">Workspace Staff</p>
                <nav class="space-y-1">
                    <!-- 1. Dashboard -->
                    <a href="{{ route('staff.dashboard') }}" 
                       class="flex items-center space-x-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('staff.dashboard') && !request('status') ? 'bg-purple-600/15 text-purple-400 font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-800/40' }} text-sm transition">
                        <i class="fa-solid fa-table-cells-large w-5 text-center"></i>
                        <span class="flex-1">Dashboard</span>
                        @if(request()->routeIs('staff.dashboard') && !request('status'))
                            <span class="w-1.5 h-1.5 rounded-full bg-purple-400"></span>
                        @endif
                    </a>

                    <!-- 2. Daftar Booking (Scroll ke tabel / filter semua) -->
                    <a href="{{ route('staff.dashboard', ['status' => 'Semua']) }}#table-booking" 
                       class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800/40 text-sm font-medium transition">
                        <i class="fa-regular fa-calendar-check w-5 text-center"></i>
                        <span>Daftar booking</span>
                    </a>

                    <!-- 3. Sesi Aktif (Scroll ke panel sesi) -->
                    <a href="#sesi-aktif" 
                       class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800/40 text-sm font-medium transition">
                        <i class="fa-regular fa-clock w-5 text-center"></i>
                        <span>Sesi aktif</span>
                    </a>

                    <!-- 4. Booking Walk-in (Membuka Popup Modal Walk-in) -->
                    <button type="button" 
                            @click="walkinModal = true" 
                            class="w-full text-left flex items-center space-x-3 px-3 py-2.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800/40 text-sm font-medium transition">
                        <i class="fa-solid fa-user-plus w-5 text-center"></i>
                        <span>Booking walk-in</span>
                    </button>

                    <!-- 5. Status Perangkat (Scroll ke grid perangkat) -->
                    <a href="#status-perangkat" 
                       class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800/40 text-sm font-medium transition">
                        <i class="fa-solid fa-desktop w-5 text-center"></i>
                        <span>Status perangkat</span>
                    </a>

                    <!-- 6. Maintenance (Membuka Popup Modal Status Perbaikan) -->
                    <button type="button" 
                            @click="maintenanceModal = true" 
                            class="w-full text-left flex items-center space-x-3 px-3 py-2.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800/40 text-sm font-medium transition">
                        <i class="fa-solid fa-wrench w-5 text-center"></i>
                        <span>Maintenance</span>
                    </button>
                </nav>
            </div>
        </div>

        <!-- Profil Staff & Logout -->
        <div class="p-4 border-t border-slate-800/60 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 rounded-full bg-purple-950 border border-purple-500/30 flex items-center justify-center text-xs font-semibold text-purple-300">
                    {{ strtoupper(substr(Auth::user()->nama ?? 'ST', 0, 2)) }}
                </div>
                <div class="overflow-hidden">
                    <p class="text-xs font-semibold text-white truncate">{{ Auth::user()->nama ?? 'Staff' }}</p>
                    <p class="text-[11px] text-slate-400 capitalize">{{ Auth::user()->role ?? 'Staff' }}</p>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="text-slate-400 hover:text-rose-400 p-1.5 transition" title="Logout">
                    <i class="fa-solid fa-arrow-right-from-bracket text-sm"></i>
                </button>
            </form>
        </div>
    </aside>

    <!-- KONTEN UTAMA -->
    <main class="flex-1 overflow-y-auto px-8 py-6">
        <!-- Topbar Sederhana -->
        <header class="flex justify-between items-center pb-6">
            <div class="flex items-center space-x-3">
                <div class="w-7 h-7 rounded-lg bg-purple-600 flex items-center justify-center text-white text-xs">
                    <i class="fa-solid fa-gamepad"></i>
                </div>
                <span class="font-bold text-white text-sm">GameHub</span>
            </div>
            <div class="flex items-center space-x-4">
                <button class="text-slate-400 hover:text-white transition">
                    <i class="fa-regular fa-bell text-sm"></i>
                </button>
                <div class="w-7 h-7 rounded-full bg-purple-600 flex items-center justify-center text-xs font-bold text-white">
                    {{ strtoupper(substr(Auth::user()->nama ?? 'S', 0, 1)) }}
                </div>
            </div>
        </header>

        <!-- Flash Message Notifikasi Sukses / Gagal -->
        @if(session('success'))
            <div class="mb-4 p-3 rounded-lg bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs flex items-center space-x-2">
                <i class="fa-solid fa-circle-check text-sm"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 p-3 rounded-lg bg-rose-500/10 border border-rose-500/30 text-rose-400 text-xs">
                @foreach ($errors->all() as $error)
                    <p>&bull; {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <!-- Tempat Konten Tiap Halaman -->
        @yield('content')
    </main>

    <!-- MODAL POPUP: BOOKING WALK-IN (Menggunakan kolom id_device & durasi) -->
    <div x-show="walkinModal" 
         style="display: none;" 
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4">
        <div class="bg-[#12131a] border border-slate-800 rounded-xl max-w-md w-full p-6 shadow-2xl" 
             @click.away="walkinModal = false">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-base font-bold text-white">Booking Walk-in Baru</h3>
                <button type="button" @click="walkinModal = false" class="text-slate-400 hover:text-white text-xl leading-none">&times;</button>
            </div>

            <form action="{{ route('staff.booking.walkin.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Pilih Perangkat Tersedia</label>
                    <select name="id_device" required class="w-full bg-[#181924] border border-slate-800 rounded-lg px-3 py-2 text-xs text-slate-300 focus:outline-none focus:border-purple-600">
                        @forelse($devicesTersedia ?? [] as $dev)
                            <option value="{{ $dev->id }}">
                                {{ $dev->nama_device }} ({{ $dev->tipe }}) - Rp {{ number_format($dev->harga_per_jam, 0, ',', '.') }}/jam
                            </option>
                        @empty
                            <option value="" disabled selected>Tidak ada unit yang siap saat ini</option>
                        @endforelse
                    </select>
                </div>

                <div>
                    <label class="block text-xs text-slate-400 mb-1">Durasi Bermain (Jam)</label>
                    <input type="number" name="durasi" value="1" min="1" max="12" required 
                           class="w-full bg-[#181924] border border-slate-800 rounded-lg px-3 py-2 text-xs text-white focus:outline-none focus:border-purple-600">
                </div>

                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" @click="walkinModal = false" class="px-3 py-1.5 rounded-lg bg-slate-800 text-xs text-slate-300 hover:bg-slate-700">Batal</button>
                    <button type="submit" class="px-4 py-1.5 rounded-lg bg-purple-600 text-xs font-semibold text-white hover:bg-purple-500 transition">Mulai Sesi Langsung</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL POPUP: MAINTENANCE (Menggunakan kolom id_device) -->
    <div x-show="maintenanceModal" 
         style="display: none;" 
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4">
        <div class="bg-[#12131a] border border-slate-800 rounded-xl max-w-md w-full p-6 shadow-2xl" 
             @click.away="maintenanceModal = false">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-base font-bold text-white">Status Perbaikan Perangkat</h3>
                <button type="button" @click="maintenanceModal = false" class="text-slate-400 hover:text-white text-xl leading-none">&times;</button>
            </div>

            <form action="{{ route('staff.maintenance.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Pilih Perangkat</label>
                    <select name="id_device" required class="w-full bg-[#181924] border border-slate-800 rounded-lg px-3 py-2 text-xs text-slate-300 focus:outline-none focus:border-purple-600">
                        @foreach(\App\Models\Device::all() as $dev)
                            <option value="{{ $dev->id }}">
                                {{ $dev->nama_device }} ({{ $dev->tipe }}) - Status: {{ $dev->status }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <p class="text-[11px] text-slate-400">Tindakan ini akan mengubah status perangkat menjadi <b>perbaikan</b> dan menonaktifkannya dari sistem sewa.</p>

                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" @click="maintenanceModal = false" class="px-3 py-1.5 rounded-lg bg-slate-800 text-xs text-slate-300 hover:bg-slate-700">Batal</button>
                    <button type="submit" class="px-4 py-1.5 rounded-lg bg-amber-600 text-xs font-semibold text-white hover:bg-amber-500 transition">Set ke Perbaikan</button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>