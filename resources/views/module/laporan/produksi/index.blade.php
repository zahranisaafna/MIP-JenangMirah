@extends('layouts.master')
@section('title','Laporan Produksi')
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
                    <i class="fas fa-fw fa-chart-bar"></i> Laporan Produksi
                </h1>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('owner.dashboard') }}">Beranda</a></li>
                        <li class="breadcrumb-item active">Laporan Produksi</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    @php
        function tgl3($date) {
            if (!$date) return '-';
            $b = [1=>'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
            $ts = strtotime($date);
            return date('d', $ts) . '-' . $b[(int)date('n',$ts)] . '-' . date('Y',$ts);
        }
    @endphp


    <section class="content">
        <div class="container-fluid">
            {{-- <!-- Statistik Card -->
            <div class="row mb-3">
                <div class="col-lg-3 col-md-6">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Total Produksi</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalProduksi }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-industry fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Total Target</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalTarget }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-bullseye fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Total Berhasil</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalBerhasil }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Persentase Keberhasilan</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($persentaseKeberhasilan, 2) }}%</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-percentage fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> --}}

            <!-- Data Table Card -->
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Rekap Data Produksi Jenang Mirah</h5>
                        @if($produksi->total() > 0)
                        <form method="GET" action="{{ route('laporan.produksi.pdf') }}" target="_blank">
                            <input type="hidden" name="periode" value="{{ request('periode') }}">
                            <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                            <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="fas fa-file-pdf"></i> Download PDF
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    {{-- Filter Card --}}
                    <div class="card mb-3">
                        <div class="card-body py-2">
                            <form method="GET" action="{{ route('laporan.produksi.index') }}">
                                <div class="row g-3">
                                    {{-- Periode Cepat --}}
                                    <div class="col-md-2 mb-2">
                                        <label class="small text-muted">Periode:</label>
                                        <select name="periode" class="form-control form-control-sm" onchange="toggleDateInputs(this)">
                                            <option value="">-- Pilih Periode --</option>
                                            <option value="harian"  {{ request('periode') == 'harian'  ? 'selected' : '' }}>Hari Ini</option>
                                            <option value="bulanan" {{ request('periode') == 'bulanan' ? 'selected' : '' }}>Bulan Ini</option>
                                            <option value="tahunan" {{ request('periode') == 'tahunan' ? 'selected' : '' }}>Tahun Ini</option>
                                            <option value="custom"  {{ request('start_date') || request('end_date') ? 'selected' : '' }}>Custom Range</option>
                                        </select>
                                    </div>

                                    {{-- Tanggal Mulai --}}
                                    <div class="col-md-2 mb-2" id="start_date_wrapper">
                                        <label class="small text-muted">Dari Tanggal:</label>
                                        <input type="text" 
                                            id="start_date"
                                            name="start_date" 
                                            class="form-control form-control-sm" 
                                            value="{{ request('start_date') }}">
                                            {{-- style="width: 100%;"> --}}
                                    </div>

                                    {{-- Tanggal Akhir --}}
                                    <div class="col-md-2 mb-2" id="end_date_wrapper">
                                        <label class="small text-muted">Sampai Tanggal:</label>
                                        <input type="text" 
                                            id="end_date"
                                            name="end_date" 
                                            class="form-control form-control-sm" 
                                            value="{{ request('end_date') }}">
                                    </div>

                                    {{-- Tombol Aksi --}}
                                    <div class="col-md-6 mb-2 d-flex align-items-end justify-content-end">
                                        <button type="submit" class="btn btn-primary btn-sm mr-1">
                                            <i class="fas fa-search"></i> Filter
                                        </button>
                                        <a href="{{ route('laporan.produksi.index') }}" class="btn btn-secondary btn-sm">
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
                    @if(request('periode') || request('start_date') || request('end_date'))
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <i class="fas fa-info-circle"></i> 
                            <strong>Hasil Filter:</strong>
                            
                            {{-- Periode --}}
                            @if(request('periode') && request('periode') != 'custom')
                                Periode: <strong>
                                    {{ request('periode') == 'harian'  ? 'Hari Ini'   : '' }}
                                    {{ request('periode') == 'bulanan' ? 'Bulan Ini'  : '' }}
                                    {{ request('periode') == 'tahunan' ? 'Tahun Ini'  : '' }}
                                </strong>
                            @endif
                            
                            {{-- Range tanggal (format d-M-Y => 12-Nov-2025) --}}
                            @if(request('start_date') || request('end_date'))
                                @if(request('start_date') && request('end_date'))
                                    Tanggal: 
                                    <strong>{{ date('d-M-Y', strtotime(request('start_date'))) }}</strong> 
                                    s/d 
                                    <strong>{{ date('d-M-Y', strtotime(request('end_date'))) }}</strong>
                                @elseif(request('start_date'))
                                    Dari tanggal: 
                                    <strong>{{ date('d-M-Y', strtotime(request('start_date'))) }}</strong>
                                @elseif(request('end_date'))
                                    Sampai tanggal: 
                                    <strong>{{ date('d-M-Y', strtotime(request('end_date'))) }}</strong>
                                @endif
                            @endif            

                            <br>
                            Ditemukan <strong>{{ $produksi->total() }}</strong> batch produksi
                            dengan total produk dihasilkan: <strong>{{ number_format($totalProdukDihasilkan, 0, ',', '.') }}</strong>
                            {{-- | Persentase Keberhasilan: <strong>{{ number_format($persentaseKeberhasilan, 2) }}%</strong> --}}
                            
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover table-sm align-middle">
                            <colgroup>
                                <col style="width:1%">
                                <col style="width:6%">
                                <col style="width:5%">
                                <col style="width:5%">
                                <col style="width:6%">
                                <col style="width:4%">
                                <col style="width:25%">
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
                                    <th class="text-center text-nowrap">Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($produksi as $item)
                                    <tr>
                                        <td class="text-center">{{ $produksi->firstItem() + $loop->index }}</td>
                                        <td class="text-center text-nowrap">
                                            <span class="badge badge-secondary">{{ $item->id_produksi }}</span>
                                        </td>
                                        {{-- <td class="text-center text-nowrap">
                                            <span class="badge badge-secondary">{{ $item->kode_batch }}</span>
                                        </td> --}}
                                        <td class="text-center text-nowrap">{{ tgl3($item->tanggal_produksi) }}</td>
                                        <td class="text-center text-nowrap">
                                            <small>
                                                {{ $item->waktu_mulai }} - {{ $item->waktu_selesai ?? '-' }}
                                            </small>
                                        </td>
                                        <td class="text-nowrap">{{ $item->user->nama_user }}</td>
                                        <td class="text-center text-nowrap">
                                            <strong>{{ $item->total_produk_dihasilkan }}</strong>
                                            <br>
                                            <small class="text-muted">({{ $item->detailProduksis->count() }} item)</small>
                                        </td>
                                        <td class="text-truncate" style="max-width: 520px;">{{ $item->catatan ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Tidak ada data produksi untuk periode yang dipilih</td>
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

                    <div class="mt-3">
                        {{ $produksi->appends(request()->query())->links('pagination::bootstrap-4') }}
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
        // Flatpickr untuk start_date
        flatpickr("#start_date", {
            dateFormat: "Y-m-d",   // value ke server
            altInput: true,
            altFormat: "d-M-Y",    // tampilan: 12-Nov-2025
            defaultDate: "{{ request('start_date') }}",
            allowInput: true,
        });

        // Flatpickr untuk end_date
        flatpickr("#end_date", {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "d-M-Y",
            defaultDate: "{{ request('end_date') }}",
            allowInput: true,
        });

        // Set kondisi awal sesuai periode yang terpilih saat ini
        const selectPeriode = document.querySelector('select[name="periode"]');
        if (selectPeriode) {
            toggleDateInputs(selectPeriode);
        }
    });

// Enable/disable input tanggal berdasarkan periode
function toggleDateInputs(select) {
    const periode   = select.value;
    const startWrap = document.getElementById('start_date_wrapper');
    const endWrap   = document.getElementById('end_date_wrapper');
    const start     = document.getElementById('start_date');
    const end       = document.getElementById('end_date');

    if (!start || !end) return;

    if (periode === 'custom') {
        // Custom range -> user bebas pilih tanggal
        start.disabled = false;
        end.disabled   = false;
        startWrap.classList.remove('opacity-50');
        endWrap.classList.remove('opacity-50');
    } else {
        // Harian / bulanan / tahunan / kosong -> tanggal dikontrol di controller
        start.disabled = true;
        end.disabled   = true;
        startWrap.classList.add('opacity-50');
        endWrap.classList.add('opacity-50');
    }
}
// Toggle tampilan input tanggal berdasarkan pilihan periode
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
@endpush

<style>
  .table-sm td, .table-sm th {
    padding: .35rem .5rem !important;
    vertical-align: middle;
  }
  .table th, .table td {
    white-space: nowrap;
  }
  .card + .card { margin-top: .75rem; }

</style>

@endsection