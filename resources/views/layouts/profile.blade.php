@extends('layouts.master')
@section('title', 'Profile')

@section('content')
<style>
/* =============== STYLE PROFIL 4 KARTU =============== */

/* Layout global */
.profile-container {
  padding: 0 10px;
}
.profile-container .page-title-box {
  margin-bottom: .5rem;
}

/* Kurangi gap antar kolom
.profile-container .row.g-2 {
  --bs-gutter-x: 0.75rem;
  --bs-gutter-y: 0.75rem;
} */

/* Card umum */
.profile-container .card {
  border-radius: .6rem;
  box-shadow: 0 0 4px rgba(0,0,0,.05);
  margin-bottom: 0;
  height: 100%;
}
.profile-container .card-header {
  padding: .5rem .75rem;
  background-color: #f8f9fa;
  border-bottom: 1px solid #e3e6eb;
}
.profile-container .card-header h5 {
  margin-bottom: 0;
  font-size: 1rem;
  font-weight: 600;
}
.profile-container .card-body {
  padding: .75rem;
}

/* === Daftar Identitas User === */
.profile-info dl.dl-kv {
  display: grid;
  grid-template-columns: 120px 1fr;
  grid-row-gap: 4px;
  align-items: center;
  margin: 0;
}
.profile-info dl.dl-kv dt,
.profile-info dl.dl-kv dd {
  margin: 0;
  padding: .12rem 0;
  font-size: .9rem;
}
.profile-info dl.dl-kv dt {
  font-weight: 600;
  color: #212529;
  position: relative;
  padding-right: 0px;
}
.profile-info dl.dl-kv dt::after {
  content: ":";
  position: absolute;
  right: 0px;
  color: #6c757d;
}
.profile-info dl.dl-kv dd {
  color: #6c757d;
}

/* Badge status */
.profile-info dl.dl-kv dd .badge {
  font-size: .75rem;
  padding: .2rem .45rem;
}

/* Form edit dan password */
.profile-container .form-label {
  font-size: .875rem;
  margin-bottom: .25rem;
  font-weight: 500;
}
.profile-container .form-control {
  height: 34px;
  font-size: .875rem;
  padding: .3rem .45rem;
}
.profile-container .mb-3 {
  margin-bottom: .5rem !important;
}

/* Alert success */
.profile-container .alert {
  padding: .5rem .75rem;
  margin-bottom: .5rem;
  font-size: .9rem;
}

/* Tombol aksi */
.profile-container .sticky-actions {
  text-align: right;
  margin-top: .6rem;
}
.profile-container .btn {
  padding: .35rem .7rem;
  font-size: .875rem;
}
.profile-container .btn i {
  font-size: .85rem;
}

/* Breadcrumb */
.profile-container .breadcrumb {
  margin-bottom: 0;
  font-size: .875rem;
}

/* Responsive */
@media (max-width: 767.98px) {
  .profile-info dl.dl-kv {
    grid-template-columns: 95px 1fr;
  }
}
</style>

<div class="container-fluid profile-container">
  <div class="row">
    <div class="col-12">
      <div class="page-title-box d-sm-flex align-items-center justify-content-between">
        <h1 class="h3 mb-0 text-gray-800">
          <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i> Profile Pengguna
        </h1>
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Beranda</a></li>
          <li class="breadcrumb-item active">Profile</li>
        </ol>
      </div>
    </div>
  </div>

  @if(session('success'))
    <div class="alert alert-success py-2 mb-2" role="alert">
      <i class="mdi mdi-check-all me-2"></i>{{ session('success') }}
    </div>
  @endif

  {{-- === 4 KARTU LAYOUT 2×2 === --}}
  {{-- <div class="row row-cols-1 row-cols-lg-2 g-2"> --}}
  <div class="row row-cols-1 row-cols-lg-2 g-4">

    
    {{-- Kartu 1: Info Admin / ID / Username --}}
    <div class="col">
      <div class="card profile-info">
        <div class="card-header">
          <h5 class="mb-0">Informasi Akun</h5>
        </div>
        <div class="card-body">
          <dl class="dl-kv mb-0">
            <dt>Nama Lengkap</dt>
            <dd>{{ $user->nama_user }}</dd>

            <dt>ID User</dt>
            <dd>{{ $user->id_user }}</dd>

            <dt>Username</dt>
            <dd>{{ $user->username }}</dd>

            <dt>Role</dt>
            <dd>{{ ucfirst($user->role) }}</dd>
          </dl>
        </div>
      </div>
    </div>

    {{-- Kartu 2: Email / No HP / Status --}}
    <div class="col">
      <div class="card profile-info">
        <div class="card-header">
          <h5 class="mb-0">Kontak & Status</h5>
        </div>
        <div class="card-body">
          <dl class="dl-kv mb-0">
            <dt>Email</dt>
            <dd>{{ $user->email ?? '-' }}</dd>

            <dt>No Telepon</dt>
            <dd>{{ $user->no_telepon }}</dd>

            <dt>Status</dt>
            <dd>
              @if($user->status === 'aktif')
                <span class="badge bg-success">Aktif</span>
              @else
                <span class="badge bg-danger">Non Aktif</span>
              @endif
            </dd>
          </dl>
        </div>
      </div>
    </div>

    {{-- Kartu 3: Edit Data --}}
    <div class="col">
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">Update Profil</h5>
        </div>
        <div class="card-body">
          <form action="{{ route('profile.update') }}" method="POST">
            @csrf @method('PUT')

            <div class="mb-3">
              <label for="nama_user" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
              <input id="nama_user" name="nama_user" type="text"
                class="form-control @error('nama_user') is-invalid @enderror"
                value="{{ old('nama_user', $user->nama_user) }}" required>
              @error('nama_user') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
              <label for="no_telepon" class="form-label">No Telepon <span class="text-danger">*</span></label>
              <input id="no_telepon" name="no_telepon" type="text"
                class="form-control @error('no_telepon') is-invalid @enderror"
                value="{{ old('no_telepon', $user->no_telepon) }}" required>
              @error('no_telepon') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
              <label for="email" class="form-label">Email</label>
              <input id="email" name="email" type="email"
                class="form-control @error('email') is-invalid @enderror"
                value="{{ old('email', $user->email) }}">
              @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="sticky-actions">
              <button type="submit" class="btn btn-primary">
                <i class="mdi mdi-content-save me-1"></i> Simpan Perubahan
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    {{-- Kartu 4: Ubah Password --}}
    <div class="col">
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">Password</h5>
        </div>
        <div class="card-body">
          <form action="{{ route('profile.password.update') }}" method="POST">
            @csrf @method('PUT')

            <div class="mb-3">
              <label for="current_password" class="form-label">Password Lama <span class="text-danger">*</span></label>
              <input id="current_password" name="current_password" type="password"
                class="form-control @error('current_password') is-invalid @enderror"
                placeholder="Password lama" required>
              @error('current_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
              <label for="password" class="form-label">Password Baru <span class="text-danger">*</span></label>
              <input id="password" name="password" type="password"
                class="form-control @error('password') is-invalid @enderror"
                placeholder="Minimal 4 karakter" required>
              @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
              <label for="password_confirmation" class="form-label">Konfirmasi Password Baru <span class="text-danger">*</span></label>
              <input id="password_confirmation" name="password_confirmation" type="password"
                class="form-control" placeholder="Ulangi password" required>
            </div>

            <div class="sticky-actions">
              <button type="submit" class="btn btn-warning">
                <i class="mdi mdi-lock-reset me-1"></i> Ubah Password
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

  </div>
</div>
@endsection


{{-- @extends('layouts.master')

@section('page-title', 'Profile')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Profil Pengguna</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Beranda</a></li>
                        <li class="breadcrumb-item active">Profile</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="mdi mdi-check-all me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row">
        <!-- Informasi Profile -->
        <div class="col-xl-4">
            <div class="card">
                <div class="card-body">
                    <div class="text-center">
                        <div class="avatar-xl mx-auto mb-3">
                            <div class="avatar-title bg-soft-primary text-primary rounded-circle font-size-24">
                                <i class="mdi mdi-account-circle"></i>
                            </div>
                        </div>
                        <h5 class="mb-1">{{ $user->nama_user }}</h5>
                        <p class="text-muted">{{ ucfirst($user->role) }}</p>
                    </div>

                    <hr class="my-4">

                    <div class="table-responsive">
                        <table class="table table-borderless mb-0">
                            <tbody>
                                <tr>
                                    <th class="ps-0" scope="row">ID User :</th>
                                    <td class="text-muted">{{ $user->id_user }}</td>
                                </tr>
                                <tr>
                                    <th class="ps-0" scope="row">Username :</th>
                                    <td class="text-muted">{{ $user->username }}</td>
                                </tr>
                                <tr>
                                    <th class="ps-0" scope="row">Email :</th>
                                    <td class="text-muted">{{ $user->email ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="ps-0" scope="row">No Telepon :</th>
                                    <td class="text-muted">{{ $user->no_telepon }}</td>
                                </tr>
                                <tr>
                                    <th class="ps-0" scope="row">Status :</th>
                                    <td>
                                        @if($user->status == 'aktif')
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-danger">Non Aktif</span>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Edit Profile -->
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Edit Profil</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- <div class="mb-3">
                            <label for="id_user" class="form-label">ID User <span class="text-danger">*</span></label>
                            <input type="text" class="form-control bg-light" id="id_user" value="{{ $user->id_user }}" disabled>
                            <small class="text-muted">Field ini tidak dapat diubah</small>
                        </div>

                        <div class="mb-3">
                            <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                            <input type="text" class="form-control bg-light" id="username" value="{{ $user->username }}" disabled>
                            <small class="text-muted">Field ini tidak dapat diubah</small>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control bg-light" id="email" value="{{ $user->email }}" disabled>
                            <small class="text-muted">Field ini tidak dapat diubah</small>
                        </div>

                        <div class="mb-3">
                            <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                            <input type="text" class="form-control bg-light" id="role" value="{{ ucfirst($user->role) }}" disabled>
                            <small class="text-muted">Field ini tidak dapat diubah</small>
                        </div> --}}

                        {{-- <hr class="my-4">
                        <h5 class="card-title mb-3">Field yang Dapat Diubah</h5>

                        <div class="mb-3">
                            <label for="nama_user" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nama_user') is-invalid @enderror" 
                                   id="nama_user" name="nama_user" value="{{ old('nama_user', $user->nama_user) }}" 
                                   placeholder="Masukkan nama lengkap" required>
                            @error('nama_user')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="no_telepon" class="form-label">No Telepon <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('no_telepon') is-invalid @enderror" 
                                   id="no_telepon" name="no_telepon" value="{{ old('no_telepon', $user->no_telepon) }}" 
                                   placeholder="Masukkan no telepon" required>
                            @error('no_telepon')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="mdi mdi-content-save me-1"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Form Ubah Password -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Ubah Password</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('profile.password.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="current_password" class="form-label">Password Lama <span class="text-danger">*</span></label>
                            <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                                   id="current_password" name="current_password" 
                                   placeholder="Masukkan password lama" required>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password Baru <span class="text-danger">*</span></label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                   id="password" name="password" 
                                   placeholder="Masukkan password baru (minimal 8 karakter)" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" 
                                   id="password_confirmation" name="password_confirmation" 
                                   placeholder="Masukkan ulang password baru" required>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-warning">
                                <i class="mdi mdi-lock-reset me-1"></i> Ubah Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection --}}