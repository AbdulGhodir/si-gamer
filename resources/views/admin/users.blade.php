@extends('layouts.admin')

@section('content')
@php
    $staffList = collect($users ?? []);
@endphp

<header class="p-6 pb-4 border-b border-gray-800/50 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-white tracking-tight">Manajemen Akun Staff</h1>
        <p class="text-xs text-gray-400 mt-0.5">Kelola akun staff operasional rental; akun pelanggan mendaftar secara mandiri.</p>
    </div>
    <div>
        <button type="button" id="btnOpenTambahUser" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-xl text-sm font-medium transition shadow-lg shadow-purple-900/30 flex items-center gap-2 cursor-pointer">
            <span>➕</span> Tambah Staff
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
        <div class="p-4 border-b border-gray-800 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
            <h3 class="font-semibold text-white">Daftar Akun Staff</h3>
            <input type="text" id="searchUserInput" placeholder="Cari nama atau email staff..." class="bg-[#0b0a12] border border-gray-800 rounded-xl px-3 py-1.5 text-xs text-gray-300 placeholder-gray-500 focus:outline-none focus:border-purple-600 w-full sm:w-64">
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
                    @forelse($staffList as $u)
                    @php
                        $staffId = $u['id'] ?? null;
                        $nama = $u['nama'] ?? '-';
                        $email = $u['email'] ?? '-';
                        $status = $u['status'] ?? 'Aktif';
                        $isActive = $status === 'aktif';
                    @endphp
                    <tr class="user-row hover:bg-white/[0.02] transition"
                        data-id="{{ $staffId }}"
                        data-nama="{{ $nama }}"
                        data-email="{{ $email }}"
                        data-status="{{ $status }}">
                        <td class="p-4 font-medium text-white">{{ $nama }}</td>
                        <td class="p-4 text-gray-400 text-xs">{{ $email }}</td>
                        <td class="p-4">
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-blue-900/50 text-blue-300 border border-blue-700/50">
                                Staff
                            </span>
                        </td>
                        <td class="p-4">
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $isActive ? 'bg-emerald-900/50 text-emerald-300' : 'bg-rose-900/50 text-rose-300' }}">
                                {{ ucfirst($status) }}
                            </span>
                        </td>
                        <td class="p-4 text-center whitespace-nowrap space-x-2">
                            @if($staffId)
                                <button type="button" class="btn-edit-user text-xs text-purple-400 hover:text-purple-300 cursor-pointer">Edit</button>
                                <form action="{{ route('admin.users.destroy', $staffId) }}" method="POST" class="inline" onsubmit="return confirm('Hapus akun staff {{ $nama }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs text-red-400 hover:text-red-300 cursor-pointer">Hapus</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr id="emptyInitialRow">
                        <td colspan="5" class="p-6 text-center text-xs text-gray-500">
                            Belum ada akun staff di database.
                        </td>
                    </tr>
                    @endforelse
                    <tr id="emptySearchRow" class="hidden">
                        <td colspan="5" class="p-6 text-center text-xs text-gray-500">
                            Data staff tidak ditemukan.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah / Edit Staff -->
<div id="modalUser" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 backdrop-blur-xs p-4">
    <div class="bg-[#141221] border border-gray-800 rounded-2xl w-full max-w-md p-6 shadow-xl">
        <div class="flex justify-between items-center border-b border-gray-800 pb-3 mb-4">
            <h3 id="modalUserTitle" class="font-semibold text-white text-base">Tambah Akun Staff</h3>
            <button type="button" id="btnCloseModalUser" class="text-gray-400 hover:text-white text-lg leading-none cursor-pointer">&times;</button>
        </div>

        <form id="formUser" action="{{ route('admin.users.store') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="_method" id="formUserMethod" value="POST">

            <div>
                <label class="block text-xs text-gray-400 mb-1">Nama Lengkap</label>
                <input type="text" name="nama" id="inputNamaUser" required placeholder="Contoh: Dina Putri"
                    class="w-full bg-[#0b0a12] border border-gray-800 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:border-purple-600">
            </div>

            <div>
                <label class="block text-xs text-gray-400 mb-1">Email</label>
                <input type="email" name="email" id="inputEmailUser" required placeholder="dina@gmail.com"
                    class="w-full bg-[#0b0a12] border border-gray-800 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:border-purple-600">
            </div>

            <div>
                <label class="block text-xs text-gray-400 mb-1" id="labelPasswordUser">Password (Minimal 6 karakter)</label>
                <input type="password" name="password" id="inputPasswordUser" required minlength="6" placeholder="Minimal 6 karakter"
                    class="w-full bg-[#0b0a12] border border-gray-800 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:border-purple-600">
            </div>

            <div>
                <label class="block text-xs text-gray-400 mb-1">Role</label>
                <input type="text" id="inputRoleUser" value="Staff" readonly
                    class="w-full bg-[#0b0a12] border border-gray-800 rounded-xl px-3 py-2 text-sm text-gray-400 focus:outline-none">
            </div>

            <div id="statusUserField" class="hidden">
                <label class="block text-xs text-gray-400 mb-1">Status Akun</label>
                <select name="status" id="inputStatusUser"
                    class="w-full bg-[#0b0a12] border border-gray-800 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:border-purple-600">
                    <option value="aktif">Aktif</option>
                    <option value="nonaktif">Nonaktif</option>
                </select>
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
    const storeUrl = "{{ route('admin.users.store') }}";
    const updateUrlTemplate = "{{ route('admin.users.update', ['id' => '__ID__']) }}";

    const searchInput = document.getElementById('searchUserInput');
    const emptyRow = document.getElementById('emptySearchRow');
    const tbody = document.getElementById('usersTableBody');

    const modal = document.getElementById('modalUser');
    const modalTitle = document.getElementById('modalUserTitle');
    const btnOpenTambah = document.getElementById('btnOpenTambahUser');
    const btnClose = document.getElementById('btnCloseModalUser');
    const btnCancel = document.getElementById('btnBatalModalUser');
    const form = document.getElementById('formUser');
    const methodInput = document.getElementById('formUserMethod');

    const inputNama = document.getElementById('inputNamaUser');
    const inputEmail = document.getElementById('inputEmailUser');
    const inputPassword = document.getElementById('inputPasswordUser');
    const labelPassword = document.getElementById('labelPasswordUser');
    const statusField = document.getElementById('statusUserField');
    const inputStatus = document.getElementById('inputStatusUser');
    let originalStatus = null;

    function filterUsers() {
        if (!searchInput || !emptyRow) return;
        const keyword = searchInput.value.toLowerCase().trim();
        const rows = document.querySelectorAll('#usersTableBody .user-row');
        let visibleCount = 0;

        rows.forEach((row) => {
            const isMatch = row.textContent.toLowerCase().includes(keyword);
            row.style.display = isMatch ? '' : 'none';
            if (isMatch) visibleCount++;
        });

        emptyRow.classList.toggle('hidden', rows.length > 0 && visibleCount === 0);
    }

    searchInput?.addEventListener('input', filterUsers);

    function openTambahModal() {
        form.reset();
        form.action = storeUrl;
        methodInput.value = 'POST';
        inputPassword.required = true;
        inputStatus.value = 'aktif';
        originalStatus = null;
        statusField.classList.add('hidden');
        labelPassword.textContent = 'Password (Minimal 6 karakter)';
        modalTitle.textContent = 'Tambah Akun Staff';
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
        inputEmail.value = row.dataset.email || '';
        inputStatus.value = row.dataset.status || 'aktif';
        originalStatus = inputStatus.value;
        statusField.classList.remove('hidden');
        inputPassword.required = false;
        labelPassword.textContent = 'Password Baru (Kosongkan jika tidak diubah)';

        modalTitle.textContent = 'Edit Akun Staff';
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        inputNama.focus();
    }

    function closeModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        form?.reset();
    }

    btnOpenTambah?.addEventListener('click', openTambahModal);
    btnClose?.addEventListener('click', closeModal);
    btnCancel?.addEventListener('click', closeModal);
    form?.addEventListener('submit', function (event) {
        if (
            methodInput.value === 'PUT' &&
            originalStatus !== null &&
            inputStatus.value !== originalStatus &&
            !confirm(`Ubah status akun staff menjadi ${inputStatus.value}?`)
        ) {
            event.preventDefault();
        }
    });

    modal?.addEventListener('click', function (e) {
        if (e.target === modal) closeModal();
    });

    tbody?.addEventListener('click', function (e) {
        const btnEdit = e.target.closest('.btn-edit-user');
        if (!btnEdit) return;
        const row = btnEdit.closest('.user-row');
        if (row) openEditModal(row);
    });
});
</script>
@endsection