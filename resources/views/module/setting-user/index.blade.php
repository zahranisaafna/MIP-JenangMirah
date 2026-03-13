@extends('layouts.master')
@section('title', 'Kelola Pengguna')
@section('content')

<div class="container-fluid">
    <!-- Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-fw fa-users-cog"></i> Kelola Pengguna
                </h1>
                {{-- <h4 class="mb-sm-0">Kelola Pengguna</h4> --}}
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Beranda</a></li>
                        <li class="breadcrumb-item active">Kelola Pengguna</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    {{-- <!-- Alert Messages -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="mdi mdi-check-all me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="mdi mdi-block-helper me-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif --}}
    <!-- User List Card -->
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title">Halaman untuk mengelola data pengguna beserta hak aksesnya</h5>
                        <a href="{{ route('setting-user.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Tambah User
                        </a>
                    </div>
                </div>
                <div class="card-body">
                <!-- Alert Messages -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="mdi mdi-check-all me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="mdi mdi-block-helper me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                        
                    @php
                        $allowed = $allowed ?? [20, 40, 60, 80, 99];
                        $perPage = $perPage ?? (int) request('per_page', 20);
                   @endphp

                    {{-- Form Filter --}}
                    <div class="card mb-3">
                        <div class="card-body py-2">
                            <form method="GET" action="{{ route('setting-user.index') }}" class="form-inline">
                               <div class="row w-100">
                                    {{-- Nama User --}}
                                    <div class="col-md-2 mb-2">
                                        <label class="mr-2 mb-0 small text-muted">Nama User:</label>
                                        <input type="text" 
                                            name="search_nama" 
                                            class="form-control form-control-sm" 
                                            placeholder="Cari nama user..."
                                            value="{{ request('search_nama') }}"
                                            style="width: 100%;">
                                    </div>

                                    {{-- Username --}}
                                    <div class="col-md-2 mb-2">
                                        <label class="mr-2 mb-0 small text-muted">Username:</label>
                                        <input type="text" 
                                        name="search_username" 
                                        class="form-control form-control-sm" 
                                        placeholder="Cari username..."
                                        value="{{ request('search_username') }}"
                                        style="width: 100%;">
                                    </div>

                                    {{-- Role --}}
                                    <div class="col-md-2 mb-2">
                                        <label class="mr-2 mb-0 small text-muted">Role:</label>
                                        <select name="search_role" class="form-control form-control-sm" style="width: 100%;">
                                            <option value="">-- Semua Role --</option>
                                            <option value="admin" {{ request('search_role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                            <option value="karyawanproduksi" {{ request('search_role') == 'karyawanproduksi' ? 'selected' : '' }}>Karyawan Produksi</option>
                                            <option value="owner" {{ request('search_role') == 'owner' ? 'selected' : '' }}>Owner</option>
                                        </select>
                                    </div>
                                    {{-- untuk tombol --}}
                                    <div class="col-md-6 mb-2 d-flex align-items-end justify-content-end">
                                        <button type="submit" class="btn btn-primary btn-sm mr-1">
                                            <i class="fas fa-search"></i> Filter
                                        </button>
                                        <a href="{{ route('setting-user.index') }}" class="btn btn-secondary btn-sm">
                                            <i class="fas fa-redo"></i> Reset
                                        </a>
                                    </div>
                                </div>
                                {{-- Preserve per_page parameter --}}
                                <input type="hidden" name="per_page" value="{{ request('per_page', 20) }}">
                            </form>
                        </div>
                    </div>

                    {{-- Info hasil pencarian --}}
                    @if(request('search_nama') || request('search_username') || request('search_email') || request('search_role') || request('search_status'))
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <i class="fas fa-info-circle"></i> 
                            <strong>Hasil Pencarian:</strong>
                                
                            @if(request('search_nama'))
                                Nama: <strong>"{{ request('search_nama') }}"</strong>
                            @endif
                                
                            @if(request('search_username'))
                                Username: <strong>"{{ request('search_username') }}"</strong>
                            @endif
                                
                            @if(request('search_role'))
                                Role: <strong>
                                    @if(request('search_role') == 'admin')
                                        Admin
                                    @elseif(request('search_role') == 'karyawanproduksi')
                                        Karyawan Produksi
                                    @else
                                        Owner
                                    @endif
                                </strong>
                            @endif
                                
                            - Ditemukan <strong>{{ $users->total() }}</strong> user
                                
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover table-sm align-middle">
                            <colgroup>
                                <col style="width:1%">   {{-- No --}}
                                <col style="width:4%">   {{-- ID --}}
                                <col style="width:8%">  {{-- Nama --}}
                                <col style="width:5%">  {{-- Username --}}
                                <col style="width:25%">   {{-- Email --}}
                                <col style="width:5%">  {{-- Role --}}
                                <col style="width:4%">  {{-- No Telepon --}}
                                <col style="width:2%">  {{-- Status --}}
                                <col style="width:2%">   {{-- Aksi --}}
                            </colgroup>
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">No</th>
                                    <th class="text-center">ID User</th>
                                    <th class="text-center">Nama User</th>
                                    <th class="text-center">Username</th>
                                    <th class="text-center">Email</th>
                                    <th class="text-center">Role</th>
                                    <th class="text-center">No Telepon</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $index => $user)
                                <tr>
                                    <td class="text-center">{{ $users->firstItem() + $index }}</td>
                                    {{-- <td class="text-center">{{ $index + 1 }}</td> --}}
                                    <td class="text-center text-nowrap">{{ $user->id_user }}</td>
                                    <td class="text-truncate" style="max-width:200px">{{ $user->nama_user }}</td>
                                    <td class="text-nowrap">{{ $user->username }}</td>
                                    <td class="text-truncate" style="max-width:260px">{{ $user->email ?? '-' }}</td>
                                    <td class="text-center text-nowrap">
                                        @if($user->role == 'admin')
                                            <span class="badge badge-primary">Admin</span>
                                        @elseif($user->role == 'karyawanproduksi')
                                            <span class="badge badge-info">Karyawan Produksi</span>
                                        @else
                                            <span class="badge badge-warning">Owner</span>
                                        @endif
                                    </td>
                                    <td  class="text-nowrap">{{ $user->no_telepon }}</td>
                                    <td  class="text-center text-nowrap">
                                        @if($user->status == 'aktif')
                                            <span class="badge badge-success">Aktif</span>
                                        @else
                                            <span class="badge badge-danger">Non Aktif</span>
                                        @endif
                                    </td>
                                    <td class="text-center text-nowrap">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('setting-user.edit', $user->id_user) }}" 
                                            class="btn btn-sm btn-warning" 
                                            title="Edit">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-sm btn-danger" 
                                                    onclick="confirmDelete('{{ $user->id_user }}')"
                                                    title="Hapus"
                                                    @if($user->id_user === auth()->user()->id_user) disabled @endif>
                                                    <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                            
                                        <form id="delete-form-{{ $user->id_user }}" 
                                            action="{{ route('setting-user.destroy', $user->id_user) }}" 
                                            method="POST" 
                                            style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <i class="mdi mdi-information-outline me-2"></i>
                                        Belum ada data user
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{-- (Opsional) kontrol "Tampilkan X baris" --}}
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <form method="GET" class="d-flex align-items-center">
                            @foreach(request()->except(['per_page','page']) as $k => $v)
                                <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                            @endforeach
                            <label class="me-2 mb-0 small text-muted">Tampilkan</label>
                            <select name="per_page" class="custom-select custom-select-sm" style="width: 90px"
                                    onchange="this.form.submit()">
                                @foreach($allowed as $opt)
                                    <option value="{{ $opt }}" {{ $perPage==$opt ? 'selected' : '' }}>{{ $opt }}</option>
                                @endforeach
                            </select>
                            <span class="ml-2 small text-muted">data per halaman</span>
                        </form>
                    </div>

                    <div class="mt-3">
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@endsection

@push('scripts')
<script>
    // DataTable initialization
    $(document).ready(function() {
        $('#datatable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json',
            },
            order: [[1, 'asc']],
            columnDefs: [
                { orderable: false, targets: [0, 8] }
            ]
        });
    });

    // Confirm delete
    function confirmDelete(userId) {
        if (confirm('Apakah Anda yakin ingin menghapus user ini?')) {
            document.getElementById('delete-form-' + userId).submit();
        }
    }
</script>
@endpush