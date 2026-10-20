
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GameHub — Dashboard</title>

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg: #0b0811;
            --panel: #171020;
            --dark: #100b18;
            --border: #30243d;
            --purple: #982cf0;
            --light: #c17aff;
            --muted: #9586a9;
            --green: #55d9b0;
            --red: #ff7979;
        }

        body {
            background: var(--bg);
            color: #f8f5ff;
            font-family: Arial, sans-serif;
            font-size: 13px;
        }

        a { color: inherit; text-decoration: none; }
        button, input, select { font: inherit; }
        button { cursor: pointer; }

        .navbar {
            min-height: 76px;
            padding: 15px 4%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            border-bottom: 1px solid var(--border);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 21px;
        }

        .brand-icon {
            width: 38px;
            height: 38px;
            display: grid;
            place-items: center;
            background: var(--purple);
            border-radius: 11px;
            font-size: 21px;
        }

        .nav-links {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            color: var(--muted);
        }

        .nav-links a:hover, .purple { color: var(--light); }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .profile {
            width: 36px;
            height: 36px;
            display: grid;
            place-items: center;
            border-radius: 50%;
            background: #301047;
            color: #d9a9ff;
            font-weight: bold;
        }

        .logout {
            padding: 9px 12px;
            background: transparent;
            color: white;
            border: 1px solid #49345d;
            border-radius: 8px;
        }

        .container {
            max-width: 1600px;
            margin: auto;
            padding: 32px 4%;
        }

        .welcome h1 {
            font-size: 28px;
            font-weight: 500;
            margin-bottom: 9px;
        }

        .muted { color: var(--muted); line-height: 1.6; }

        .grid {
            display: grid;
            grid-template-columns: minmax(0, 1.25fr) minmax(320px, 1fr);
            gap: 22px;
            align-items: start;
            margin-top: 28px;
        }

        .section-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 16px;
        }

        h2 { font-size: 17px; font-weight: 600; }

        .panel {
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 13px;
            padding: 19px;
            margin-bottom: 20px;
            min-width: 0;
        }

        .device-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 13px;
        }

        .device-card {
            overflow: hidden;
            background: var(--panel);
            border: 1px solid var(--border);
            border-radius: 12px;
            min-width: 0;
            transition: .2s;
        }

        .device-card.selected,
        .device-card:hover { border-color: var(--purple); }

        .device-art {
            height: 110px;
            display: grid;
            place-items: center;
            font-size: 45px;
            background: linear-gradient(135deg, #30203e, #111018);
            border-bottom: 1px solid var(--border);
        }

        .device-info { padding: 13px; }
        .device-info h3 { font-size: 14px; margin-bottom: 8px; }

        .spec {
            color: #aa9bb9;
            font-size: 11px;
            line-height: 1.7;
            min-height: 36px;
        }

        .price { font-size: 16px; margin-top: 11px; }
        .price small { font-size: 11px; color: var(--muted); }

        .device-bottom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 6px;
            margin-top: 13px;
        }

        .availability {
            font-size: 10px;
            color: var(--green);
            line-height: 1.5;
        }

        .availability.none { color: var(--red); }

        .select-device {
            padding: 7px 10px;
            color: white;
            background: transparent;
            border: 1px solid var(--border);
            border-radius: 7px;
        }

        .device-card.selected .select-device,
        .primary {
            background: var(--purple);
            border-color: var(--purple);
            color: white;
        }

        .primary {
            width: 100%;
            padding: 13px 15px;
            border: 0;
            border-radius: 9px;
            font-weight: bold;
            margin-top: 12px;
        }

        .primary:hover { background: #8522d8; }

        .field-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 13px;
            margin-top: 20px;
        }

        .field { min-width: 0; }
        .field.full { grid-column: 1 / -1; }

        .field label {
            display: block;
            color: var(--muted);
            margin-bottom: 8px;
        }

        .field input, .field select {
            width: 100%;
            min-width: 0;
            padding: 12px;
            background: var(--dark);
            color: white;
            border: 1px solid var(--border);
            border-radius: 8px;
            outline: none;
        }

        .field input:focus, .field select:focus {
            border-color: var(--purple);
        }

        .field select option { background: var(--dark); }

        .price-summary { margin-top: 19px; }

        .price-row {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            margin: 12px 0;
            color: var(--muted);
        }

        .price-row strong { color: white; font-weight: 500; }

        .price-total {
            border-top: 1px solid var(--border);
            padding-top: 13px;
        }

        .price-total strong { font-size: 16px; }

        .payment-note {
            display: flex;
            gap: 12px;
            align-items: center;
            background: var(--dark);
            border-radius: 9px;
            padding: 14px;
            margin-top: 17px;
        }

        .qr { font-size: 25px; }

        .payment-note p {
            color: var(--muted);
            font-size: 11px;
            margin-top: 5px;
            line-height: 1.5;
        }

        .booking-info {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-top: 18px;
        }

        .booking-icon {
            width: 43px;
            height: 43px;
            display: grid;
            place-items: center;
            flex-shrink: 0;
            background: #301047;
            border-radius: 10px;
            font-size: 21px;
        }

        .booking-details { flex: 1; min-width: 0; }
        .booking-details strong { display: block; margin-bottom: 6px; }

        .status {
            display: inline-block;
            padding: 5px 9px;
            border-radius: 6px;
            font-size: 10px;
            white-space: nowrap;
        }

        .green { background: #10352e; color: var(--green); }
        .gray { background: #30263b; color: #d0c3df; }
        .red { background: #411e2a; color: var(--red); }

        .booking-code {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            align-items: center;
            border-top: 1px solid var(--border);
            padding-top: 15px;
            margin-top: 15px;
        }

        .booking-code strong {
            display: block;
            font-size: 17px;
            margin-top: 6px;
        }

        .small-label { color: var(--muted); font-size: 10px; }

        .table-wrap { overflow-x: auto; margin-top: 15px; }

        table {
            width: 100%;
            min-width: 500px;
            border-collapse: collapse;
            text-align: left;
        }

        th, td {
            padding: 12px 10px;
            border-bottom: 1px solid var(--border);
            white-space: nowrap;
        }

        th {
            background: var(--dark);
            color: var(--muted);
            font-size: 11px;
            font-weight: normal;
        }

        td { font-size: 11px; }

        .transaction-date {
            display: block;
            color: var(--muted);
            margin-top: 5px;
            font-size: 10px;
        }

        .notice {
            padding: 12px 14px;
            margin-bottom: 20px;
            border-radius: 9px;
            background: #10352e;
            color: var(--green);
            line-height: 1.6;
        }

        .notice.error { background: #411e2a; color: #ffaaaa; }

        .member-card {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-top: 15px;
            padding: 16px;
            border: 1px solid #55336e;
            border-radius: 10px;
            background: linear-gradient(120deg, #24112f, #171020);
        }

        .member-discount {
            color: var(--light);
            font-size: 19px;
            font-weight: bold;
        }

        .review-stars { color: #f2c05b; letter-spacing: 3px; }

        .empty {
            padding: 20px 0;
            text-align: center;
            color: var(--muted);
            line-height: 1.7;
        }

        .errors { color: #ffaaaa; margin-top: 14px; line-height: 1.8; }

        @media(max-width: 1100px) {
            .navbar { flex-wrap: wrap; }
            .nav-links { order: 3; width: 100%; }
            .grid { grid-template-columns: 1fr; }
        }

        @media(max-width: 650px) {
            .navbar { padding: 16px; }
            .nav-links { gap: 12px; }
            .container { padding: 25px 15px; }
            .welcome h1 { font-size: 23px; }
            .device-grid { grid-template-columns: 1fr; }
            .device-art { height: 145px; }
            .field-grid { grid-template-columns: 1fr; }
            .field.full { grid-column: auto; }
            .panel { padding: 15px; }
            .booking-info { align-items: flex-start; flex-wrap: wrap; }
            .booking-code { align-items: flex-start; flex-direction: column; }
        }
    </style>
</head>
<body>
<nav class="navbar">
    <a class="brand" href="#top">
        <span class="brand-icon">🎮</span><strong>GameHub</strong>
    </a>

    <div class="nav-links">
        <a href="#katalog">Katalog perangkat</a>
        <a href="#booking">Booking saya</a>
        <a href="#riwayat">Riwayat transaksi</a>
        <a href="#membership">Membership</a>
        <a href="#ulasan">Rating & ulasan</a>
    </div>

    <div class="nav-actions">
        <div class="profile">{{ strtoupper(substr($user->nama ?? 'P', 0, 1)) }}</div>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button class="logout" type="submit">Logout</button>
        </form>
    </div>
</nav>

<main class="container" id="top">
    @if(session('success'))
        <div class="notice">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="notice error">
            <strong>Booking belum berhasil:</strong>
            <div class="errors">
                @foreach($errors->all() as $error)
                    <div>• {{ $error }}</div>
                @endforeach
            </div>
        </div>
    @endif

    <header class="welcome">
        <h1>Mau main apa hari ini, {{ $user->nama }}?</h1>
        <p class="muted">Pilih perangkat, tentukan jadwal, dan amankan sesi bermainmu.</p>
    </header>

    <div class="grid">
        <div>
            <section id="katalog">
                <div class="section-heading">
                    <h2>Katalog perangkat</h2>
                    <span class="muted">{{ $devices->sum('jumlah') }} unit</span>
                </div>

                <div class="device-grid">
                    @forelse($devices as $device)
                        @php
                            $nama = match($device->tipe) {
                                'PS4' => 'PlayStation 4',
                                'PS5' => 'PlayStation 5',
                                default => 'PC Gaming',
                            };

                            $emoji = $device->tipe === 'PC' ? '🖥️' : '🎮';

                            $spesifikasi = match($device->tipe) {
                                'PS4' => 'PS4 Console<br>TV FHD · Controller',
                                'PS5' => 'PS5 Console<br>TV 4K · Controller',
                                default => 'Gaming PC<br>Monitor · Keyboard & Mouse',
                            };

                            $tersedia = (int) $device->tersedia;
                        @endphp

                        <article class="device-card">
                            <div class="device-art">{{ $emoji }}</div>
                            <div class="device-info">
                                <h3>{{ $nama }}</h3>
                                <p class="spec">{!! $spesifikasi !!}</p>

                                <div class="price">
                                    Rp{{ number_format($device->harga_per_jam, 0, ',', '.') }}
                                    <small>/ jam</small>
                                </div>

                                <div class="device-bottom">
                                    <span class="availability {{ $tersedia < 1 ? 'none' : '' }}">
                                        {{ $tersedia }} dari {{ $device->jumlah }} tersedia
                                    </span>

                                    @if($tersedia > 0 && $device->id)
                                        <button
                                            type="button"
                                            class="select-device"
                                            data-id="{{ $device->id }}"
                                            data-name="{{ $nama }}"
                                            data-price="{{ $device->harga_per_jam }}"
                                            onclick="pilihPerangkat(this)">
                                            Pilih
                                        </button>
                                    @else
                                        <button type="button" class="select-device" disabled>
                                            Habis
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="panel">
                            <p class="empty">
                                Belum ada perangkat di database.<br>
                                Tambahkan perangkat melalui database terlebih dahulu.
                            </p>
                        </div>
                    @endforelse
                </div>
            </section>

            <section class="panel" id="booking" style="margin-top:20px">
                <div class="section-heading">
                    <h2>Booking Saya</h2>
                    <span class="muted">{{ $bookings->count() }} booking</span>
                </div>

                @forelse($bookings as $booking)
                    <div class="booking-info">
                        <div class="booking-icon">🎮</div>

                        <div class="booking-details">
                            <strong>{{ $booking->nama_device }}</strong>
                            <p class="muted">
                                {{ \Carbon\Carbon::parse($booking->waktu_mulai)->format('d M Y, H:i') }}
                                –
                                {{ \Carbon\Carbon::parse($booking->waktu_selesai)->format('H:i') }}
                                · {{ $booking->durasi }} jam
                            </p>

                            <p class="muted" style="margin-top:6px">
                                Total: Rp{{ number_format($booking->total_harga, 0, ',', '.') }}
                            </p>
                        </div>

                        @php
                            $statusBooking = strtolower($booking->status ?? 'menunggu');

                            $kelasBooking = in_array($statusBooking, ['selesai', 'berhasil'])
                                ? 'green'
                                : (in_array($statusBooking, ['ditolak', 'dibatalkan'])
                                    ? 'red'
                                    : 'gray');
                        @endphp

                        <span class="status {{ $kelasBooking }}">
                            {{ ucfirst($booking->status ?? 'menunggu') }}
                        </span>
                    </div>

                    <div class="booking-code">
                        <div>
                            <span class="small-label">KODE BOOKING</span>
                            <strong>{{ $booking->kode_booking }}</strong>
                        </div>

                        <span class="status {{ ($booking->status_konfirmasi ?? '') === 'dikonfirmasi' ? 'green' : 'gray' }}">
                            {{ ($booking->status_konfirmasi ?? '') === 'dikonfirmasi'
                                ? 'Terkonfirmasi'
                                : ucfirst(str_replace('_', ' ', $booking->status_konfirmasi ?? 'menunggu')) }}
                        </span>
                    </div>
                @empty
                    <p class="empty">
                        Belum ada booking.<br>
                        Pilih perangkat dan buat booking pertamamu.
                    </p>
                @endforelse
            </section>

            <section class="panel" id="riwayat">
                <div class="section-heading">
                    <h2>Riwayat transaksi</h2>
                    <span class="muted">{{ $riwayat->count() }} transaksi</span>
                </div>

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Kode / tanggal</th>
                                <th>Perangkat</th>
                                <th>Durasi</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($riwayat as $item)
                                <tr>
                                    <td>
                                        {{ $item->kode_transaksi ?? $item->kode_booking }}
                                        <span class="transaction-date">
                                            {{ \Carbon\Carbon::parse($item->created_at)->format('d M Y') }}
                                        </span>
                                    </td>
                                    <td>{{ $item->nama_device }}</td>
                                    <td>{{ $item->durasi }} jam</td>
                                    <td>Rp{{ number_format($item->total_harga, 0, ',', '.') }}</td>
                                    <td>
                                        @php
                                            $statusBayar = $item->status_pembayaran ?? 'menunggu';
                                            $kelasStatus = $statusBayar === 'berhasil' ? 'green' : 'gray';
                                        @endphp

                                        <span class="status {{ $kelasStatus }}">
                                            {{ ucfirst($statusBayar) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="empty">Belum ada transaksi.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <div>
            <section class="panel">
                <div class="section-heading">
                    <h2>Booking jadwal</h2>
                    <span class="muted">Buat booking</span>
                </div>

                <p class="muted" id="selectedDeviceLabel">
                    Pilih perangkat yang tersedia.
                </p>

                <form action="{{ route('customer.booking.store') }}" method="POST" id="bookingForm">
                    @csrf
                    <input type="hidden" name="id_device" id="deviceId" value="">

                    <div class="field-grid">
                        <div class="field">
                            <label for="tanggal">Tanggal main</label>
                            <input
                                type="date"
                                name="tanggal"
                                id="tanggal"
                                min="{{ now()->toDateString() }}"
                                value="{{ old('tanggal', now()->toDateString()) }}"
                                required>
                        </div>

                        <div class="field">
                            <label for="durasi">Durasi</label>
                            <select name="durasi" id="durasi" required>
                                @foreach([1,2,3,4,5] as $jam)
                                    <option value="{{ $jam }}" @selected(old('durasi', 2) == $jam)>
                                        {{ $jam }} jam
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="field full">
                            <label for="jam_mulai">Jam bermain</label>
                            <select name="jam_mulai" id="jam_mulai" required>
                                @foreach([8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23] as $jam)
                                    <option value="{{ $jam }}" @selected(old('jam_mulai', 18) == $jam)>
                                        {{ sprintf('%02d', $jam) }}.00 WIB
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="price-summary">
                        <div class="price-row">
                            <span>Subtotal</span>
                            <strong id="subtotal">Rp0</strong>
                        </div>

                        <div class="price-row" id="discountRow" hidden>
                            <span>Diskon membership (10%)</span>
                            <strong id="diskon" style="color:var(--green)">-Rp0</strong>
                        </div>

                        <div class="price-row price-total">
                            <span>Total estimasi</span>
                            <strong id="total">Rp0</strong>
                        </div>
                    </div>

                    <div class="payment-note">
                        <span class="qr">▦</span>
                        <div>
                            <strong>Pembayaran QRIS</strong>
                            <p>Transaksi dibuat dengan status menunggu. Pembayaran perlu diverifikasi oleh petugas.</p>
                        </div>
                    </div>

                    <button type="submit" class="primary">Buat booking →</button>
                </form>
            </section>

            <section class="panel" id="membership">
                <div class="section-heading">
                    <h2>Membership</h2>

                    @if($membershipAktif)
                        <span class="status green">Aktif</span>
                    @else
                        <span class="status gray">Non-member</span>
                    @endif
                </div>

                <div class="member-card">
                    <div>
                        <strong>GameHub Member</strong>

                        <p class="muted" style="margin-top:6px">
                            Diskon 10% untuk booking selama membership aktif.
                        </p>

                        @if($membershipAktif)
                            <p class="muted" style="margin-top:8px">
                                Berlaku sampai
                                {{ \Carbon\Carbon::parse($tanggalBerakhirMember)->format('d M Y') }}
                            </p>
                        @else
                            <p class="muted" style="margin-top:8px">
                                Aktifkan membership untuk mendapatkan diskon.
                            </p>
                        @endif
                    </div>

                    <span class="member-discount">10%</span>
                </div>

                @unless($membershipAktif)
                    <form action="{{ route('customer.membership.aktifkan') }}" method="POST">
                        @csrf
                        <button type="submit" class="primary">Aktifkan Membership</button>
                    </form>
                @endunless
            </section>

            <section class="panel" id="ulasan">
                <div class="section-heading">
                    <h2>Rating & ulasan</h2>
                    <span class="review-stars">★★★★★</span>
                </div>

                <p class="empty">
                    Belum ada ulasan yang tersimpan.<br>
                    Fitur ulasan dapat diaktifkan setelah tabel ulasan dibuat.
                </p>
            </section>
        </div>
    </div>
</main>

<script>
    const devices = {{ Illuminate\Support\Js::from(
        $devices->map(function ($device) {
            $nama = match ($device->tipe) {
                'PS4' => 'PlayStation 4',
                'PS5' => 'PlayStation 5',
                default => 'PC Gaming',
            };

            return [
                'id' => (int) $device->id,
                'name' => $nama,
                'price' => (int) $device->harga_per_jam,
                'available' => (int) $device->tersedia > 0,
            ];
        })->values()
    ) }};

    const memberAktif = @json((bool) $membershipAktif);

    const dateInput = document.getElementById('tanggal');
    const durationInput = document.getElementById('durasi');
    const timeInput = document.getElementById('jam_mulai');
    const deviceIdInput = document.getElementById('deviceId');
    const bookingForm = document.getElementById('bookingForm');

    let selectedDevice = null;

    function rupiah(value) {
        return 'Rp' + Number(value || 0).toLocaleString('id-ID');
    }

    function pilihPerangkat(button) {
        const id = Number(button.dataset.id);

        selectedDevice = devices.find(device =>
            device.id === id && device.available
        );

        if (!selectedDevice) {
            alert('Perangkat ini tidak tersedia.');
            return;
        }

        document.querySelectorAll('.device-card').forEach(card => {
            card.classList.remove('selected');

            const btn = card.querySelector('.select-device');

            if (btn && !btn.disabled) {
                btn.textContent = 'Pilih';
            }
        });

        const card = button.closest('.device-card');

        if (card) {
            card.classList.add('selected');
        }

        button.textContent = 'Dipilih';
        deviceIdInput.value = selectedDevice.id;

        document.getElementById('selectedDeviceLabel').textContent =
            selectedDevice.name + ' · ' +
            rupiah(selectedDevice.price) + '/jam';

        hitungHarga();
    }

    function hitungHarga() {
        const durasi = Number(durationInput.value) || 1;
        const harga = selectedDevice ? selectedDevice.price : 0;
        const subtotal = harga * durasi;

        const diskon = memberAktif ? Math.round(subtotal * 0.10) : 0;
        const total = subtotal - diskon;

        document.getElementById('priceDescription')?.textContent;

        document.getElementById('subtotal').textContent = rupiah(subtotal);
        document.getElementById('diskon').textContent = '-' + rupiah(diskon);
        document.getElementById('total').textContent = rupiah(total);

        document.getElementById('discountRow').hidden = !memberAktif;

        const labelDurasi = durasi + ' jam × ' + rupiah(harga);
        const subtotalLabel = document.querySelector('#subtotal');

        if (subtotalLabel) {
            subtotalLabel.title = labelDurasi;
        }
    }

    function validasiJadwal(tampilkanPesan = false) {
        const jamMulai = Number(timeInput.value);
        const durasi = Number(durationInput.value);
        const jamSelesai = jamMulai + durasi;

        if (jamSelesai > 24) {
            if (tampilkanPesan) {
                alert('Jam selesai tidak boleh melewati pukul 24.00.');
            }

            durationInput.value = '1';
            hitungHarga();
            return false;
        }

        return true;
    }

    durationInput.addEventListener('change', () => {
        validasiJadwal(true);
        hitungHarga();
    });

    timeInput.addEventListener('change', () => {
        validasiJadwal(true);
        hitungHarga();
    });

    dateInput.addEventListener('change', () => {
        hitungHarga();
    });

    bookingForm.addEventListener('submit', event => {
        if (!selectedDevice || !selectedDevice.available) {
            event.preventDefault();
            alert('Pilih perangkat yang tersedia terlebih dahulu.');
            return;
        }

        if (!dateInput.value) {
            event.preventDefault();
            alert('Pilih tanggal bermain terlebih dahulu.');
            dateInput.focus();
            return;
        }

        if (dateInput.value < dateInput.min) {
            event.preventDefault();
            alert('Tanggal bermain tidak boleh sebelum hari ini.');
            dateInput.focus();
            return;
        }

        if (!validasiJadwal(true)) {
            event.preventDefault();
        }
    });

    // Pilih perangkat pertama yang tersedia secara otomatis.
    const firstAvailableButton = Array.from(
        document.querySelectorAll('.select-device[data-id]')
    ).find(button => {
        const id = Number(button.dataset.id);
        return devices.some(device => device.id === id && device.available);
    });

    if (firstAvailableButton) {
        pilihPerangkat(firstAvailableButton);
    } else {
        hitungHarga();
    }
</script>
</body>
</html>
