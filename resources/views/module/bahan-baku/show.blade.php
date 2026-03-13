@extends('layouts.master')
@section('title','Detail Bahan Baku')
@section('content')

<div class="container-fluid">
    <!-- Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h3 class="mb-sm-0">Detail Bahan Baku</h3>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Beranda</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('bahan-baku.index') }}">Bahan Baku</a></li>
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

        // Fungsi konversi satuan ke satuan dasar
        // function konversiKeKg($jumlah, $satuan, $deskripsi) {
        //     $konversi = [
        //         'kg' => 1,
        //         'gram' => 0.001,
        //         'liter' => 1,
        //         'pcs' => 1,
        //         'butir' => 1,
        //     ];

        //     // Jika sudah dalam satuan dasar
        //     if (isset($konversi[strtolower($satuan)])) {
        //         return $jumlah * $konversi[strtolower($satuan)];
        //     }

        //     // Parse deskripsi untuk konversi satuan kemasan
        //     if (empty($deskripsi)) {
        //         return $jumlah;
        //     }

        //     // Format: "1 karton = 20 pcs = 10000 gram = 10 kg"
        //     preg_match_all(
        //         '/1\s*' . preg_quote($satuan, '/') . '\s*=\s*(\d+(?:\.\d+)?)\s*(kg|liter|pcs|gram|butir)/i',
        //         $deskripsi,
        //         $allMatches,
        //         PREG_SET_ORDER
        //     );

        //     if (!$allMatches) {
        //         return $jumlah;
        //     }

        //     // Prioritas: kg > gram > liter > pcs > butir
        //     $prioritas = ['kg' => 1, 'gram' => 2, 'liter' => 3, 'pcs' => 4, 'butir' => 5];

        //     usort($allMatches, function ($a, $b) use ($prioritas) {
        //         $sa = strtolower($a[2]);
        //         $sb = strtolower($b[2]);
        //         return ($prioritas[$sa] ?? 99) <=> ($prioritas[$sb] ?? 99);
        //     });

        //     $pilihan = $allMatches[0];
        //     $nilaiKonversi = (float) $pilihan[1];
        //     $satuanTujuan  = strtolower($pilihan[2]);

        //     return $jumlah * $nilaiKonversi * ($konversi[$satuanTujuan] ?? 1);
        // }

        // Fungsi untuk mendapatkan satuan konversi untuk ditampilkan
        // function getSatuanKonversi($satuan, $deskripsi) {
        //     $satuanDasar = ['kg', 'gram', 'liter', 'pcs', 'butir'];
            
        //     if (in_array(strtolower($satuan), $satuanDasar)) {
        //         return null;
        //     }

        //     if (empty($deskripsi)) {
        //         return null;
        //     }

        //     // Cari konversi pertama yang ditemukan
        //     if (preg_match('/1\s*' . preg_quote($satuan, '/') . '\s*=\s*(\d+(?:\.\d+)?)\s*(kg|liter|pcs|gram|butir)/i', $deskripsi, $matches)) {
        //         return '1 ' . $satuan . ' = ' . $matches[1] . ' ' . $matches[2];
        //     }

        //     return null;
        // }
    @endphp

    <section class="content">
        <div class="container-fluid">
            <!-- Info Bahan Baku -->
            <div class="card mb-2">
                <div class="card-header py-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Menampilkan rincian bahan baku beserta data stok</h5>
                        <div>
                            <a href="{{ route('bahan-baku.index') }}" class="btn btn-secondary btn-sm">
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
                                    <th class="font-weight-normal" style="width:170px;">ID Bahan Baku</th>
                                    <td>: {{ $bahanBaku->id_bahan_baku }}</td>
                                </tr>
                                <tr>
                                    <th class="font-weight-normal">Nama Bahan</th>
                                    <td>: {{ $bahanBaku->nama_bahan }}</td>
                                </tr>
                                <tr>
                                    <th class="font-weight-normal">Kategori</th>
                                    <td>: {{ $bahanBaku->kategori }}</td>
                                </tr>
                                <tr>
                                    <th class="font-weight-normal">Satuan</th>
                                    <td>: {{ $bahanBaku->satuan }}</td>
                                </tr>
                                {{-- @if(getSatuanKonversi($bahanBaku->satuan, $bahanBaku->deskripsi))
                                <tr>
                                    <th class="font-weight-normal">Konversi Satuan</th>
                                    <td>: <span class="badge badge-info">{{ getSatuanKonversi($bahanBaku->satuan, $bahanBaku->deskripsi) }}</span></td>
                                </tr>
                                @endif --}}
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm mb-0">
                                <tr>
                                    <th class="font-weight-normal" style="width:170px;">Stok Minimum</th>
                                    <td>: {{ number_format($bahanBaku->stok_minimum) }} {{ $bahanBaku->satuan }}</td>
                                </tr>
                                <tr>
                                    <th class="font-weight-normal">Stok Saat Ini</th>
                                    <td>: 
                                        <span class="font-weight-bold {{ $bahanBaku->stok_saat_ini < $bahanBaku->stok_minimum ? 'text-danger' : 'text-success' }}">
                                            {{ number_format($bahanBaku->stok_saat_ini) }} {{ $bahanBaku->satuan }}
                                        </span>
                                        @if($bahanBaku->stok_saat_ini < $bahanBaku->stok_minimum)
                                            <span class="badge badge-danger ml-2">Stok Menipis</span>
                                        @else
                                            <span class="badge badge-success ml-2">Stok Aman</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="font-weight-normal">Harga Rata-rata (Rp)</th>
                                    <td>:{{ number_format($bahanBaku->harga_rata_rata, 0, '.', ',') }}.-</td>
                                </tr>
                                <tr>
                                    <th class="font-weight-normal">Tanggal Kadaluarsa</th>
                                    <td>: {{ $bahanBaku->tanggal_kadaluarsa ? tgl3($bahanBaku->tanggal_kadaluarsa) : '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    @if($bahanBaku->deskripsi)
                    <div class="row mt-2">
                        <div class="col-12">
                            <table class="table table-borderless table-sm mb-0">
                                <tr>
                                    <th class="font-weight-normal" style="width:170px;">Deskripsi</th>
                                    <td>: {{ $bahanBaku->deskripsi }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Riwayat Penggunaan Bahan Baku -->
            <div class="card">
                <div class="card-header py-2">
                    <h5 class="mb-0">Riwayat Penggunaan Bahan Baku dalam Produksi</h5>
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
                                <col style="width:3%">
                                <col style="width:10%">
                                <col style="width:10%">
                                <col style="width:8%">
                                <col style="width:15%">
                                <col style="width:15%">
                                <col style="width:10%">
                                <col style="width:10%">
                                <col style="width:10%">
                                <col style="width:9%">
                            </colgroup>
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">No</th>
                                    <th class="text-center">ID Produksi</th>
                                    {{-- <th class="text-center">Kode Batch</th> --}}
                                    <th class="text-center">Tanggal Produksi</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Resep</th>
                                    <th class="text-center">Produk</th>
                                    <th class="text-center">Jumlah Digunakan (Resep)</th>
                                    <th class="text-center">Jumlah Digunakan (Stok)</th>
                                    <th class="text-center">Target Produksi</th>
                                    <th class="text-center">User</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($riwayatPenggunaan as $key => $item)
                                @php
                                    $produksi  = $item['produksi'];
                                    $detail    = $item['detail'];
                                    $komposisi = $item['komposisi'];
                                    $resep     = $detail->resep;
                                    
                                    // Hitung multiplier
                                    $multiplier = $detail->jumlah_target / $resep->kapasitas_produksi;
                                    
                                    // Jumlah yang diperlukan dalam satuan resep (satuan resep = satuan stok)
                                    $jumlahResep = $komposisi->jumlah_diperlukan * $multiplier;

                                    // Karena satuan di sistem sekarang sudah diseragamkan,
                                    // jumlah yang dipakai di stok sama dengan jumlah di resep
                                    $jumlahStok = $jumlahResep;
                                @endphp

                                    {{-- @php
                                        $produksi = $item['produksi'];
                                        $detail = $item['detail'];
                                        $komposisi = $item['komposisi'];
                                        $resep = $detail->resep;
                                        
                                        // Hitung multiplier
                                        $multiplier = $detail->jumlah_target / $resep->kapasitas_produksi;
                                        
                                        // Jumlah yang diperlukan dalam satuan resep
                                        $jumlahResep = $komposisi->jumlah_diperlukan * $multiplier;
                                        
                                        // Konversi ke satuan dasar
                                        $jumlahDasar = konversiKeKg(
                                            $jumlahResep,
                                            $komposisi->satuan,
                                            ''
                                        );
                                        
                                        // Konversi satuan stok bahan baku ke satuan dasar
                                        $nilaiPerSatuanStok = konversiKeKg(
                                            1,
                                            $bahanBaku->satuan,
                                            $bahanBaku->deskripsi
                                        );
                                        
                                        // Hitung berapa satuan stok yang dibutuhkan
                                        $jumlahStok = $nilaiPerSatuanStok > 0 
                                            ? $jumlahDasar / $nilaiPerSatuanStok 
                                            : $jumlahDasar;
                                    @endphp --}}
                                    <tr>
                                        <td class="text-center">{{ $key + 1 }}</td>
                                        {{-- <td class="text-center">
                                            <a href="{{ route('produksi.show', $produksi->id_produksi) }}" class="text-primary">
                                                {{ $produksi->kode_batch }}
                                            </a>
                                            {{ $produksi->kode_batch }}
                                        </td> --}}
                                        <td class="text-center"> {{ $produksi->id_produksi }}
                                            {{-- <a href="{{ route('produksi.show', $produksi->id_produksi) }}" class="text-primary">
                                                {{ $produksi->id_produksi }}
                                            </a> --}}
                                        </td>
                                        <td class="text-center text-nowrap">{{ tgl3($produksi->tanggal_produksi) }}</td>
                                        <td class="text-center">
                                            @if($produksi->status == 'selesai')
                                                <span class="badge badge-success">Selesai</span>
                                            @elseif($produksi->status == 'proses')
                                                <span class="badge badge-info">Proses</span>
                                            @elseif($produksi->status == 'pending')
                                                <span class="badge badge-warning">Pending</span>
                                            @else
                                                <span class="badge badge-danger">Gagal</span>
                                            @endif
                                        </td>
                                        <td>{{ $resep->nama_resep }}</td>
                                        <td>{{ $detail->produk->nama_produk }}</td>
                                        <td class="text-center text-nowrap">
                                            {{ number_format($jumlahResep, 2) }} {{ $komposisi->satuan }}
                                        </td>
                                        <td class="text-center text-nowrap">
                                            <span class="font-weight-bold text-danger">
                                                {{ number_format($jumlahStok, 2) }} {{ $bahanBaku->satuan }}
                                            </span>
                                        </td>
                                        <td class="text-center text-nowrap">
                                            {{ number_format($detail->jumlah_target) }} {{ $resep->satuan_output }}
                                        </td>
                                        <td class="text-center text-nowrap">{{ $produksi->user->nama_user }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center">Belum ada riwayat penggunaan bahan baku ini dalam produksi</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if(count($riwayatPenggunaan) > 0)
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="7" class="text-right">Total Penggunaan:</th>
                                    <th class="text-center text-nowrap">
                                        @php
                                            $totalPenggunaan = 0;
                                            foreach($riwayatPenggunaan as $item) {
                                                $detail    = $item['detail'];
                                                $komposisi = $item['komposisi'];
                                                $resep     = $detail->resep;

                                                $multiplier   = $detail->jumlah_target / $resep->kapasitas_produksi;
                                                $jumlahResep  = $komposisi->jumlah_diperlukan * $multiplier;

                                                $totalPenggunaan += $jumlahResep; // langsung
                                            }
                                        @endphp
                                        <span class="font-weight-bold text-danger">
                                            {{ number_format($totalPenggunaan, 2) }} {{ $bahanBaku->satuan }}
                                        </span>

                                        {{-- @php
                                            $totalPenggunaan = 0;
                                            foreach($riwayatPenggunaan as $item) {
                                                $produksi  = $item['produksi'];
                                                $detail    = $item['detail'];
                                                $komposisi = $item['komposisi'];
                                                $resep     = $detail->resep;

                                                multiplier tetap sama
                                                $multiplier   = $detail->jumlah_target / $resep->kapasitas_produksi;

                                                jumlah yang dipakai sesuai resep, satuannya sama dengan stok
                                                $jumlahResep  = $komposisi->jumlah_diperlukan * $multiplier;
                                                $jumlahStok   = $jumlahResep; // karena satuan sudah seragam
                                                $totalPenggunaan += $jumlahStok;
                                                $detail = $item['detail'];
                                                $komposisi = $item['komposisi'];
                                                $resep = $detail->resep;
                                                
                                                $multiplier = $detail->jumlah_target / $resep->kapasitas_produksi;
                                                $jumlahResep = $komposisi->jumlah_diperlukan * $multiplier;
                                                
                                                $jumlahDasar = konversiKeKg($jumlahResep, $komposisi->satuan, '');
                                                $nilaiPerSatuanStok = konversiKeKg(1, $bahanBaku->satuan, $bahanBaku->deskripsi);
                                                $jumlahStok = $nilaiPerSatuanStok > 0 ? $jumlahDasar / $nilaiPerSatuanStok : $jumlahDasar;
                                                
                                                $totalPenggunaan += $jumlahStok;
                                            }
                                        @endphp --}}
                                        {{-- <span class="font-weight-bold text-danger">
                                            {{ number_format($totalPenggunaan, 2) }} {{ $bahanBaku->satuan }}
                                        </span> --}}
                                    </th>
                                    <th colspan="2"></th>
                                </tr>
                            </tfoot>
                            @endif
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
  .badge { font-size: 85%; }
</style>

@endsection