@extends('layouts.master')
@section('title','Detail Distribusi')
@section('content')

<div class="container-fluid">
    <!-- Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h3 class="mb-sm-0">Detail Distribusi</h3>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('produksi.dashboard') }}">Beranda</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('distribusi.index') }}">Distribusi</a></li>
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

        function tglJam($datetime) {
            if (!$datetime) return '-';
            $b = [1=>'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
            $ts = strtotime($datetime);
            return date('d', $ts) . '-' . $b[(int)date('n',$ts)] . '-' . date('Y H:i', $ts);
        }
    @endphp

    <section class="content">
        <div class="container-fluid">
            <!-- Info Distribusi -->
            <div class="card mb-2">
                <div class="card-header py-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Data terkait tanggal, jenis, dan status distribusi produk</h5>
                        <div class="d-flex gap-2">
                            <a href="{{ route('distribusi.edit', $distribusi->id_distribusi) }}" 
                            class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>                        
                            <a href="{{ route('distribusi.index') }}" class="btn btn-secondary btn-sm">
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
                                    <td class="font-weight-normal" style="width:170px;">ID Distribusi</td>
                                    <td>: {{ $distribusi->id_distribusi }}</td>
                                </tr>
                                {{-- <tr>
                                    <th class="font-weight-normal">Kode Distribusi</th>
                                    <td>: {{ $distribusi->kode_distribusi }}</td>
                                </tr> --}}
                                <tr>
                                    <th class="font-weight-normal">Tanggal Distribusi</th>
                                    <td>: {{ tgl3($distribusi->tanggal_distribusi) }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm mb-0">
                                <tr>
                                    <th class="font-weight-normal" style="width:170px;">Jenis Distribusi</th>
                                    <td>: 
                                        @if($distribusi->jenis_distribusi == 'internal')
                                            <span class="badge badge-info">Internal</span>
                                        @else
                                            <span class="badge badge-warning">Eksternal</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="font-weight-normal">Status</th>
                                    <td>: 
                                        @if($distribusi->status == 'pending')
                                            <span class="badge badge-warning">Pending</span>
                                        @elseif($distribusi->status == 'selesai')
                                            <span class="badge badge-success">Selesai</span>
                                        @else
                                            <span class="badge badge-danger">Batal</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="font-weight-normal">Keterangan</th>
                                    <td>: {{ $distribusi->keterangan ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detail Distribusi per Lokasi -->
            @foreach($distribusi->distribusiDetails as $index => $detail)
            <div class="card mb-3">
                <div class="card-header py-2 bg-light">
                    <h5 class="mb-0">Tujuan {{ $index + 1 }}: {{ $detail->lokasi->nama_lokasi ?? '-' }}</h5>
                </div>
                <div class="card-body py-2">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm mb-0">
                                <tr>
                                    <td class="font-weight-normal" style="width:170px;">ID Detail</td>
                                    <td>: {{ $detail->id_distribusi_detail }}</td>
                                </tr>
                                <tr>
                                    <th class="font-weight-normal">Jenis Lokasi</th>
                                    <td>: 
                                        @if($detail->lokasi->jenis_lokasi == 'gudang')
                                            <span class="badge badge-primary">Gudang</span>
                                        @else
                                            <span class="badge badge-success">Toko</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="font-weight-normal">Tanggal Detail</th>
                                    <td>: {{ tgl3($detail->tanggal_detail) }}</td>
                                </tr>
                                <tr>
                                    <th class="font-weight-normal">User</th>
                                    <td>: {{ $detail->user->nama_user ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm mb-0">
                                <tr>
                                    <th class="font-weight-normal" style="width:170px;">Status Detail</th>
                                    <td>: 
                                        @if($detail->status_detail == 'pending')
                                            <span class="badge badge-warning">Pending</span>
                                        @else
                                            <span class="badge badge-success">Diterima</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="font-weight-normal">Nama Penerima</th>
                                    <td>: {{ $detail->nama_penerima }}</td>
                                </tr>
                                <tr>
                                    <th class="font-weight-normal">Catatan</th>
                                    <td>: {{ $detail->catatan ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <hr>

                    <h6>Item Distribusi:</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover table-sm align-middle">
                            <colgroup>
                                <col style="width:1%">
                                <col style="width:4%">
                                <col style="width:8%">
                                <col style="width:4%">
                                <col style="width:2%">
                                <col style="width:15%">
                            </colgroup>
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">No</th>
                                    <th class="text-center">ID Item</th>
                                    <th class="text-center">Produk</th>
                                    <th class="text-center">Jumlah</th>
                                    <th class="text-center">Kondisi</th>
                                    <th class="text-center">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($detail->itemDistribusis as $itemIndex => $item)
                                    <tr>
                                        <td class="text-center">{{ $itemIndex + 1 }}</td>
                                        <td class="text-center text-nowrap">{{ $item->id_item_distribusi }}</td>
                                        <td class="text-center text-nowrap">{{ $item->produk->nama_produk ?? '-' }}</td>
                                        <td class="text-center text-nowrap">{{ $item->jumlah }} {{ $item->satuan }}</td>
                                        <td class="text-center">
                                            @if($item->kondisi == 'baik')
                                                <span class="badge badge-success">Baik</span>
                                            @elseif($item->kondisi == 'rusak')
                                                <span class="badge badge-danger">Rusak</span>
                                            @else
                                                <span class="badge badge-warning">Kadaluarsa</span>
                                            @endif
                                        </td>
                                        <td>{{ $item->keterangan ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Tidak ada item</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endforeach
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