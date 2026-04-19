@extends('layouts.master')
@section('title', request()->is('setting-user/create') ? 'Tambah Pengguna' : 'Edit Pengguna')
@section('content')

<div class="container-fluid">
    <!-- Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">{{ $isEdit ? 'Edit Pengguna' : 'Tambah Data Pengguna' }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Beranda</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('setting-user.index') }}">Kelola Pengguna</a></li>
                        <li class="breadcrumb-item active">{{ $isEdit ? 'Edit' : 'Tambah' }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="mdi mdi-account-plus me-2"></i>
                        {{ $isEdit ? 'Form untuk mengedit data pengguna dalam sistem.' 
                          : 'Form untuk menambahkan data pengguna ke dalam sistem.' }}
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ $isEdit ? route('setting-user.update', $user->id_user) : route('setting-user.store') }}" 
                          method="POST"
                          id="formUser">
                        @csrf
                        @if($isEdit)
                            @method('PUT')
                        @endif

                        <!-- ID User (Auto Generate - Readonly saat edit) -->
                        @if($isEdit)
                        <div class="mb-3">
                            <label for="id_user" class="form-label">ID User</label>
                            <input type="text" class="form-control bg-light" id="id_user" 
                                   value="{{ $user->id_user }}" disabled>
                            <small class="text-muted">ID User tidak dapat diubah</small>
                        </div>
                        @endif
                        <div class="row">  
                            <!-- Nama User -->
                            <div class="col-md-6 mb-3">
                                <label for="nama_user" class="form-label">
                                    Nama Lengkap <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                    class="form-control @error('nama_user') is-invalid @enderror" 
                                    id="nama_user" 
                                    name="nama_user" 
                                    value="{{ old('nama_user', $user->nama_user) }}" 
                                    placeholder="Contoh: Budi Santoso"
                                    required>
                                @error('nama_user')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Username -->
                            <div class="col-md-6 mb-3">
                                <label for="username" class="form-label">
                                    Username <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                    class="form-control @error('username') is-invalid @enderror" 
                                    id="username" 
                                    name="username" 
                                    value="{{ old('username', $user->username) }}" 
                                    placeholder="Contoh: budisantoso"
                                    required>
                                @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" 
                                    class="form-control @error('email') is-invalid @enderror" 
                                    id="email" 
                                    name="email" 
                                    value="{{ old('email', $user->email) }}" 
                                    placeholder="Contoh: budi.santoso@example.com (opsional)">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">
                                    Password 
                                    @if(!$isEdit)<span class="text-danger">*</span>@endif
                                </label>
                                <div class="input-group">
                                    <input type="password" 
                                        class="form-control @error('password') is-invalid @enderror" 
                                        id="password" 
                                        name="password" 
                                        placeholder="{{ $isEdit ? 'Kosongkan jika tidak ingin mengubah password' : 'Contoh: 1234 (minimal 4 karakter)' }}"
                                        @if(!$isEdit) required @endif>
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePw('password', 'icon-pw1')">
                                        <i class="fas fa-eye" id="icon-pw1"></i>
                                    </button>
                                    @error('password')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                @if($isEdit)
                                    <small class="text-muted">Kosongkan jika tidak ingin mengubah password</small>
                                @endif
                            </div>
                            {{-- <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">
                                    Password 
                                    @if(!$isEdit)
                                        <span class="text-danger">*</span>
                                    @endif
                                </label>
                                <input type="password" 
                                    class="form-control @error('password') is-invalid @enderror" 
                                    id="password" 
                                    name="password" 
                                    placeholder="{{ $isEdit ? 'Kosongkan jika tidak ingin mengubah password' : 'Contoh: 1234 (minimal 4 karakter)' }}"
                                    @if(!$isEdit) required @endif>
                                @if($isEdit)
                                    <small class="text-muted">Kosongkan jika tidak ingin mengubah password</small>
                                @endif
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div> --}}

                            <!-- Password Confirmation -->
                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label">
                                    Konfirmasi Password
                                    @if(!$isEdit)<span class="text-danger">*</span>@endif
                                </label>
                                <div class="input-group">
                                    <input type="password" 
                                        class="form-control" 
                                        id="password_confirmation" 
                                        name="password_confirmation" 
                                        placeholder="Contoh: 1234"
                                        @if(!$isEdit) required @endif>
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePw('password_confirmation', 'icon-pw2')">
                                        <i class="fas fa-eye" id="icon-pw2"></i>
                                    </button>
                                </div>
                            </div>
                            {{-- <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label">
                                    Konfirmasi Password
                                    @if(!$isEdit)
                                        <span class="text-danger">*</span>
                                    @endif
                                </label>
                                <input type="password" 
                                    class="form-control" 
                                    id="password_confirmation" 
                                    name="password_confirmation" 
                                    placeholder="Contoh: 1234"
                                    @if(!$isEdit) required @endif>
                            </div> --}}

                            <!-- Role -->
                            <div class="col-md-6 mb-3">
                                <label for="role" class="form-label">
                                    Role <span class="text-danger">*</span>
                                </label>
                                <select class="form-control @error('role') is-invalid @enderror" 
                                        id="role" 
                                        name="role" 
                                        required>
                                    <option value="">-- Pilih Role --</option>
                                    {{-- <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>
                                        Admin
                                    </option> --}}
                                    <option value="karyawanproduksi" {{ old('role', $user->role) == 'karyawanproduksi' ? 'selected' : '' }}>
                                        Karyawan Produksi
                                    </option>
                                    <option value="owner" {{ old('role', $user->role) == 'owner' ? 'selected' : '' }}>
                                        Owner
                                    </option>
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- No Telepon -->
                            <div class="col-md-6 mb-3">
                                <label for="no_telepon" class="form-label">
                                    No Telepon <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                    class="form-control @error('no_telepon') is-invalid @enderror" 
                                    id="no_telepon" 
                                    name="no_telepon" 
                                    value="{{ old('no_telepon', $user->no_telepon) }}" 
                                    placeholder="Contoh: 081234567890"
                                    maxlength="15"
                                    required>
                                @error('no_telepon')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            {{-- <div class="col-md-6 mb-3">
                                <label for="no_telepon" class="form-label">
                                    No Telepon <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                    class="form-control @error('no_telepon') is-invalid @enderror" 
                                    id="no_telepon" 
                                    name="no_telepon" 
                                    value="{{ old('no_telepon', $user->no_telepon) }}" 
                                    placeholder="Contoh: 081234567890"
                                    required>
                                @error('no_telepon')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div> --}}

                            <!-- Status -->
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">
                                    Status <span class="text-danger">*</span>
                                </label>
                                <select class="form-control @error('status') is-invalid @enderror" 
                                        id="status" 
                                        name="status" 
                                        required>
                                    <option value="">-- Pilih Status --</option>
                                    <option value="aktif" {{ old('status', $user->status) == 'aktif' ? 'selected' : '' }}>
                                        Aktif
                                    </option>
                                    <option value="non_aktif" {{ old('status', $user->status) == 'non_aktif' ? 'selected' : '' }}>
                                        Non Aktif
                                    </option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- <hr class="my-4"> --}}

                        <!-- Buttons -->
                        <div class="d-flex left-content-center gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> 
                                {{ $isEdit ? 'Update' : 'Simpan' }}
                            </button>
                            <a href="{{ route('setting-user.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
document.getElementById('formUser').addEventListener('submit', async function(e) {
    e.preventDefault();
    const form = this;

    try {
        const res = await fetch('{{ route("setting-user.check-duplicate") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                username: document.getElementById('username').value,
                email: document.getElementById('email').value,
                no_telepon: document.getElementById('no_telepon').value,
                exclude_id: '{{ $isEdit ? $user->id_user : "" }}'
            })
        });

        const data = await res.json();

        if (data.duplicates.length > 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Data Sudah Terdaftar!',
                html: 'Field berikut sudah digunakan:<br><br><b>' + data.duplicates.join(', ') + '</b><br><br>Silakan gunakan data yang berbeda.',
                confirmButtonText: 'Oke, Perbaiki'
            });
            return;
        }

        form.submit();

    } catch (err) {
        // Kalau AJAX gagal, langsung submit saja (fallback ke validasi Laravel)
        form.submit();
    }
});
// Toggle show/hide password
function togglePw(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}
</script>
@endpush
@endsection