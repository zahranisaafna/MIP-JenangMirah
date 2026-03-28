<?php

namespace App\Http\Controllers;

use App\Models\Produksi;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class LaporanProduksiController extends Controller
{
    /**
     * Tampilkan halaman laporan produksi
     */
    public function index(Request $request)
    {
        $allowed = [20, 40, 60, 80, 100, 250, 500];
        $perPage = (int) request('per_page', 20);
        
        if (!in_array($perPage, $allowed)) {
            $perPage = 20;
        }

        $query = Produksi::with(['user', 'detailProduksis.resep', 'detailProduksis.produk'])
            ->where('status', 'selesai'); // Hanya tampilkan produksi yang sudah selesai

        $filterType = $request->input('periode', 'custom');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        switch ($filterType) {
            case 'harian':
                $date = now()->format('Y-m-d');
                $query->whereDate('tanggal_produksi', $date);
                $periodeName = 'Hari ini: ' . Carbon::parse($date)->format('d-M-Y');
                break;

            case 'bulanan':
                $month = now()->format('Y-m');
                $query->whereYear('tanggal_produksi', Carbon::parse($month)->year)
                    ->whereMonth('tanggal_produksi', Carbon::parse($month)->month);
                $periodeName = 'Bulan ini: ' . Carbon::parse($month)->format('F Y');
                break;

            case 'tahunan':
                $year = now()->year;
                $query->whereYear('tanggal_produksi', $year);
                $periodeName = 'Tahun ini: ' . $year;
                break;

            case 'custom':
            default:
                if ($startDate && $endDate) {
                    $query->whereBetween('tanggal_produksi', [$startDate, $endDate]);
                    $periodeName = 'Custom: ' . Carbon::parse($startDate)->format('d-M-Y') . ' s/d ' . Carbon::parse($endDate)->format('d-M-Y');
                } elseif ($startDate) {
                    $query->where('tanggal_produksi', '>=', $startDate);
                    $periodeName = 'Custom: Dari ' . Carbon::parse($startDate)->format('d-M-Y');
                } elseif ($endDate) {
                    $query->where('tanggal_produksi', '<=', $endDate);
                    $periodeName = 'Custom: Sampai ' . Carbon::parse($endDate)->format('d-M-Y');
                } else {
                    $query->whereMonth('tanggal_produksi', now()->month)
                        ->whereYear('tanggal_produksi', now()->year);
                    $periodeName = 'Bulan ini: ' . now()->format('F Y');
                }
                break;
        }
        // Filter berdasarkan periode preset (harian, bulanan, tahunan)
        // if (request('periode')) {
        //     $today = now();
        //     switch (request('periode')) {
        //         case 'harian':
        //             $query->whereDate('tanggal_produksi', $today->toDateString());
        //             break;
        //         case 'bulanan':
        //             $query->whereYear('tanggal_produksi', $today->year)
        //                   ->whereMonth('tanggal_produksi', $today->month);
        //             break;
        //         case 'tahunan':
        //             $query->whereYear('tanggal_produksi', $today->year);
        //             break;
        //     }
        // }

        // // Filter berdasarkan tanggal start
        // if (request('start_date')) {
        //     $query->whereDate('tanggal_produksi', '>=', request('start_date'));
        // }

        // // Filter berdasarkan tanggal end
        // if (request('end_date')) {
        //     $query->whereDate('tanggal_produksi', '<=', request('end_date'));
        // }

        $produksi = $query->orderBy('tanggal_produksi', 'desc')
            ->paginate($perPage)
            ->appends(request()->except('page'));

        // Hitung statistik untuk data yang difilter
        $allFiltered = (clone $query)->get();
        $totalProduksi = $allFiltered->count();
        $totalProdukDihasilkan = $allFiltered->sum('total_produk_dihasilkan');
        $totalTarget = 0;
        $totalBerhasil = 0;
        $totalGagal = 0;

        foreach ($allFiltered as $item) {
            $totalTarget += $item->detailProduksis->sum('jumlah_target');
            $totalBerhasil += $item->detailProduksis->sum('jumlah_berhasil');
            $totalGagal += $item->detailProduksis->sum('jumlah_gagal');
        }

        $persentaseKeberhasilan = $totalTarget > 0 ? ($totalBerhasil / $totalTarget) * 100 : 0;

        return view('module.laporan.produksi.index', compact(
            'produksi',
            'allowed',
            'perPage',
            'periodeName',
            'totalProduksi',
            'totalProdukDihasilkan',
            'totalTarget',
            'totalBerhasil',
            'totalGagal',
            'persentaseKeberhasilan'
        ));
    }

    /**
     * Download PDF laporan produksi
     */
    public function downloadPdf(Request $request)
    {
        $query = Produksi::with(['user', 'detailProduksis.resep.komposisiReseps.bahanBaku', 'detailProduksis.produk'])
            ->where('status', 'selesai');
        $filterType = $request->input('periode', 'custom');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        switch ($filterType) {
            case 'harian':
                $date = now()->format('Y-m-d');
                $query->whereDate('tanggal_produksi', $date);
                $periodeName = 'Hari ini: ' . Carbon::parse($date)->format('d-M-Y');
                break;

            case 'bulanan':
                $month = now()->format('Y-m');
                $query->whereYear('tanggal_produksi', Carbon::parse($month)->year)
                    ->whereMonth('tanggal_produksi', Carbon::parse($month)->month);
                $periodeName = 'Bulan ini: ' . Carbon::parse($month)->format('F Y');
                break;

            case 'tahunan':
                $year = now()->year;
                $query->whereYear('tanggal_produksi', $year);
                $periodeName = 'Tahun ini: ' . $year;
                break;

            case 'custom':
            default:
                if ($startDate && $endDate) {
                    $query->whereBetween('tanggal_produksi', [$startDate, $endDate]);
                    $periodeName = 'Custom: ' . Carbon::parse($startDate)->format('d-M-Y') . ' s/d ' . Carbon::parse($endDate)->format('d-M-Y');
                } elseif ($startDate) {
                    $query->where('tanggal_produksi', '>=', $startDate);
                    $periodeName = 'Custom: Dari ' . Carbon::parse($startDate)->format('d-M-Y');
                } elseif ($endDate) {
                    $query->where('tanggal_produksi', '<=', $endDate);
                    $periodeName = 'Custom: Sampai ' . Carbon::parse($endDate)->format('d-M-Y');
                } else {
                    $query->whereMonth('tanggal_produksi', now()->month)
                        ->whereYear('tanggal_produksi', now()->year);
                    $periodeName = 'Bulan ini: ' . now()->format('F Y');
                }
                break;
        }
        // Tentukan periode untuk nama file dan header PDF
        // $periodeName = 'Semua Periode';
        
        // // Filter berdasarkan periode preset
        // if (request('periode')) {
        //     $today = now();
        //     switch (request('periode')) {
        //         case 'harian':
        //             $query->whereDate('tanggal_produksi', $today->toDateString());
        //             $periodeName = 'Hari Ini (' . $today->format('d F Y') . ')';
        //             break;
        //         case 'bulanan':
        //             $query->whereYear('tanggal_produksi', $today->year)
        //                   ->whereMonth('tanggal_produksi', $today->month);
        //             $periodeName = 'Bulan ' . $today->format('F Y');
        //             break;
        //         case 'tahunan':
        //             $query->whereYear('tanggal_produksi', $today->year);
        //             $periodeName = 'Tahun ' . $today->year;
        //             break;
        //     }
        // }

        // // Filter berdasarkan tanggal start
        // if (request('start_date')) {
        //     $query->whereDate('tanggal_produksi', '>=', request('start_date'));
        // }

        // // Filter berdasarkan tanggal end
        // if (request('end_date')) {
        //     $query->whereDate('tanggal_produksi', '<=', request('end_date'));
        // }

        // // Jika ada custom range, update nama periode
        // if (request('start_date') && request('end_date')) {
        //     $periodeName = Carbon::parse(request('start_date'))->format('d F Y') . ' - ' . 
        //                   Carbon::parse(request('end_date'))->format('d F Y');
        // } elseif (request('start_date')) {
        //     $periodeName = 'Dari ' . Carbon::parse(request('start_date'))->format('d F Y');
        // } elseif (request('end_date')) {
        //     $periodeName = 'Sampai ' . Carbon::parse(request('end_date'))->format('d F Y');
        // }

        $produksiList = $query->orderBy('tanggal_produksi', 'desc')->get();

        // Hitung statistik
        $totalProduksi = $produksiList->count();
        $totalProdukDihasilkan = $produksiList->sum('total_produk_dihasilkan');
        $totalTarget = 0;
        $totalBerhasil = 0;
        $totalGagal = 0;

        foreach ($produksiList as $item) {
            $totalTarget += $item->detailProduksis->sum('jumlah_target');
            $totalBerhasil += $item->detailProduksis->sum('jumlah_berhasil');
            $totalGagal += $item->detailProduksis->sum('jumlah_gagal');
        }

        $persentaseKeberhasilan = $totalTarget > 0 ? ($totalBerhasil / $totalTarget) * 100 : 0;

        // Hitung kebutuhan bahan untuk setiap detail
        $detailWithBahan = [];
        foreach ($produksiList as $produksi) {
            foreach ($produksi->detailProduksis as $detail) {
                $multiplier = $detail->jumlah_target / $detail->resep->kapasitas_produksi;
                $kebutuhan = $this->hitungKebutuhanBahanBaku($detail->id_resep, $multiplier);
                
                $detailWithBahan[] = [
                    'produksi' => $produksi,
                    'detail' => $detail,
                    'kebutuhan_bahan' => $kebutuhan,
                ];
            }
        }

        $pdf = Pdf::loadView('module.laporan.produksi.pdf', compact(
            'produksiList',
            'detailWithBahan',
            'periodeName',
            'totalProduksi',
            'totalProdukDihasilkan',
            'totalTarget',
            'totalBerhasil',
            'totalGagal',
            'persentaseKeberhasilan'
        ));

        $pdf->setPaper('a4', 'portrait');
        // Tentukan nama file
        if (request('start_date') && request('end_date')) {
            $filename = 'produksi-' . request('start_date') . '_to_' . request('end_date') . '.pdf';
        }
        elseif (request('start_date')) {
            $filename = 'produksi-dari-' . request('start_date') . '.pdf';
        }
        elseif (request('end_date')) {
            $filename = 'produksi-sampai-' . request('end_date') . '.pdf';
        }
        elseif (request('periode') == 'harian') {
            $filename = 'produksi-harian-' . now()->format('Y-m-d') . '.pdf';
        }
        elseif (request('periode') == 'bulanan') {
            $filename = 'produksi-bulanan-' . now()->format('Y-m') . '.pdf';
        }
        elseif (request('periode') == 'tahunan') {
            $filename = 'produksi-tahunan-' . now()->format('Y') . '.pdf';
        }
        else {
            $filename = 'produksi-semua-periode.pdf';
        }

        // $filename = 'Laporan_Produksi_' . str_replace([' ', '(', ')'], '_', $periodeName) . '_' . date('YmdHis') . '.pdf';

        // return $pdf->download($filename);
        $filename = 'Laporan-Produksi-' . now('Asia/Jakarta')->format('d-m-Y_H-i-s') . '.pdf';
        return $pdf->stream($filename);
    }

    /**
     * Hitung kebutuhan bahan baku (sama seperti di ProduksiController)
     */
    private function hitungKebutuhanBahanBaku($idResep, $multiplier = 1)
    {
        $resep = \App\Models\Resep::with('komposisiReseps.bahanBaku')->find($idResep);
        
        if (!$resep) {
            return [];
        }

        $kebutuhan = [];

        foreach ($resep->komposisiReseps as $item) {
            $bahanBaku = $item->bahanBaku;

            $jumlahDiperlukanResep = $item->jumlah_diperlukan * $multiplier;

            $kebutuhan[] = [
                'id_bahan_baku'           => $bahanBaku->id_bahan_baku,
                'nama_bahan'              => $bahanBaku->nama_bahan,
                'jumlah_diperlukan_resep' => $jumlahDiperlukanResep,
                'satuan_resep'            => $item->satuan,
                'jumlah_dibutuhkan_stok'  => $jumlahDiperlukanResep, // langsung
                'satuan_stok'             => $bahanBaku->satuan,
                'keterangan'              => $item->keterangan,
            ];
        }

        return $kebutuhan;
    }

    // private function konversiSatuanBahanBaku($jumlah, $satuanDari, $deskripsi)
    // {
    //     $konversi = [
    //         'kg'    => 1,
    //         'gram'  => 0.001,
    //         'liter' => 1,
    //         'pcs'   => 1,
    //         'butir' => 1,
    //     ];

    //     if (isset($konversi[strtolower($satuanDari)])) {
    //         return $jumlah * $konversi[strtolower($satuanDari)];
    //     }

    //     if (empty($deskripsi)) {
    //         return $jumlah;
    //     }

    //     preg_match_all(
    //         '/1\s*' . preg_quote($satuanDari, '/') . '\s*=\s*(\d+(?:\.\d+)?)\s*(kg|liter|pcs|gram|butir)/i',
    //         $deskripsi,
    //         $allMatches,
    //         PREG_SET_ORDER
    //     );

    //     if (!$allMatches) {
    //         return $jumlah;
    //     }

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

    // private function hitungKebutuhanBahanBaku($idResep, $multiplier = 1)
    // {
    //     $resep = \App\Models\Resep::with('komposisiReseps.bahanBaku')->find($idResep);
        
    //     if (!$resep) {
    //         return [];
    //     }

    //     $kebutuhan = [];

    //     foreach ($resep->komposisiReseps as $item) {
    //         $bahanBaku = $item->bahanBaku;
            
    //         $jumlahDiperlukanResep = $item->jumlah_diperlukan * $multiplier;
            
    //         $jumlahDasar = $this->konversiSatuanBahanBaku(
    //             $jumlahDiperlukanResep,
    //             $item->satuan,
    //             ''
    //         );

    //         $nilaiPerSatuanStok = $this->konversiSatuanBahanBaku(
    //             1,
    //             $bahanBaku->satuan,
    //             $bahanBaku->deskripsi
    //         );

    //         $jumlahSatuanStokDibutuhkan = $nilaiPerSatuanStok > 0 
    //             ? $jumlahDasar / $nilaiPerSatuanStok 
    //             : $jumlahDasar;

    //         $kebutuhan[] = [
    //             'id_bahan_baku' => $bahanBaku->id_bahan_baku,
    //             'nama_bahan' => $bahanBaku->nama_bahan,
    //             'jumlah_diperlukan_resep' => $jumlahDiperlukanResep,
    //             'satuan_resep' => $item->satuan,
    //             'jumlah_dibutuhkan_stok' => $jumlahSatuanStokDibutuhkan,
    //             'satuan_stok' => $bahanBaku->satuan,
    //             'keterangan' => $item->keterangan,
    //         ];
    //     }

    //     return $kebutuhan;
    // }
}