@extends('layouts.master')
@section('title','Detail Komposisi Produk')
@section('content')

<div class="container-fluid">
    <!-- Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h3 class="mb-sm-0">Detail Komposisi Produk</h3>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Beranda</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('resep.index') }}">Komposisi Produk</a></li>
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
            <!-- Info Resep -->
            <div class="card mb-2">
                <div class="card-header py-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Data identitas komposisi dan detail parameter produksi untuk produk ini</h5>
                        <div>
                            <a href="{{ route('resep.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                            {{-- @if(auth()->user()->role == 'admin') --}}
                            {{-- <a href="{{ route('resep.edit', $resep->id_resep) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a> --}}
                            {{-- @endif --}}
                        </div>
                    </div>
                </div>
                <div class="card-body py-2">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm mb-0">
                                <tr>
                                    <th class="font-weight-normal" style="width:170px;">ID Resep</th>
                                    <td>: {{ $resep->id_resep }}</td>
                                </tr>
                                <tr>
                                    <th class="font-weight-normal">Nama Komposisi Produk</th>
                                    <td>: {{ $resep->nama_resep }}</td>
                                </tr>
                                <tr>
                                    <th class="font-weight-normal">Waktu Produksi</th>
                                    <td>: {{ $resep->waktu_produksi }} menit</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm mb-0">
                                <tr>
                                    <th class="font-weight-normal" style="width:170px;">Kapasitas Produksi</th>
                                    <td>: {{ $resep->kapasitas_produksi }} {{ $resep->satuan_output }}</td>
                                </tr>
                                <tr>
                                    <th class="font-weight-normal">Status</th>
                                    <td>: 
                                        @if($resep->status == 'aktif')
                                            <span class="badge badge-success">Aktif</span>
                                        @else
                                            <span class="badge badge-secondary">Non Aktif</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="font-weight-normal">Catatan</th>
                                    <td>: {{ $resep->catatan ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Komposisi Bahan Baku -->
            <div class="card">
                <div class="card-header py-2">
                    <h5 class="mb-0">Daftar bahan baku dan jumlah yang dibutuhkan per satu batch produksi</h5>
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

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover table-sm align-middle">
                            <colgroup>
                                <col style="width:2%">
                                {{-- <col style="width:10%"> --}}
                                <col style="width:3%">
                                <col style="width:5%">
                                <col style="width:3%">
                                <col style="width:2%">
                                <col style="width:45%">
                            </colgroup>
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">No</th>
                                    {{-- <th class="text-center">ID Komposisi</th> --}}
                                    <th class="text-center">ID Bahan</th>
                                    <th class="text-center">Nama Bahan</th>
                                    <th class="text-center">Jumlah Diperlukan</th>
                                    <th class="text-center">Satuan</th>
                                    <th class="text-center">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($komposisiReseps as $key => $komposisi)
                                    <tr>
                                        <td class="text-center">{{ $key + 1 }}</td>
                                        {{-- <td class="text-center text-nowrap">{{ $komposisi->id_komposisi }}</td> --}}
                                        <td class="text-center text-nowrap">{{ $komposisi->id_bahan_baku }}</td>
                                        <td class="text-nowrap">{{ $komposisi->nama_bahan }}</td>
                                        <td class="text-center text-nowrap">
                                            {{ rtrim(rtrim(number_format($komposisi->jumlah_diperlukan, 2, ',', '.'), '0'), ',') }}
                                        </td>
                                        {{-- <td class="text-center text-nowrap">{{ number_format($komposisi->jumlah_diperlukan) }}</td> --}}
                                        <td class="text-center">{{ $komposisi->satuan }}</td>
                                        <td>
                                            <small>{{ $komposisi->keterangan ?? '-' }}</small>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Belum ada komposisi bahan baku</td>
                                    </tr>
                                @endforelse
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