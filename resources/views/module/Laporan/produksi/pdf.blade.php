<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Produksi - {{ $periodeName }}</title>
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
        .header h2, .header h1 {
            margin: 5px 0;
            font-size: 18px;
        }
        .header p {
            margin: 3px 0;
            font-size: 11px;
        }

        /* Box ringkasan statistik (mirip laporan distribusi) */
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

        .text-center { text-align: center; }
        .text-right  { text-align: right;  }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }
        .badge-success { background-color: #28a745; color: white; }
        .badge-warning { background-color: #ffc107; color: #333; }
        .badge-danger  { background-color: #dc3545; color: white; }

        .produksi-section {
            margin: 15px 0;
            page-break-inside: avoid;
        }

        .detail-header, .sub-header {
            background-color: #e9ecef;
            padding: 5px;
            font-weight: bold;
            margin-bottom: 5px;
            text-align: left;
            color: #000 !important;
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

    {{-- <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #0b0b0b;
            font-size: 8pt;
            padding: 4px;
        }
        .table-produksi th {
            background-color: #34495e;
            color: #fff;
            text-align: center;
        }
        .table-produksi .sub-header {
            background-color: #2c3e50;
            font-weight: bold;
            text-align: center !important;
            text-transform: uppercase;
        }

        .table-produksi .meta-label {
            width: 15%;
            font-weight: bold;
        }
        .table-produksi .meta-value {
            width: 35%;
        }
        .produksi-section {
            margin-bottom: 15px;
            page-break-inside: avoid;
        }
        .detail-bahan {
            font-size: 7pt;
            line-height: 1.3;
        }
        .header {
            text-align: center;   
        }

    </style> --}}

    {{-- <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 9pt;
            line-height: 1.4;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        
        .header h1 {
            font-size: 16pt;
            margin-bottom: 5px;
        }
        
        .header h2 {
            font-size: 12pt;
            color: #666;
            font-weight: normal;
        }
        
        .info-section {
            margin-bottom: 15px;
        }
        
        .info-row {
            display: flex;
            margin-bottom: 3px;
        }
        
        .info-label {
            width: 150px;
            font-weight: bold;
        }
        
        .statistics {
            margin: 15px 0;
            padding: 10px;
            background-color: #f5f5f5;
            border: 1px solid #ddd;
        }
        
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
        }
        
        .stat-item {
            text-align: center;
        }
        
        .stat-value {
            font-size: 14pt;
            font-weight: bold;
            color: #2c3e50;
        }
        
        .stat-label {
            font-size: 8pt;
            color: #666;
            margin-top: 3px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        th {
            background-color: #34495e;
            color: white;
            padding: 6px 4px;
            text-align: center;
            font-size: 8pt;
            border: 1px solid #2c3e50;
        }
        
        td {
            padding: 5px 4px;
            border: 1px solid #ddd;
            font-size: 8pt;
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-nowrap { white-space: nowrap; }
        
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 7pt;
            font-weight: bold;
        }
        
        .badge-success {
            background-color: #27ae60;
            color: white;
        }
        
        .badge-warning {
            background-color: #f39c12;
            color: white;
        }
        
        .badge-danger {
            background-color: #e74c3c;
            color: white;
        }
        
        .badge-secondary {
            background-color: #95a5a6;
            color: white;
        }
        
        .produksi-section {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        
        .produksi-header {
            background-color: #ecf0f1;
            padding: 8px;
            margin-bottom: 10px;
            border-left: 4px solid #3498db;
        }
        
        .detail-bahan {
            font-size: 7pt;
            line-height: 1.3;
        }
        
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 8pt;
        }
        
        .page-break {
            page-break-after: always;
        }
    </style> --}}
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h2>LAPORAN PRODUKSI</h2>
        <p>MIP Jenang Mirah</p>
        <p>Periode: {{ $periodeName }}</p>
        <p>Dicetak pada: {{ now()->format('d-M-Y H:i:s') }}</p>
    </div>

    {{-- <div class="header">
        <h1>LAPORAN PRODUKSI</h1>
        <h2>MIP Jenang Mirah</h2>
        <div style="font-size: 10pt; margin-top: 5px;">
            Periode: <strong>{{ $periodeName }}</strong>
        </div>
        <div style="font-size: 8pt; color: #666; margin-top: 3px;">
            Dicetak: {{ date('d F Y H:i:s') }}
        </div>
    </div> --}}

    <!-- Ringkasan Statistik -->
    <div class="info-box">
        <table>
            <tr>
                <td style="width:30%;"><strong>Total Batch Produksi</strong></td>
                <td style="width:3%;">:</td>
                <td>{{ $totalProduksi }}</td>

                <td style="width:30%;"><strong>Total Produk Dihasilkan</strong></td>
                <td style="width:3%;">:</td>
                <td>{{ number_format($totalProdukDihasilkan, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td><strong>Total Target</strong></td>
                <td>:</td>
                <td>{{ number_format($totalTarget, 0, ',', '.') }}</td>

                <td><strong>Total Berhasil</strong></td>
                <td>:</td>
                <td>{{ number_format($totalBerhasil, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td><strong>Total Gagal</strong></td>
                <td>:</td>
                <td>{{ number_format($totalGagal, 0, ',', '.') }}</td>

                <td><strong>Persentase Keberhasilan</strong></td>
                <td>:</td>
                <td>{{ number_format($persentaseKeberhasilan, 2) }}%</td>
            </tr>
        </table>
    </div>

    {{-- <table class="table-produksi table-statistik" style="margin-bottom: 15px;">
        <tr>
            <th class="sub-header" colspan="4">RINGKASAN STATISTIK</th>
        </tr>
        <tr class="meta-row">
            <td class="text-center">
                <div class="stat-value">{{ $totalProduksi }}</div>
                <div class="stat-label">Total Batch Produksi</div>
            </td>
            <td class="text-center">
                <div class="stat-value">{{ $totalTarget }}</div>
                <div class="stat-label">Total Target</div>
            </td>
            <td class="text-center">
                <div class="stat-value">{{ $totalBerhasil }}</div>
                <div class="stat-label">Total Berhasil</div>
            </td>
            <td class="text-center">
                <div class="stat-value">{{ number_format($persentaseKeberhasilan, 2) }}%</div>
                <div class="stat-label">Persentase Keberhasilan</div>
            </td>
        </tr>
    </table> --}}

    {{-- <div class="statistics">
        <div style="font-weight: bold; margin-bottom: 10px; text-align: center;">RINGKASAN STATISTIK</div>
        <div class="stat-grid">
            <div class="stat-item">
                <div class="stat-value">{{ $totalProduksi }}</div>
                <div class="stat-label">Total Batch Produksi</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">{{ $totalTarget }}</div>
                <div class="stat-label">Total Target</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">{{ $totalBerhasil }}</div>
                <div class="stat-label">Total Berhasil</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">{{ number_format($persentaseKeberhasilan, 2) }}%</div>
                <div class="stat-label">Persentase Keberhasilan</div>
            </div>
        </div>
    </div> --}}

    <!-- Detail Produksi -->
    @foreach($produksiList as $produksi)
        <div class="produksi-section">
            @php
                $detailsForThisProduksi = array_filter($detailWithBahan, function($item) use ($produksi) {
                    return $item['produksi']->id_produksi === $produksi->id_produksi;
                });
            @endphp

            <table class="table-produksi">
                {{-- BARIS JUDUL BATCH --}}
                <tr>
                    <th class="sub-header" colspan="9">
                        ID Produksi: {{ $produksi->id_produksi }}
                        &nbsp; | &nbsp; Tanggal: {{ date('d-m-Y', strtotime($produksi->tanggal_produksi)) }}
                        &nbsp; | &nbsp; Operator: {{ $produksi->user->nama_user }}
                    </th>
                    {{-- <th class="sub-header" colspan="9">
                        Batch: {{ $produksi->kode_batch }}
                        &nbsp; | &nbsp; Tanggal: {{ date('d-m-Y', strtotime($produksi->tanggal_produksi)) }}
                        &nbsp; | &nbsp; Operator: {{ $produksi->user->nama_user }}
                    </th> --}}
                </tr>

                {{-- BARIS INFO WAKTU & TOTAL PRODUK --}}
                <tr>
                    <td class="meta-label">Waktu Mulai</td>
                    <td class="meta-value"><strong>{{ $produksi->waktu_mulai }}</strong></td>

                    <td class="meta-label">Waktu Selesai</td>
                    <td class="meta-value"><strong>{{ $produksi->waktu_selesai ?? '-' }}</strong></td>

                    <td class="meta-label">Total Produk</td>
                    <td class="meta-value"><strong>{{ $produksi->total_produk_dihasilkan }}</strong></td>

                    <td class="meta-label">Catatan</td>
                    <td class="meta-value" colspan="2">{{ $produksi->catatan ?? '-' }}</td>
                </tr>

                {{-- HEADER DETAIL --}}
                <tr>
                    <th style="width: 3%;">No</th>
                    <th style="width: 12%;">Resep</th>
                    <th style="width: 12%;">Produk</th>
                    <th style="width: 7%;">Target</th>
                    <th style="width: 7%;">Berhasil</th>
                    <th style="width: 7%;">Gagal</th>
                    <th style="width: 8%;">%</th>
                    <th style="width: 34%;">Kebutuhan Bahan</th>
                    <th style="width: 10%;">Keterangan</th>
                </tr>

                {{-- ISI DETAIL --}}
                @php $counter = 1; @endphp
                @foreach($detailsForThisProduksi as $item)
                    @php
                        $detail = $item['detail'];
                        $kebutuhanBahan = $item['kebutuhan_bahan'];
                    @endphp
                    <tr>
                        <td class="text-center">{{ $counter++ }}</td>
                        <td>{{ $detail->resep->nama_resep }}</td>
                        <td>{{ $detail->produk->nama_produk }}</td>
                        <td class="text-center">
                            {{ $detail->jumlah_target }} {{ $detail->resep->satuan_output }}
                        </td>
                        <td class="text-center">{{ $detail->jumlah_berhasil }}</td>
                        <td class="text-center">{{ $detail->jumlah_gagal }}</td>
                        <td class="text-center">
                            <span class="badge
                                {{ $detail->persentase_keberhasilan >= 80 ? 'badge-success'
                                    : ($detail->persentase_keberhasilan >= 50 ? 'badge-warning' : 'badge-danger') }}">
                                {{ number_format($detail->persentase_keberhasilan, 2) }}%
                            </span>
                        </td>
                        <td>
                            <div class="detail-bahan">
                                @foreach($kebutuhanBahan as $bahan)
                                    <strong>{{ $bahan['nama_bahan'] }}</strong>:
                                    {{ number_format($bahan['jumlah_dibutuhkan_stok'], 2) }}
                                    {{ $bahan['satuan_stok'] }}
                                    @if($bahan['keterangan'])
                                        <em>({{ $bahan['keterangan'] }})</em>
                                    @endif
                                    <br>
                                @endforeach
                            </div>
                        </td>
                        <td style="font-size: 7pt;">
                            {{ $detail->keterangan_gagal ?? '-' }}
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
    @endforeach

    {{-- @foreach($produksiList as $produksi)
    <div class="produksi-section">
        <div class="produksi-header">
            <strong>Batch: {{ $produksi->kode_batch }}</strong> | 
            Tanggal: {{ date('d-m-Y', strtotime($produksi->tanggal_produksi)) }} | 
            Operator: {{ $produksi->user->nama_user }}
        </div>
        
        <div class="info-section">
            <div style="display: flex; gap: 20px; margin-bottom: 8px;">
                <div>Waktu Mulai: <strong>{{ $produksi->waktu_mulai }}</strong></div>
                <div>Waktu Selesai: <strong>{{ $produksi->waktu_selesai ?? '-' }}</strong></div>
                <div>Total Produk: <strong>{{ $produksi->total_produk_dihasilkan }}</strong></div>
            </div>
            @if($produksi->catatan)
            <div>Catatan: {{ $produksi->catatan }}</div>
            @endif
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 3%;">No</th>
                    <th style="width: 12%;">Resep</th>
                    <th style="width: 12%;">Produk</th>
                    <th style="width: 7%;">Target</th>
                    <th style="width: 7%;">Berhasil</th>
                    <th style="width: 7%;">Gagal</th>
                    <th style="width: 8%;">%</th>
                    <th style="width: 34%;">Kebutuhan Bahan</th>
                    <th style="width: 10%;">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $detailsForThisProduksi = array_filter($detailWithBahan, function($item) use ($produksi) {
                        return $item['produksi']->id_produksi === $produksi->id_produksi;
                    });
                    $counter = 1;
                @endphp
                
                @foreach($detailsForThisProduksi as $item)
                    @php
                        $detail = $item['detail'];
                        $kebutuhanBahan = $item['kebutuhan_bahan'];
                    @endphp
                    <tr>
                        <td class="text-center">{{ $counter++ }}</td>
                        <td>{{ $detail->resep->nama_resep }}</td>
                        <td>{{ $detail->produk->nama_produk }}</td>
                        <td class="text-center">{{ $detail->jumlah_target }} {{ $detail->resep->satuan_output }}</td>
                        <td class="text-center">{{ $detail->jumlah_berhasil }}</td>
                        <td class="text-center">{{ $detail->jumlah_gagal }}</td>
                        <td class="text-center">
                            <span class="badge {{ $detail->persentase_keberhasilan >= 80 ? 'badge-success' : ($detail->persentase_keberhasilan >= 50 ? 'badge-warning' : 'badge-danger') }}">
                                {{ number_format($detail->persentase_keberhasilan, 2) }}%
                            </span>
                        </td>
                        <td>
                            <div class="detail-bahan">
                                @foreach($kebutuhanBahan as $bahan)
                                    <strong>{{ $bahan['nama_bahan'] }}</strong>: {{ number_format($bahan['jumlah_dibutuhkan_stok'], 2) }} {{ $bahan['satuan_stok'] }}
                                    @if($bahan['keterangan'])
                                        <em>({{ $bahan['keterangan'] }})</em>
                                    @endif
                                    <br>
                                @endforeach
                            </div>
                        </td>
                        <td style="font-size: 7pt;">{{ $detail->keterangan_gagal ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endforeach --}}

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
</body>
</html>