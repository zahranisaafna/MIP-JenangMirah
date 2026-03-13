@extends('layouts.master')
@section('title','Data Bahan Baku')
@section('content')

{{-- <div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Data Bahan Baku</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Bahan Baku</li>
                    </ol>
                </div>
            </div>
        </div>
    </div> --}}
<div class="container-fluid">
    <!-- Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                {{-- <h4 class="mb-sm-0">Data Bahan Baku</h4> --}}
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-fw fa-boxes"></i> Data Bahan Baku
                </h1>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Beranda</a></li>
                        <li class="breadcrumb-item active">Data Bahan Baku</li>
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
                        <h5 class="card-title">Data bahan baku yang digunakan sebagai dasar kebutuhan resep dan perhitungan produksi Jenang Mirah</h5>
                        <a href="{{ route('bahan-baku.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Tambah Bahan Baku
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
                        $perPage = $perPage ?? (int) request('per_page', default: 20);

                        function tgl3($date) {
                            if (!$date) return '-';
                            $b = [1=>'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
                            $ts = strtotime($date);
                            return date('d', $ts) . '-' . $b[(int)date('n',$ts)] . '-' . date('Y',$ts);
                        }
                    @endphp
                    {{-- Form Filter --}}
                    <div class="card mb-3">
                        <div class="card-body py-2">
                            <form method="GET" action="{{ route('bahan-baku.index') }}" class="form-inline">
                                <div class="row w-100">
                                    <div class="col-md-2 mb-2">
                                        <label class="mr-2 mb-0 small text-muted">Nama Bahan:</label>
                                        <input type="text" 
                                            name="search_nama" 
                                            class="form-control form-control-sm" 
                                            placeholder="Cari nama bahan..."
                                            value="{{ request('search_nama') }}"
                                            style="width: 100%;">
                                    </div>
                                    <div class="col-md-2 mb-2">
                                        <label class="mr-2 mb-0 small text-muted">Kategori:</label>
                                        <select name="search_kategori" class="form-control form-control-sm" style="width: 100%;">
                                            <option value="">-- Semua Kategori --</option>
                                            @foreach($kategoriList as $kat)
                                                <option value="{{ $kat }}" {{ request('search_kategori') == $kat ? 'selected' : '' }}>
                                                    {{ $kat }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2 mb-2">
                                        <label class="mr-2 mb-0 small text-muted">Status:</label>
                                        <select name="search_status" class="form-control form-control-sm" style="width: 100%;">
                                            <option value="">-- Semua Status --</option>
                                            <option value="aman" {{ request('search_status') == 'aman' ? 'selected' : '' }}>Stok Aman</option>
                                            <option value="menipis" {{ request('search_status') == 'menipis' ? 'selected' : '' }}>Stok Menipis</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-2 d-flex align-items-end justify-content-end">
                                        <button type="submit" class="btn btn-primary btn-sm mr-1">
                                            <i class="fas fa-search"></i> Filter
                                        </button>
                                        <a href="{{ route('bahan-baku.index') }}" class="btn btn-secondary btn-sm">
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
                    @if(request('search_nama') || request('search_kategori') || request('search_status'))
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <i class="fas fa-info-circle"></i> 
                            Menampilkan hasil pencarian
                            @if(request('search_nama'))
                                untuk nama: <strong>"{{ request('search_nama') }}"</strong>
                            @endif
                            @if(request('search_kategori'))
                                kategori: <strong>"{{ request('search_kategori') }}"</strong>
                            @endif
                            @if(request('search_status'))
                                status: <strong>"{{ request('search_status') == 'aman' ? 'Stok Aman' : 'Stok Menipis' }}"</strong>
                            @endif
                            - Ditemukan <strong>{{ $bahanBaku->total() }}</strong> data
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
                                <col style="width:4%">   {{-- ID --}}
                                <col style="width:5%">  {{-- Nama --}}
                                <col style="width:5%">  {{-- Kategori --}}
                                <col style="width:4%">   {{-- Satuan --}}
                                <col style="width:5%">  {{-- Stok Min --}}
                                <col style="width:5%">  {{-- Stok Saat Ini --}}
                                <col style="width:6%">  {{-- Harga --}}
                                <col style="width:5%">  {{-- Tgl --}}
                                <col style="width:4%">  {{-- Status --}}
                                <col style="width:20%">  {{-- deskripsi --}}
                                <col style="width:2%">   {{-- Aksi --}}
                            </colgroup>
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">No</th>
                                    <th class="text-center">ID</th>
                                    <th class="text-center">Nama Bahan</th>
                                    <th class="text-center">Kategori</th>
                                    <th class="text-center">Satuan</th>
                                    <th class="text-center">Stok Minimum</th>
                                    <th class="text-center">Stok Saat Ini</th>
                                    <th class="text-center">Harga Rata-rata (Rp)</th>
                                    <th class="text-center">Tanggal Kadaluarsa</th>
                                    <th class="text-center">Status Stok</th>
                                    <th class="text-center">Deskripsi</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                
                                @forelse($bahanBaku as $index => $bahan)
                                    <tr>
                                        <td class="text-center">{{ $bahanBaku->firstItem() + $index }}</td>
                                        <td class="text-center">{{ $bahan->id_bahan_baku }}</td>
                                        <td class="text-truncate" style="max-width:220px">{{ $bahan->nama_bahan }}</td>
                                        <td class="text-truncate">{{ $bahan->kategori }}</td>
                                        <td class="text-center">{{ $bahan->satuan }}</td>
                                        <td class="text-right">{{ number_format($bahan->stok_minimum) }}</td>
                                        <td class="text-right">{{ number_format($bahan->stok_saat_ini) }}</td>
                                        <td class="text-right">{{ number_format($bahan->harga_rata_rata, 0, '.', ',') }}.-</td>
                                        {{-- Tanggal Kadaluarsa --}}
                                        <td class="text-center text-nowrap">{{ tgl3($bahan->tanggal_kadaluarsa) }}</td>
                                        <td class="text-center">
                                            @if($bahan->stok_saat_ini <= $bahan->stok_minimum)
                                                <span class="badge badge-danger">Stok Menipis</span>
                                            @else
                                                <span class="badge badge-success">Stok Aman</span>
                                            @endif
                                        </td>
                                        {{-- Keterangan --}}
                                        <td class="text-truncate" style="max-width: 520px;">{{ $bahan->deskripsi ?? '-' }}</td>
                                        {{-- Aksi dirapatkan kanan + tidak wrap --}}
                                        <td class="text-center text-nowrap">
                                            {{-- Tombol Detail/View --}}
                                            <a href="{{ route('bahan-baku.show', $bahan->id_bahan_baku) }}" 
                                               class="btn btn-info btn-sm" 
                                               title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('bahan-baku.edit', $bahan->id_bahan_baku) }}" class="btn btn-warning btn-sm" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('bahan-baku.destroy', $bahan->id_bahan_baku) }}"
                                                  method="POST" class="d-inline"
                                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus bahan baku ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                        {{-- <td class="text-end text-nowrap">
                                            <a href="{{ route('bahan-baku.edit', $bahan->id_bahan_baku) }}" 
                                               class="btn btn-warning btn-sm" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('bahan-baku.destroy', $bahan->id_bahan_baku) }}" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus bahan baku ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td> --}}
                                    </tr>
                                @empty
                                    <tr>
                                        {{-- <td colspan="12" class="text-center">Belum ada data bahan baku</td> --}}
                                        <td colspan="12" class="text-center">
                                            @if(request('search_nama') || request('search_kategori') || request('search_status'))
                                                Tidak ada data yang sesuai dengan pencarian
                                            @else
                                                Belum ada data bahan baku
                                            @endif
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
                        {{-- {{ $bahanBaku->links('pagination::bootstrap-4') }} --}}
                        {{-- <div class="mt-3">
                            {{ $bahanBaku->links('pagination::bootstrap-4') }}
                        </div> --}}

                        {{-- {{ $bahanBaku->links() }} --}}
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@endsection