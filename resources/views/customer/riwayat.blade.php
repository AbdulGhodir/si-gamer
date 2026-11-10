@extends('layouts.customer')

@section('title', 'SI Gamer - Riwayat Booking')

@section('content')
<div class="flex flex-col gap-6">

    <div>
        <h1 class="text-3xl font-extrabold text-white tracking-wide">Riwayat Booking</h1>
    </div>

    <div class="border-b border-[#2D2442]">
        <div class="flex items-center gap-8 overflow-x-auto">
            <button type="button" 
                    onclick="filterStatus('all')" 
                    id="tab-all" 
                    class="status-tab text-sm font-semibold pb-3 border-b-2 border-purple-500 text-white transition-colors cursor-pointer focus:outline-none whitespace-nowrap">
                Semua Status
            </button>
            <button type="button" 
                    onclick="filterStatus('menunggu')" 
                    id="tab-menunggu" 
                    class="status-tab text-sm font-semibold pb-3 border-b-2 border-transparent text-[#8B7EA1] hover:text-white transition-colors cursor-pointer focus:outline-none whitespace-nowrap">
                Menunggu
            </button>
            <button type="button" 
                    onclick="filterStatus('bermain')" 
                    id="tab-bermain" 
                    class="status-tab text-sm font-semibold pb-3 border-b-2 border-transparent text-[#8B7EA1] hover:text-white transition-colors cursor-pointer focus:outline-none whitespace-nowrap">
                Bermain
            </button>
            <button type="button" 
                    onclick="filterStatus('selesai')" 
                    id="tab-selesai" 
                    class="status-tab text-sm font-semibold pb-3 border-b-2 border-transparent text-[#8B7EA1] hover:text-white transition-colors cursor-pointer focus:outline-none whitespace-nowrap">
                Selesai
            </button>
        </div>
    </div>

    <div class="flex flex-col gap-4" id="riwayat-container">
        @foreach($riwayatList as $item)
            <div class="riwayat-card bg-[#130B29] border border-[#2D2442] hover:border-purple-500/50 rounded-2xl p-5 md:p-6 flex flex-col md:flex-row md:items-center justify-between gap-5 transition-colors shadow-lg"
                 data-status="{{ $item['status'] }}">
                
                <div class="flex items-start md:items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-[#1A122E] border border-purple-500/20 flex items-center justify-center shrink-0">
                        @if($item['tipe'] === 'PC')
                            <i class="ph ph-desktop text-2xl text-purple-400"></i>
                        @else
                            <i class="ph-fill ph-game-controller text-2xl text-purple-400"></i>
                        @endif
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <div class="flex items-center gap-3 flex-wrap">
                            <h3 class="text-lg font-bold text-white tracking-wide">
                                {{ $item['nama_device'] }}
                            </h3>

                            @if($item['status'] === 'menunggu')
                                <span class="bg-amber-400/10 border border-amber-400/30 text-amber-400 px-2.5 py-0.5 rounded text-[10px] font-bold tracking-wider uppercase">
                                    MENUNGGU
                                </span>
                            @elseif($item['status'] === 'bermain')
                                <span class="bg-emerald-400/10 border border-emerald-400/30 text-emerald-400 px-2.5 py-0.5 rounded text-[10px] font-bold tracking-wider uppercase flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                    BERMAIN
                                </span>
                            @elseif($item['status'] === 'selesai')
                                <span class="bg-sky-500/15 border border-sky-400/30 text-sky-400 px-2.5 py-0.5 rounded text-[10px] font-bold tracking-wider uppercase flex items-center gap-1.5">
                                    <i class="ph ph-check-circle text-xs"></i>
                                    SELESAI
                                </span>
                            @endif
                        </div>

                        <div class="flex items-center gap-2 text-xs text-[#8B7EA1]">
                            <i class="ph ph-calendar-blank text-sm text-[#8B7EA1]"></i>
                            <span>{{ $item['jadwal'] }} ({{ $item['durasi'] }})</span>
                        </div>

                        <div class="flex items-center gap-2 text-xs text-[#8B7EA1]">
                            <i class="ph ph-qr-code text-sm text-purple-400"></i>
                            <span>Kode: <span class="font-semibold text-white">{{ $item['kode'] }}</span></span>
                        </div>
                    </div>
                </div>

                <div class="flex md:flex-col items-center md:items-end justify-between md:justify-center gap-3 pt-3 md:pt-0 border-t md:border-t-0 border-[#2D2442]">
                    <div class="flex flex-col md:text-right">
                        <span class="text-xs text-[#8B7EA1] font-medium">Total Pembayaran</span>
                        <span class="text-xl md:text-2xl font-bold text-white">
                            Rp {{ number_format($item['total'], 0, ',', '.') }}
                        </span>
                    </div>

                    <button type="button" 
                            onclick='openReceiptModal(@json($item))'
                            class="px-5 py-2 rounded-xl {{ $item['status'] === 'selesai' ? 'border border-[#2D2442] bg-[#161124] text-[#8B7EA1] hover:text-white hover:border-purple-500' : 'border border-purple-500/50 bg-purple-600/10 text-purple-300 hover:bg-purple-600 hover:text-white' }} font-semibold text-xs tracking-wide transition-colors cursor-pointer">
                        {{ $item['status'] === 'selesai' ? 'Detail' : 'Lihat Struk' }}
                    </button>
                </div>

            </div>
        @endforeach
    </div>

</div>

<div id="receipt-modal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-xs p-4">
    
    <div class="bg-[#130B29] border border-[#2D2442] rounded-2xl max-w-sm w-full p-6 shadow-2xl relative text-white flex flex-col"
         onclick="event.stopPropagation()">
        
        <div class="flex items-center justify-between pb-3">
            <h3 class="text-xl font-bold text-white tracking-wide">Struk Booking</h3>
            <button type="button" 
                    onclick="closeReceiptModal()" 
                    class="text-[#8B7EA1] hover:text-white text-xl leading-none cursor-pointer">
                <i class="ph ph-x"></i>
            </button>
        </div>

        <div class="w-24 h-24 mx-auto bg-white rounded-2xl p-2 flex items-center justify-center shadow-lg my-3"></div>

        <p class="text-xs text-[#8B7EA1] text-center font-medium">
            Tunjukkan QR atau Kode ini ke Kasir
        </p>

        <h2 id="modal-kode" class="text-2xl font-black text-purple-400 tracking-wider text-center mt-1">
            #BK-99210
        </h2>

        <div class="border-t border-dashed border-[#2D2442] my-4"></div>

        <div class="flex flex-col gap-2.5 text-xs">
            <div class="flex justify-between items-center">
                <span class="text-[#8B7EA1]">Nama Customer</span>
                <span id="modal-customer" class="font-bold text-white">Chayyarra Igda</span>
            </div>

            <div class="flex justify-between items-center">
                <span class="text-[#8B7EA1]">Perangkat</span>
                <span id="modal-perangkat" class="font-bold text-white">PlayStation 5 - A</span>
            </div>

            <div class="flex justify-between items-center">
                <span class="text-[#8B7EA1]">Jadwal</span>
                <span id="modal-jadwal" class="font-bold text-white">Hari ini, 12:00 – 16:00</span>
            </div>

            <div class="flex justify-between items-center">
                <span class="text-[#8B7EA1]">Durasi</span>
                <span id="modal-durasi" class="font-bold text-white">4 Jam</span>
            </div>

            <div class="flex justify-between items-center">
                <span class="text-[#8B7EA1]">Status</span>
                <span id="modal-status" class="font-bold uppercase tracking-wider">MENUNGGU</span>
            </div>

            <div class="flex justify-between items-center">
                <span class="text-[#8B7EA1]">Metode Bayar</span>
                <span id="modal-metode" class="font-bold text-white">QRIS</span>
            </div>
        </div>

        <div class="border-t border-dashed border-[#2D2442] my-4"></div>

        <div class="flex justify-between items-center mb-3">
            <span class="text-xs text-[#8B7EA1] font-medium">Total Pembayaran</span>
            <span id="modal-total" class="text-2xl font-black text-purple-400">Rp 60.000</span>
        </div>

        <div class="w-full py-2.5 rounded-xl bg-emerald-950/60 border border-emerald-500/40 text-emerald-400 font-bold text-xs flex items-center justify-center gap-2">
            <i class="ph-fill ph-check-circle text-base"></i>
            <span>Pembayaran Berhasil</span>
        </div>

        <button type="button" 
                onclick="closeReceiptModal()" 
                class="w-full mt-3 py-3 rounded-xl bg-[#1C1132] border border-purple-500/40 hover:bg-purple-600 text-white font-bold text-xs tracking-wide transition-colors cursor-pointer text-center">
            Tutup Struk
        </button>

    </div>
</div>

<script>
    function filterStatus(status) {
        const tabs = document.querySelectorAll('.status-tab');
        tabs.forEach(tab => {
            tab.classList.remove('border-purple-500', 'text-white');
            tab.classList.add('border-transparent', 'text-[#8B7EA1]');
        });

        const activeTab = document.getElementById('tab-' + status);
        if (activeTab) {
            activeTab.classList.remove('border-transparent', 'text-[#8B7EA1]');
            activeTab.classList.add('border-purple-500', 'text-white');
        }

        const cards = document.querySelectorAll('.riwayat-card');
        cards.forEach(card => {
            if (status === 'all' || card.getAttribute('data-status') === status) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    }

    function openReceiptModal(data) {
        document.getElementById('modal-kode').textContent = data.kode;
        document.getElementById('modal-customer').textContent = data.customer;
        document.getElementById('modal-perangkat').textContent = data.nama_device;
        document.getElementById('modal-jadwal').textContent = data.jadwal;
        document.getElementById('modal-durasi').textContent = data.durasi;
        document.getElementById('modal-metode').textContent = data.metode;
        document.getElementById('modal-total').textContent = 'Rp ' + Number(data.total).toLocaleString('id-ID');

        const statusEl = document.getElementById('modal-status');
        statusEl.textContent = data.status.toUpperCase();
        if (data.status === 'menunggu') {
            statusEl.className = 'font-bold uppercase tracking-wider text-amber-400';
        } else if (data.status === 'bermain') {
            statusEl.className = 'font-bold uppercase tracking-wider text-emerald-400';
        } else if (data.status === 'selesai') {
            statusEl.className = 'font-bold uppercase tracking-wider text-sky-400';
        }

        const modal = document.getElementById('receipt-modal');
        modal.style.display = 'flex';
    }

    function closeReceiptModal() {
        document.getElementById('receipt-modal').style.display = 'none';
    }

    document.getElementById('receipt-modal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeReceiptModal();
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeReceiptModal();
        }
    });
</script>
@endsection

