

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SI Gamer - Login</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body class="flex w-screen h-screen">
   
    <div class="flex flex-col justify-between bg-linear-to-br from-[#0B0812] via-[#130B29] to-[#2A0845] w-1/2 h-full px-16 pb-10 pt-16">
        <div class="flex items-center gap-3">
            <i class="ph-fill ph-game-controller text-5xl text-purple-500"></i>
            <div class="flex flex-col">
                <h1 class="text-2xl font-bold text-white">SI-GAMER</h1>
                <p class="text-sm font-semibold text-[#8B7EA1]">Gaming Center</p>
            </div>
        </div>

        <div class="flex flex-col gap-2 max-w-lg">
            @yield('left-content')
        </div>
    

        <div class=" flex w-full justify-between gap-4 border-t-2 border-purple-500/30 pt-10">
            <div class="flex flex-col items-center gap-2">
                <i class="ph ph-clock font-semibold text-2xl text-purple-500"></i>
                <p class="text-xs text-white font-semibold">Booking Cepat</p>
            </div>

            <div class="flex flex-col items-center gap-2">
                <i class="ph ph-qr-code font-semibold text-2xl text-purple-500"></i>
                <p class="text-xs text-white font-semibold">Pembayaran Mudah</p>
            </div>

            <div class="flex flex-col items-center gap-2">
                <i class="ph ph-desktop font-semibold text-2xl text-purple-500"></i>
                <p class="text-xs text-white font-semibold">Fasilitas Lengkap</p>
            </div>
        </div>
    
    </div>

    
    <div class="bg-[#0B0812] w-1/2 h-full flex items-center justify-center">
        <div class="flex flex-col gap-2 w-lg">
            @yield('right-content')
        </div>
    </div>
</body>
</html>