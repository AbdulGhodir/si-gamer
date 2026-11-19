@extends('layouts.admin')

@section('content')
<header class="p-6 pb-4 border-b border-gray-800/50 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-white tracking-tight">Tarif & Paket Rental</h1>
        <p class="text-xs text-gray-400 mt-0.5">Atur harga sewa per jam, paket mabar, dan diskon promo aktif.</p>
    </div>
    <div>
        <button type="button" id="btnOpenTambahTarif" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-xl text-sm font-medium transition shadow-lg shadow-purple-900/30 flex items-center gap-2 cursor-pointer">
            <span>➕</span> Tambah Tarif
        </button>
    </div>
</header>

<div class="p-6 space-y-6">
    @if(session('success'))
        <div class="bg-emerald-950/60 border border-emerald-700/60 text-emerald-300 px-4 py-3 rounded-xl text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-[#141221] border border-gray-800/80 rounded-2xl overflow-hidden shadow-sm">
        <div class="p-4 border-b border-gray-800 flex justify-between items-center">
            <h3 class="font-semibold text-white">Daftar Tarif & Paket</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse" id="tariffsTable">
                <thead>
                    <tr class="border-b border-gray-800 text-xs text-gray-400 bg-[#110f1c]">
                        <th class="p-4">Nama Layanan / Paket</th>
                        <th class="p-4">Kategori</th>
                        <th class="p-4">Tarif / Harga</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800/60 text-sm" id="tariffsTableBody">
                    @foreach($tariffs as $t)
                    <tr class="tariff-row hover:bg-white/[0.02] transition">
                        <td class="p-4 font-medium text-white col-nama">{{ $t['nama'] }}</td>
                        <td class="p-4 text-gray-300 col-kategori">{{ $t['kategori'] }}</td>
                        <td class="p-4 text-purple-400 font-semibold col-harga">{{ $t['harga'] }}</td>
                        <td class="p-4">
                            <span class="col-status px-2.5 py-1 rounded-full text-xs font-medium {{ ($t['status'] ?? 'Aktif') == 'Aktif' ? 'bg-emerald-900/50 text-emerald-300' : 'bg-amber-900/50 text-amber-300' }}">
                                {{ $t['status'] ?? 'Aktif' }}
                            </span>
                        </td>
                        <td class="p-4 text-center space-x-2">
                            <button type="button" class="btn-edit-tarif text-xs text-purple-400 hover:text-purple-300 cursor-pointer">Edit</button>
                            <button type="button" class="btn-hapus-tarif text-xs text-red-400 hover:text-red-300 cursor-pointer">Hapus</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah / Edit Tarif -->
<div id="modalTarif" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 backdrop-blur-xs p-4">
    <div class="bg-[#141221] border border-gray-800 rounded-2xl w-full max-w-md p-6 shadow-xl">
        <div class="flex justify-between items-center border-b border-gray-800 pb-3 mb-4">
            <h3 id="modalTarifTitle" class="font-semibold text-white text-base">Tambah Tarif / Paket</h3>
            <button type="button" id="btnCloseModalTarif" class="text-gray-400 hover:text-white text-lg leading-none cursor-pointer">&times;</button>
        </div>

        <form id="formTarif" action="#" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs text-gray-400 mb-1">Nama Layanan / Paket</label>
                <input type="text" name="nama" id="inputNamaTarif" required placeholder="Contoh: Reguler PlayStation 5"
                    class="w-full bg-[#0b0a12] border border-gray-800 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:border-purple-600">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs text-gray-400 mb-1">Kategori</label>
                    <select name="kategori" id="inputKategoriTarif"
                        class="w-full bg-[#0b0a12] border border-gray-800 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:border-purple-600">
                        <option value="Per Jam">Per Jam</option>
                        <option value="Paket">Paket</option>
                        <option value="Promo">Promo</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs text-gray-400 mb-1">Tarif / Harga</label>
                    <input type="text" name="harga" id="inputHargaTarif" required placeholder="Contoh: Rp25.000 atau 10%"
                        class="w-full bg-[#0b0a12] border border-gray-800 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:border-purple-600">
                </div>
            </div>

            <div>
                <label class="block text-xs text-gray-400 mb-1">Status</label>
                <select name="status" id="inputStatusTarif"
                    class="w-full bg-[#0b0a12] border border-gray-800 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:border-purple-600">
                    <option value="Aktif">Aktif</option>
                    <option value="Nonaktif">Nonaktif</option>
                </select>
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t border-gray-800">
                <button type="button" id="btnBatalModalTarif" class="px-4 py-2 rounded-xl text-xs text-gray-300 bg-gray-800 hover:bg-gray-700 transition cursor-pointer">
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
    const modal = document.getElementById('modalTarif');
    const modalTitle = document.getElementById('modalTarifTitle');
    const btnOpenTambah = document.getElementById('btnOpenTambahTarif');
    const btnClose = document.getElementById('btnCloseModalTarif');
    const btnCancel = document.getElementById('btnBatalModalTarif');
    const form = document.getElementById('formTarif');
    const tbody = document.getElementById('tariffsTableBody');

    const inputNama = document.getElementById('inputNamaTarif');
    const inputKategori = document.getElementById('inputKategoriTarif');
    const inputHarga = document.getElementById('inputHargaTarif');
    const inputStatus = document.getElementById('inputStatusTarif');

    let editingRow = null;

    function getStatusBadgeClass(status) {
        return status === 'Aktif'
            ? 'col-status px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-900/50 text-emerald-300'
            : 'col-status px-2.5 py-1 rounded-full text-xs font-medium bg-amber-900/50 text-amber-300';
    }

    function openTambahModal() {
        editingRow = null;
        form.reset();
        inputStatus.value = 'Aktif';
        modalTitle.textContent = 'Tambah Tarif / Paket';
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        inputNama.focus();
    }

    function openEditModal(row) {
        editingRow = row;
        inputNama.value = row.querySelector('.col-nama').textContent.trim();
        inputKategori.value = row.querySelector('.col-kategori').textContent.trim();
        inputHarga.value = row.querySelector('.col-harga').textContent.trim();
        inputStatus.value = row.querySelector('.col-status').textContent.trim();
        modalTitle.textContent = 'Edit Tarif / Paket';
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        inputNama.focus();
    }

    function closeModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        form.reset();
        editingRow = null;
    }

    btnOpenTambah.addEventListener('click', openTambahModal);
    btnClose.addEventListener('click', closeModal);
    btnCancel.addEventListener('click', closeModal);

    modal.addEventListener('click', function (e) {
        if (e.target === modal) closeModal();
    });

    tbody.addEventListener('click', function (e) {
        const row = e.target.closest('.tariff-row');
        if (!row) return;

        if (e.target.classList.contains('btn-edit-tarif')) {
            openEditModal(row);
        } else if (e.target.classList.contains('btn-hapus-tarif')) {
            const nama = row.querySelector('.col-nama').textContent.trim();
            if (confirm(`Hapus tarif "${nama}"?`)) {
                row.remove();
            }
        }
    });

    form.addEventListener('submit', function (e) {
        if (form.getAttribute('action') === '#') {
            e.preventDefault();
            const nama = inputNama.value.trim();
            const kategori = inputKategori.value;
            const harga = inputHarga.value.trim();
            const status = inputStatus.value;

            if (editingRow) {
                editingRow.querySelector('.col-nama').textContent = nama;
                editingRow.querySelector('.col-kategori').textContent = kategori;
                editingRow.querySelector('.col-harga').textContent = harga;
                const badge = editingRow.querySelector('.col-status');
                badge.className = getStatusBadgeClass(status);
                badge.textContent = status;
            } else {
                const tr = document.createElement('tr');
                tr.className = 'tariff-row hover:bg-white/[0.02] transition';
                tr.innerHTML = `
                    <td class="p-4 font-medium text-white col-nama"></td>
                    <td class="p-4 text-gray-300 col-kategori"></td>
                    <td class="p-4 text-purple-400 font-semibold col-harga"></td>
                    <td class="p-4"><span class="${getStatusBadgeClass(status)}"></span></td>
                    <td class="p-4 text-center space-x-2">
                        <button type="button" class="btn-edit-tarif text-xs text-purple-400 hover:text-purple-300 cursor-pointer">Edit</button>
                        <button type="button" class="btn-hapus-tarif text-xs text-red-400 hover:text-red-300 cursor-pointer">Hapus</button>
                    </td>
                `;
                tr.querySelector('.col-nama').textContent = nama;
                tr.querySelector('.col-kategori').textContent = kategori;
                tr.querySelector('.col-harga').textContent = harga;
                tr.querySelector('.col-status').textContent = status;
                tbody.appendChild(tr);
            }

            closeModal();
        }
    });
});
</script>
@endsection