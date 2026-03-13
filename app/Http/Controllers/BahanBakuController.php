<?php

namespace App\Http\Controllers;

use App\Models\BahanBaku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class BahanBakuController extends Controller
{
    /**
     * Display a listing of the resource.
    */
    public function index()
    {
        $allowed = [20, 40, 60, 80, 99];

        $perPage = (int) request('per_page', 20);
        if (!in_array($perPage, $allowed)) {
            $perPage = 20;
        }

        // Query builder untuk bahan baku
        $query = DB::table('bahan_baku');

        // Filter berdasarkan nama bahan
        if (request('search_nama')) {
            $query->where('nama_bahan', 'LIKE', '%' . request('search_nama') . '%');
        }

        // Filter berdasarkan kategori
        if (request('search_kategori')) {
            $query->where('kategori', request('search_kategori'));
        }

        // Filter berdasarkan status stok
        if (request('search_status')) {
            if (request('search_status') == 'menipis') {
                $query->whereRaw('stok_saat_ini <= stok_minimum');
            } elseif (request('search_status') == 'aman') {
                $query->whereRaw('stok_saat_ini > stok_minimum');
            }
        }

        // Ambil data dengan pagination
        $bahanBaku = $query->orderByRaw('CAST(SUBSTRING(id_bahan_baku, 4) AS UNSIGNED) ASC')
            ->paginate($perPage)
            ->appends(request()->except('page'));

        // Ambil daftar kategori unik untuk dropdown filter
        $kategoriList = DB::table('bahan_baku')
            ->select('kategori')
            ->distinct()
            ->orderBy('kategori')
            ->pluck('kategori');

        return view('module.bahan-baku.index', compact('bahanBaku', 'allowed', 'perPage', 'kategoriList'));
    }

    // public function index()
    // {
    //     $allowed = [20, 40, 60, 80, 99];

    //     $perPage = (int) request('per_page', 20);
    //     if (!in_array($perPage, $allowed)) {
    //         $perPage = 20;
    //     }

    //     $bahanBaku = DB::table('bahan_baku')
    //         ->orderByRaw('CAST(SUBSTRING(id_bahan_baku, 4) AS UNSIGNED) ASC')
    //         ->paginate($perPage)
    //         ->appends(request()->except('page'));

    //     return view('module.bahan-baku.index', compact('bahanBaku', 'allowed', 'perPage'));
        
    //     // $bahanBaku = DB::table('bahan_baku')
    //     //     ->orderByRaw('CAST(SUBSTRING(id_bahan_baku, 4) AS UNSIGNED) ASC')
    //     //     // ->orderBy('created_at', 'desc')
    //     //     ->paginate(10);
            
    //     // return view('module.bahan-baku.index', compact('bahanBaku'));
    // }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('module.bahan-baku.form', [
            'bahanBaku' => new BahanBaku(),
            'isEdit' => false
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_bahan' => 'required|string|max:20',
            'kategori' => 'required|string|max:15',
            'satuan' => 'required|string|max:10',
            'stok_minimum' => 'required|numeric|min:0',
            'stok_saat_ini' => 'required|numeric|min:0',
            'harga_rata_rata' => 'required|numeric|min:0',
            'tanggal_kadaluarsa' => 'nullable|date|after:today',
            'deskripsi' => 'nullable|string',
        ], [
            'nama_bahan.required' => 'Nama bahan harus diisi',
            'nama_bahan.max' => 'Nama bahan maksimal 20 karakter',
            'kategori.required' => 'Kategori harus diisi',
            'satuan.required' => 'Satuan harus diisi',
            'stok_minimum.required' => 'Stok minimum harus diisi',
            'stok_minimum.min' => 'Stok minimum tidak boleh negatif',
            'stok_saat_ini.required' => 'Stok saat ini harus diisi',
            'stok_saat_ini.min' => 'Stok saat ini tidak boleh negatif',
            'harga_rata_rata.required' => 'Harga rata-rata harus diisi',
            'harga_rata_rata.min' => 'Harga rata-rata tidak boleh negatif',
            'tanggal_kadaluarsa.after' => 'Tanggal kadaluarsa harus setelah hari ini',
        ]);
        
        DB::transaction(function () use ($validated) {
        $payload = $validated;
        $payload['id_bahan_baku'] = $this->generateId(); // BBU01..BBU99
        $payload['created_at'] = now();
        $payload['updated_at'] = now();

        try {
            DB::table('bahan_baku')->insert($payload);
        } catch (\Illuminate\Database\QueryException $e) {
            // retry sekali jika ID kebetulan bentrok
            $payload['id_bahan_baku'] = $this->generateId();
            DB::table('bahan_baku')->insert($payload);
        }
    });

    return redirect()->route('bahan-baku.index')
        ->with('success', 'Bahan baku berhasil ditambahkan');

        // // Generate ID otomatis
        // $validated['id_bahan_baku'] = $this->generateId();
        
        // // Tambahkan timestamps
        // $validated['created_at'] = now();
        // $validated['updated_at'] = now();

        // DB::table('bahan_baku')->insert($validated);

        // return redirect()->route('bahan-baku.index')
        //     ->with('success', 'Bahan baku berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $bahanBaku = DB::table('bahan_baku')
            ->where('id_bahan_baku', $id)
            ->first();

        if (!$bahanBaku) {
            abort(404, 'Bahan baku tidak ditemukan');
        }

        // Ambil riwayat penggunaan bahan baku dari produksi
        // Cari di komposisi_resep yang menggunakan bahan baku ini
        $riwayatPenggunaan = DB::table('komposisi_resep')
            ->join('detail_produksi', 'komposisi_resep.id_resep', '=', 'detail_produksi.id_resep')
            ->join('produksi', 'detail_produksi.id_produksi', '=', 'produksi.id_produksi')
            ->join('resep', 'komposisi_resep.id_resep', '=', 'resep.id_resep')
            ->join('produk', 'detail_produksi.id_produk', '=', 'produk.id_produk')
            ->join('users', 'produksi.id_user', '=', 'users.id_user')
            ->where('komposisi_resep.id_bahan_baku', $id)
            ->select(
                'produksi.id_produksi',
                // 'produksi.kode_batch',
                'produksi.tanggal_produksi',
                'produksi.status',
                'detail_produksi.jumlah_target',
                'komposisi_resep.jumlah_diperlukan',
                'komposisi_resep.satuan',
                'resep.nama_resep',
                'resep.kapasitas_produksi',
                'resep.satuan_output',
                'produk.nama_produk',
                'users.nama_user'
            )
            ->orderBy('produksi.tanggal_produksi', 'desc')
            ->orderBy('produksi.created_at', 'desc')
            ->get();

        // Format data untuk view
        $riwayatFormatted = [];
        foreach ($riwayatPenggunaan as $item) {
            $riwayatFormatted[] = [
                'produksi' => (object)[
                    'id_produksi' => $item->id_produksi,
                    // 'kode_batch' => $item->kode_batch,
                    'tanggal_produksi' => $item->tanggal_produksi,
                    'status' => $item->status,
                    'user' => (object)['nama_user' => $item->nama_user]
                ],
                'detail' => (object)[
                    'jumlah_target' => $item->jumlah_target,
                    'resep' => (object)[
                        'nama_resep' => $item->nama_resep,
                        'kapasitas_produksi' => $item->kapasitas_produksi,
                        'satuan_output' => $item->satuan_output
                    ],
                    'produk' => (object)['nama_produk' => $item->nama_produk]
                ],
                'komposisi' => (object)[
                    'jumlah_diperlukan' => $item->jumlah_diperlukan,
                    'satuan' => $item->satuan
                ]
            ];
        }

        return view('module.bahan-baku.show', [
            'bahanBaku' => $bahanBaku,
            'riwayatPenggunaan' => $riwayatFormatted
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $bahanBaku = DB::table('bahan_baku')
            ->where('id_bahan_baku', $id)
            ->first();
            
        if (!$bahanBaku) {
            abort(404, 'Bahan baku tidak ditemukan');
        }

        return view('module.bahan-baku.form', [
            'bahanBaku' => $bahanBaku,
            'isEdit' => true
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'nama_bahan' => 'required|string|max:20',
            'kategori' => 'required|string|max:15',
            'satuan' => 'required|string|max:10',
            'stok_minimum' => 'required|numeric|min:0',
            'stok_saat_ini' => 'required|numeric|min:0',
            'harga_rata_rata' => 'required|numeric|min:0',
            // 'tanggal_kadaluarsa' => 'nullable|date|after:today',
            'tanggal_kadaluarsa' => 'nullable|date',
            'deskripsi' => 'nullable|string',
        ], [
            'nama_bahan.required' => 'Nama bahan harus diisi',
            'nama_bahan.max' => 'Nama bahan maksimal 20 karakter',
            'kategori.required' => 'Kategori harus diisi',
            'satuan.required' => 'Satuan harus diisi',
            'stok_minimum.required' => 'Stok minimum harus diisi',
            'stok_minimum.min' => 'Stok minimum tidak boleh negatif',
            'stok_saat_ini.required' => 'Stok saat ini harus diisi',
            'stok_saat_ini.min' => 'Stok saat ini tidak boleh negatif',
            'harga_rata_rata.required' => 'Harga rata-rata harus diisi',
            'harga_rata_rata.min' => 'Harga rata-rata tidak boleh negatif',
            'tanggal_kadaluarsa.after' => 'Tanggal kadaluarsa harus setelah hari ini',
        ]);
        
        // Update timestamps
        $validated['updated_at'] = now();

        $updated = DB::table('bahan_baku')
            ->where('id_bahan_baku', $id)
            ->update($validated);
            
        if (!$updated) {
            return redirect()->route('bahan-baku.index')
                ->with('error', 'Bahan baku tidak ditemukan');
        }

        return redirect()->route('bahan-baku.index')
            ->with('success', 'Bahan baku berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $deleted = DB::table('bahan_baku')
                ->where('id_bahan_baku', $id)
                ->delete();
                
            if (!$deleted) {
                return redirect()->route('bahan-baku.index')
                    ->with('error', 'Bahan baku tidak ditemukan');
            }

            return redirect()->route('bahan-baku.index')
                ->with('success', 'Bahan baku berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('bahan-baku.index')
                ->with('error', 'Bahan baku tidak dapat dihapus karena masih digunakan');
        }
    }

    /**
     * Generate ID otomatis untuk bahan baku
     */
    private function generateId(): string
    {
        $max = DB::table('bahan_baku')
            ->selectRaw('MAX(CAST(SUBSTRING(id_bahan_baku, 4) AS UNSIGNED)) AS maxnum')
            ->value('maxnum');

        $next = (int)$max + 1;

        // tetap 2 digit karena kolom CHAR(5): BBU01..BBU99
        return 'BBU' . str_pad((string)$next, 2, '0', STR_PAD_LEFT);
    }

    // private function generateId()
    // {
    //     $lastBahan = DB::table('bahan_baku')
    //         ->orderBy('id_bahan_baku', 'desc')
    //         ->first();

    //     if (!$lastBahan) {
    //         return 'BBU01';
    //     }

    //     $lastNumber = intval(substr($lastBahan->id_bahan_baku, 3)); // ambil angka setelah 'BBU'
    //     $newNumber = $lastNumber + 1;

    //     return 'BBU' . str_pad($newNumber, 2, '0', STR_PAD_LEFT);
    // }
}
