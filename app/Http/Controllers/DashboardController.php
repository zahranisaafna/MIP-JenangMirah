<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login');
        }

        //Validasi query string
        $validated = $request->validate([
            'periode'    => 'in:harian,mingguan,bulanan',
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date',
        ]);

        // Filter
        $periode   = $validated['periode'] ?? 'bulanan'; // harian|mingguan|bulanan
        $startDate = $validated['start_date'] ?? $request->get('start_date');
        $endDate   = $validated['end_date']   ?? $request->get('end_date');


        if ($startDate && $endDate && $startDate > $endDate) {
            [$startDate, $endDate] = [$endDate, $startDate];
        }

        // Default range
        if (!$startDate || !$endDate) {
            switch ($periode) {
                case 'harian':
                    $startDate = now()->subDays(7)->toDateString();
                    $endDate   = now()->toDateString();
                    break;
                case 'mingguan':
                    $startDate = now()->subWeeks(8)->startOfWeek()->toDateString();
                    $endDate   = now()->endOfWeek()->toDateString();
                    break;
                default: // bulanan
                    $startDate = now()->subMonths(6)->startOfMonth()->toDateString();
                    $endDate   = now()->endOfMonth()->toDateString();
            }
        }

        // Chart & statistik
        $produksiData   = $this->getProduksiData($periode, $startDate, $endDate);
        $distribusiData = $this->getDistribusiData($periode, $startDate, $endDate);
        $statistik      = $this->getStatistik($startDate, $endDate);

        $viewData = compact('produksiData','distribusiData','statistik','periode','startDate','endDate');

        return match ($user->role) {
            'admin'            => view('layouts.dashboard.admin', $viewData),
            'karyawanproduksi' => view('layouts.dashboard.produksi', $viewData),
            'owner'            => view('layouts.dashboard.owner', $viewData),
            default            => view('layouts.dashboard.admin', $viewData),
        };
    }

    /** PRODUKSI: data untuk chart */
    private function getProduksiData(string $periode, string $startDate, string $endDate): array
    {
        $query = DB::table('produksi')
            ->whereBetween('tanggal_produksi', [$startDate, $endDate]);

        switch ($periode) {
            case 'harian':
                $data = $query->select(
                        DB::raw('DATE(tanggal_produksi) as tanggal'),
                        DB::raw('SUM(total_produk_dihasilkan) as total')
                    )
                    ->groupBy('tanggal')
                    ->orderBy('tanggal')
                    ->get();

                $labels = $data->map(fn($r) => Carbon::parse($r->tanggal)->format('d M'));
                break;

            case 'mingguan':
                // 3 = ISO week (Senin awal pekan), biar konsisten dengan startOfWeek()
                $data = $query->select(
                        DB::raw('YEAR(tanggal_produksi) as tahun'),
                        DB::raw('WEEK(tanggal_produksi, 3) as minggu'),
                        DB::raw('SUM(total_produk_dihasilkan) as total')
                    )
                    ->groupBy('tahun','minggu')
                    ->orderBy('tahun')
                    ->orderBy('minggu')
                    ->get();

                $labels = $data->map(fn($r) => 'Minggu '.$r->minggu);
                break;

            case 'bulanan':
            default:
                $data = $query->select(
                        DB::raw('YEAR(tanggal_produksi) as tahun'),
                        DB::raw('MONTH(tanggal_produksi) as bulan'),
                        DB::raw('SUM(total_produk_dihasilkan) as total')
                    )
                    ->groupBy('tahun','bulan')
                    ->orderBy('tahun')
                    ->orderBy('bulan')
                    ->get();

                $labels = $data->map(fn($r) => Carbon::create($r->tahun, $r->bulan, 1)->format('M Y'));
                break;
        }

        $values = $data->pluck('total');

        return [
            'labels' => $labels->values()->all(),
            'values' => $values->values()->all(),
        ];
    }

    /** DISTRIBUSI: data untuk chart */
    private function getDistribusiData(string $periode, string $startDate, string $endDate): array
    {
        $query = DB::table('distribusi')
            ->whereBetween('tanggal_distribusi', [$startDate, $endDate]);

        switch ($periode) {
            case 'harian':
                $data = $query->select(
                        DB::raw('DATE(tanggal_distribusi) as tanggal'),
                        DB::raw('COUNT(*) as total')
                    )
                    ->groupBy('tanggal')
                    ->orderBy('tanggal')
                    ->get();

                $labels = $data->map(fn($r) => Carbon::parse($r->tanggal)->format('d M'));
                break;

            case 'mingguan':
                $data = $query->select(
                        DB::raw('YEAR(tanggal_distribusi) as tahun'),
                        DB::raw('WEEK(tanggal_distribusi, 3) as minggu'),
                        DB::raw('COUNT(*) as total')
                    )
                    ->groupBy('tahun','minggu')
                    ->orderBy('tahun')
                    ->orderBy('minggu')
                    ->get();

                $labels = $data->map(fn($r) => 'Minggu '.$r->minggu);
                break;

            case 'bulanan':
            default:
                $data = $query->select(
                        DB::raw('YEAR(tanggal_distribusi) as tahun'),
                        DB::raw('MONTH(tanggal_distribusi) as bulan'),
                        DB::raw('COUNT(*) as total')
                    )
                    ->groupBy('tahun','bulan')
                    ->orderBy('tahun')
                    ->orderBy('bulan')
                    ->get();

                $labels = $data->map(fn($r) => Carbon::create($r->tahun, $r->bulan, 1)->format('M Y'));
                break;
        }

        $values = $data->pluck('total');

        return [
            'labels' => $labels->values()->all(),
            'values' => $values->values()->all(),
        ];
    }

    /** Statistik ringkas di header */
    private function getStatistik(string $startDate, string $endDate): array
    {
        $produksiRange = DB::table('produksi')
            ->whereBetween('tanggal_produksi', [$startDate, $endDate]);

        $distribusiRange = DB::table('distribusi')
            ->whereBetween('tanggal_distribusi', [$startDate, $endDate]);

        $hariProduksi = $produksiRange
            ->select(DB::raw('COUNT(DISTINCT DATE(tanggal_produksi)) as total'))
            ->value('total');

        $hariDistribusi = $distribusiRange
            ->select(DB::raw('COUNT(DISTINCT DATE(tanggal_distribusi)) as total'))
            ->value('total');

        return [
            'total_produksi'       => DB::table('produksi')->sum('total_produk_dihasilkan'),
            'total_distribusi'     => DB::table('distribusi')->count(),
            'total_produk'         => DB::table('produk')->count(),
            'total_lokasi'         => DB::table('lokasi')->count(),

            'produksi_bulan_ini'   => $produksiRange->sum('total_produk_dihasilkan'),
            'distribusi_bulan_ini' => $distribusiRange->count(),

            'hari_produksi'        => $hariProduksi,
            'hari_distribusi'      => $hariDistribusi,
        ];
        // return [
        //     'total_produksi'        => DB::table('produksi')->sum('total_produk_dihasilkan'),
        //     'total_distribusi'      => DB::table('distribusi')->count(),
        //     'total_produk'          => DB::table('produk')->count(),
        //     'total_lokasi'          => DB::table('lokasi')->count(),
        //     'produksi_bulan_ini'    => DB::table('produksi')
        //                                     ->whereMonth('tanggal_produksi', now()->month)
        //                                     ->whereYear('tanggal_produksi', now()->year)
        //                                     ->sum('total_produk_dihasilkan'),
        //     'distribusi_bulan_ini'  => DB::table('distribusi')
        //                                     ->whereMonth('tanggal_distribusi', now()->month)
        //                                     ->whereYear('tanggal_distribusi', now()->year)
        //                                     ->count(),
        // ];
    }

    /** Endpoint AJAX (optional) */
    public function getChartData(Request $request)
    {
        // Validasi query string
        $validated = $request->validate([
            'periode'    => 'in:harian,mingguan,bulanan',
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date',
        ]);

        $periode   = $validated['periode'] ?? 'bulanan';
        $startDate = $validated['start_date'] ?? $request->get('start_date');
        $endDate   = $validated['end_date']   ?? $request->get('end_date');

        // Tukar jika user kirim rentang terbalik
        if ($startDate && $endDate && $startDate > $endDate) {
            [$startDate, $endDate] = [$endDate, $startDate];
        }

        // Default range
        if (!$startDate || !$endDate) {
            switch ($periode) {
                case 'harian':
                    $startDate = now()->subDays(7)->toDateString();
                    $endDate   = now()->toDateString();
                    break;
                case 'mingguan':
                    $startDate = now()->subWeeks(8)->startOfWeek()->toDateString();
                    $endDate   = now()->endOfWeek()->toDateString();
                    break;
                default:
                    $startDate = now()->subMonths(6)->startOfMonth()->toDateString();
                    $endDate   = now()->endOfMonth()->toDateString();
            }
        }

        $produksiData   = $this->getProduksiData($periode, $startDate, $endDate);
        $distribusiData = $this->getDistribusiData($periode, $startDate, $endDate);

        return response()->json([
            'success'    => true,
            'produksi'   => $produksiData,
            'distribusi' => $distribusiData,
        ]);
    }
}


// namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use Carbon\Carbon;
// use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Auth;

// class DashboardController extends Controller
// {
//     public function index(Request $request)
//     {
//         $user = Auth::user();

//         // Filter
//         $periode   = $request->get('periode', 'bulanan'); // harian|mingguan|bulanan
//         $startDate = $request->get('start_date');
//         $endDate   = $request->get('end_date');

//         // Default range
//         if (!$startDate || !$endDate) {
//             switch ($periode) {
//                 case 'harian':
//                     $startDate = now()->subDays(7)->toDateString();
//                     $endDate   = now()->toDateString();
//                     break;
//                 case 'mingguan':
//                     $startDate = now()->subWeeks(8)->startOfWeek()->toDateString();
//                     $endDate   = now()->endOfWeek()->toDateString();
//                     break;
//                 default: // bulanan
//                     $startDate = now()->subMonths(6)->startOfMonth()->toDateString();
//                     $endDate   = now()->endOfMonth()->toDateString();
//             }
//         }

//         // Chart & statistik
//         $produksiData   = $this->getProduksiData($periode, $startDate, $endDate);
//         $distribusiData = $this->getDistribusiData($periode, $startDate, $endDate);
//         $statistik      = $this->getStatistik();

//         $viewData = compact('produksiData','distribusiData','statistik','periode','startDate','endDate');

//         return match ($user->role) {
//             'admin'            => view('layouts.dashboard.admin', $viewData),
//             'karyawanproduksi' => view('layouts.dashboard.produksi', $viewData),
//             'owner'            => view('layouts.dashboard.owner', $viewData),
//             default            => view('layouts.dashboard.admin', $viewData),
//         };
//     }

//     /** PRODUKSI: data untuk chart */
//     private function getProduksiData($periode, $startDate, $endDate)
//     {
//         $query = DB::table('produksi')
//             ->whereBetween('tanggal_produksi', [$startDate, $endDate]);

//         switch ($periode) {
//             case 'harian':
//                 $data = $query->select(
//                         DB::raw('DATE(tanggal_produksi) as tanggal'),
//                         DB::raw('SUM(total_produk_dihasilkan) as total')
//                     )
//                     ->groupBy('tanggal')
//                     ->orderBy('tanggal')
//                     ->get();

//                 $labels = $data->map(fn($r) => Carbon::parse($r->tanggal)->format('d M'));
//                 break;

//             case 'mingguan':
//                 $data = $query->select(
//                         DB::raw('YEAR(tanggal_produksi) as tahun'),
//                         DB::raw('WEEK(tanggal_produksi) as minggu'),
//                         DB::raw('SUM(total_produk_dihasilkan) as total')
//                     )
//                     ->groupBy('tahun','minggu')
//                     ->orderBy('tahun')
//                     ->orderBy('minggu')
//                     ->get();

//                 $labels = $data->map(fn($r) => 'Minggu '.$r->minggu);
//                 break;

//             case 'bulanan':
//             default:
//                 $data = $query->select(
//                         DB::raw('YEAR(tanggal_produksi) as tahun'),
//                         DB::raw('MONTH(tanggal_produksi) as bulan'),
//                         DB::raw('SUM(total_produk_dihasilkan) as total')
//                     )
//                     ->groupBy('tahun','bulan')
//                     ->orderBy('tahun')
//                     ->orderBy('bulan')
//                     ->get();

//                 $labels = $data->map(fn($r) => Carbon::create($r->tahun, $r->bulan, 1)->format('M Y'));
//                 break;
//         }

//         $values = $data->pluck('total');

//         return [
//             'labels' => $labels,
//             'values' => $values,
//         ];
//     }

//     /** DISTRIBUSI: data untuk chart */
//     private function getDistribusiData($periode, $startDate, $endDate)
//     {
//         $query = DB::table('distribusi')
//             ->whereBetween('tanggal_distribusi', [$startDate, $endDate]);

//         switch ($periode) {
//             case 'harian':
//                 $data = $query->select(
//                         DB::raw('DATE(tanggal_distribusi) as tanggal'),
//                         DB::raw('COUNT(*) as total')
//                     )
//                     ->groupBy('tanggal')
//                     ->orderBy('tanggal')
//                     ->get();

//                 $labels = $data->map(fn($r) => Carbon::parse($r->tanggal)->format('d M'));
//                 break;

//             case 'mingguan':
//                 $data = $query->select(
//                         DB::raw('YEAR(tanggal_distribusi) as tahun'),
//                         DB::raw('WEEK(tanggal_distribusi) as minggu'),
//                         DB::raw('COUNT(*) as total')
//                     )
//                     ->groupBy('tahun','minggu')
//                     ->orderBy('tahun')
//                     ->orderBy('minggu')
//                     ->get();

//                 $labels = $data->map(fn($r) => 'Minggu '.$r->minggu);
//                 break;

//             case 'bulanan':
//             default:
//                 $data = $query->select(
//                         DB::raw('YEAR(tanggal_distribusi) as tahun'),
//                         DB::raw('MONTH(tanggal_distribusi) as bulan'),
//                         DB::raw('COUNT(*) as total')
//                     )
//                     ->groupBy('tahun','bulan')
//                     ->orderBy('tahun')
//                     ->orderBy('bulan')
//                     ->get();

//                 $labels = $data->map(fn($r) => Carbon::create($r->tahun, $r->bulan, 1)->format('M Y'));
//                 break;
//         }

//         $values = $data->pluck('total');

//         return [
//             'labels' => $labels,
//             'values' => $values,
//         ];
//     }

//     /** Statistik ringkas di header */
//     private function getStatistik()
//     {
//         return [
//             'total_produksi'        => DB::table('produksi')->sum('total_produk_dihasilkan'),
//             'total_distribusi'      => DB::table('distribusi')->count(),
//             'total_produk'          => DB::table('produk')->count(),
//             'total_lokasi'          => DB::table('lokasi')->count(),
//             'produksi_bulan_ini'    => DB::table('produksi')
//                                             ->whereMonth('tanggal_produksi', now()->month)
//                                             ->whereYear('tanggal_produksi', now()->year)
//                                             ->sum('total_produk_dihasilkan'),
//             'distribusi_bulan_ini'  => DB::table('distribusi')
//                                             ->whereMonth('tanggal_distribusi', now()->month)
//                                             ->whereYear('tanggal_distribusi', now()->year)
//                                             ->count(),
//         ];
//     }

//     /** Endpoint AJAX (optional) */
//     public function getChartData(Request $request)
//     {
//         $periode   = $request->get('periode', 'bulanan');
//         $startDate = $request->get('start_date');
//         $endDate   = $request->get('end_date');

//         $produksiData   = $this->getProduksiData($periode, $startDate, $endDate);
//         $distribusiData = $this->getDistribusiData($periode, $startDate, $endDate);

//         return response()->json([
//             'success'   => true,
//             'produksi'  => $produksiData,
//             'distribusi'=> $distribusiData,
//         ]);
//     }
// }
