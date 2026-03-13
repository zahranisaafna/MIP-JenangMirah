@extends('layouts.master')
@section('title','Supplier')
@section('content')

<div class="container-fluid">
    <!-- Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-fw fa-truck"></i> Supplier
                </h1>
                {{-- <h4 class="mb-sm-0">Data Supplier</h4> --}}
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Beranda</a></li>
                        <li class="breadcrumb-item active">Supplier</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title">Informasi supplier yang menyediakan bahan baku produksi</h5>
                        <a href="{{ route('supplier.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Tambah Supplier
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
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
                            <form method="GET" action="{{ route('supplier.index') }}" class="form-inline">
                                <div class="row w-100">
                                    <div class="col-md-2 mb-2">
                                        <label class="mr-2 mb-0 small text-muted">Nama Supplier:</label>
                                        <input type="text" 
                                            name="search_nama" 
                                            class="form-control form-control-sm" 
                                            placeholder="Cari nama supplier..."
                                            value="{{ request('search_nama') }}"
                                            style="width: 100%;">
                                    </div>
                                    <div class="col-md-2 mb-2">
                                        <label class="mr-2 mb-0 small text-muted">Kontak Person:</label>
                                        <input type="text" 
                                            name="search_kontak" 
                                            class="form-control form-control-sm" 
                                            placeholder="Cari kontak person..."
                                            value="{{ request('search_kontak') }}"
                                            style="width: 100%;">
                                    </div>
                                    <div class="col-md-2 mb-2">
                                        <label class="mr-2 mb-0 small text-muted">Bahan Baku:</label>
                                        <select name="search_bahan" class="form-control form-control-sm" style="width: 100%;">
                                            <option value="">-- Semua Bahan --</option>
                                            @foreach($bahanBakuList as $bahan)
                                                <option value="{{ $bahan->id_bahan_baku }}" {{ request('search_bahan') == $bahan->id_bahan_baku ? 'selected' : '' }}>
                                                    {{ $bahan->nama_bahan }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-2 d-flex align-items-end justify-content-end">
                                        <button type="submit" class="btn btn-primary btn-sm mr-1">
                                            <i class="fas fa-search"></i> Filter
                                        </button>
                                        <a href="{{ route('supplier.index') }}" class="btn btn-secondary btn-sm">
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
                    @if(request('search_nama') || request('search_kontak') || request('search_bahan'))
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <i class="fas fa-info-circle"></i> 
                            Menampilkan hasil pencarian
                            @if(request('search_nama'))
                                untuk nama supplier: <strong>"{{ request('search_nama') }}"</strong>
                            @endif
                            @if(request('search_kontak'))
                                kontak person: <strong>"{{ request('search_kontak') }}"</strong>
                            @endif
                            @if(request('search_bahan'))
                                @php
                                    $selectedBahan = $bahanBakuList->firstWhere('id_bahan_baku', request('search_bahan'));
                                @endphp
                                bahan baku: <strong>"{{ $selectedBahan->nama_bahan ?? '' }}"</strong>
                            @endif
                            - Ditemukan <strong>{{ $suppliers->total() }}</strong> data
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover table-sm align-middle">
                        {{-- <table class="table table-bordered table-striped table-hover"> --}}
                            <colgroup>
                                <col style="width:1%">   {{-- No --}}
                                <col style="width:3%">   {{-- ID --}}
                                <col style="width:5%">  {{-- Nama --}}
                                <col style="width:5%">  {{-- Bahan Baku --}}
                                <col style="width:25%">   {{-- Alamat --}}
                                <col style="width:3%">  {{-- No. Telepon --}}
                                <col style="width:4%">  {{-- Kontak Person --}}
                                <col style="width:2%">  {{-- Status --}}
                                <col style="width:2%">   {{-- Aksi --}}
                            </colgroup>
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">No</th>
                                    <th class="text-center">ID Supplier</th>
                                    <th class="text-center">Nama Supplier</th>
                                    <th class="text-center">Bahan Baku</th>
                                    <th class="text-center">Alamat</th>
                                    <th class="text-center">No Telepon</th>
                                    <th class="text-center">Kontak Person</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            {{-- <thead class="table-light">
                                <tr>
                                    <th style="width: 5%">No</th>
                                    <th>ID Supplier</th>
                                    <th>Nama Supplier</th>
                                    <th>Bahan Baku</th>
                                    <th>Alamat</th>
                                    <th>No. Telepon</th>
                                    <th>Kontak Person</th>
                                    <th>Status</th>
                                    <th style="width: 12%">Aksi</th>
                                </tr>
                            </thead> --}}
                            <tbody>
                                @forelse($suppliers as $index => $supplier)
                                    <tr>
                                        <td class="text-center">{{ $suppliers->firstItem() + $index }}</td>
                                        <td class="text-center">{{ $supplier->id_supplier }}</td>
                                        <td class="text-nowrap">{{ $supplier->nama_supplier }}</td>
                                        <td class="text-nowrap">{{ $supplier->nama_bahan }}</td>
                                        {{-- alamat: dibatasi lebar + elipsis agar rapi --}}
                                        <td class="text-truncate" style="max-width: 460px;">
                                            {{ \Illuminate\Support\Str::limit($supplier->alamat, 140) }}
                                        </td>
                                        {{-- <td>{{ Str::limit($supplier->alamat, 50) }}</td> --}}
                                        <td class="text-nowrap">{{ $supplier->no_telepon }}</td>
                                        <td class="text-nowrap">{{ $supplier->kontak_person }}</td>
                                        <td class="text-center">
                                            @if($supplier->status == 'aktif')
                                                <span class="badge badge-success">Aktif</span>
                                            @else
                                                <span class="badge badge-secondary">Non Aktif</span>
                                            @endif
                                        </td>
                                        <td class="text-center text-nowrap">
                                            <a href="{{ route('supplier.edit', $supplier->id_supplier) }}" 
                                               class="btn btn-warning btn-sm" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('supplier.destroy', $supplier->id_supplier) }}" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus supplier ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">Belum ada data supplier</td>
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
                        {{-- {{ $suppliers->links('pagination::bootstrap-4') }} --}}
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@endsection