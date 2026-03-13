<?php

namespace App\Http\Controllers;

use App\Models\Distribusi;
use App\Models\DistribusiDetail;
use App\Models\ItemDistribusi;
use App\Models\Lokasi;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DistribusiController extends Controller
{
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

        $query = Distribusi::with(['distribusiDetails.lokasi', 'distribusiDetails.user']);

        // ========== FILTER SATU TANGGAL ==========
        $filterTanggal = $request->get('filter_tanggal'); // name dari input

        if (!empty($filterTanggal)) {
            // filter exact tanggal_distribusi
            $query->whereDate('tanggal_distribusi', $filterTanggal);
        }
        // ========== END FILTER SATU TANGGAL ==========

        $distribusi = $query
            ->orderBy('id_distribusi', 'asc')
            ->paginate($perPage)
            ->appends($request->except('page'));

        return view('module.distribusi.index', compact('distribusi', 'allowed', 'perPage'));
    }

    // public function index()
    // {
    //     $allowed = [20, 40, 60, 80, 100, 250, 500];
    //     $perPage = (int) request('per_page', 20);
        
    //     if (!in_array($perPage, $allowed)) {
    //         $perPage = 20;
    //     }

    //     $distribusi = Distribusi::with(['distribusiDetails.lokasi', 'distribusiDetails.user'])
    //         ->orderBy('id_distribusi', 'asc')
    //         ->paginate($perPage)
    //         ->appends(request()->except('page'));

    //     return view('module.distribusi.index', compact('distribusi', 'allowed', 'perPage'));
    // }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $lokasi = Lokasi::where('status', 'aktif')->get();
        $produk = Produk::where('status', 'tersedia')
            ->where('stok_tersedia', '>', 0)
            ->get();

        return view('module.distribusi.form', compact('lokasi', 'produk'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal_distribusi' => 'required|date',
            'jenis_distribusi' => 'required|in:internal,eksternal',
            'keterangan' => 'nullable|string',
            'details' => 'required|array|min:1',
            'details.*.id_lokasi' => 'required|exists:lokasi,id_lokasi',
            'details.*.tanggal_detail' => 'required|date',
            'details.*.nama_penerima' => 'required|string|max:50',
            'details.*.catatan' => 'nullable|string',
            'details.*.items' => 'required|array|min:1',
            'details.*.items.*.id_produk' => 'required|exists:produk,id_produk',
            'details.*.items.*.jumlah' => 'required|integer|min:1',
            'details.*.items.*.kondisi' => 'required|in:baik,rusak,kadaluarsa',
        ]);

        DB::beginTransaction();
        try {
            // Generate ID Distribusi
            $lastDistribusi = Distribusi::orderBy('id_distribusi', 'desc')->first();
            $nextNumber = $lastDistribusi 
                ? intval(substr($lastDistribusi->id_distribusi, 3)) + 1 
                : 1;
            $idDistribusi = 'DST' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

            // Generate Kode Distribusi
            // $kodeDistribusi = 'DIST-' . date('Ymd') . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

            // Cek ketersediaan stok untuk semua item
            $semuaCukup = true;
            $pesanError = [];

            foreach ($request->details as $detail) {
                foreach ($detail['items'] as $item) {
                    $produk = Produk::find($item['id_produk']);
                    if ($produk->stok_tersedia < $item['jumlah']) {
                        $semuaCukup = false;
                        $pesanError[] = sprintf(
                            '%s: Stok tersedia %d %s, diminta %d %s',
                            $produk->nama_produk,
                            $produk->stok_tersedia,
                            $produk->satuan,
                            $item['jumlah'],
                            $produk->satuan
                        );
                    }
                }
            }

            if (!$semuaCukup) {
                DB::rollBack();
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Stok produk tidak mencukupi:<br>' . implode('<br>', $pesanError));
            }

            // Create Distribusi
            $distribusi = Distribusi::create([
                'id_distribusi' => $idDistribusi,
                // 'kode_distribusi' => $kodeDistribusi,
                'tanggal_distribusi' => $request->tanggal_distribusi,
                'jenis_distribusi' => $request->jenis_distribusi,
                'keterangan' => $request->keterangan ?? '',
                'status' => 'pending',
            ]);
            // ========== GENERATOR ID DETAIL (DDBxxxxx) ==========
            $lastDetail = DistribusiDetail::orderBy('id_distribusi_detail', 'desc')->first();
            $nextDetailNumber = $lastDetail
                ? intval(substr($lastDetail->id_distribusi_detail, 3)) + 1 // buang "DDB"
                : 1;

            // ========== GENERATOR ID ITEM (IDSxxxxx) ==========
            $lastItem = ItemDistribusi::orderBy('id_item_distribusi', 'desc')->first();
            $nextItemNumber = $lastItem
                ? intval(substr($lastItem->id_item_distribusi, 3)) + 1      // buang "IDS"
                : 1;
            // Create Detail Distribusi dan Item Distribusi
            foreach ($request->details as $detailIndex => $detailData) {
                // Generate ID Detail
                // Generate ID Detail (DDB + 5 digit, global unik)
                $idDetail = 'DDB' . str_pad($nextDetailNumber, 5, '0', STR_PAD_LEFT);
                $nextDetailNumber++; // increment untuk detail berikutnya

                // $idDetail = substr($idDistribusi . ($detailIndex + 1), 0, 8);

                $lokasi = Lokasi::find($detailData['id_lokasi']);

                DistribusiDetail::create([
                    'id_distribusi_detail' => $idDetail,
                    'id_distribusi' => $idDistribusi,
                    'id_lokasi' => $detailData['id_lokasi'],
                    'id_user' => Auth::id(),
                    'tanggal_detail' => $detailData['tanggal_detail'],
                    'lokasi_tujuan' => $lokasi->nama_lokasi,
                    'status_detail' => 'pending',
                    'nama_penerima' => $detailData['nama_penerima'],
                    'catatan' => $detailData['catatan'],
                ]);

                // Create Item Distribusi
                foreach ($detailData['items'] as $itemIndex => $itemData) {
                    // Generate ID Item
                    $idItem = 'IDS' . str_pad($nextItemNumber, 5, '0', STR_PAD_LEFT);
                    $nextItemNumber++;   // increment untuk item berikutnya                    
                    // $idItem = substr($idDistribusi . ($detailIndex + 1) . ($itemIndex + 1), 0, 8);

                    $produk = Produk::find($itemData['id_produk']);

                    ItemDistribusi::create([
                        'id_item_distribusi' => $idItem,
                        'id_distribusi_detail' => $idDetail,
                        'id_produk' => $itemData['id_produk'],
                        'jumlah' => $itemData['jumlah'],
                        'satuan' => $produk->satuan,
                        'kondisi' => $itemData['kondisi'],
                        'keterangan' => $itemData['keterangan'] ?? null,
                    ]);

                    // Kurangi stok produk
                    // $produk->stok_tersedia -= $itemData['jumlah'];
                    
                    // if ($produk->stok_tersedia < 0) {
                    //     $produk->stok_tersedia = 0;
                    // }

                    // // Update status produk
                    // $produk->status = $produk->stok_tersedia > 0 ? 'tersedia' : 'habis';
                    // $produk->save();
                }
            }

            DB::commit();
            return redirect()->route('distribusi.show', $idDistribusi)
                ->with('success', 'Distribusi berhasil dibuat. Stok produk telah dikurangi.');

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
        $distribusi = Distribusi::with([
            'distribusiDetails.lokasi',
            'distribusiDetails.user',
            'distribusiDetails.itemDistribusis.produk'
        ])->findOrFail($id);

        return view('module.distribusi.show', compact('distribusi'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $distribusi = Distribusi::with([
            'distribusiDetails.itemDistribusis.produk',
            'distribusiDetails.lokasi',
            'distribusiDetails.user',
        ])->findOrFail($id);

        // Hanya bisa edit jika status masih pending
        // if ($distribusi->status !== 'pending') {
        //     return redirect()->route('distribusi.show', $id)
        //         ->with('error', 'Distribusi dengan status ' . $distribusi->status . ' tidak dapat diedit.');
        // }
        // 🔒 hanya batal yang benar-benar terkunci
        if ($distribusi->status === 'batal') {
            return redirect()->route('distribusi.show', $id)
                ->with('error', 'Distribusi dengan status batal tidak dapat diedit.');
        }
        $lokasi = Lokasi::where('status', 'aktif')->get();
        $produk = Produk::whereIn('status', ['tersedia', 'habis'])->get();

        return view('module.distribusi.form', compact('distribusi', 'lokasi', 'produk'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // $distribusi = Distribusi::findOrFail($id);
        $distribusi = Distribusi::with('distribusiDetails.itemDistribusis.produk')
            ->findOrFail($id);

        $statusLama = $distribusi->status;
        $statusBaru = $request->status ?? $statusLama;
        // $statusBaru = $request->status;
        $request->validate([
            'tanggal_distribusi' => 'required|date',
            'status' => 'required|in:pending,selesai,batal',
            'keterangan' => 'nullable|string',
            'detail' => 'required|array|min:1',
            'detail.*.status_detail' => 'nullable|in:pending,diterima',
            // 'detail.*.status_detail' => 'required|in:pending,diterima',
            'detail.*.nama_penerima' => 'required|string|max:50',
            'detail.*.catatan' => 'nullable|string',
        ]);
        // FINAL LOCK (SAMA PRODUKSI)
        if (in_array($statusLama, ['selesai','batal']) && $statusBaru !== $statusLama) {
            return back()->with('error', 'Distribusi sudah final dan tidak dapat diubah.');
        }
        DB::beginTransaction();
        try {
            // Update header distribusi
            $dataUpdate = [
                'keterangan' => $request->keterangan,
                'tanggal_distribusi' => $request->tanggal_distribusi,
            ];

            // hanya boleh ganti status kalau masih pending
            if ($statusLama === 'pending') {
                $dataUpdate['status'] = $request->status;
            }

            // tanggal BOLEH diubah walaupun selesai
            $dataUpdate['tanggal_distribusi'] = $request->tanggal_distribusi;

            $distribusi->update($dataUpdate);


            // Update detail distribusi
            foreach ($request->detail as $detailData) {
                $detail = DistribusiDetail::find($detailData['id_distribusi_detail']);
                if ($detail->status_detail === 'pending') {
                    $detail->update([
                        'status_detail' => $detailData['status_detail'] ?? 'pending',
                        'nama_penerima' => $detailData['nama_penerima'],
                        'catatan' => $detailData['catatan'],
                    ]);
                } else {
                    // status_detail diterima → LOCK
                    $detail->update([
                        'nama_penerima' => $detailData['nama_penerima'],
                        'catatan' => $detailData['catatan'],
                    ]);
                }
                // if ($detail) {
                //     $detail->update([
                //         'status_detail' => $detailData['status_detail'],
                //         'nama_penerima' => $detailData['nama_penerima'],
                //         'catatan' => $detailData['catatan'],
                //     ]);
                // }
            }
            // 🔥 POTONG STOK SEKALI SAAT pending → selesai
            if ($statusLama === 'pending' && $statusBaru === 'selesai') {
                foreach ($distribusi->distribusiDetails as $detail) {
                    foreach ($detail->itemDistribusis as $item) {
                        $produk = $item->produk;
                        $produk->stok_tersedia -= $item->jumlah;
                        if ($produk->stok_tersedia < 0) $produk->stok_tersedia = 0;
                        $produk->status = $produk->stok_tersedia > 0 ? 'tersedia' : 'habis';
                        $produk->save();
                    }
                }
            }
            DB::commit();
            return redirect()->route('distribusi.show', $id)
                ->with('success', 'Distribusi berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $distribusi = Distribusi::findOrFail($id);

            // 🔒 HANYA BOLEH HAPUS JIKA MASIH PENDING
            if ($distribusi->status === 'batal') {
                return redirect()->route('distribusi.show', $id)
                    ->with('error', 'Distribusi dengan status batal tidak dapat diedit.');
            }

            // ❌ TIDAK ADA LOGIKA STOK DI SINI
            // karena stok belum pernah dipotong (pending)

            // Hapus distribusi (cascade detail & item)
            $distribusi->delete();

            DB::commit();
            return redirect()->route('distribusi.index')
                ->with('success', 'Distribusi berhasil dihapus.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // public function destroy($id)
    // {
    //     DB::beginTransaction();
    //     try {
    //         $distribusi = Distribusi::with([
    //             'distribusiDetails.itemDistribusis.produk'
    //         ])->findOrFail($id);
    //         // Hanya bisa hapus jika status masih pending
    //         if (in_array($distribusi->status, ['selesai', 'batal'])) {
    //             return redirect()->back()
    //                 ->with('error', 'Distribusi dengan status ' . $distribusi->status . ' tidak dapat dihapus.');
    //         }
    //         // Kembalikan stok HANYA jika status selesai
    //         if ($distribusi->status === 'selesai') {
    //             foreach ($distribusi->distribusiDetails as $detail) {
    //                 foreach ($detail->itemDistribusis as $item) {
    //                     $produk = $item->produk;
    //                     if ($produk) {
    //                         $produk->stok_tersedia += $item->jumlah;
    //                         $produk->status = $produk->stok_tersedia > 0 ? 'tersedia' : 'habis';
    //                         $produk->save();
    //                     }
    //                 }
    //             }
    //         }
    //         // // Kembalikan stok produk
    //         // foreach ($distribusi->distribusiDetails as $detail) {
    //         //     foreach ($detail->itemDistribusis as $item) {
    //         //         $produk = $item->produk;
    //         //         if ($produk) {
    //         //             $produk->stok_tersedia += $item->jumlah;
    //         //             $produk->status = $produk->stok_tersedia > 0 ? 'tersedia' : 'habis';
    //         //             $produk->save();
    //         //         }
    //         //     }
    //         // }

    //         // Hapus distribusi (cascade akan menghapus detail dan item)
    //         $distribusi->delete();

    //         DB::commit();
    //         return redirect()->route('distribusi.index')
    //             ->with('success', 'Distribusi berhasil dihapus dan stok produk telah dikembalikan.');

    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return redirect()->back()
    //             ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    //     }
    // }

    /**
     * Delete distribusi detail
     */
    // public function deleteDetail($id)
    // {
    //     DB::beginTransaction();
    //     try {
    //         $detail = DistribusiDetail::with('itemDistribusis.produk')->findOrFail($id);

    //         // Kembalikan stok produk
    //         foreach ($detail->itemDistribusis as $item) {
    //             $produk = $item->produk;
    //             if ($produk) {
    //                 $produk->stok_tersedia += $item->jumlah;
    //                 $produk->status = $produk->stok_tersedia > 0 ? 'tersedia' : 'habis';
    //                 $produk->save();
    //             }
    //         }

    //         $detail->delete();

    //         DB::commit();
    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Detail distribusi berhasil dihapus dan stok produk dikembalikan.'
    //         ]);

    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Terjadi kesalahan: ' . $e->getMessage()
    //         ], 500);
    //     }
    // }
}