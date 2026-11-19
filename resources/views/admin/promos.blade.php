@extends('layouts.admin')

@section('content')
<header class="p-6 pb-4 border-b border-gray-800/50 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-white tracking-tight">Manajemen Promo</h1>
        <p class="text-xs text-gray-400 mt-0.5">Kelola kupon diskon dan penawaran spesial untuk pelanggan.</p>
    </div>
    <div>
        <button type="button" id="btnOpenTambahPromo" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-xl text-sm font-medium transition shadow-lg shadow-purple-900/30 flex items-center gap-2 cursor-pointer">
            <span>➕</span> Tambah Promo
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
            <h3 class="font-semibold text-white">Daftar Promo Aktif</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse" id="promosTable">
                <thead>
                    <tr class="border-b border-gray-800 text-xs text-gray-400 bg-[#110f1c]">
                        <th class="p-4">Kode Promo</th>
                        <th class="p-4">Diskon</th>
                        <th class="p-4">Keterangan</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800/60 text-sm" id="promosTableBody">
                    @foreach($promos as $p)
                    <tr class="promo-row hover:bg-white/[0.02] transition">
                        <td class="p-4 font-medium text-white col-kode">{{ $p['kode'] }}</td>
                        <td class="p-4 text-purple-400 font-semibold col-diskon">{{ $p['diskon'] }}</td>
                        <td class="p-4 text-gray-400 text-xs col-ket">{{ $p['ket'] }}</td>
                        <td class="p-4">
                            <span class="col-status px-2.5 py-1 rounded-full text-xs font-medium {{ $p['status'] == 'Aktif' ? 'bg-emerald-900/50 text-emerald-300' : 'bg-gray-800 text-gray-400' }}">
                                {{ $p['status'] }}
                            </span>
                        </td>
                        <td class="p-4 text-center space-x-2">
                            <button type="button" class="btn-edit-promo text-xs text-purple-400 hover:text-purple-300 cursor-pointer">Edit</button>
                            <button type="button" class="btn-hapus-promo text-xs text-red-400 hover:text-red-300 cursor-pointer">Hapus</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah / Edit Promo -->
<div id="modalPromo" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 backdrop-blur-xs p-4">
    <div class="bg-[#141221] border border-gray-800 rounded-2xl w-full max-w-md p-6 shadow-xl">
        <div class="flex justify-between items-center border-b border-gray-800 pb-3 mb-4">
            <h3 id="modalPromoTitle" class="font-semibold text-white text-base">Tambah Promo Baru</h3>
            <button type="button" id="btnCloseModalPromo" class="text-gray-400 hover:text-white text-lg leading-none cursor-pointer">&times;</button>
        </div>

        <form id="formPromo" action="#" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs text-gray-400 mb-1">Kode Promo</label>
                    <input type="text" name="kode" id="inputKodePromo" required placeholder="Contoh: MABAR10"
                        class="w-full bg-[#0b0a12] border border-gray-800 rounded-xl px-3 py-2 text-sm text-white uppercase focus:outline-none focus:border-purple-600">
                </div>

                <div>
                    <label class="block text-xs text-gray-400 mb-1">Diskon</label>
                    <input type="text" name="diskon" id="inputDiskonPromo" required placeholder="Contoh: 10% atau Rp15.000"
                        class="w-full bg-[#0b0a12] border border-gray-800 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:border-purple-600">
                </div>
            </div>

            <div>
                <label class="block text-xs text-gray-400 mb-1">Keterangan</label>
                <input type="text" name="ket" id="inputKetPromo" required placeholder="Contoh: Diskon min. transaksi Rp50.000"
                    class="w-full bg-[#0b0a12] border border-gray-800 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:border-purple-600">
            </div>

            <div>
                <label class="block text-xs text-gray-400 mb-1">Status</label>
                <select name="status" id="inputStatusPromo"
                    class="w-full bg-[#0b0a12] border border-gray-800 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:border-purple-600">
                    <option value="Aktif">Aktif</option>
                    <option value="Nonaktif">Nonaktif</option>
                </select>
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t border-gray-800">
                <button type="button" id="btnBatalModalPromo" class="px-4 py-2 rounded-xl text-xs text-gray-300 bg-gray-800 hover:bg-gray-700 transition cursor-pointer">
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
    const modal = document.getElementById('modalPromo');
    const modalTitle = document.getElementById('modalPromoTitle');
    const btnOpenTambah = document.getElementById('btnOpenTambahPromo');
    const btnClose = document.getElementById('btnCloseModalPromo');
    const btnCancel = document.getElementById('btnBatalModalPromo');
    const form = document.getElementById('formPromo');
    const tbody = document.getElementById('promosTableBody');

    const inputKode = document.getElementById('inputKodePromo');
    const inputDiskon = document.getElementById('inputDiskonPromo');
    const inputKet = document.getElementById('inputKetPromo');
    const inputStatus = document.getElementById('inputStatusPromo');

    let editingRow = null;

    function getStatusBadgeClass(status) {
        return status === 'Aktif'
            ? 'col-status px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-900/50 text-emerald-300'
            : 'col-status px-2.5 py-1 rounded-full text-xs font-medium bg-gray-800 text-gray-400';
    }

    function openTambahModal() {
        editingRow = null;
        form.reset();
        inputStatus.value = 'Aktif';
        modalTitle.textContent = 'Tambah Promo Baru';
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        inputKode.focus();
    }

    function openEditModal(row) {
        editingRow = row;
        inputKode.value = row.querySelector('.col-kode').textContent.trim();
        inputDiskon.value = row.querySelector('.col-diskon').textContent.trim();
        inputKet.value = row.querySelector('.col-ket').textContent.trim();
        inputStatus.value = row.querySelector('.col-status').textContent.trim();
        modalTitle.textContent = 'Edit Promo';
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        inputKode.focus();
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
        const row = e.target.closest('.promo-row');
        if (!row) return;

        if (e.target.classList.contains('btn-edit-promo')) {
            openEditModal(row);
        } else if (e.target.classList.contains('btn-hapus-promo')) {
            const kode = row.querySelector('.col-kode').textContent.trim();
            if (confirm(`Hapus promo "${kode}"?`)) {
                row.remove();
            }
        }
    });

    form.addEventListener('submit', function (e) {
        if (form.getAttribute('action') === '#') {
            e.preventDefault();
            const kode = inputKode.value.trim().toUpperCase();
            const diskon = inputDiskon.value.trim();
            const ket = inputKet.value.trim();
            const status = inputStatus.value;

            if (editingRow) {
                editingRow.querySelector('.col-kode').textContent = kode;
                editingRow.querySelector('.col-diskon').textContent = diskon;
                editingRow.querySelector('.col-ket').textContent = ket;
                const badge = editingRow.querySelector('.col-status');
                badge.className = getStatusBadgeClass(status);
                badge.textContent = status;
            } else {
                const tr = document.createElement('tr');
                tr.className = 'promo-row hover:bg-white/[0.02] transition';
                tr.innerHTML = `
                    <td class="p-4 font-medium text-white col-kode"></td>
                    <td class="p-4 text-purple-400 font-semibold col-diskon"></td>
                    <td class="p-4 text-gray-400 text-xs col-ket"></td>
                    <td class="p-4"><span class="${getStatusBadgeClass(status)}"></span></td>
                    <td class="p-4 text-center space-x-2">
                        <button type="button" class="btn-edit-promo text-xs text-purple-400 hover:text-purple-300 cursor-pointer">Edit</button>
                        <button type="button" class="btn-hapus-promo text-xs text-red-400 hover:text-red-300 cursor-pointer">Hapus</button>
                    </td>
                `;
                tr.querySelector('.col-kode').textContent = kode;
                tr.querySelector('.col-diskon').textContent = diskon;
                tr.querySelector('.col-ket').textContent = ket;
                tr.querySelector('.col-status').textContent = status;
                tbody.appendChild(tr);
            }

            closeModal();
        }
    });
});
</script>
@endsection