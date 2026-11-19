@extends('layouts.admin')

@section('content')
<header class="p-6 pb-4 border-b border-gray-800/50 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-white tracking-tight">Manajemen Perangkat</h1>
        <p class="text-xs text-gray-400 mt-0.5">Kelola status konsol PlayStation, PC Gaming, dan pemeliharaan unit.</p>
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

    <div class="bg-[#141221] border border-gray-800/80 rounded-2xl overflow-hidden shadow-sm">
        <div class="p-4 border-b border-gray-800 flex justify-between items-center">
            <h3 class="font-semibold text-white">Daftar Unit Perangkat Rental</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse" id="devicesTable">
                <thead>
                    <tr class="border-b border-gray-800 text-xs text-gray-400 bg-[#110f1c]">
                        <th class="p-4">Kode Unit</th>
                        <th class="p-4">Tipe Perangkat</th>
                        <th class="p-4">Status</th>
                        <th class="p-4">Keterangan</th>
                        <th class="p-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800/60 text-sm" id="devicesTableBody">
                    @foreach($devices as $d)
                    <tr class="device-row hover:bg-white/[0.02] transition">
                        <td class="p-4 font-medium text-white col-kode">{{ $d['kode'] }}</td>
                        <td class="p-4 text-gray-300 col-tipe">{{ $d['tipe'] }}</td>
                        <td class="p-4">
                            <span class="col-status px-2.5 py-1 rounded-full text-xs font-medium {{ $d['status'] == 'Tersedia' ? 'bg-emerald-900/50 text-emerald-300' : ($d['status'] == 'Digunakan' ? 'bg-blue-900/50 text-blue-300' : 'bg-amber-900/50 text-amber-300') }}">
                                {{ $d['status'] }}
                            </span>
                        </td>
                        <td class="p-4 text-gray-400 text-xs col-keterangan">{{ $d['keterangan'] }}</td>
                        <td class="p-4 text-center space-x-2">
                            <button type="button" class="btn-edit-unit text-xs text-purple-400 hover:text-purple-300 cursor-pointer">Edit</button>
                            <button type="button" class="btn-hapus-unit text-xs text-red-400 hover:text-red-300 cursor-pointer">Hapus</button>
                        </td>
                    </tr>
                    @endforeach
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

        <form id="formDevice" action="#" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs text-gray-400 mb-1">Kode Unit</label>
                    <input type="text" name="kode" id="inputKodeUnit" required placeholder="Contoh: PS5-02"
                        class="w-full bg-[#0b0a12] border border-gray-800 rounded-xl px-3 py-2 text-sm text-white uppercase focus:outline-none focus:border-purple-600">
                </div>

                <div>
                    <label class="block text-xs text-gray-400 mb-1">Tipe Perangkat</label>
                    <select name="tipe" id="inputTipeUnit"
                        class="w-full bg-[#0b0a12] border border-gray-800 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:border-purple-600">
                        <option value="PlayStation 5">PlayStation 5</option>
                        <option value="PlayStation 4">PlayStation 4</option>
                        <option value="PC Gaming">PC Gaming</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs text-gray-400 mb-1">Status</label>
                <select name="status" id="inputStatusUnit"
                    class="w-full bg-[#0b0a12] border border-gray-800 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:border-purple-600">
                    <option value="Tersedia">Tersedia</option>
                    <option value="Digunakan">Digunakan</option>
                    <option value="Maintenance">Maintenance</option>
                </select>
            </div>

            <div>
                <label class="block text-xs text-gray-400 mb-1">Keterangan</label>
                <input type="text" name="keterangan" id="inputKeteranganUnit" required placeholder="Contoh: Normal atau Sesi aktif (Budi)"
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
    const modal = document.getElementById('modalDevice');
    const modalTitle = document.getElementById('modalDeviceTitle');
    const btnOpenTambah = document.getElementById('btnOpenTambahUnit');
    const btnClose = document.getElementById('btnCloseModalDevice');
    const btnCancel = document.getElementById('btnBatalModalDevice');
    const form = document.getElementById('formDevice');
    const tbody = document.getElementById('devicesTableBody');

    const inputKode = document.getElementById('inputKodeUnit');
    const inputTipe = document.getElementById('inputTipeUnit');
    const inputStatus = document.getElementById('inputStatusUnit');
    const inputKeterangan = document.getElementById('inputKeteranganUnit');

    let editingRow = null;

    function getStatusBadgeClass(status) {
        if (status === 'Tersedia') {
            return 'col-status px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-900/50 text-emerald-300';
        }
        if (status === 'Digunakan') {
            return 'col-status px-2.5 py-1 rounded-full text-xs font-medium bg-blue-900/50 text-blue-300';
        }
        return 'col-status px-2.5 py-1 rounded-full text-xs font-medium bg-amber-900/50 text-amber-300';
    }

    function openTambahModal() {
        editingRow = null;
        form.reset();
        inputStatus.value = 'Tersedia';
        inputKeterangan.value = 'Normal';
        modalTitle.textContent = 'Tambah Unit Perangkat';
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        inputKode.focus();
    }

    function openEditModal(row) {
        editingRow = row;
        inputKode.value = row.querySelector('.col-kode').textContent.trim();
        inputTipe.value = row.querySelector('.col-tipe').textContent.trim();
        inputStatus.value = row.querySelector('.col-status').textContent.trim();
        inputKeterangan.value = row.querySelector('.col-keterangan').textContent.trim();
        modalTitle.textContent = 'Edit Unit Perangkat';
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
        const row = e.target.closest('.device-row');
        if (!row) return;

        if (e.target.classList.contains('btn-edit-unit')) {
            openEditModal(row);
        } else if (e.target.classList.contains('btn-hapus-unit')) {
            const kode = row.querySelector('.col-kode').textContent.trim();
            if (confirm(`Hapus unit "${kode}"?`)) {
                row.remove();
            }
        }
    });

    form.addEventListener('submit', function (e) {
        if (form.getAttribute('action') === '#') {
            e.preventDefault();
            const kode = inputKode.value.trim().toUpperCase();
            const tipe = inputTipe.value;
            const status = inputStatus.value;
            const keterangan = inputKeterangan.value.trim();

            if (editingRow) {
                editingRow.querySelector('.col-kode').textContent = kode;
                editingRow.querySelector('.col-tipe').textContent = tipe;
                const badge = editingRow.querySelector('.col-status');
                badge.className = getStatusBadgeClass(status);
                badge.textContent = status;
                editingRow.querySelector('.col-keterangan').textContent = keterangan;
            } else {
                const tr = document.createElement('tr');
                tr.className = 'device-row hover:bg-white/[0.02] transition';
                tr.innerHTML = `
                    <td class="p-4 font-medium text-white col-kode"></td>
                    <td class="p-4 text-gray-300 col-tipe"></td>
                    <td class="p-4"><span class="${getStatusBadgeClass(status)}"></span></td>
                    <td class="p-4 text-gray-400 text-xs col-keterangan"></td>
                    <td class="p-4 text-center space-x-2">
                        <button type="button" class="btn-edit-unit text-xs text-purple-400 hover:text-purple-300 cursor-pointer">Edit</button>
                        <button type="button" class="btn-hapus-unit text-xs text-red-400 hover:text-red-300 cursor-pointer">Hapus</button>
                    </td>
                `;
                tr.querySelector('.col-kode').textContent = kode;
                tr.querySelector('.col-tipe').textContent = tipe;
                tr.querySelector('.col-status').textContent = status;
                tr.querySelector('.col-keterangan').textContent = keterangan;
                tbody.appendChild(tr);
            }

            closeModal();
        }
    });
});
</script>
@endsection