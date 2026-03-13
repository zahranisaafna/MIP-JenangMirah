@extends('layouts.master')
@section('title','Detail Pembelian')
@section('content')

<div class="container-fluid">
    <!-- Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h3 class="mb-sm-0">Detail Pembelian</h3>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Beranda</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('pembelian.index') }}">Pembelian</a></li>
                        <li class="breadcrumb-item active">Detail</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    @php
    // Formatter tanggal: 05-Jan-2025 (ID: Jan, Feb, Mar, Apr, Mei, Jun, Jul, Agu, Sep, Okt, Nov, Des)
        function tgl3($date) {
            if (!$date) return '-';
                $b = [1=>'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
                $ts = strtotime($date);
                return date('d', $ts) . '-' . $b[(int)date('n',$ts)] . '-' . date('Y',$ts);
        }
    @endphp

    <section class="content">
        <div class="container-fluid">
            <!-- Info Pembelian -->
            <div class="card mb-2">
                <div class="card-header py-2">
                {{-- <div class="card-header"> --}}
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Data pembelian bahan baku beserta detail jumlah dan pembayaran</h5>
                        <a href="{{ route('pembelian.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body py-2">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm mb-0">
                                <tr>
                                    <td class="font-weight-normal" style="width:170px;">ID Pembelian</td>
                                    <td>: {{ $pembelian->id_pembelian }}</td>
                                </tr>
                                <tr>
                                    <th class="font-weight-normal">Tanggal Pembelian</th>
                                    <td>: {{ tgl3($pembelian->tanggal_pembelian) }}</td>
                                </tr>
                                <tr>
                                    <th class="font-weight-normal">User</th>
                                    <td>: {{ $pembelian->nama_user }}</td>
                                </tr>
                                <tr>
                                    <th class="font-weight-normal">Metode Pembayaran</th>
                                    <td>: 
                                        @if($pembelian->metode_pembayaran == 'cash')
                                            <span class="badge badge-dark">Cash</span>
                                        @else
                                            <span class="badge badge-primary">Transfer</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm mb-0">
                                <tr>
                                    <th class="font-weight-normal" style="width:170px;">Status Pembayaran</th>
                                    <td>: 
                                        @if($pembelian->status_pembayaran == 'lunas')
                                            <span class="badge badge-success">Lunas</span>
                                        @else
                                            <span class="badge badge-warning">Belum Lunas</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="font-weight-normal">Tanggal Jatuh Tempo</th>
                                    <td>: {{ tgl3($pembelian->tanggal_jatuh_tempo) }}</td>
                                </tr>
                                <tr>
                                    <th class="font-weight-normal">Total Pembelian</th>
                                    <td>: <strong class="text-info">Rp {{ number_format($pembelian->total_pembelian, 0, ',', '.') }},-</strong></td>
                                </tr>
                                <tr>
                                    <th class="font-weight-normal">Keterangan</th>
                                    <td>: {{ $pembelian->keterangan ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detail Items -->
            <div class="card">
                <div class="card-header py-2">
                    <h5 class="mb-0">Data Bahan Baku yang dibeli</h5>
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
                            <col style="width:7%">
                            <col style="width:7%">
                            <col style="width:7%">
                            <col style="width:2%">
                        </colgroup>
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">No</th>
                                    <th class="text-center">ID Detail</th>
                                    <th class="text-center">Bahan Baku</th>
                                    <th class="text-center">Supplier</th>
                                    <th class="text-center">Jumlah</th>
                                    <th class="text-center">Harga Satuan (Rp)</th>
                                    <th class="text-center">Subtotal (Rp)</th>
                                    <th class="text-center">Tanggal Diterima</th>
                                    <th class="text-center">Kondisi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $grandTotal = 0; @endphp
                                @foreach($details as $index => $detail)
                                    @php $grandTotal += $detail->subtotal; @endphp
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td class="text-center text-nowrap">{{ $detail->id_detail_pembelian }}</td>
                                        <td class="text-center text-nowrap">{{ $detail->nama_bahan }}</td>
                                        <td class="text-center text-nowrap">{{ $detail->nama_supplier }}</td>
                                        <td class="text-center text-nowrap">{{ number_format($detail->jumlah) }} {{ $detail->satuan }}</td>
                                        <td class="text-right text-nowrap">{{ number_format($detail->harga_satuan, 0, ',', '.')}},-</td>
                                        <td class="text-right text-nowrap">{{ number_format($detail->subtotal, 0, ',', '.')}},-</td>
                                        <td class="text-center text-nowrap">{{ tgl3($detail->tanggal_diterima) }}</td>
                                        <td class="text-center">
                                            @if($detail->kondisi == 'baik')
                                                <span class="badge badge-success">Baik</span>
                                            @elseif($detail->kondisi == 'rusak')
                                                <span class="badge badge-danger">Rusak</span>
                                            @else
                                                <span class="badge badge-warning">Kadaluarsa</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="thead-light">
                                <tr>
                                    <td colspan="6" class="text-right"><strong>Total Pembelian (Rp):</strong></td>
                                    <td colspan="3" class="text-right text-nowrap">
                                        <strong class="text-info">{{ number_format($grandTotal, 0, ',', '.') }},-</strong>
                                    </td>
                                    {{-- <td colspan="3"><strong class="text-success">{{ number_format($grandTotal, 0, ',', '.') }}</strong></td> --}}
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@endsection

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
