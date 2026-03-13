@extends('layouts.master')
@section('title','Pembelian')
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
                    <i class="fas fa-fw fa-shopping-cart"></i> Pembelian
                </h1>
                {{-- <h4 class="mb-sm-0">Data Pembelian</h4> --}}
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Beranda</a></li>
                        <li class="breadcrumb-item active">Pembelian</li>
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
                        <h5 class="card-title">Data riwayat pembelian bahan baku untuk keperluan produksi</h5>
                        <a href="{{ route('pembelian.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Tambah Pembelian
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    {{-- Form Filter --}}
                    <div class="card mb-3">
                        <div class="card-body py-2">
                            <form method="GET" action="{{ route('pembelian.index') }}">
                                <div class="row g-3">

                                    {{-- Periode --}}
                                    <div class="col-md-2 mb-2">
                                        <label class="small text-muted">Periode:</label>
                                        <select name="periode" class="form-control form-control-sm" onchange="toggleDateInputs(this)">
                                            <option value="">-- Pilih Periode --</option>
                                            <option value="harian"  {{ request('periode') == 'harian' ? 'selected' : '' }}>Hari Ini</option>
                                            <option value="bulanan" {{ request('periode') == 'bulanan' ? 'selected' : '' }}>Bulan Ini</option>
                                            <option value="tahunan" {{ request('periode') == 'tahunan' ? 'selected' : '' }}>Tahun Ini</option>
                                            <option value="custom"  {{ request('start_date') || request('end_date') ? 'selected' : '' }}>Custom Range</option>
                                        </select>
                                    </div>

                                    {{-- Dari Tanggal --}}
                                    <div class="col-md-2 mb-2" id="start_date_wrapper">
                                        <label class="small text-muted">Dari Tanggal:</label>
                                        <input type="text" id="start_date" name="start_date"
                                            class="form-control form-control-sm"
                                            value="{{ request('start_date') }}">
                                    </div>

                                    {{-- Sampai Tanggal --}}
                                    <div class="col-md-2 mb-2" id="end_date_wrapper">
                                        <label class="small text-muted">Sampai Tanggal:</label>
                                        <input type="text" id="end_date" name="end_date"
                                            class="form-control form-control-sm"
                                            value="{{ request('end_date') }}">
                                    </div>

                                    {{-- Tombol --}}
                                    <div class="col-md-6 mb-2 d-flex align-items-end justify-content-end">
                                        <button type="submit" class="btn btn-primary btn-sm mr-1">
                                            <i class="fas fa-search"></i> Filter
                                        </button>
                                        <a href="{{ route('pembelian.index') }}" class="btn btn-secondary btn-sm">
                                            <i class="fas fa-redo"></i> Reset
                                        </a>
                                    </div>

                                </div>

                                <input type="hidden" name="per_page" value="{{ request('per_page', 20) }}">
                            </form>
                        </div>
                    </div>

                    {{-- <div class="card mb-3">
                        <div class="card-body py-2">
                            <form method="GET" action="{{ route('pembelian.index') }}" class="form-inline">
                                <div class="row w-100">
                                    Periode Cepat
                                    <div class="col-md-3 mb-2">
                                        <label class="mr-2 mb-0 small text-muted">Periode:</label>
                                        <select name="periode" class="form-control form-control-sm" style="width: 100%;" onchange="toggleDateInputs(this)">
                                            <option value="">-- Pilih Periode --</option>
                                            <option value="harian" {{ request('periode') == 'harian' ? 'selected' : '' }}>Hari Ini</option>
                                            <option value="bulanan" {{ request('periode') == 'bulanan' ? 'selected' : '' }}>Bulan Ini</option>
                                            <option value="tahunan" {{ request('periode') == 'tahunan' ? 'selected' : '' }}>Tahun Ini</option>
                                            <option value="custom" {{ request('start_date') || request('end_date') ? 'selected' : '' }}>Custom Range</option>
                                        </select>
                                    </div>

                                    Tanggal Mulai
                                    <div class="col-md-3 mb-2" id="start_date_wrapper">
                                        <label class="mr-2 mb-0 small text-muted">Dari Tanggal:</label>
                                        <input type="text"
                                            name="start_date"
                                            id="start_date"
                                            class="form-control form-control-sm"
                                            value="{{ request('start_date') }}">
                                    </div>
                                    Tanggal Akhir
                                    <div class="col-md-3 mb-2" id="end_date_wrapper">
                                        <label class="mr-2 mb-0 small text-muted">Sampai Tanggal:</label>
                                        <input type="text"
                                            name="end_date"
                                            id="end_date"
                                            class="form-control form-control-sm"
                                            value="{{ request('end_date') }}">
                                    </div>
                                    Tombol Aksi
                                    <div class="col-md-3 mb-2 d-flex align-items-end justify-content-end">
                                        <button type="submit" class="btn btn-primary btn-sm mr-1">
                                            <i class="fas fa-search"></i> Filter
                                        </button>
                                        <a href="{{ route('pembelian.index') }}" class="btn btn-secondary btn-sm">
                                            <i class="fas fa-redo"></i> Reset
                                        </a>
                                    </div>
                                </div>
                                Preserve per_page parameter
                                <input type="hidden" name="per_page" value="{{ request('per_page', 20) }}">
                            </form>
                        </div>
                    </div> --}}

                    {{-- Info hasil pencarian --}}
                    @if(request('periode') || request('start_date') || request('end_date') || request('status_pembayaran') || request('metode_pembayaran'))
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <i class="fas fa-info-circle"></i> 
                            <strong>Hasil Filter:</strong>
                            
                            @if(request('periode') && request('periode') != 'custom')
                                Periode: <strong>
                                    {{ request('periode') == 'harian' ? 'Hari Ini' : '' }}
                                    {{ request('periode') == 'bulanan' ? 'Bulan Ini' : '' }}
                                    {{ request('periode') == 'tahunan' ? 'Tahun Ini' : '' }}
                                </strong>
                            @endif
                            
                            @if(request('start_date') || request('end_date'))
                                @if(request('start_date') && request('end_date'))
                                    Tanggal: <strong>{{ date('d/m/Y', strtotime(request('start_date'))) }}</strong> s/d <strong>{{ date('d/m/Y', strtotime(request('end_date'))) }}</strong>
                                @elseif(request('start_date'))
                                    Dari tanggal: <strong>{{ date('d/m/Y', strtotime(request('start_date'))) }}</strong>
                                @elseif(request('end_date'))
                                    Sampai tanggal: <strong>{{ date('d/m/Y', strtotime(request('end_date'))) }}</strong>
                                @endif
                            @endif            
                            <br>
                            Ditemukan <strong>{{ $pembelian->total() }}</strong> transaksi 
                            dengan total: <strong>Rp {{ number_format($totalFiltered ?? 0, 0, ',', '.') }},-</strong>
                            
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

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
                        // fallback kalau controller lupa kirim (aman)
                        $allowed = $allowed ?? [20, 40, 60, 80, 100, 250, 500];
                        $perPage = $perPage ?? (int) request('per_page', 20);

                        // Formatter tanggal: 05-Jan-2025 (ID: Jan, Feb, Mar, Apr, Mei, Jun, Jul, Agu, Sep, Okt, Nov, Des)
                        function tgl3($date) {
                            if (!$date) return '-';
                            $b = [1=>'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
                            $ts = strtotime($date);
                            return date('d', $ts) . '-' . $b[(int)date('n',$ts)] . '-' . date('Y',$ts);
                        }
                    @endphp


                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover table-sm align-middle">
                        {{-- <table class="table table-bordered table-striped table-hover"> --}}
                                <colgroup>
                                <col style="width:1%">   {{-- No --}}
                                <col style="width:4%">   {{-- ID --}}
                                <col style="width:5%">  {{-- Tanggal --}}
                                <col style="width:6%">  {{-- User --}}
                                <col style="width:6%">   {{-- Total Pembelian --}}
                                <col style="width:3%">  {{-- Metode --}}
                                <col style="width:3%">  {{-- Status Pembeyaran --}}
                                <col style="width:5%">  {{-- Jatuh Tempo --}}
                                <col style="width:25%">  {{-- Keterangan --}}
                                <col style="width:1%">   {{-- Aksi --}}
                            </colgroup>
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center text-nowrap">No</th>
                                    <th class="text-center text-nowrap">ID Pembelian</th>
                                    <th class="text-center text-nowrap">Tanggal</th>
                                    <th class="text-center text-nowrap">Pengguna</th>
                                    <th class="text-center text-nowrap">Total Pembelian(Rp)</th>
                                    <th class="text-center text-nowrap">Metode Pembayaran</th>
                                    <th class="text-center text-nowrap">Status</th>
                                    <th class="text-center text-nowrap">Jatuh Tempo</th>
                                    <th class="text-center text-nowrap">Keterangan</th>
                                    <th class="text-center text-nowrap">Aksi</th>
                                </tr>
                            </thead>
                            {{-- <tbody>
                                @forelse($pembelian as $item)
                                    <tr>
                                        <td>{{ $item->id_pembelian }}</td>
                                        <td>{{ $pembelian->firstItem()  + $loop->index }}</td>
                                        <td>{{ $item->id_pembelian }}</td>
                                        <td>{{ date('d/m/Y', strtotime($item->tanggal_pembelian)) }}</td>
                                        <td>{{ $item->nama_user }}</td>
                                        <td>Rp {{ number_format($item->total_pembelian, 0, ',', '.') }}</td>
                                        <td>
                                            @if($item->metode_pembayaran == 'cash')
                                                <span class="badge badge-success">Cash</span>
                                            @else
                                                <span class="badge badge-info">Transfer</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($item->status_pembayaran == 'lunas')
                                                <span class="badge badge-success">Lunas</span>
                                            @else
                                                <span class="badge badge-warning">Belum Lunas</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $item->tanggal_jatuh_tempo ? date('d/m/Y', strtotime($pembelian->tanggal_jatuh_tempo)) : '-' }}
                                        </td>
                                        <td>
                                            <a href="{{ route('pembelian.show', $item->id_pembelian) }}" 
                                               class="btn btn-info btn-sm" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('pembelian.edit', $item->id_pembelian) }}" 
                                               class="btn btn-warning btn-sm" title="Edit Status">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('pembelian.destroy', $item->id_pembelian) }}" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('Hapus pembelian akan mengembalikan stok. Yakin ingin menghapus?')">
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
                                        <td colspan="9" class="text-center">Belum ada data pembelian</td>
                                    </tr>
                                @endforelse
                            </tbody> --}}
                            <tbody>
                                @forelse($pembelian as $item)
                                    <tr>
                                        {{-- Nomor urut --}}
                                        <td class="text-center">{{ $pembelian->firstItem() + $loop->index }}</td>

                                        {{-- ID Pembelian --}}
                                        <td class="text-center">{{ $item->id_pembelian }}</td>

                                        {{-- Tanggal Pembelian --}}
                                        <td class="text-center text-nowrap">{{ tgl3($item->tanggal_pembelian) }}</td>
                                        {{-- <td>{{ date('d/m/Y', strtotime($item->tanggal_pembelian)) }}</td> --}}

                                        {{-- User --}}
                                        <td class="text-nowrap">{{ $item->nama_user }}</td>

                                        {{-- Total Pembelian --}}
                                        <td class="text-right text-nowrap">{{ number_format($item->total_pembelian, 0, ',', '.')}},-</td>

                                        {{-- Metode --}}
                                        <td class="text-center text-nowrap">
                                            @if($item->metode_pembayaran == 'cash')
                                                <span class="badge badge-dark">Cash</span>
                                            @else
                                                <span class="badge badge-primary">Transfer</span>
                                            @endif
                                        </td>

                                        {{-- Status --}}
                                        <td class="text-center text-nowrap">
                                            @if($item->status_pembayaran == 'lunas')
                                                <span class="badge badge-success">Lunas</span>
                                            @else
                                                <span class="badge badge-warning">Belum Lunas</span>
                                            @endif
                                        </td>

                                        {{-- Jatuh Tempo --}}
                                        <td class="text-center text-nowrap">{{ $item->tanggal_jatuh_tempo ? tgl3($item->tanggal_jatuh_tempo) : '-' }}</td>

                                        {{-- Keterangan --}}
                                        <td class="text-truncate" style="max-width: 520px;">{{ $item->keterangan ?? '-' }}</td>
                                        {{-- <td>{{ $item->keterangan ?? '-' }}</td> --}}

                                        {{-- Aksi --}}
                                        <td class="text-center text-nowrap">
                                            <a href="{{ route('pembelian.show', $item->id_pembelian) }}" 
                                            class="btn btn-info btn-sm" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            <a href="{{ route('pembelian.edit', $item->id_pembelian) }}" 
                                            class="btn btn-warning btn-sm" title="Edit Status">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <form action="{{ route('pembelian.destroy', $item->id_pembelian) }}" 
                                                method="POST" 
                                                class="d-inline"
                                                onsubmit="return confirm('Hapus pembelian akan mengembalikan stok. Yakin ingin menghapus?')">
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
                                        <td colspan="10" class="text-center">Belum ada data pembelian</td>
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
                        {{-- {{ $pembelian->links('pagination::bootstrap-4') }} --}}
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
    // Start date
    flatpickr("#start_date", {
        dateFormat: "Y-m-d",       // nilai dikirim ke server
        altInput: true,            // input cantik
        altFormat: "d-M-Y",        // tampilan: 15-Nov-2025
        defaultDate: "{{ request('start_date') }}",
        allowInput: true,
    });

    // End date
    flatpickr("#end_date", {
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "d-M-Y",
        defaultDate: "{{ request('end_date') }}",
        allowInput: true,
    });

    // Kalau masih mau logic show/hide tanggal berdasarkan periode:
    const periodeSelect = document.querySelector('select[name="periode"]');
    if (periodeSelect) {
        toggleDateInputs(periodeSelect);
        periodeSelect.addEventListener('change', function () {
            toggleDateInputs(this);
        });
    }
});

// Fungsi show/hide input tanggal (boleh pakai yang lama)
function toggleDateInputs(select) {
    const value = select.value;
    const startDateWrapper = document.getElementById('start_date_wrapper');
    const endDateWrapper   = document.getElementById('end_date_wrapper');
    const startDateInput   = document.getElementById('start_date');
    const endDateInput     = document.getElementById('end_date');

    if (value === 'custom') {
        // tampilkan untuk custom range
        startDateWrapper.style.display = 'block';
        endDateWrapper.style.display   = 'block';
    } else if (value === '') {
        // tampilkan tapi kosongkan nilai
        startDateWrapper.style.display = 'block';
        endDateWrapper.style.display   = 'block';
        startDateInput.value = '';
        endDateInput.value   = '';
    } else {
        // sembunyikan untuk preset (harian/bulanan/tahunan)
        startDateWrapper.style.display = 'none';
        endDateWrapper.style.display   = 'none';
        startDateInput.value = '';
        endDateInput.value   = '';
    }
}
</script>
@endpush

{{-- @push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
Toggle tampilan input tanggal berdasarkan pilihan periode
function toggleDateInputs(select) {
    const value = select.value;
    const startDateWrapper = document.getElementById('start_date_wrapper');
    const endDateWrapper = document.getElementById('end_date_wrapper');
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    
    if (value === 'custom') {
        // Tampilkan input tanggal untuk custom range
        startDateWrapper.style.display = 'block';
        endDateWrapper.style.display = 'block';
    } else if (value === '') {
        // Tampilkan tapi kosongkan nilai
        startDateWrapper.style.display = 'block';
        endDateWrapper.style.display = 'block';
        startDateInput.value = '';
        endDateInput.value = '';
    } else {
        // Sembunyikan input tanggal untuk periode preset
        startDateWrapper.style.display = 'none';
        endDateWrapper.style.display = 'none';
        startDateInput.value = '';
        endDateInput.value = '';
    }
}

// Jalankan saat halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
    const periodeSelect = document.querySelector('select[name="periode"]');
    if (periodeSelect) {
        toggleDateInputs(periodeSelect);
    }
});
</script>
@endpush --}}
@endsection