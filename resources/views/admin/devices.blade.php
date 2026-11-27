@extends('layouts.admin')

@section('content')
@php
    $deviceList = collect($devices ?? []);
@endphp

<header class="p-6 pb-4 border-b border-gray-800/50 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-white tracking-tight">Manajemen Perangkat</h1>
        <p class="text-xs text-gray-400 mt-0.5">Kelola unit konsol PlayStation, PC Gaming, dan status ketersediaan.</p>
    </div>
    <div>
        <button type="button" id="btnOpenTambahUnit" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-xl text-sm font-medium transition shadow-lg shadow-purple-900/30 flex items-center gap-2 cursor-pointer">
            <span>➕</span> Tambah Unit
        </button>
    </div>
</header>

<div class="p-6 space-y-6">
    @if(session('success'))
        <div class="bg-emerald-950/60 border border-emerald-700/60 text-emerald-300 px-4 py-3 rounded-xl text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-rose-950/60 border border-rose-700/60 text-rose-300 px-4 py-3 rounded-xl text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-[#141221] border border-gray-800/80 rounded-2xl overflow-hidden shadow-sm">
        <div class="p-4 border-b border-gray-800 flex justify-between items-center">
            <h3 class="font-semibold text-white">Daftar Unit Perangkat Rental</h3>
            <span class="text-xs text-gray-400">Total: {{ $deviceList->count() }} unit</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse" id="devicesTable">
                <thead>
                    <tr class="border-b border-gray-800 text-xs text-gray-400 bg-[#110f1c]">
                        <th class="p-4">Nama Unit</th>
                        <th class="p-4">Tipe</th>
                        <th class="p-4">Kategori</th>
                        <th class="p-4">Harga / Jam</th>
                        <th class="p-4">Status</th>
                        <th class="p-4">Spesifikasi</th>
                        <th class="p-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800/60 text-sm" id="devicesTableBody">
                    @forelse($deviceList as $d)
                    @php
                        $id = is_array($d) ? ($d['id'] ?? null) : ($d->id ?? null);
                        $namaDevice = is_array($d) ? ($d['nama_device'] ?? '-') : ($d->nama_device ?? '-');
                        $tipe = is_array($d) ? ($d['tipe'] ?? 'PS4') : ($d->tipe ?? 'PS4');
                        $kategori = is_array($d) ? ($d['kategori'] ?? '-') : ($d->kategori ?? '-');
                        $hargaPerJam = (int) (is_array($d) ? ($d['harga_per_jam'] ?? 0) : ($d->harga_per_jam ?? 0));
                        $status = is_array($d) ? ($d['status'] ?? 'tersedia') : ($d->status ?? 'tersedia');
                        $spesifikasi = is_array($d) ? ($d['spesifikasi'] ?? '-') : ($d->spesifikasi ?? '-');

                        $badgeClass = match($status) {
                            'tersedia'  => 'bg-emerald-900/50 text-emerald-300',
                            'bermain', 'dipesan' => 'bg-blue-900/50 text-blue-300',
                            'perbaikan' => 'bg-amber-900/50 text-amber-300',
                            default     => 'bg-gray-800 text-gray-400',
                        };
                    @endphp
                    <tr class="device-row hover:bg-white/[0.02] transition"
                        data-id="{{ $id }}"
                        data-nama="{{ $namaDevice }}"
                        data-tipe="{{ $tipe }}"
                        data-kategori="{{ $kategori === '-' ? '' : $kategori }}"
                        data-harga="{{ $hargaPerJam }}"
                        data-status="{{ $status }}"
                        data-spesifikasi="{{ $spesifikasi === '-' ? '' : $spesifikasi }}">
                        <td class="p-4 font-medium text-white">{{ $namaDevice }}</td>
                        <td class="p-4 text-purple-400 font-medium">{{ $tipe }}</td>
                        <td class="p-4 text-gray-300 text-xs">{{ $kategori ?: '-' }}</td>
                        <td class="p-4 text-white font-semibold">Rp{{ number_format($hargaPerJam, 0, ',', '.') }}</td>
                        <td class="p-4">
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $badgeClass }}">
                                {{ ucfirst($status) }}
                            </span>
                        </td>
                        <td class="p-4 text-gray-400 text-xs max-w-xs truncate" title="{{ $spesifikasi }}">{{ $spesifikasi ?: '-' }}</td>
                        <td class="p-4 text-center whitespace-nowrap space-x-2">
                            @if($id)
                                <button type="button" class="btn-edit-unit text-xs text-purple-400 hover:text-purple-300 cursor-pointer">Edit</button>
                                <form action="{{ route('admin.devices.destroy', $id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus unit {{ $namaDevice }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs text-red-400 hover:text-red-300 cursor-pointer">Hapus</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-6 text-center text-xs text-gray-500">
                            Belum ada data perangkat di database.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah / Edit Unit -->
<div id="modalDevice" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 backdrop-blur-xs p-4">
    <div class="bg-[#141221] border border-gray-800 rounded-2xl w-full max-w-md p-6 shadow-xl">
        <div class="flex justify-between items-center border-b border-gray-800 pb-3 mb-4">
            <h3 id="modalDeviceTitle" class="font-semibold text-white text-base">Tambah Unit Perangkat</h3>
            <button type="button" id="btnCloseModalDevice" class="text-gray-400 hover:text-white text-lg leading-none cursor-pointer">&times;</button>
        </div>

        <form id="formDevice" action="{{ route('admin.devices.store') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="_method" id="formDeviceMethod" value="POST">

            <div>
                <label class="block text-xs text-gray-400 mb-1">Nama Unit</label>
                <input type="text" name="nama_device" id="inputNamaDevice" required placeholder="Contoh: PlayStation 5 - E"
                    class="w-full bg-[#0b0a12] border border-gray-800 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:border-purple-600">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs text-gray-400 mb-1">Tipe Perangkat</label>
                    <select name="tipe" id="inputTipeDevice"
                        class="w-full bg-[#0b0a12] border border-gray-800 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:border-purple-600">
                        <option value="PS4">PS4</option>
                        <option value="PS5">PS5</option>
                        <option value="PC">PC</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs text-gray-400 mb-1">Kategori</label>
                    <select name="kategori" id="inputKategoriDevice" required
                        class="w-full bg-[#0b0a12] border border-gray-800 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:border-purple-600">
                        <option value="Reguler">Reguler</option>
                        <option value="VIP">VIP</option>
                        <option value="VVIP">VVIP</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs text-gray-400 mb-1">Harga / Jam (Rp)</label>
                    <input type="number" name="harga_per_jam" id="inputHargaDevice" required min="0" step="500" placeholder="25000"
                        class="w-full bg-[#0b0a12] border border-gray-800 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:border-purple-600">
                </div>

                <div>
                    <label class="block text-xs text-gray-400 mb-1">Status</label>
                    <select name="status" id="inputStatusDevice"
                        class="w-full bg-[#0b0a12] border border-gray-800 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:border-purple-600">
                        <option value="tersedia">Tersedia</option>
                        <option value="dipesan">Dipesan</option>
                        <option value="bermain">Bermain</option>
                        <option value="perbaikan">Perbaikan</option>
                        <option value="offline">Offline</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs text-gray-400 mb-1">Spesifikasi</label>
                <input type="text" name="spesifikasi" id="inputSpesifikasiDevice" placeholder="Contoh: DualSense, TV 4K 43 inch"
                    class="w-full bg-[#0b0a12] border border-gray-800 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:border-purple-600">
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t border-gray-800">
                <button type="button" id="btnBatalModalDevice" class="px-4 py-2 rounded-xl text-xs text-gray-300 bg-gray-800 hover:bg-gray-700 transition cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 rounded-xl text-xs font-medium text-white bg-purple-600 hover:bg-purple-700 transition cursor-pointer">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const storeUrl = "{{ route('admin.devices.store') }}";
    const updateUrlTemplate = "{{ route('admin.devices.update', ['id' => '__ID__']) }}";

    const modal = document.getElementById('modalDevice');
    const modalTitle = document.getElementById('modalDeviceTitle');
    const btnOpenTambah = document.getElementById('btnOpenTambahUnit');
    const btnClose = document.getElementById('btnCloseModalDevice');
    const btnCancel = document.getElementById('btnBatalModalDevice');
    const form = document.getElementById('formDevice');
    const methodInput = document.getElementById('formDeviceMethod');
    const tbody = document.getElementById('devicesTableBody');

    const inputNama = document.getElementById('inputNamaDevice');
    const inputTipe = document.getElementById('inputTipeDevice');
    const inputKategori = document.getElementById('inputKategoriDevice');
    const inputHarga = document.getElementById('inputHargaDevice');
    const inputStatus = document.getElementById('inputStatusDevice');
    const inputSpesifikasi = document.getElementById('inputSpesifikasiDevice');
    let originalStatus = null;

    function openTambahModal() {
        form.reset();
        form.action = storeUrl;
        methodInput.value = 'POST';
        inputTipe.value = 'PS4';
        inputStatus.value = 'tersedia';
        originalStatus = null;
        inputHarga.value = 15000;
        modalTitle.textContent = 'Tambah Unit Perangkat';
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        inputNama.focus();
    }

    function openEditModal(row) {
        const id = row.dataset.id;
        if (!id) return;

        form.reset();
        form.action = updateUrlTemplate.replace('__ID__', id);
        methodInput.value = 'PUT';

        inputNama.value = row.dataset.nama || '';
        inputTipe.value = row.dataset.tipe || 'PS4';
        inputKategori.value = row.dataset.kategori || '';
        inputHarga.value = row.dataset.harga || 0;
        inputStatus.value = row.dataset.status || 'tersedia';
        originalStatus = inputStatus.value;
        inputSpesifikasi.value = row.dataset.spesifikasi || '';

        modalTitle.textContent = 'Edit Unit Perangkat';
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        inputNama.focus();
    }

    function closeModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        form.reset();
    }

    btnOpenTambah.addEventListener('click', openTambahModal);
    btnClose.addEventListener('click', closeModal);
    btnCancel.addEventListener('click', closeModal);
    form.addEventListener('submit', function (event) {
        if (
            methodInput.value === 'PUT' &&
            originalStatus !== null &&
            inputStatus.value !== originalStatus &&
            !confirm(`Ubah status perangkat menjadi ${inputStatus.value}?`)
        ) {
            event.preventDefault();
        }
    });

    modal.addEventListener('click', function (e) {
        if (e.target === modal) closeModal();
    });

    tbody.addEventListener('click', function (e) {
        const btnEdit = e.target.closest('.btn-edit-unit');
        if (!btnEdit) return;
        const row = btnEdit.closest('.device-row');
        if (row) openEditModal(row);
    });
});
</script>
@endsection