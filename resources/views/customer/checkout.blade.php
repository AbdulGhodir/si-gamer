@extends('layouts.customer')

@section('title', 'Checkout Pembayaran')

@section('content')
<div class="max-w-3xl mx-auto py-8">
    <a href="{{ route('customer.riwayat') }}"
       class="inline-flex items-center gap-2 text-sm text-[#8B7EA1] hover:text-white transition-colors">
        <i class="ph ph-arrow-left"></i>
        Kembali ke Riwayat
    </a>

    <div class="mt-6 bg-[#130B29] border border-[#2D2442] rounded-2xl p-6 md:p-8 shadow-xl">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-purple-500/10 flex items-center justify-center">
                <i class="ph ph-credit-card text-2xl text-purple-400"></i>
            </div>

            <div>
                <p class="text-sm text-[#8B7EA1]">Pembayaran booking</p>
                <h1 class="text-2xl font-bold text-white">Checkout</h1>
            </div>
        </div>

        <div class="border-t border-[#2D2442] my-6"></div>

        <div class="space-y-4 text-sm">
            <div class="flex justify-between gap-4">
                <span class="text-[#8B7EA1]">Kode transaksi</span>
                <span class="font-semibold text-white">{{ $payment->kode_transaksi }}</span>
            </div>

            <div class="flex justify-between gap-4">
                <span class="text-[#8B7EA1]">Perangkat</span>
                <span class="font-semibold text-white">
                    {{ $payment->booking->device->nama_device }}
                </span>
            </div>

            <div class="flex justify-between gap-4">
                <span class="text-[#8B7EA1]">Total pembayaran</span>
                <span class="text-xl font-bold text-purple-400">
                    Rp {{ number_format($payment->jumlah, 0, ',', '.') }}
                </span>
            </div>
        </div>

        <div id="checkout-message"
             class="mt-6 rounded-xl border border-purple-500/30 bg-purple-500/10 p-4 text-sm text-purple-200">
            <span id="checkout-message-text">
                Menyiapkan pembayaran. Jendela pembayaran akan segera dibuka.
            </span>
        </div>

        <button id="retry-payment"
                type="button"
                class="hidden mt-4 w-full rounded-xl bg-purple-600 px-5 py-3 font-bold text-white hover:bg-purple-500 transition-colors">
            Coba buka pembayaran lagi
        </button>
    </div>
</div>

<script
    src="{{ config('midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
    data-client-key="{{ config('midtrans.client_key') }}">
</script>

<script>
    const snapToken = @json($payment->snap_token);
    const messageText = document.getElementById('checkout-message-text');
    const retryButton = document.getElementById('retry-payment');

    function openPayment() {
        if (!snapToken) {
            messageText.textContent = 'Token pembayaran tidak tersedia. Silakan kembali ke riwayat.';
            retryButton.classList.add('hidden');
            return;
        }

        retryButton.classList.add('hidden');
        messageText.textContent = 'Menyiapkan pembayaran. Jendela pembayaran akan segera dibuka.';

        window.snap.pay(snapToken, {
            onSuccess: function () {
                messageText.textContent = 'Pembayaran berhasil. Memuat riwayat booking…';
                window.location.href = @json(route('customer.riwayat'));
            },
            onPending: function () {
                messageText.textContent = 'Pembayaran sedang menunggu konfirmasi.';
                window.location.href = @json(route('customer.riwayat'));
            },
            onError: function () {
                messageText.textContent = 'Pembayaran gagal diproses. Silakan coba lagi.';
                retryButton.classList.remove('hidden');
            },
            onClose: function () {
                messageText.textContent = 'Jendela pembayaran ditutup. Kamu bisa membukanya lagi.';
                retryButton.classList.remove('hidden');
            }
        });
    }

    retryButton.addEventListener('click', openPayment);

    window.addEventListener('load', openPayment);
</script>
@endsection
