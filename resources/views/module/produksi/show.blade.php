@extends('layouts.master')
@section('title','Detail Produksi')
@section('content')

<div class="container-fluid">
    <!-- Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h3 class="mb-sm-0">Detail Produksi</h3>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('produksi.dashboard') }}">Beranda</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('produksi.index') }}">Produksi</a></li>
                        <li class="breadcrumb-item active">Detail</li>
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
            <!-- Info Produksi -->
            <div class="card mb-2">
                <div class="card-header py-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Informasi yang berisi tanggal, waktu, status, operator produksi sebuah produk dilakukan</h5>
                        <div>
                            {{-- @if(in_array($produksi->status, ['proses', 'pending'])) --}}
                                <a href="{{ route('produksi.edit', $produksi->id_produksi) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                            {{-- @endif --}}
                            <a href="{{ route('produksi.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body py-2">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm mb-0">
                                <tr>
                                    <td class="font-weight-normal" style="width:170px;">ID Produksi</td>
                                    <td>: {{ $produksi->id_produksi }}</td>
                                </tr>
                                {{-- <tr>
                                    <th class="font-weight-normal">Kode Batch</th>
                                    <td>: <span class="badge badge-secondary">{{ $produksi->kode_batch }}</span></td>
                                </tr> --}}
                                <tr>
                                    <th class="font-weight-normal">Tanggal Produksi</th>
                                    <td>: {{ tgl3($produksi->tanggal_produksi) }}</td>
                                </tr>
                                <tr>
                                    <th class="font-weight-normal">Waktu Mulai</th>
                                    <td>: {{ $produksi->waktu_mulai }}</td>
                                </tr>
                                <tr>
                                    <th class="font-weight-normal">Waktu Selesai</th>
                                    <td>: {{ $produksi->waktu_selesai ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm mb-0">
                                <tr>
                                    <th class="font-weight-normal" style="width:170px;">Operator</th>
                                    <td>: {{ $produksi->user->nama_user }}</td>
                                </tr>
                                <tr>
                                    <th class="font-weight-normal">Status</th>
                                    <td>: 
                                        @if($produksi->status === 'proses')
                                            <span class="badge badge-info">Proses</span>
                                        @elseif($produksi->status === 'selesai')
                                            <span class="badge badge-success">Selesai</span>
                                        @elseif($produksi->status === 'gagal')
                                            <span class="badge badge-danger">Gagal</span>
                                        @else
                                            <span class="badge badge-warning">Pending</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="font-weight-normal">Total Produk Dihasilkan</th>
                                    <td>: <strong class="text-info">{{ $produksi->total_produk_dihasilkan }} produk</strong></td>
                                </tr>
                                <tr>
                                    <th class="font-weight-normal">Jumlah Item</th>
                                    <td>: {{ $produksi->detailProduksis->count() }} item</td>
                                </tr>
                                <tr>
                                    <th class="font-weight-normal">Catatan</th>
                                    <td>: {{ $produksi->catatan ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ringkasan -->
            <div class="card mb-2">
                <div class="card-header py-2">
                    <h5 class="mb-0">Presentase produksi yang berhasil sekali masak</h5>
                </div>
                <div class="card-body py-2">
                    @php
                        $totalTarget = $produksi->detailProduksis->sum('jumlah_target');
                        $totalBerhasil = $produksi->detailProduksis->sum('jumlah_berhasil');
                        $totalGagal = $produksi->detailProduksis->sum('jumlah_gagal');
                        $persentaseKeseluruhan = $totalTarget > 0 ? ($totalBerhasil / $totalTarget) * 100 : 0;
                    @endphp

                    <div class="row text-center mb-2">
                        <div class="col-4">
                            <div class="border rounded p-2">
                                <h3 class="mb-0 text-primary">{{ $totalTarget }}</h3>
                                <small class="text-muted">Target</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border rounded p-2">
                                <h3 class="mb-0 text-success">{{ $totalBerhasil }}</h3>
                                <small class="text-muted">Berhasil</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border rounded p-2">
                                <h3 class="mb-0 text-danger">{{ $totalGagal }}</h3>
                                <small class="text-muted">Gagal</small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-2">
                        <strong>Persentase Keberhasilan:</strong>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar bg-success" role="progressbar" 
                                 style="width: {{ $persentaseKeseluruhan }}%">
                                {{ number_format($persentaseKeseluruhan, 2) }}%
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detail Items -->
            <div class="card">
                <div class="card-header py-2">
                    <h5 class="mb-0">Informasi mengenai produk yang diproduksi serta keberhasilan produksi tersebut</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover table-sm align-middle">
                            <colgroup>
                                <col style="width:1%">
                                <col style="width:4%">
                                <col style="width:8%">
                                <col style="width:8%">
                                <col style="width:4%">
                                <col style="width:4%">
                                <col style="width:4%">
                                <col style="width:5%">
                                <col style="width:30%">
                                <col style="width:15%">
                            </colgroup>
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">No</th>
                                    <th class="text-center">ID Detail</th>
                                    <th class="text-center">Resep</th>
                                    <th class="text-center">Produk</th>
                                    <th class="text-center">Target</th>
                                    <th class="text-center">Berhasil</th>
                                    <th class="text-center">Gagal</th>
                                    <th class="text-center">Persentase</th>
                                    <th class="text-center">Kebutuhan Bahan</th>
                                    <th class="text-center">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($detailWithBahan as $index => $item)
                                    @php
                                        $detail = $item['detail'];
                                        $kebutuhanBahan = $item['kebutuhan_bahan'];
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td class="text-center text-nowrap">{{ $detail->id_detail_produksi }}</td>
                                        <td class="text-nowrap">{{ $detail->resep->nama_resep }}</td>
                                        <td class="text-nowrap">{{ $detail->produk->nama_produk }}</td>
                                        <td class="text-center text-nowrap">{{ $detail->jumlah_target }} {{ $detail->resep->satuan_output }}</td>
                                        <td class="text-center text-success">{{ $detail->jumlah_berhasil }}</td>
                                        <td class="text-center text-danger">{{ $detail->jumlah_gagal }}</td>
                                        <td class="text-center">
                                            <span class="badge {{ $detail->persentase_keberhasilan >= 80 ? 'badge-success' : ($detail->persentase_keberhasilan >= 50 ? 'badge-warning' : 'badge-danger') }}">
                                                {{ number_format($detail->persentase_keberhasilan, 2) }}%
                                            </span>
                                        </td>
                                        <td>
                                            <small>
                                                @foreach($kebutuhanBahan as $bahan)
                                                    <strong>{{ $bahan['nama_bahan'] }}</strong>: {{ number_format($bahan['jumlah_dibutuhkan_stok'], 2) }} {{ $bahan['satuan_stok'] }}
                                                    @if($bahan['keterangan'])
                                                        <em class="text-muted">({{ $bahan['keterangan'] }})</em>
                                                    @endif
                                                    <br>
                                                @endforeach
                                            </small>
                                        </td>
                                        <td>{{ $detail->keterangan_gagal ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
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
  .card + .card { margin-top: .75rem; }
  .text-muted { color: #555 !important; font-weight: 500; }
</style>

@endsection