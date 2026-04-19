@extends('layouts.master')
@section('title','Distribusi')
@section('content')
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
                    <i class="fas fa-fw fa-truck"></i> Distribusi
                </h1>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('produksi.dashboard') }}">Beranda</a></li>
                        <li class="breadcrumb-item active">Distribusi</li>
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
                        <h5 class="card-title">Daftar distribusi produk dari gudang ke toko beserta tanggal pengiriman</h5>
                        <a href="{{ route('distribusi.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Tambah Distribusi
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
                    {{-- Form Filter Tanggal Distribusi --}}
                    <div class="card mb-3">
                        <div class="card-body py-2">
                            <form method="GET" action="{{ route('distribusi.index') }}">
                                <div class="row g-3">

                                    <div class="col-md-3 mb-2">
                                        <label class="small text-muted">Tanggal Distribusi:</label>
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
                                        <a href="{{ route('distribusi.index') }}" class="btn btn-secondary btn-sm">
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
                            Tanggal Distribusi = <strong>{{ request('filter_tanggal') }}</strong>
                            - Ditemukan <strong>{{ $distribusi->total() }}</strong> data
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover table-sm align-middle">
                            <colgroup>
                                <col style="width:1%">
                                <col style="width:4%">
                                {{-- <col style="width:6%"> --}}
                                <col style="width:5%">
                                <col style="width:5%">
                                <col style="width:3%">
                                <col style="width:25%">
                                <col style="width:1%">
                            </colgroup>
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center text-nowrap">No</th>
                                    <th class="text-center text-nowrap">ID Distribusi</th>
                                    {{-- <th class="text-center text-nowrap">Kode Distribusi</th> --}}
                                    <th class="text-center text-nowrap">Tanggal</th>
                                    <th class="text-center text-nowrap">Jenis</th>
                                    <th class="text-center text-nowrap">Status</th>
                                    <th class="text-center text-nowrap">Keterangan</th>
                                    <th class="text-center text-nowrap">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($distribusi as $item)
                                    <tr>
                                        <td class="text-center">{{ $distribusi->firstItem() + $loop->index }}</td>
                                        <td class="text-center">{{ $item->id_distribusi }}</td>
                                        {{-- <td class="text-center text-nowrap">{{ $item->kode_distribusi }}</td> --}}
                                        <td class="text-center text-nowrap">{{ tgl3($item->tanggal_distribusi) }}</td>
                                        <td class="text-center text-nowrap">
                                            @if($item->jenis_distribusi == 'internal')
                                                <span class="badge badge-info">Internal</span>
                                            @else
                                                <span class="badge badge-warning">Eksternal</span>
                                            @endif
                                        </td>
                                        <td class="text-center text-nowrap">
                                            @if($item->status == 'pending')
                                                <span class="badge badge-warning">Pending</span>
                                            @elseif($item->status == 'selesai')
                                                <span class="badge badge-success">Selesai</span>
                                            @else
                                                <span class="badge badge-danger">Batal</span>
                                            @endif
                                        </td>
                                        <td class="text-truncate" style="max-width: 520px;">{{ $item->keterangan ?? '-' }}</td>
                                        <td class="text-center text-nowrap">
                                            <a href="{{ route('distribusi.show', $item->id_distribusi) }}" 
                                               class="btn btn-info btn-sm" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            {{-- @if($item->status == 'pending') --}}
                                            <a href="{{ route('distribusi.edit', $item->id_distribusi) }}" 
                                               class="btn btn-warning btn-sm" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            {{-- @endif --}}

                                            <form action="{{ route('distribusi.destroy', $item->id_distribusi) }}" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('Hapus distribusi akan mengembalikan stok produk. Yakin ingin menghapus?')">
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
                                        <td colspan="7" class="text-center">Belum ada data distribusi</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

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
                        {{-- {{ $distribusi->links('pagination::bootstrap-4') }} --}}
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    flatpickr("#filter_tanggal", {
        dateFormat: "Y-m-d",   // value yang dikirim ke server
        altInput: true,
        altFormat: "d-M-Y",    // tampilan: 12-Nov-2025
        defaultDate: "{{ request('filter_tanggal') }}",
        allowInput: true,
    });
});
</script>
@endpush

@endsection