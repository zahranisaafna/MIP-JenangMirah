@extends('layouts.master')
@section('title','Komposisi Produk')
@section('content')

<div class="container-fluid">
    <!-- Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-fw fa-book"></i> Komposisi Produk
                </h1>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Beranda</a></li>
                        <li class="breadcrumb-item active">Komposisi Produk</li>
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
                        <h5 class="card-title mb-0">Rincian komposisi bahan yang digunakan dalam pembuatan satu produk</h5>
                        {{-- @if(auth()->user()->role == 'admin') --}}
                        <a href="{{ route('resep.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Tambah Komposisi Produk
                        </a>
                        {{-- @endif --}}
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

                        function tgl3($date) {
                            if (!$date) return '-';
                            $b = [1=>'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
                            $ts = strtotime($date);
                            return date('d', $ts) . '-' . $b[(int)date('n',$ts)] . '-' . date('Y',$ts);
                        }
                    @endphp
                    {{-- Filter pencarian nama komposisi / nama resep --}}
                    <div class="card mb-3">
                        <div class="card-body">
                            <form action="{{ route('resep.index') }}" method="GET">
                                <div class="row align-items-end">
                                    <div class="col-md-2">
                                        <label for="search_nama" class="form-label">
                                            Nama Komposisi Produk
                                        </label>
                                        <input type="text"
                                            name="search_nama"
                                            id="search_nama"
                                            class="form-control"
                                            placeholder="Contoh: Jenang Ketan"
                                            value="{{ request('search_nama') }}">
                                    </div>

                                    <div class="col-md-10 mb-2 d-flex align-items-end justify-content-end">
                                        <button type="submit" class="btn btn-primary btn-sm mr-1">
                                            <i class="fas fa-search"></i> Filter
                                        </button>
                                        <a href="{{ route('resep.index') }}" class="btn btn-secondary btn-sm">
                                            <i class="fas fa-redo"></i> Reset
                                        </a>

                                        {{-- @if(request('search_nama'))
                                            <a href="{{ route('resep.index') }}" class="btn btn-secondary">
                                                <i class="fas fa-times"></i> Reset
                                            </a>
                                        @endif --}}
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    @if(request('search_nama'))
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <i class="fas fa-info-circle"></i> 
                            <strong>Hasil Pencarian:</strong>
                            Nama Komposisi / Resep: <strong>"{{ request('search_nama') }}"</strong>
                            - Ditemukan <strong>{{ $resep->total() }}</strong> data

                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif


                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover table-sm align-middle">
                            <colgroup>
                                <col style="width:1%">   {{-- No --}}
                                <col style="width:3%">   {{-- ID --}}
                                <col style="width:5%">   {{-- Nama --}}
                                <col style="width:2%">   {{-- Waktu --}}
                                <col style="width:2%">   {{-- Kapasitas --}}
                                <col style="width:2%">   {{-- Status --}}
                                <col style="width:2%">   {{-- Komposisi --}}
                                <col style="width:35%">  {{-- Catatan --}}
                                <col style="width:2%">   {{-- Aksi --}}
                            </colgroup>
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center text-nowrap">No</th>
                                    <th class="text-center text-nowrap">ID Resep</th>
                                    <th class="text-center text-nowrap">Nama Komposisi Produk</th>
                                    <th class="text-center text-nowrap">Waktu (menit)</th>
                                    <th class="text-center text-nowrap">Kapasitas</th>
                                    <th class="text-center text-nowrap">Status</th>
                                    <th class="text-center text-nowrap">Jumlah Bahan Baku</th>
                                    <th class="text-center text-nowrap">Catatan</th>
                                    <th class="text-center text-nowrap">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($resep as $item)
                                    <tr>
                                        <td class="text-center">{{ $resep->firstItem() + $loop->index }}</td>
                                        <td class="text-center text-nowrap">{{ $item->id_resep }}</td>
                                        <td class="text-nowrap">{{ $item->nama_resep }}</td>
                                        <td class="text-center">{{ $item->waktu_produksi }}</td>
                                        <td class="text-center text-nowrap">{{ $item->kapasitas_produksi }} {{ $item->satuan_output }}</td>
                                        <td class="text-center text-nowrap">
                                            @if($item->status == 'aktif')
                                                <span class="badge badge-success">Aktif</span>
                                            @else
                                                <span class="badge badge-secondary">Non Aktif</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-info">
                                                {{ $item->komposisiReseps->count() }} item
                                            </span>
                                        </td>
                                        <td class="text-truncate" style="max-width: 300px;">
                                            <small>{{ $item->catatan ? $item->catatan : '-' }}</small>
                                        </td>
                                        <td class="text-center text-nowrap">
                                            <a href="{{ route('resep.show', $item->id_resep) }}" 
                                               class="btn btn-info btn-sm" 
                                               title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            {{-- @if(auth()->user()->role == 'admin') --}}
                                            <a href="{{ route('resep.edit', $item->id_resep) }}" 
                                               class="btn btn-warning btn-sm" 
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('resep.destroy', $item->id_resep) }}" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus resep ini? Semua komposisi terkait akan ikut terhapus.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            {{-- @endif --}}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">Tidak ada data resep</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Per Page Selector --}}
                    <div class="d-flex align-items-center mb-2">
                        <form method="GET" class="d-flex align-items-center">
                            @foreach(request()->except(['per_page','page']) as $k => $v)
                                <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                            @endforeach

                            <label class="mb-0 small text-muted mr-2">Tampilkan</label>
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
                        {{-- {{ $resep->links('pagination::bootstrap-4') }} --}}
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
  .table-sm td, .table-sm th {
    padding: .35rem .5rem !important;
    vertical-align: middle;
  }
  .table th, .table td {
    white-space: nowrap;
  }
</style>

@endsection