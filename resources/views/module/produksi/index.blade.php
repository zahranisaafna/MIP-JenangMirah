@extends('layouts.master')
@section('title','Produksi')
@section('content')
<style>
    .flatpickr-input[readonly] {
        background-color: white !important;
        color: inherit !important;
        cursor: pointer !important;
    }
</style>

<div class="container-fluid">
    <!-- Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-fw fa-industry"></i> Produksi
                </h1>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('produksi.dashboard') }}">Beranda</a></li>
                        <li class="breadcrumb-item active">Produksi</li>
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
                        <h5 class="card-title">Daftar kegiatan produksi yang mencatat proses pembuatan produk pada setiap batch harian.</h5>
                        <a href="{{ route('produksi.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Tambah Produksi
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {!! session('success') !!}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {!! session('error') !!}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @php
                        $allowed = $allowed ?? [20, 40, 60, 80, 100, 250, 500];
                        $perPage = $perPage ?? (int) request('per_page', 20);

                        function tgl3($date) {
                            if (!$date) return '-';
                            $b = [1=>'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
                            $ts = strtotime($date);
                            return date('d', $ts) . '-' . $b[(int)date('n',$ts)] . '-' . date('Y',$ts);
                        }
                    @endphp
                    {{-- Form Filter Tanggal Produksi --}}
                    <div class="card mb-3">
                        <div class="card-body py-2">
                            <form method="GET" action="{{ route('produksi.index') }}">
                                <div class="row g-3">

                                    <div class="col-md-3 mb-2">
                                        <label class="small text-muted">Tanggal Produksi:</label>
                                        <input type="text"
                                            id="filter_tanggal"
                                            name="filter_tanggal"
                                            class="form-control form-control-sm"
                                            placeholder="Pilih tanggal"
                                            value="{{ request('filter_tanggal') }}">
                                    </div>

                                    <div class="col-md-9 mb-2 d-flex align-items-end justify-content-end">
                                        <button type="submit" class="btn btn-primary btn-sm mr-1">
                                            <i class="fas fa-search"></i> Filter
                                        </button>
                                        <a href="{{ route('produksi.index') }}" class="btn btn-secondary btn-sm">
                                            <i class="fas fa-redo"></i> Reset
                                        </a>
                                    </div>

                                </div>
                            </form>
                        </div>
                    </div>
                    @if(request('filter_tanggal'))
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <i class="fas fa-info-circle"></i>
                            <strong>Filter:</strong>
                            Tanggal Produksi = <strong>{{ request('filter_tanggal') }}</strong>
                            - Ditemukan <strong>{{ $produksi->total() }}</strong> data
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
                                {{-- <col style="width:6%">    --}}
                                {{-- <col style="width:6%">   Kode Batch --}}
                                <col style="width:5%">   {{-- Tanggal --}}
                                <col style="width:5%">   {{-- Waktu --}}
                                <col style="width:6%">   {{-- Operator --}}
                                <col style="width:4%">   {{-- Total Produk --}}
                                <col style="width:3%">   {{-- Status --}}
                                <col style="width:30%">  {{-- Catatan --}}
                                <col style="width:3%">   {{-- Aksi --}}
                            </colgroup>
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center text-nowrap">No</th>
                                    <th class="text-center text-nowrap">ID Produksi</th>
                                    {{-- <th class="text-center text-nowrap">Kode Batch</th> --}}
                                    <th class="text-center text-nowrap">Tanggal</th>
                                    <th class="text-center text-nowrap">Waktu</th>
                                    <th class="text-center text-nowrap">Operator</th>
                                    <th class="text-center text-nowrap">Total Produk</th>
                                    <th class="text-center text-nowrap">Status</th>
                                    <th class="text-center text-nowrap">Catatan</th>
                                    <th class="text-center text-nowrap">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($produksi as $item)
                                    <tr>
                                        {{-- Nomor urut --}}
                                        <td class="text-center">{{ $produksi->firstItem() + $loop->index }}</td>

                                        {{-- ID Produksi --}}
                                        <td class="text-center text-nowrap">{{ $item->id_produksi }}</td>

                                        {{-- Kode Batch --}}
                                        {{-- <td class="text-center text-nowrap">
                                            <span class="badge badge-secondary">{{ $item->kode_batch }}</span>
                                        </td> --}}

                                        {{-- Tanggal Produksi --}}
                                        <td class="text-center text-nowrap">{{ tgl3($item->tanggal_produksi) }}</td>

                                        {{-- Waktu --}}
                                        <td class="text-center text-nowrap">
                                            <small>
                                                Mulai: {{ $item->waktu_mulai }}<br>
                                                @if($item->waktu_selesai)
                                                    Selesai: {{ $item->waktu_selesai }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </small>
                                        </td>

                                        {{-- Operator --}}
                                        <td class="text-nowrap">{{ $item->user->nama_user }}</td>

                                        {{-- Total Produk --}}
                                        <td class="text-center text-nowrap">
                                            <strong>{{ $item->total_produk_dihasilkan }}</strong>
                                            <br>
                                            <small class="text-muted">({{ $item->detailProduksis->count() }} item)</small>
                                        </td>

                                        {{-- Status --}}
                                        <td class="text-center text-nowrap">
                                            @if($item->status === 'proses')
                                                <span class="badge badge-info">Proses</span>
                                            @elseif($item->status === 'selesai')
                                                <span class="badge badge-success">Selesai</span>
                                            @elseif($item->status === 'gagal')
                                                <span class="badge badge-danger">Gagal</span>
                                            @else
                                                <span class="badge badge-warning">Pending</span>
                                            @endif
                                        </td>

                                        {{-- Catatan --}}
                                        <td class="text-truncate" style="max-width: 520px;">{{ $item->catatan ?? '-' }}</td>

                                        {{-- Aksi --}}
                                        <td class="text-center text-nowrap">
                                            <a href="{{ route('produksi.show', $item->id_produksi) }}" 
                                               class="btn btn-info btn-sm" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            {{-- EDIT untuk status proses, pending, dan selesai --}}
                                            {{-- @if(in_array($item->status, ['proses', 'pending', 'selesai'])) --}}
                                                <a href="{{ route('produksi.edit', $item->id_produksi) }}" 
                                                class="btn btn-warning btn-sm" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            {{-- @endif --}}
                                            {{-- DELETE hanya untuk status pending --}}
                                            {{-- @if($item->status === 'pending') --}}
                                                <form action="{{ route('produksi.destroy', $item->id_produksi) }}" method="POST" class="d-inline"
                                                    onsubmit="return confirm('Hapus produksi akan mengembalikan stok bahan baku. Yakin ingin menghapus?')">
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
                                        <td colspan="10" class="text-center">Belum ada data produksi</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Kontrol "Tampilkan X baris" --}}
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

                    {{-- <div class="mt-3">
                        {{ $produksi->links('pagination::bootstrap-4') }}
                    </div> --}}
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
  .card + .card { margin-top: .75rem; }
  .text-muted { color: #555 !important; font-weight: 500; }
</style>
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    flatpickr("#filter_tanggal", {
        dateFormat: "Y-m-d",   // value yang dikirim ke server
        altInput: true,
        altFormat: "d-M-Y",    // tampilan: 15-Nov-2025
        defaultDate: "{{ request('filter_tanggal') }}",
        allowInput: true,
    });
});
</script>
@endpush

@endsection