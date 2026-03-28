<?php

namespace App\Http\Controllers;

use App\Models\Distribusi;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class LaporanDistribusiController extends Controller
{
    /**
     * Display a listing of the resource with filters.
     */
    public function index(Request $request)
    {
        $allowed = [20, 40, 60, 80, 100, 250, 500];
        $perPage = (int) request('per_page', 20);
        
        if (!in_array($perPage, $allowed)) {
            $perPage = 20;
        }

        $query = Distribusi::with([
            'distribusiDetails.lokasi',
            'distribusiDetails.user',
            'distribusiDetails.itemDistribusis.produk'
        ])->where('status', 'selesai'); // Hanya tampilkan yang selesai

        // Filter berdasarkan tipe filter
        $filterType = $request->input('periode', 'custom');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

    switch ($filterType) {
        case 'harian':
            $date = now()->format('Y-m-d');
            $query->whereDate('tanggal_distribusi', $date);
            $periodText = 'Hari ini: ' . Carbon::parse($date)->format('d-M-Y');
            break;

        case 'bulanan':
            $month = now()->format('Y-m');
            $query->whereYear('tanggal_distribusi', Carbon::parse($month)->year)
                ->whereMonth('tanggal_distribusi', Carbon::parse($month)->month);
            $periodText = 'Bulan ini: ' . Carbon::parse($month)->format('F Y');
            break;

        case 'tahunan':
            $year = now()->year;
            $query->whereYear('tanggal_distribusi', $year);
            $periodText = 'Tahun ini: ' . $year;
            break;

        case 'custom':
        default:
            if ($startDate && $endDate) {
                $query->whereBetween('tanggal_distribusi', [$startDate, $endDate]);
                $periodText = 'Custom: ' . Carbon::parse($startDate)->format('d-M-Y') . ' s/d ' . Carbon::parse($endDate)->format('d-M-Y');
            } elseif ($startDate) {
                $query->where('tanggal_distribusi', '>=', $startDate);
                $periodText = 'Custom: Dari ' . Carbon::parse($startDate)->format('d-M-Y');
            } elseif ($endDate) {
                $query->where('tanggal_distribusi', '<=', $endDate);
                $periodText = 'Custom: Sampai ' . Carbon::parse($endDate)->format('d-M-Y');
            } else {
                // kalau mau default bulan ini, boleh tetap seperti ini
                $query->whereMonth('tanggal_distribusi', now()->month)
                    ->whereYear('tanggal_distribusi', now()->year);
                $periodText = 'Bulan ini: ' . now()->format('F Y');
            }
            break;
    }


        $distribusi = $query->orderBy('tanggal_distribusi', 'desc')
            ->paginate($perPage)
            ->appends($request->except('page'));

        // Hitung statistik
        $totalDistribusi = $query->count();
        $totalLokasi = $query->get()->pluck('distribusiDetails')->flatten()->pluck('id_lokasi')->unique()->count();
        $totalItem = $query->get()->pluck('distribusiDetails')->flatten()->pluck('itemDistribusis')->flatten()->sum('jumlah');

        return view('module.laporan.distribusi.index', compact(
            'distribusi', 
            'allowed', 
            'perPage',
            'periodText',
            'totalDistribusi',
            'totalLokasi',
            'totalItem'
        ));
    }

    /**
     * Download PDF Report
     */
    public function downloadPdf(Request $request)
    {
        $query = Distribusi::with([
            'distribusiDetails.lokasi',
            'distribusiDetails.user',
            'distribusiDetails.itemDistribusis.produk'
        ])->where('status', 'selesai');

        // Apply same filters as index
        $filterType = $request->input('periode', 'custom');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

    switch ($filterType) {
        case 'harian':
            $date = now()->format('Y-m-d');
            $query->whereDate('tanggal_distribusi', $date);
            $periodText = 'Hari ini: ' . Carbon::parse($date)->format('d-M-Y');
            break;

        case 'bulanan':
            $month = now()->format('Y-m');
            $query->whereYear('tanggal_distribusi', Carbon::parse($month)->year)
                ->whereMonth('tanggal_distribusi', Carbon::parse($month)->month);
            $periodText = 'Bulan ini: ' . Carbon::parse($month)->format('F Y');
            break;

        case 'tahunan':
            $year = now()->year;
            $query->whereYear('tanggal_distribusi', $year);
            $periodText = 'Tahun ini: ' . $year;
            break;

        case 'custom':
        default:
            if ($startDate && $endDate) {
                $query->whereBetween('tanggal_distribusi', [$startDate, $endDate]);
                $periodText = 'Custom: ' . Carbon::parse($startDate)->format('d-M-Y') . ' s/d ' . Carbon::parse($endDate)->format('d-M-Y');
            } elseif ($startDate) {
                $query->where('tanggal_distribusi', '>=', $startDate);
                $periodText = 'Custom: Dari ' . Carbon::parse($startDate)->format('d-M-Y');
            } elseif ($endDate) {
                $query->where('tanggal_distribusi', '<=', $endDate);
                $periodText = 'Custom: Sampai ' . Carbon::parse($endDate)->format('d-M-Y');
            } else {
                // kalau mau default bulan ini, boleh tetap seperti ini
                $query->whereMonth('tanggal_distribusi', now()->month)
                    ->whereYear('tanggal_distribusi', now()->year);
                $periodText = 'Bulan ini: ' . now()->format('F Y');
            }
            break;
    }

        $distribusi = $query->orderBy('tanggal_distribusi', 'desc')->get();

        // Hitung statistik
        $totalDistribusi = $distribusi->count();
        $totalLokasi = $distribusi->pluck('distribusiDetails')->flatten()->pluck('id_lokasi')->unique()->count();
        $totalItem = $distribusi->pluck('distribusiDetails')->flatten()->pluck('itemDistribusis')->flatten()->sum('jumlah');

        $pdf = Pdf::loadView('module.Laporan.distribusi.pdf', compact(
            'distribusi',
            'periodText',
            'totalDistribusi',
            'totalLokasi',
            'totalItem'
        ));
        $pdf->setPaper('a4', 'portrait');
        $filename = 'Laporan-Distribusi-' . now('Asia/Jakarta')->format('d-m-Y_H-i-s') . '.pdf';
        return $pdf->stream($filename);
        // return $pdf->download($filename);
    }
}