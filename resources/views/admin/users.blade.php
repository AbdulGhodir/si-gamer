@extends('layouts.admin')

@section('content')
<header class="p-6 pb-4 border-b border-gray-800/50 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-white tracking-tight">Manajemen Akun Pengguna</h1>
        <p class="text-xs text-gray-400 mt-0.5">Kelola data pelanggan, staff, dan verifikasi akun dalam sistem.</p>
    </div>
    <div>
        <button type="button" id="btnOpenTambahUser" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-xl text-sm font-medium transition shadow-lg shadow-purple-900/30 flex items-center gap-2 cursor-pointer">
            <span>➕</span> Tambah Pengguna
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
        <div class="p-4 border-b border-gray-800 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
            <h3 class="font-semibold text-white">Daftar Pengguna Terdaftar</h3>
            <input type="text" id="searchUserInput" placeholder="Cari pengguna..." class="bg-[#0b0a12] border border-gray-800 rounded-xl px-3 py-1.5 text-xs text-gray-300 placeholder-gray-500 focus:outline-none focus:border-purple-600 w-full sm:w-64">
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse" id="usersTable">
                <thead>
                    <tr class="border-b border-gray-800 text-xs text-gray-400 bg-[#110f1c]">
                        <th class="p-4">Nama</th>
                        <th class="p-4">Email</th>
                        <th class="p-4">Role</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800/60 text-sm" id="usersTableBody">
                    @foreach($users as $u)
                    <tr class="user-row hover:bg-white/[0.02] transition">
                        <td class="p-4 font-medium text-white col-nama">{{ $u['nama'] }}</td>
                        <td class="p-4 text-gray-400 text-xs col-email">{{ $u['email'] }}</td>
                        <td class="p-4">
                            <span class="col-role px-2.5 py-1 rounded-full text-xs font-medium {{ $u['role'] == 'Admin' ? 'bg-purple-900/50 text-purple-300 border border-purple-700/50' : ($u['role'] == 'Staff' ? 'bg-blue-900/50 text-blue-300 border border-blue-700/50' : 'bg-gray-800 text-gray-300') }}">
                                {{ $u['role'] }}
                            </span>
                        </td>
                        <td class="p-4">
                            <span class="col-status px-2.5 py-1 rounded-full text-xs font-medium {{ $u['status'] == 'Aktif' ? 'bg-emerald-900/50 text-emerald-300' : 'bg-amber-900/50 text-amber-300' }}">
                                {{ $u['status'] }}
                            </span>
                        </td>
                        <td class="p-4 text-center space-x-2">
                            <button type="button" class="btn-edit-user text-xs text-purple-400 hover:text-purple-300 cursor-pointer">Edit</button>
                            <button type="button" class="btn-hapus-user text-xs text-red-400 hover:text-red-300 cursor-pointer">Hapus</button>
                        </td>
                    </tr>
                    @endforeach
                    <tr id="emptySearchRow" class="hidden">
                        <td colspan="5" class="p-6 text-center text-xs text-gray-500">
                            Pengguna tidak ditemukan.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah / Edit Pengguna -->
<div id="modalUser" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 backdrop-blur-xs p-4">
    <div class="bg-[#141221] border border-gray-800 rounded-2xl w-full max-w-md p-6 shadow-xl">
        <div class="flex justify-between items-center border-b border-gray-800 pb-3 mb-4">
            <h3 id="modalUserTitle" class="font-semibold text-white text-base">Tambah Pengguna Baru</h3>
            <button type="button" id="btnCloseModalUser" class="text-gray-400 hover:text-white text-lg leading-none cursor-pointer">&times;</button>
        </div>

        <form id="formUser" action="#" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs text-gray-400 mb-1">Nama Lengkap</label>
                <input type="text" name="nama" id="inputNamaUser" required placeholder="Contoh: Budi Santoso"
                    class="w-full bg-[#0b0a12] border border-gray-800 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:border-purple-600">
            </div>

            <div>
                <label class="block text-xs text-gray-400 mb-1">Email</label>
                <input type="email" name="email" id="inputEmailUser" required placeholder="budi@gmail.com"
                    class="w-full bg-[#0b0a12] border border-gray-800 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:border-purple-600">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs text-gray-400 mb-1">Role</label>
                    <select name="role" id="inputRoleUser"
                        class="w-full bg-[#0b0a12] border border-gray-800 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:border-purple-600">
                        <option value="Customer">Customer</option>
                        <option value="Staff">Staff</option>
                        <option value="Admin">Admin</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs text-gray-400 mb-1">Status</label>
                    <select name="status" id="inputStatusUser"
                        class="w-full bg-[#0b0a12] border border-gray-800 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:border-purple-600">
                        <option value="Aktif">Aktif</option>
                        <option value="Pending">Pending</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t border-gray-800">
                <button type="button" id="btnBatalModalUser" class="px-4 py-2 rounded-xl text-xs text-gray-300 bg-gray-800 hover:bg-gray-700 transition cursor-pointer">
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
    const searchInput = document.getElementById('searchUserInput');
    const emptyRow = document.getElementById('emptySearchRow');
    const tbody = document.getElementById('usersTableBody');

    const modal = document.getElementById('modalUser');
    const modalTitle = document.getElementById('modalUserTitle');
    const btnOpenTambah = document.getElementById('btnOpenTambahUser');
    const btnClose = document.getElementById('btnCloseModalUser');
    const btnCancel = document.getElementById('btnBatalModalUser');
    const form = document.getElementById('formUser');

    const inputNama = document.getElementById('inputNamaUser');
    const inputEmail = document.getElementById('inputEmailUser');
    const inputRole = document.getElementById('inputRoleUser');
    const inputStatus = document.getElementById('inputStatusUser');

    let editingRow = null;

    function getRoleBadgeClass(role) {
        if (role === 'Admin') {
            return 'col-role px-2.5 py-1 rounded-full text-xs font-medium bg-purple-900/50 text-purple-300 border border-purple-700/50';
        }
        if (role === 'Staff') {
            return 'col-role px-2.5 py-1 rounded-full text-xs font-medium bg-blue-900/50 text-blue-300 border border-blue-700/50';
        }
        return 'col-role px-2.5 py-1 rounded-full text-xs font-medium bg-gray-800 text-gray-300';
    }

    function getStatusBadgeClass(status) {
        return status === 'Aktif'
            ? 'col-status px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-900/50 text-emerald-300'
            : 'col-status px-2.5 py-1 rounded-full text-xs font-medium bg-amber-900/50 text-amber-300';
    }

    function filterUsers() {
        const keyword = searchInput.value.toLowerCase().trim();
        const rows = document.querySelectorAll('#usersTableBody .user-row');
        let visibleCount = 0;

        rows.forEach((row) => {
            const isMatch = row.textContent.toLowerCase().includes(keyword);
            row.style.display = isMatch ? '' : 'none';
            if (isMatch) visibleCount++;
        });

        emptyRow.classList.toggle('hidden', visibleCount > 0);
    }

    searchInput.addEventListener('input', filterUsers);

    function openTambahModal() {
        editingRow = null;
        form.reset();
        inputRole.value = 'Customer';
        inputStatus.value = 'Aktif';
        modalTitle.textContent = 'Tambah Pengguna Baru';
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        inputNama.focus();
    }

    function openEditModal(row) {
        editingRow = row;
        inputNama.value = row.querySelector('.col-nama').textContent.trim();
        inputEmail.value = row.querySelector('.col-email').textContent.trim();
        inputRole.value = row.querySelector('.col-role').textContent.trim();
        inputStatus.value = row.querySelector('.col-status').textContent.trim();
        modalTitle.textContent = 'Edit Pengguna';
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
        const row = e.target.closest('.user-row');
        if (!row) return;

        if (e.target.classList.contains('btn-edit-user')) {
            openEditModal(row);
        } else if (e.target.classList.contains('btn-hapus-user')) {
            const nama = row.querySelector('.col-nama').textContent.trim();
            if (confirm(`Hapus pengguna "${nama}"?`)) {
                row.remove();
                filterUsers();
            }
        }
    });

    form.addEventListener('submit', function (e) {
        if (form.getAttribute('action') === '#') {
            e.preventDefault();
            const nama = inputNama.value.trim();
            const email = inputEmail.value.trim();
            const role = inputRole.value;
            const status = inputStatus.value;

            if (editingRow) {
                editingRow.querySelector('.col-nama').textContent = nama;
                editingRow.querySelector('.col-email').textContent = email;

                const roleBadge = editingRow.querySelector('.col-role');
                roleBadge.className = getRoleBadgeClass(role);
                roleBadge.textContent = role;

                const statusBadge = editingRow.querySelector('.col-status');
                statusBadge.className = getStatusBadgeClass(status);
                statusBadge.textContent = status;
            } else {
                const tr = document.createElement('tr');
                tr.className = 'user-row hover:bg-white/[0.02] transition';
                tr.innerHTML = `
                    <td class="p-4 font-medium text-white col-nama"></td>
                    <td class="p-4 text-gray-400 text-xs col-email"></td>
                    <td class="p-4"><span class="${getRoleBadgeClass(role)}"></span></td>
                    <td class="p-4"><span class="${getStatusBadgeClass(status)}"></span></td>
                    <td class="p-4 text-center space-x-2">
                        <button type="button" class="btn-edit-user text-xs text-purple-400 hover:text-purple-300 cursor-pointer">Edit</button>
                        <button type="button" class="btn-hapus-user text-xs text-red-400 hover:text-red-300 cursor-pointer">Hapus</button>
                    </td>
                `;
                tr.querySelector('.col-nama').textContent = nama;
                tr.querySelector('.col-email').textContent = email;
                tr.querySelector('.col-role').textContent = role;
                tr.querySelector('.col-status').textContent = status;

                tbody.insertBefore(tr, emptyRow);
            }

            closeModal();
            filterUsers();
        }
    });
});
</script>
@endsection