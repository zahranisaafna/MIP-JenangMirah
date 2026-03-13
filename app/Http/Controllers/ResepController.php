<?php

namespace App\Http\Controllers;

use App\Models\Resep;
use App\Models\KomposisiResep;
use App\Models\BahanBaku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResepController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $allowed = [20, 40, 60, 80, 99];
        $perPage = (int) $request->get('per_page', 20);
        if (!in_array($perPage, $allowed)) {
            $perPage = 20;
        }

        // mulai query dasar
        $query = Resep::with('komposisiReseps');

        // FILTER: cari berdasarkan nama resep / nama komposisi produk
        if ($request->filled('search_nama')) {
            $search = $request->get('search_nama');

            $query->where(function ($q) use ($search) {
                // cari di kolom nama_resep
                $q->where('nama_resep', 'like', "%{$search}%");
            });
        }

        $resep = $query
            ->orderByRaw('CAST(SUBSTRING(id_resep, 4) AS UNSIGNED) ASC')
            ->paginate($perPage)
            ->appends($request->except('page')); // biar search_nama & per_page tetap kebawa saat pindah halaman

        return view('module.resep.index', compact('resep', 'allowed', 'perPage'));
    }

    // public function index()
    // {
    //     $allowed = [20, 40, 60, 80, 99];
    //     $perPage = (int) request('per_page', 20);
    //     if (!in_array($perPage, $allowed)) {
    //         $perPage = 20;
    //     }
    //     $resep = Resep::with('komposisiReseps')
    //         ->orderByRaw('CAST(SUBSTRING(id_resep, 4) AS UNSIGNED) ASC')
    //         ->paginate($perPage)
    //         ->appends(request()->except('page'));

    //     return view('module.resep.index', compact('resep', 'allowed', 'perPage'));
    // }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $resep = (object)[]; // Empty object untuk konsistensi
        $bahanBaku = BahanBaku::orderBy('nama_bahan')->get();
        
        return view('module.resep.form', compact('resep', 'bahanBaku'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_resep' => 'required|string|max:20',
            'waktu_produksi' => 'required|integer|min:1',
            'kapasitas_produksi' => 'required|integer|min:1',
            'satuan_output' => 'required|string|max:5',
            'status' => 'required|in:aktif,non_aktif',
            'catatan' => 'nullable|string',
            'komposisi' => 'required|array|min:1',
            'komposisi.*.id_bahan_baku' => 'required|exists:bahan_baku,id_bahan_baku',
            'komposisi.*.jumlah_diperlukan' => 'required|numeric|min:0',
            'komposisi.*.satuan' => 'required|string|max:5',
            'komposisi.*.keterangan' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Generate ID Resep (sama seperti pembelian)
            $lastResep = DB::table('resep')
                ->orderByRaw('CAST(SUBSTRING(id_resep, 4) AS UNSIGNED) DESC')
                ->first();

            if ($lastResep && preg_match('/(\d+)$/', $lastResep->id_resep, $m)) {
                $lastNumber = (int) $m[1];
                $newNumber = $lastNumber + 1;
            } else {
                $newNumber = 1;
            }

            // Pastikan unik
            do {
                $idResep = 'RSP' . str_pad($newNumber, 2, '0', STR_PAD_LEFT);
                $exists = DB::table('resep')->where('id_resep', $idResep)->exists();
                if ($exists) $newNumber++;
            } while ($exists);

            // Simpan Resep
            DB::table('resep')->insert([
                'id_resep' => $idResep,
                'nama_resep' => $validated['nama_resep'],
                'waktu_produksi' => $validated['waktu_produksi'],
                'kapasitas_produksi' => $validated['kapasitas_produksi'],
                'satuan_output' => $validated['satuan_output'],
                'status' => $validated['status'],
                'catatan' => $validated['catatan'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Generate ID Komposisi (seperti detail pembelian)
            $lastKomposisi = DB::table('komposisi_resep')
                ->orderByRaw('CAST(SUBSTRING(id_komposisi, 4) AS UNSIGNED) DESC')
                ->first();

            if ($lastKomposisi && preg_match('/(\d+)$/', $lastKomposisi->id_komposisi, $m)) {
                $lastKomposisiNumber = (int) $m[1];
            } else {
                $lastKomposisiNumber = 0;
            }

            // Simpan Komposisi
            foreach ($request->komposisi as $komposisi) {
                $lastKomposisiNumber++;
                $idKomposisi = 'KMP' . str_pad($lastKomposisiNumber, 2, '0', STR_PAD_LEFT);

                DB::table('komposisi_resep')->insert([
                    'id_komposisi' => $idKomposisi,
                    'id_resep' => $idResep,
                    'id_bahan_baku' => $komposisi['id_bahan_baku'],
                    'jumlah_diperlukan' => $komposisi['jumlah_diperlukan'],
                    'satuan' => $komposisi['satuan'],
                    'keterangan' => $komposisi['keterangan'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::commit();
            return redirect()->route('resep.index')
                ->with('success', 'Resep berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan resep: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $resep = DB::table('resep')
            ->where('id_resep', $id)
            ->first();

        if (!$resep) {
            return redirect()->route('resep.index')
                ->with('error', 'Data resep tidak ditemukan');
        }

        $komposisiReseps = DB::table('komposisi_resep')
            ->join('bahan_baku', 'komposisi_resep.id_bahan_baku', '=', 'bahan_baku.id_bahan_baku')
            ->select(
                'komposisi_resep.*',
                'bahan_baku.nama_bahan',
                'bahan_baku.satuan as satuan_bahan'
            )
            ->where('komposisi_resep.id_resep', $id)
            ->get();

        return view('module.resep.show', compact('resep', 'komposisiReseps'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $resep = DB::table('resep')
            ->where('id_resep', $id)
            ->first();

        if (!$resep) {
            return redirect()->route('resep.index')
                ->with('error', 'Data resep tidak ditemukan');
        }

        $bahanBaku = BahanBaku::orderBy('nama_bahan')->get();
        
        $komposisiReseps = DB::table('komposisi_resep')
            ->join('bahan_baku', 'komposisi_resep.id_bahan_baku', '=', 'bahan_baku.id_bahan_baku')
            ->select(
                'komposisi_resep.*',
                'bahan_baku.nama_bahan',
                'bahan_baku.satuan as satuan_bahan'
            )
            ->where('komposisi_resep.id_resep', $id)
            ->get();

        return view('module.resep.form', compact('resep', 'bahanBaku', 'komposisiReseps'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'nama_resep' => 'required|string|max:20',
            'waktu_produksi' => 'required|integer|min:1',
            'kapasitas_produksi' => 'required|integer|min:1',
            'satuan_output' => 'required|string|max:5',
            'status' => 'required|in:aktif,non_aktif',
            'catatan' => 'nullable|string',
            'komposisi' => 'required|array|min:1',
            'komposisi.*.id_komposisi' => 'required',
            'komposisi.*.jumlah_diperlukan' => 'required|numeric|min:0',
            'komposisi.*.satuan' => 'required|string|max:5',
            'komposisi.*.keterangan' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $resep = DB::table('resep')->where('id_resep', $id)->first();

            if (!$resep) {
                return redirect()->route('resep.index')
                    ->with('error', 'Data resep tidak ditemukan');
            }

            // Update Resep
            DB::table('resep')
                ->where('id_resep', $id)
                ->update([
                    'nama_resep' => $validated['nama_resep'],
                    'waktu_produksi' => $validated['waktu_produksi'],
                    'kapasitas_produksi' => $validated['kapasitas_produksi'],
                    'satuan_output' => $validated['satuan_output'],
                    'status' => $validated['status'],
                    'catatan' => $validated['catatan'] ?? null,
                    'updated_at' => now(),
                ]);

            // Update Komposisi
            foreach ($request->komposisi as $komposisi) {
                DB::table('komposisi_resep')
                    ->where('id_komposisi', $komposisi['id_komposisi'])
                    ->update([
                        'jumlah_diperlukan' => $komposisi['jumlah_diperlukan'],
                        'satuan' => $komposisi['satuan'],
                        'keterangan' => $komposisi['keterangan'] ?? null,
                        'updated_at' => now(),
                    ]);
            }

            DB::commit();
            return redirect()->route('resep.index')
                ->with('success', 'Resep berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui resep: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            $resep = DB::table('resep')->where('id_resep', $id)->first();

            if (!$resep) {
                return redirect()->route('resep.index')
                    ->with('error', 'Data resep tidak ditemukan');
            }

            // Hapus komposisi resep
            DB::table('komposisi_resep')->where('id_resep', $id)->delete();

            // Hapus resep
            DB::table('resep')->where('id_resep', $id)->delete();

            DB::commit();
            return redirect()->route('resep.index')
                ->with('success', 'Resep berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal menghapus resep: ' . $e->getMessage());
        }
    }

    /**
     * Delete komposisi resep (via AJAX)
     */
    public function deleteKomposisi($id)
    {
        DB::beginTransaction();
        try {
            $komposisi = DB::table('komposisi_resep')
                ->where('id_komposisi', $id)
                ->first();

            if (!$komposisi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Komposisi tidak ditemukan'
                ], 404);
            }

            // Hapus komposisi
            DB::table('komposisi_resep')
                ->where('id_komposisi', $id)
                ->delete();

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Komposisi berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}