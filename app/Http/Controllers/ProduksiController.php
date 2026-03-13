<?php

namespace App\Http\Controllers;

use App\Models\Produksi;
use App\Models\DetailProduksi;
use App\Models\Resep;
use App\Models\Produk;
use App\Models\BahanBaku;
use App\Models\KomposisiResep;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class ProduksiController extends Controller
{
    
    /**
     * Konversi satuan bahan baku ke satuan dasar
     */
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

    //     // Ambil SEMUA pasangan konversi dari deskripsi
    //     preg_match_all(
    //         '/1\s*' . preg_quote($satuanDari, '/') . '\s*=\s*(\d+(?:\.\d+)?)\s*(kg|liter|pcs|gram|butir)/i',
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

    // private function konversiSatuanBahanBaku($jumlah, $satuanDari, $deskripsi)
    // {
    //     $konversi = [
    //         'kg' => 1,
    //         'gram' => 0.001,
    //         'liter' => 1,
    //         'pcs' => 1,
    //         'butir' => 1,
    //     ];

    //     // Jika sudah dalam satuan dasar, return langsung
    //     if (isset($konversi[$satuanDari])) {
    //         return $jumlah * $konversi[$satuanDari];
    //     }

    //     // Parse deskripsi untuk konversi satuan kemasan
    //     if (empty($deskripsi)) {
    //         return $jumlah;
    //     }

    //     // Parsing deskripsi untuk berbagai format
    //     // Format: "1 karton = 20 pcs = 10000 gram = 10 kg"
    //     preg_match('/1\s*' . preg_quote($satuanDari, '/') . '\s*=\s*(\d+(?:\.\d+)?)\s*(kg|liter|pcs|gram|butir)/i', $deskripsi, $matches);
        
    //     if (count($matches) >= 3) {
    //         $nilaiKonversi = floatval($matches[1]);
    //         $satuanTujuan = strtolower($matches[2]);
            
    //         return $jumlah * $nilaiKonversi * ($konversi[$satuanTujuan] ?? 1);
    //     }

    //     return $jumlah;
    // }

    /**
     * Hitung kebutuhan bahan baku dalam satuan stok
     */
    private function hitungKebutuhanBahanBaku($idResep, $multiplier = 1)
    {
        $komposisi = KomposisiResep::where('id_resep', $idResep)
            ->with('bahanBaku')
            ->get();

        $kebutuhan = [];

        foreach ($komposisi as $item) {
            $bahanBaku = $item->bahanBaku;
            
            // jumlah yang diperlukan sesuai resep (satuan sama dengan stok)
            $jumlahDiperlukanResep = $item->jumlah_diperlukan * $multiplier;

            $kebutuhan[] = [
                'id_bahan_baku'           => $bahanBaku->id_bahan_baku,
                'nama_bahan'              => $bahanBaku->nama_bahan,
                'jumlah_diperlukan_resep' => $jumlahDiperlukanResep,
                'satuan_resep'            => $item->satuan,          // sama dengan satuan stok
                'jumlah_dibutuhkan_stok'  => $jumlahDiperlukanResep, // LANGSUNG
                'satuan_stok'             => $bahanBaku->satuan,
                'stok_tersedia'           => $bahanBaku->stok_saat_ini,
                'cukup'                   => $bahanBaku->stok_saat_ini >= $jumlahDiperlukanResep,
                'keterangan'              => $item->keterangan,
            ];
        }

        return $kebutuhan;
    }

    // private function hitungKebutuhanBahanBaku($idResep, $multiplier = 1)
    // {
    //     $komposisi = KomposisiResep::where('id_resep', $idResep)
    //         ->with('bahanBaku')
    //         ->get();

    //     $kebutuhan = [];

    //     foreach ($komposisi as $item) {
    //         $bahanBaku = $item->bahanBaku;
            
    //         // Jumlah yang diperlukan dalam satuan resep
    //         $jumlahDiperlukanResep = $item->jumlah_diperlukan * $multiplier;
            
    //         // Konversi jumlah yang diperlukan ke satuan dasar
    //         $jumlahDasar = $this->konversiSatuanBahanBaku(
    //             $jumlahDiperlukanResep,
    //             $item->satuan,
    //             ''
    //         );

    //         // Konversi satuan stok bahan baku ke satuan dasar
    //         $nilaiPerSatuanStok = $this->konversiSatuanBahanBaku(
    //             1,
    //             $bahanBaku->satuan,
    //             $bahanBaku->deskripsi
    //         );

    //         // Hitung berapa satuan stok yang dibutuhkan
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
    //             'stok_tersedia' => $bahanBaku->stok_saat_ini,
    //             'cukup' => $bahanBaku->stok_saat_ini >= $jumlahSatuanStokDibutuhkan,
    //             'keterangan' => $item->keterangan,
    //         ];
    //     }

    //     return $kebutuhan;
    // }

    /**
     * Display a listing of the resource.
     */


    public function index(Request $request)
    {
        $allowed = [20, 40, 60, 80, 100, 250, 500];
        $perPage = (int) $request->get('per_page', 20);
        
        if (!in_array($perPage, $allowed)) {
            $perPage = 20;
        }

        $query = Produksi::with(['user', 'detailProduksis.resep', 'detailProduksis.produk']);

        // ========== FILTER SATU TANGGAL ==========
        $filterTanggal = $request->get('filter_tanggal'); // name dari input

        if (!empty($filterTanggal)) {
            // filter exact tanggal_produksi
            $query->whereDate('tanggal_produksi', $filterTanggal);
        }
        // ========== END FILTER SATU TANGGAL ==========

        $produksi = $query
            ->orderBy('id_produksi', 'asc')
            ->paginate($perPage)
            ->appends($request->except('page'));

        return view('module.produksi.index', compact('produksi', 'allowed', 'perPage'));
    }

    // public function index()
    // {
    //     $allowed = [20, 40, 60, 80, 100, 250, 500];
    //     $perPage = (int) request('per_page', 20);
        
    //     if (!in_array($perPage, $allowed)) {
    //         $perPage = 20;
    //     }

    //     $produksi = Produksi::with(['user', 'detailProduksis.resep', 'detailProduksis.produk'])
    //         ->orderBy('id_produksi', 'asc')
    //         ->paginate($perPage)
    //         ->appends(request()->except('page'));

    //     return view('module.produksi.index', compact('produksi', 'allowed', 'perPage'));
    // }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $resepList = Resep::where('status', 'aktif')
            ->with('komposisiReseps.bahanBaku')
            ->get();
        // $produkList = Produk::all();
        $produkList = Produk::whereIn('status', ['tersedia', 'habis'])->get();

        // $produkList = Produk::where('status', 'tersedia')->get();

        return view('module.produksi.form', compact('resepList', 'produkList'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal_produksi' => 'required|date',
            'waktu_mulai' => 'required',
            'details' => 'required|array|min:1',
            'details.*.id_resep' => 'required|exists:resep,id_resep',
            'details.*.id_produk' => 'required|exists:produk,id_produk',
            'details.*.jumlah_target' => 'required|integer|min:1',
            'catatan' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Generate ID Produksi
            $lastProduksi = Produksi::orderBy('id_produksi', 'desc')->first();
            $nextNumber = $lastProduksi 
                ? intval(substr($lastProduksi->id_produksi, 3)) + 1 
                : 1;
            $idProduksi = 'PRD' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

            // Generate Kode Batch
            // $kodeBatch = 'BATCH-' . date('Ymd') . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

            // Cek ketersediaan bahan baku untuk semua detail
            $semuaCukup = true;
            $pesanError = [];

            foreach ($request->details as $detail) {
                $resep = Resep::find($detail['id_resep']);
                $produk = Produk::findOrFail($detail['id_produk']);
                // VALIDASI: produk harus milik resep yang sama
                if ($produk->id_resep !== $resep->id_resep) {
                    DB::rollBack();
                    return back()
                        ->withInput()
                        ->with('error', 'Produk "'.$produk->nama_produk.'" tidak sesuai dengan resep "'.$resep->nama_resep.'".');
                }
                $multiplier = $detail['jumlah_target'] / $resep->kapasitas_produksi;
                
                $kebutuhan = $this->hitungKebutuhanBahanBaku($detail['id_resep'], $multiplier);
                
                foreach ($kebutuhan as $bahan) {
                    if (!$bahan['cukup']) {
                        $semuaCukup = false;
                        $pesanError[] = sprintf(
                            '%s: Dibutuhkan %.2f %s, tersedia %.2f %s',
                            $bahan['nama_bahan'],
                            $bahan['jumlah_dibutuhkan_stok'],
                            $bahan['satuan_stok'],
                            $bahan['stok_tersedia'],
                            $bahan['satuan_stok']
                        );
                    }
                }
            }

            if (!$semuaCukup) {
                DB::rollBack();
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Stok bahan baku tidak mencukupi:<br>' . implode('<br>', $pesanError));
            }

            // Create Produksi
            $produksi = Produksi::create([
                'id_produksi' => $idProduksi,
                'id_user' => Auth::id(),
                // 'kode_batch' => $kodeBatch,
                'tanggal_produksi' => $request->tanggal_produksi,
                'waktu_mulai' => $request->waktu_mulai,
                'status' => 'proses',
                'catatan' => $request->catatan,
            ]);

            // Create Detail Produksi dan kurangi stok bahan baku
            foreach ($request->details as $index => $detail) {
                $resep = Resep::find($detail['id_resep']);
                $multiplier = $detail['jumlah_target'] / $resep->kapasitas_produksi;
                // === Generate ID Detail global ===
                $lastDetail = DetailProduksi::orderBy('id_detail_produksi', 'desc')->first();
                $nextNumber = $lastDetail
                    ? intval(substr($lastDetail->id_detail_produksi, 3)) + 1   // ambil angka setelah 'DPD'
                    : 1;

                $idDetail = 'DPD' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
                // Generate ID Detail
                // $idDetail = $idProduksi . str_pad($index + 1, 1, '0', STR_PAD_LEFT);

                DetailProduksi::create([
                    'id_detail_produksi' => $idDetail,
                    'id_produksi' => $idProduksi,
                    'id_resep' => $detail['id_resep'],
                    'id_produk' => $detail['id_produk'],
                    'jumlah_target' => $detail['jumlah_target'],
                    'jumlah_berhasil' => 0,
                    'jumlah_gagal' => 0,
                ]);

                // Kurangi stok bahan baku
                // $kebutuhan = $this->hitungKebutuhanBahanBaku($detail['id_resep'], $multiplier);
                
                // foreach ($kebutuhan as $bahan) {
                //     $bahanBaku = BahanBaku::find($bahan['id_bahan_baku']);
                //     // $bahanBaku->stok_saat_ini -= $bahan['jumlah_dibutuhkan_stok'];
                    
                //     // Update status stok
                //     // Kurangi stok bahan baku
                //     $bahanBaku->stok_saat_ini -= $bahan['jumlah_dibutuhkan_stok'];

                //     // Jangan sampai minus
                //     if ($bahanBaku->stok_saat_ini < 0) {
                //         $bahanBaku->stok_saat_ini = 0;
                //     }
                //     $bahanBaku->save();

                // }
            }

            DB::commit();
            return redirect()->route('produksi.show', $idProduksi)
                ->with('success', 'Produksi berhasil dibuat. Stok bahan baku telah dikurangi.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $produksi = Produksi::with([
            'user',
            'detailProduksis.resep.komposisiReseps.bahanBaku',
            'detailProduksis.produk'
        ])->findOrFail($id);

        // Hitung kebutuhan bahan baku untuk setiap detail
        $detailWithBahan = [];
        foreach ($produksi->detailProduksis as $detail) {
            $multiplier = $detail->jumlah_target / $detail->resep->kapasitas_produksi;
            $kebutuhan = $this->hitungKebutuhanBahanBaku($detail->id_resep, $multiplier);
            
            $detailWithBahan[] = [
                'detail' => $detail,
                'kebutuhan_bahan' => $kebutuhan,
            ];
        }

        return view('module.produksi.show', compact('produksi', 'detailWithBahan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $produksi = Produksi::with('detailProduksis')->findOrFail($id);
        
        // Hanya bisa edit jika status masih proses atau pending
        // Sekarang status 'selesai' juga bisa diedit, jadi kita longgarkan pengecekannya
        // if (!in_array($produksi->status, ['proses', 'pending', 'selesai'])) {
        //     return redirect()->route('produksi.show', $id)
        //         ->with('error', 'Produksi dengan status ' . $produksi->status . ' tidak dapat diedit.');
        // }

        $resepList = Resep::where('status', 'aktif')->get();
        $produkList = Produk::where('status', 'tersedia')->get();
        $detailProduksi = $produksi->detailProduksis;

        return view('module.produksi.form', compact('produksi', 'resepList', 'produkList', 'detailProduksi'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // $produksi = Produksi::findOrFail($id);
        $produksi = Produksi::with('detailProduksis.resep')->findOrFail($id);

        // // ✅ TAMBAH INI
        // $statusLama = $produksi->status;
        $request->validate([
            'waktu_mulai'                 => 'required',
            'waktu_selesai'               => 'nullable|required_if:status,selesai',
            'status'                      => 'required|in:proses,selesai,gagal,pending',
            'detail'                      => 'required|array|min:1',
            'detail.*.jumlah_berhasil'    => 'required|integer|min:0',
            'detail.*.jumlah_gagal'       => 'required|integer|min:0',
            'detail.*.keterangan_gagal'   => 'nullable|string',
            'catatan'                     => 'nullable|string',
        ]);
        $statusLama = $produksi->status;
        $statusBaru = $request->status;

        // 🚫 kalau sudah selesai → dilarang ubah status
        if ($statusLama === 'selesai' && $statusBaru !== 'selesai') {
            return back()
                ->withInput()
                ->with('error', 'Status produksi sudah selesai dan tidak dapat diubah lagi.');
        }


        DB::beginTransaction();
        try {
            // Update header produksi
            $produksi->update([
                'waktu_mulai'   => $request->waktu_mulai,
                'waktu_selesai' => $request->waktu_selesai,
                'status'        => $statusBaru,
                'catatan'       => $request->catatan,
            ]);

            // POTONG STOK BAHAN BAKU HANYA SEKALI SAAT SELESAI
            if ($statusLama !== 'selesai' && $statusBaru === 'selesai') {
                foreach ($produksi->detailProduksis as $detail) {
                    $resep = $detail->resep;
                    $multiplier = $detail->jumlah_target / $resep->kapasitas_produksi;

                    $kebutuhan = $this->hitungKebutuhanBahanBaku($detail->id_resep, $multiplier);

                    foreach ($kebutuhan as $bahan) {
                        BahanBaku::where('id_bahan_baku', $bahan['id_bahan_baku'])
                            ->decrement('stok_saat_ini', $bahan['jumlah_dibutuhkan_stok']);
                    }
                }
            }

            $totalProdukDihasilkan = 0;
            foreach ($request->detail as $detailData) {
                $detail = DetailProduksi::find($detailData['id_detail_produksi']);

                if ($detail) {
                    $lama = (int) $detail->jumlah_berhasil;
                    $baru = (int) $detailData['jumlah_berhasil'];

                    $persentase = $detail->jumlah_target > 0
                        ? ($baru / $detail->jumlah_target) * 100
                        : 0;

                    $detail->update([
                        'jumlah_berhasil'         => $baru,
                        'jumlah_gagal'            => (int) $detailData['jumlah_gagal'],
                        'persentase_keberhasilan' => $persentase,
                        'keterangan_gagal'        => $detailData['keterangan_gagal'] ?? null,
                    ]);

                    $totalProdukDihasilkan += $baru;

                    // 🧱 UPDATE STOK PRODUK HANYA JIKA SELESAI
                    if ($statusBaru === 'selesai') {
                        $produk = Produk::find($detail->id_produk);
                        if ($produk) {
                            $produk->stok_tersedia += ($baru - $lama);
                            $produk->stok_tersedia = max(0, $produk->stok_tersedia);
                            $produk->status = $produk->stok_tersedia > 0 ? 'tersedia' : 'habis';
                            $produk->save();
                        }
                    }
                }
            }
            // foreach ($request->detail as $detailData) {
            //     // /** @var DetailProduksi $detail */
            //     $detail = DetailProduksi::find($detailData['id_detail_produksi']);

            //     if ($detail) {
            //         $jumlahBaruBerhasil = (int) $detailData['jumlah_berhasil'];
            //         $jumlahBaruGagal    = (int) $detailData['jumlah_gagal'];
            //         $jumlahTarget       = (int) $detail->jumlah_target;

            //         // SIMPAN jumlah berhasil lama SEBELUM di-update
            //         $jumlahLamaBerhasil = (int) $detail->jumlah_berhasil;

            //         // Hitung persentase keberhasilan
            //         $persentase = $jumlahTarget > 0
            //             ? ($jumlahBaruBerhasil / $jumlahTarget) * 100
            //             : 0;

            //         // Update detail produksi
            //         $detail->update([
            //             'jumlah_berhasil'          => $jumlahBaruBerhasil,
            //             'jumlah_gagal'             => $jumlahBaruGagal,
            //             'persentase_keberhasilan'  => $persentase,
            //             'keterangan_gagal'         => $detailData['keterangan_gagal'] ?? null,
            //         ]);

            //         // Akumulasi total produk
            //         $totalProdukDihasilkan += $jumlahBaruBerhasil;

            //         // Hanya sentuh stok produk kalau status produksi = selesai
            //         if ($request->status === 'selesai') {
            //             $produk = Produk::find($detail->id_produk);
            //             if ($produk) {
            //                 // Hitung selisih antara nilai baru dan lama
            //                 $selisih = $jumlahBaruBerhasil - $jumlahLamaBerhasil;

            //                 $produk->stok_tersedia += $selisih;

            //                 // Jangan sampai minus
            //                 if ($produk->stok_tersedia < 0) {
            //                     $produk->stok_tersedia = 0;
            //                 }

            //                 // Update status produk
            //                 $produk->status = $produk->stok_tersedia > 0 ? 'tersedia' : 'habis';
            //                 $produk->save();
            //             }
            //         }
            //     }
            // }

            // Update total produk dihasilkan di header
            $produksi->total_produk_dihasilkan = $totalProdukDihasilkan;
            $produksi->save();

            DB::commit();
            return redirect()->route('produksi.show', $id)
                ->with('success', 'Produksi berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // public function update(Request $request, $id)
    // {
    //     $produksi = Produksi::findOrFail($id);

    //     $request->validate([
    //         'waktu_selesai' => 'nullable|required_if:status,selesai',
    //         'status' => 'required|in:proses,selesai,gagal,pending',
    //         'detail' => 'required|array|min:1',
    //         'detail.*.jumlah_berhasil' => 'required|integer|min:0',
    //         'detail.*.jumlah_gagal' => 'required|integer|min:0',
    //         'detail.*.keterangan_gagal' => 'nullable|string',
    //         'catatan' => 'nullable|string',
    //     ]);

    //     DB::beginTransaction();
    //     try {
    //         // Update Produksi
    //         $produksi->update([
    //             'waktu_selesai' => $request->waktu_selesai,
    //             'status' => $request->status,
    //             'catatan' => $request->catatan,
    //         ]);

    //         $totalProdukDihasilkan = 0;

    //         // Update Detail Produksi
    //         foreach ($request->detail as $idDetail => $detailData) {
    //             $detail = DetailProduksi::find($detailData['id_detail_produksi']);
                
    //             if ($detail) {
    //                 $jumlahBerhasil = $detailData['jumlah_berhasil'];
    //                 $jumlahGagal = $detailData['jumlah_gagal'];
    //                 $jumlahTarget = $detail->jumlah_target;

    //                 // Hitung persentase keberhasilan
    //                 $persentase = $jumlahTarget > 0 
    //                     ? ($jumlahBerhasil / $jumlahTarget) * 100 
    //                     : 0;

    //                 $detail->update([
    //                     'jumlah_berhasil' => $jumlahBerhasil,
    //                     'jumlah_gagal' => $jumlahGagal,
    //                     'persentase_keberhasilan' => $persentase,
    //                     'keterangan_gagal' => $detailData['keterangan_gagal'],
    //                 ]);

    //                 // Hitung total produk dihasilkan
    //                 $totalProdukDihasilkan += $jumlahBerhasil;

    //                 // Hanya update stok produk jika status produksi adalah selesai
    //                 if ($request->status === 'selesai') {
    //                     $produk = Produk::find($detail->id_produk);

    //                     // Jika jumlah berhasil berubah, hitung selisih dan update stok produk
    //                     $stokLama = $produk->stok_tersedia;
    //                     $stokBaru = $produk->stok_tersedia + ($jumlahBerhasil - $detail->jumlah_berhasil);
                        
    //                     $produk->stok_tersedia = $stokBaru;
    //                     $produk->status = $stokBaru > 0 ? 'tersedia' : 'habis'; // Update status produk jika stoknya cukup
    //                     $produk->save();
    //                 }
    //             }
    //         }

    //         // Update total produk dihasilkan
    //         $produksi->total_produk_dihasilkan = $totalProdukDihasilkan;
    //         $produksi->save();

    //         DB::commit();
    //         return redirect()->route('produksi.show', $id)
    //             ->with('success', 'Produksi berhasil diperbarui.');

    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return redirect()->back()
    //             ->withInput()
    //             ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    //     }
    // }

    // public function update(Request $request, $id)
    // {
    //     $produksi = Produksi::findOrFail($id);

    //     $request->validate([
    //         'waktu_selesai' => 'nullable|required_if:status,selesai',
    //         'status' => 'required|in:proses,selesai,gagal,pending',
    //         'detail' => 'required|array|min:1',
    //         'detail.*.jumlah_berhasil' => 'required|integer|min:0',
    //         'detail.*.jumlah_gagal' => 'required|integer|min:0',
    //         'detail.*.keterangan_gagal' => 'nullable|string',
    //         'catatan' => 'nullable|string',
    //     ]);

    //     DB::beginTransaction();
    //     try {
    //         // Update Produksi
    //         $produksi->update([
    //             'waktu_selesai' => $request->waktu_selesai,
    //             'status' => $request->status,
    //             'catatan' => $request->catatan,
    //         ]);

    //         $totalProdukDihasilkan = 0;

    //         // Update Detail Produksi
    //         foreach ($request->detail as $idDetail => $detailData) {
    //             $detail = DetailProduksi::find($detailData['id_detail_produksi']);
                
    //             if ($detail) {
    //                 $jumlahBerhasil = $detailData['jumlah_berhasil'];
    //                 $jumlahGagal = $detailData['jumlah_gagal'];
    //                 $jumlahTarget = $detail->jumlah_target;

    //                 // Hitung persentase keberhasilan
    //                 $persentase = $jumlahTarget > 0 
    //                     ? ($jumlahBerhasil / $jumlahTarget) * 100 
    //                     : 0;

    //                 $detail->update([
    //                     'jumlah_berhasil' => $jumlahBerhasil,
    //                     'jumlah_gagal' => $jumlahGagal,
    //                     'persentase_keberhasilan' => $persentase,
    //                     'keterangan_gagal' => $detailData['keterangan_gagal'],
    //                 ]);

    //                 $totalProdukDihasilkan += $jumlahBerhasil;

    //                 // Update stok produk jika status selesai
    //                 if ($request->status === 'selesai') {
    //                     $produk = Produk::find($detail->id_produk);
    //                     $produk->stok_tersedia += $jumlahBerhasil;
                        
    //                     // Update status produk
    //                     if ($produk->stok_tersedia > 0) {
    //                         $produk->status = 'tersedia';
    //                     }
                        
    //                     $produk->save();
    //                 }
    //             }
    //         }

    //         // Update total produk dihasilkan
    //         $produksi->total_produk_dihasilkan = $totalProdukDihasilkan;
    //         $produksi->save();

    //         DB::commit();
    //         return redirect()->route('produksi.show', $id)
    //             ->with('success', 'Produksi berhasil diperbarui.');

    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return redirect()->back()
    //             ->withInput()
    //             ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    //     }
    // }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $produksi = Produksi::with('detailProduksis.resep')->findOrFail($id);

            // ❌ LARANG HAPUS JIKA FINAL
            if (in_array($produksi->status, ['selesai', 'gagal'])) {
                return back()->with('error', 'Produksi final tidak boleh dihapus.');
            }

            // 🔁 rollback stok HANYA pending / proses
            foreach ($produksi->detailProduksis as $detail) {
                $multiplier = $detail->jumlah_target / $detail->resep->kapasitas_produksi;

                $kebutuhan = $this->hitungKebutuhanBahanBaku($detail->id_resep, $multiplier);

                foreach ($kebutuhan as $bahan) {
                    BahanBaku::where('id_bahan_baku', $bahan['id_bahan_baku'])
                        ->increment('stok_saat_ini', $bahan['jumlah_dibutuhkan_stok']);
                }
            }

            $produksi->delete();
            DB::commit();

            return redirect()->route('produksi.index')
                ->with('success', 'Produksi berhasil dihapus.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    // public function destroy($id)
    // {
    //     DB::beginTransaction();
    //     try {
    //         $produksi = Produksi::with('detailProduksis')->findOrFail($id);

    //         // 1. Kembalikan stok bahan baku dan produk
    //         foreach ($produksi->detailProduksis as $detail) {
    //             // Kembalikan stok bahan baku
    //             $resep = Resep::find($detail->id_resep);
    //             $multiplier = $detail->jumlah_target / $resep->kapasitas_produksi;
                
    //             $kebutuhan = $this->hitungKebutuhanBahanBaku($detail->id_resep, $multiplier);
                
    //             foreach ($kebutuhan as $bahan) {
    //                 $bahanBaku = BahanBaku::find($bahan['id_bahan_baku']);
    //                 $bahanBaku->stok_saat_ini += $bahan['jumlah_dibutuhkan_stok'];
    //                 $bahanBaku->save();
    //             }

    //             // Kembalikan stok produk jika produksi selesai
    //             $produk = Produk::find($detail->id_produk);
    //             if ($produk) {
    //                 $produk->stok_tersedia -= $detail->jumlah_berhasil;
    //                 if ($produk->stok_tersedia <= 0) {
    //                     $produk->status = 'habis';
    //                 }
    //                 $produk->save();
    //             }
    //         }

    //         // 2. Hapus data produksi
    //         $produksi->delete();

    //         DB::commit();
    //         return redirect()->route('produksi.index')
    //             ->with('success', 'Produksi berhasil dihapus dan stok bahan baku serta produk telah dikembalikan.');
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return redirect()->back()
    //             ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    //     }
    // }

    // public function destroy($id)
    // {
    //     DB::beginTransaction();
    //     try {
    //         $produksi = Produksi::with('detailProduksis')->findOrFail($id);

    //         // Hanya bisa hapus jika status pending
    //         // if ($produksi->status !== 'pending') {
    //         //     return redirect()->back()
    //         //         ->with('error', 'Hanya produksi dengan status pending yang dapat dihapus.');
    //         // }\
    //         $produksi = Produksi::findOrFail($id);

    //         // Kembalikan stok bahan baku
    //         foreach ($produksi->detailProduksis as $detail) {
    //             $resep = Resep::find($detail->id_resep);
    //             $multiplier = $detail->jumlah_target / $resep->kapasitas_produksi;
                
    //             $kebutuhan = $this->hitungKebutuhanBahanBaku($detail->id_resep, $multiplier);
                
    //             foreach ($kebutuhan as $bahan) {
    //                 $bahanBaku = BahanBaku::find($bahan['id_bahan_baku']);
    //                 $bahanBaku->stok_saat_ini += $bahan['jumlah_dibutuhkan_stok'];
                    
    //                 // Update status stok
    //                 $bahanBaku->stok_saat_ini += $bahan['jumlah_dibutuhkan_stok'];
    //                 $bahanBaku->save();

    //             }
    //         }

    //         $produksi->delete();

    //         DB::commit();
    //         return redirect()->route('produksi.index')
    //             ->with('success', 'Produksi berhasil dihapus dan stok bahan baku dikembalikan.');

    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return redirect()->back()
    //             ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    //     }
    // }

    /**
     * API untuk mendapatkan kebutuhan bahan baku
     */
    public function getKebutuhanBahan(Request $request)
    {
        $idResep = $request->id_resep;
        $jumlahTarget = $request->jumlah_target;

        $resep = Resep::find($idResep);
        if (!$resep) {
            return response()->json(['error' => 'Resep tidak ditemukan'], 404);
        }

        $multiplier = $jumlahTarget / $resep->kapasitas_produksi;
        $kebutuhan = $this->hitungKebutuhanBahanBaku($idResep, $multiplier);

        return response()->json([
            'success' => true,
            'kebutuhan' => $kebutuhan,
            'resep' => $resep,
        ]);
    }
    
    /**
     * Simpan produk baru via AJAX
     */
    public function storeProduk(Request $request)
    {
        $request->validate([
            'id_resep' => 'required|exists:resep,id_resep',
            'nama_produk' => 'required|string|max:20|unique:produk,nama_produk',
            'kategori' => 'required|string|max:15',
            'harga_jual' => 'required|numeric|min:0',
            'satuan' => 'required|string|max:3',
            'stok_minimum' => 'required|integer|min:0',
            'deskripsi' => 'nullable|string',
        ]);

        try {
            // Cek apakah produk sudah ada
            $produkExist = Produk::where('nama_produk', $request->nama_produk)->first();

            if ($produkExist) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produk dengan nama tersebut sudah ada.'
                ], 400); // Return error jika produk sudah ada
            }

            // Generate ID Produk
            $lastProduk = Produk::orderBy('id_produk', 'desc')->first();
            $nextNumber = $lastProduk 
                ? intval(substr($lastProduk->id_produk, 3)) + 1 
                : 1;
            $idProduk = 'PRK' . str_pad($nextNumber, 2, '0', STR_PAD_LEFT);

            // Generate Kode Produk
            $kodeProduk = 'PRD' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

            // Simpan produk baru
            $produk = Produk::create([
                'id_produk' => $idProduk,
                'id_resep' => $request->id_resep,
                'nama_produk' => $request->nama_produk,
                'kode_produk' => $kodeProduk,
                'kategori' => $request->kategori,
                'harga_jual' => $request->harga_jual,
                'satuan' => $request->satuan,
                'stok_tersedia' => 0,
                'stok_minimum' => $request->stok_minimum,
                'deskripsi' => $request->deskripsi,
                'status' => 'habis',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil ditambahkan',
                'produk' => $produk
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan produk: ' . $e->getMessage()
            ], 500);
        }
    }
    // ProdukController
    public function byResep($id_resep)
    {
        $produk = Produk::where('id_resep', $id_resep)->get();
        return response()->json($produk);
    }
    public function updateProduk(Request $request, $id_produk)
    {
        $produk = Produk::where('id_produk', $id_produk)->firstOrFail();

        $request->validate([
            'id_resep' => 'required|exists:resep,id_resep',
            'nama_produk' => [
                'required','string','max:20',
                Rule::unique('produk', 'nama_produk')->ignore($produk->id_produk, 'id_produk')
            ],
            'kategori' => 'required|string|max:15',
            'harga_jual' => 'required|numeric|min:0',
            'satuan' => 'required|string|max:10',
            'stok_minimum' => 'required|integer|min:0',
            'deskripsi' => 'nullable|string',
        ]);

        $produk->update([
            'id_resep' => $request->id_resep,
            'nama_produk' => $request->nama_produk,
            'kategori' => $request->kategori,
            'harga_jual' => $request->harga_jual,
            'satuan' => $request->satuan,
            'stok_minimum' => $request->stok_minimum,
            'deskripsi' => $request->deskripsi,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil diupdate',
            'produk' => $produk
        ]);
    }

    public function deleteProduk($id_produk)
    {
        $produk = Produk::where('id_produk', $id_produk)->firstOrFail();

        $dipakai = DetailProduksi::where('id_produk', $produk->id_produk)->exists();
        if ($dipakai) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak bisa dihapus karena sudah dipakai di data produksi.'
            ], 400);
        }

        $produk->delete();

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil dihapus'
        ]);
    }

    // public function storeProduk(Request $request)
    // {
    //     $request->validate([
    //         'id_resep' => 'required|exists:resep,id_resep',
    //         'nama_produk' => 'required|string|max:20',
    //         'kategori' => 'required|string|max:15',
    //         'harga_jual' => 'required|numeric|min:0',
    //         'satuan' => 'required|string|max:3',
    //         'stok_minimum' => 'required|integer|min:0',
    //         'deskripsi' => 'nullable|string',
    //     ]);

    //     try {
    //         // Generate ID Produk
    //         $lastProduk = Produk::orderBy('id_produk', 'desc')->first();
    //         $nextNumber = $lastProduk 
    //             ? intval(substr($lastProduk->id_produk, 3)) + 1 
    //             : 1;
    //         $idProduk = 'PRK' . str_pad($nextNumber, 2, '0', STR_PAD_LEFT);

    //         // Generate Kode Produk
    //         $kodeProduk = 'PRD' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

    //         $produk = Produk::create([
    //             'id_produk' => $idProduk,
    //             'id_resep' => $request->id_resep,
    //             'nama_produk' => $request->nama_produk,
    //             'kode_produk' => $kodeProduk,
    //             'kategori' => $request->kategori,
    //             'harga_jual' => $request->harga_jual,
    //             'satuan' => $request->satuan,
    //             'stok_tersedia' => 0,
    //             'stok_minimum' => $request->stok_minimum,
    //             'deskripsi' => $request->deskripsi,
    //             'status' => 'habis',
    //         ]);

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Produk berhasil ditambahkan',
    //             'produk' => $produk
    //         ]);

    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Gagal menambahkan produk: ' . $e->getMessage()
    //         ], 500);
    //     }
    // }
}