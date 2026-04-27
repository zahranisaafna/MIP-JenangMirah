<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Distribusi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h2 {
            margin: 5px 0;
            font-size: 18px;
        }
        .header p {
            margin: 3px 0;
            font-size: 11px;
        }
        .info-box {
            margin: 15px 0;
            padding: 10px;
            background: #f5f5f5;
            border: 1px solid #ddd;
        }
        .info-box table {
            width: 100%;
        }
        .info-box td {
            padding: 3px 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table, th, td {
            border: 1px solid #333;
        }
        th {
            background-color: #4CAF50;
            color: white;
            padding: 6px;
            font-size: 10px;
            text-align: center;
        }
        td {
            padding: 5px;
            font-size: 10px;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }
        .badge-info {
            background-color: #17a2b8;
            color: white;
        }
        .badge-warning {
            background-color: #ffc107;
            color: #333;
        }
        .badge-success {
            background-color: #28a745;
            color: white;
        }
        .badge-danger {
            background-color: #dc3545;
            color: white;
        }
        .detail-section {
            margin: 15px 0;
            page-break-inside: avoid;
        }
        .detail-header {
            background-color: #e9ecef;
            padding: 5px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .footer {
            margin-top: 30px;
            font-size: 10px;
            text-align: right;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
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

    <!-- Header -->
    <div class="header">
        <h2>LAPORAN DISTRIBUSI</h2>
        <p>Periode: {{ $periodText ?? 'Semua Data' }}</p>
        <p>Dicetak pada: {{ now('Asia/Jakarta')->format('d-M-Y H:i:s') }}</p>
    </div>

    <!-- Summary Box -->
    <div class="info-box">
        <table>
            <tr>
                <td style="width: 30%;"><strong>Total Distribusi</strong></td>
                <td style="width: 3%;">:</td>
                <td>{{ $totalDistribusi }}</td>
                <td style="width: 30%;"><strong>Total Lokasi Tujuan</strong></td>
                <td style="width: 3%;">:</td>
                <td>{{ $totalLokasi }}</td>
            </tr>
            <tr>
                <td><strong>Total Item Terdistribusi</strong></td>
                <td>:</td>
                <td colspan="4">{{ number_format($totalItem, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <!-- Data Distribusi -->
    @foreach($distribusi as $index => $item)
        <div class="detail-section">
            {{-- <div class="detail-header">
                Distribusi #{{ $index + 1 }} - {{ $item->id_distribusi }}
            </div> --}}

            {{-- <div class="detail-header">
                Distribusi #{{ $index + 1 }} - {{ $item->kode_distribusi }}
            </div> --}}
            
            <table style="margin-bottom: 10px;">
                <tr>
                    <td style="width: 20%; border-right: none;"><strong>ID Distribusi</strong></td>
                    <td style="width: 30%; border-left: none;">: {{ $item->id_distribusi }}</td>
                    <td style="width: 20%; border-right: none;"><strong>Tanggal</strong></td>
                    <td style="width: 30%; border-left: none;">: {{ tgl3($item->tanggal_distribusi) }}</td>
                </tr>
                <tr>
                    <td style="border-right: none;"><strong>Jenis Distribusi</strong></td>
                    <td style="border-left: none;">: 
                        @if($item->jenis_distribusi == 'internal')
                            <span class="badge badge-info">Internal</span>
                        @else
                            <span class="badge badge-warning">Eksternal</span>
                        @endif
                    </td>
                    <td style="border-right: none;"><strong>Status</strong></td>
                    <td style="border-left: none;">: <span class="badge badge-success">Selesai</span></td>
                </tr>
                <tr>
                    <td style="border-right: none;"><strong>Keterangan</strong></td>
                    <td colspan="3" style="border-left: none;">: {{ $item->keterangan ?? '-' }}</td>
                </tr>
            </table>

            <!-- Detail per Lokasi -->
            @foreach($item->distribusiDetails as $detailIndex => $detail)
                <div style="margin-bottom: 10px;">
                    <strong>Tujuan {{ $detailIndex + 1 }}: {{ $detail->lokasi->nama_lokasi ?? '-' }}</strong>
                    
                    <table style="margin-bottom: 10px;">
                        <tr>
                            <td style="width: 20%; border-right: none;"><strong>ID Detail</strong></td>
                            <td style="width: 30%; border-left: none;">: {{ $detail->id_distribusi_detail }}</td>
                            <td style="width: 20%; border-right: none;"><strong>Jenis Lokasi</strong></td>
                            <td style="width: 30%; border-left: none;">: 
                                @if($detail->lokasi->jenis_lokasi == 'gudang')
                                    <span class="badge badge-info">Gudang</span>
                                @else
                                    <span class="badge badge-success">Toko</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td style="border-right: none;"><strong>Tanggal Detail</strong></td>
                            <td style="border-left: none;">: {{ tglJam($detail->tanggal_detail) }}</td>
                            <td style="border-right: none;"><strong>User</strong></td>
                            <td style="border-left: none;">: {{ $detail->user->nama_user ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td style="border-right: none;"><strong>Status Detail</strong></td>
                            <td style="border-left: none;">: 
                                @if($detail->status_detail == 'pending')
                                    <span class="badge badge-warning">Pending</span>
                                @else
                                    <span class="badge badge-success">Diterima</span>
                                @endif
                            </td>
                            <td style="border-right: none;"><strong>Nama Penerima</strong></td>
                            <td style="border-left: none;">: {{ $detail->nama_penerima }}</td>
                        </tr>
                        <tr>
                            <td style="border-right: none;"><strong>Catatan</strong></td>
                            <td colspan="3" style="border-left: none;">: {{ $detail->catatan ?? '-' }}</td>
                        </tr>
                    </table>

                    <!-- Item Distribusi -->
                    <table style="margin-bottom: 10px;">
                        <thead>
                            <tr>
                                <th style="width: 5%;">No</th>
                                <th style="width: 15%;">ID Item</th>
                                <th style="width: 30%;">Produk</th>
                                <th style="width: 15%;">Jumlah</th>
                                <th style="width: 15%;">Kondisi</th>
                                <th style="width: 20%;">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($detail->itemDistribusis as $itemIndex => $produkItem)
                                <tr>
                                    <td class="text-center">{{ $itemIndex + 1 }}</td>
                                    <td class="text-center">{{ $produkItem->id_item_distribusi }}</td>
                                    <td>{{ $produkItem->produk->nama_produk ?? '-' }}</td>
                                    <td class="text-center">{{ $produkItem->jumlah }} {{ $produkItem->satuan }}</td>
                                    <td class="text-center">
                                        @if($produkItem->kondisi == 'baik')
                                            <span class="badge badge-success">Baik</span>
                                        @elseif($produkItem->kondisi == 'rusak')
                                            <span class="badge badge-danger">Rusak</span>
                                        @else
                                            <span class="badge badge-warning">Kadaluarsa</span>
                                        @endif
                                    </td>
                                    <td>{{ $produkItem->keterangan ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endforeach
        </div>

        @if(!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach

    @if($distribusi->isEmpty())
        <p class="text-center" style="margin-top: 20px;">Tidak ada data distribusi untuk periode ini.</p>
    @endif

    <!-- Footer -->
    <div class="footer">
        <div style="margin-top: 40px;">
            <div>Dicetak oleh: <strong>{{ auth()->user()->nama_user }}</strong></div>
            {{-- <div style="margin-top: 30px; margin-right: 50px;">
                <div style="border-top: 1px solid #333; width: 150px; display: inline-block;"></div>
                <div>Tanda Tangan & Cap</div>
            </div> --}}
        </div>
    </div>
    {{-- <div class="footer">
        <p>Laporan ini digenerate secara otomatis oleh sistem</p>
    </div> --}}
</body>
</html>