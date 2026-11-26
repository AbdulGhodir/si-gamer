<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'GameHub - Workspace Staff')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-[#0b0c10] text-slate-200 font-sans min-h-screen flex">
    <aside class="w-64 bg-[#12131a] border-r border-slate-800/80 flex flex-col justify-between shrink-0 min-h-screen sticky top-0 h-screen">
        <div>
            <div class="p-6 flex items-center space-x-3 border-b border-slate-800/60">
                <div class="w-9 h-9 rounded-xl bg-purple-600 flex items-center justify-center text-white shadow-lg shadow-purple-600/30">
                    <i class="fa-solid fa-gamepad text-lg"></i>
                </div>
                <div>
                    <h1 class="font-bold text-white tracking-wide text-base leading-tight">GameHub</h1>
                    <p class="text-[10px] text-slate-400 font-medium tracking-wider uppercase">SI-GAMER / RENTAL SYSTEM</p>
                </div>
            </div>

            <div class="px-4 py-6">
                <p class="text-[10px] uppercase font-bold text-slate-500 tracking-wider mb-3 px-3">Workspace Staff</p>
                <nav class="space-y-1">
                    @php
                        $dashboardActive = request()->routeIs('staff.dashboard');
                        $bookingsActive = request()->routeIs('staff.bookings');
                        $sessionsActive = request()->routeIs('staff.sessions');
                        $walkInActive = request()->routeIs('staff.booking.walkin');
                        $devicesActive = request()->routeIs('staff.devices');
                        $maintenanceActive = request()->routeIs('staff.maintenance');
                        $activeNavClass = 'bg-purple-600/15 text-purple-400 font-semibold';
                        $inactiveNavClass = 'text-slate-400 hover:text-white hover:bg-slate-800/40';
                    @endphp

                    <a href="{{ route('staff.dashboard') }}"
                       class="flex items-center space-x-3 px-3 py-2.5 rounded-lg {{ $dashboardActive ? $activeNavClass : $inactiveNavClass }} text-sm transition"
                       @if($dashboardActive) aria-current="page" @endif>
                        <i class="fa-solid fa-table-cells-large w-5 text-center"></i>
                        <span>Dashboard</span>
                    </a>

                    <a href="{{ route('staff.bookings') }}"
                       class="flex items-center space-x-3 px-3 py-2.5 rounded-lg {{ $bookingsActive ? $activeNavClass : $inactiveNavClass }} text-sm font-medium transition"
                       @if($bookingsActive) aria-current="page" @endif>
                        <i class="fa-regular fa-calendar-check w-5 text-center"></i>
                        <span>Daftar booking</span>
                    </a>

                    <a href="{{ route('staff.sessions') }}"
                       class="flex items-center space-x-3 px-3 py-2.5 rounded-lg {{ $sessionsActive ? $activeNavClass : $inactiveNavClass }} text-sm font-medium transition"
                       @if($sessionsActive) aria-current="page" @endif>
                        <i class="fa-regular fa-clock w-5 text-center"></i>
                        <span>Sesi aktif</span>
                    </a>

                    <a href="{{ route('staff.booking.walkin') }}"
                       class="flex items-center space-x-3 px-3 py-2.5 rounded-lg {{ $walkInActive ? $activeNavClass : $inactiveNavClass }} text-sm font-medium transition"
                       @if($walkInActive) aria-current="page" @endif>
                        <i class="fa-solid fa-user-plus w-5 text-center"></i>
                        <span>Booking walk-in</span>
                    </a>

                    <a href="{{ route('staff.devices') }}"
                       class="flex items-center space-x-3 px-3 py-2.5 rounded-lg {{ $devicesActive ? $activeNavClass : $inactiveNavClass }} text-sm font-medium transition"
                       @if($devicesActive) aria-current="page" @endif>
                        <i class="fa-solid fa-desktop w-5 text-center"></i>
                        <span>Status perangkat</span>
                    </a>

                    <a href="{{ route('staff.maintenance') }}"
                       class="flex items-center space-x-3 px-3 py-2.5 rounded-lg {{ $maintenanceActive ? $activeNavClass : $inactiveNavClass }} text-sm font-medium transition"
                       @if($maintenanceActive) aria-current="page" @endif>
                        <i class="fa-solid fa-wrench w-5 text-center"></i>
                        <span>Maintenance</span>
                    </a>
                </nav>
            </div>
        </div>

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
                <button type="submit" class="text-slate-400 hover:text-rose-400 p-1.5 transition" title="Logout" aria-label="Logout">
                    <i class="fa-solid fa-arrow-right-from-bracket text-sm"></i>
                </button>
            </form>
        </div>
    </aside>

    <main class="flex-1 overflow-y-auto px-8 py-6">
        <div class="w-full max-w-7xl mx-auto">
            <header class="flex justify-between items-center pb-6">
                <div class="flex items-center space-x-3">
                    <div class="w-7 h-7 rounded-lg bg-purple-600 flex items-center justify-center text-white text-xs">
                        <i class="fa-solid fa-gamepad"></i>
                    </div>
                    <span class="font-bold text-white text-sm">GameHub</span>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="w-7 h-7 rounded-full bg-purple-600 flex items-center justify-center text-xs font-bold text-white">
                        {{ strtoupper(substr(Auth::user()->nama ?? 'S', 0, 1)) }}
                    </div>
                </div>
            </header>

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

            @yield('content')
        </div>
    </main>

    <x-lucide />
</body>
</html>
