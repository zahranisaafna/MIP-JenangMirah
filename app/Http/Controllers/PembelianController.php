<?php

namespace App\Http\Controllers;

use App\Models\BahanBaku;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class PembelianController extends Controller
{
    public function index()
    {
        $allowed = [20, 40, 60, 80, 100, 250, 500];

        $perPage = (int) request('per_page', 20);
        if (!in_array($perPage, $allowed)) {
            $perPage = 20;
        }

        // Query builder untuk pembelian
        $query = DB::table('pembelian')
            ->join('users', 'pembelian.id_user', '=', 'users.id_user')
            ->select(
                'pembelian.*',
                'users.nama_user'
            );

        // Filter berdasarkan periode (harian, bulanan, tahunan)
        if (request('periode')) {
            $today = now();
            switch (request('periode')) {
                case 'harian':
                    $query->whereDate('pembelian.tanggal_pembelian', $today->toDateString());
                    break;
                case 'bulanan':
                    $query->whereYear('pembelian.tanggal_pembelian', $today->year)
                        ->whereMonth('pembelian.tanggal_pembelian', $today->month);
                    break;
                case 'tahunan':
                    $query->whereYear('pembelian.tanggal_pembelian', $today->year);
                    break;
            }
        }

        // Filter berdasarkan tanggal start
        if (request('start_date')) {
            $query->whereDate('pembelian.tanggal_pembelian', '>=', request('start_date'));
        }

        // Filter berdasarkan tanggal end
        if (request('end_date')) {
            $query->whereDate('pembelian.tanggal_pembelian', '<=', request('end_date'));
        }

        // Filter berdasarkan status pembayaran
        if (request('status_pembayaran')) {
            $query->where('pembelian.status_pembayaran', request('status_pembayaran'));
        }

        // Filter berdasarkan metode pembayaran
        if (request('metode_pembayaran')) {
            $query->where('pembelian.metode_pembayaran', request('metode_pembayaran'));
        }

        // Ambil data dengan pagination
        $pembelian = $query
            ->orderBy('pembelian.tanggal_pembelian', 'ASC')
            ->orderByRaw("CAST(SUBSTRING(pembelian.id_pembelian, 4) AS UNSIGNED) ASC")
            ->paginate($perPage)
            ->appends(request()->except('page'));

        // Hitung total pembelian yang difilter (untuk info)
        $totalFiltered = $query->sum('pembelian.total_pembelian');

        return view('module.pembelian.index', compact('pembelian', 'allowed', 'perPage', 'totalFiltered'));
    }
    // public function index()
    // {
    //     $allowed = [20, 40, 60, 80, 100, 250, 500];

    //     $perPage = (int) request('per_page', 20);
    //     if (!in_array($perPage, $allowed)) {
    //         $perPage = 20;
    //     }
    //     $pembelian = DB::table('pembelian')
    //         ->join('users', 'pembelian.id_user', '=', 'users.id_user')
    //         ->select(
    //             'pembelian.*',
    //             'users.nama_user'
    //         )
    //         ->orderByRaw("CAST(SUBSTRING(pembelian.id_pembelian, 4) AS UNSIGNED) ASC")
    //         // ->orderBy('pembelian.tanggal_pembelian', 'desc')
    //         // ->paginate(10);
    //         ->paginate($perPage)
    //         ->appends(request()->except('page'));

    //     return view('module.pembelian.index', compact('pembelian'));
    // }

    public function create()
    {
        $bahanBaku = DB::table('bahan_baku')->get();
        $suppliers = DB::table('suppliers')->where('status', 'aktif')->get();
        $pembelian = (object)[]; // Empty object untuk konsistensi

        return view('module.pembelian.form', compact('bahanBaku', 'suppliers', 'pembelian'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal_pembelian' => 'required|date',
            'metode_pembayaran' => 'required|in:cash,transfer',
            'status_pembayaran' => 'required|in:lunas,belum_lunas',
            //'tanggal_jatuh_tempo' => 'nullable|date|after:tanggal_pembelian',
            'tanggal_jatuh_tempo' => 'nullable|date|after_or_equal:tanggal_pembelian',
            'keterangan' => 'nullable|string',
            'detail' => 'required|array|min:1',
            'detail.*.id_bahan_baku' => 'required|exists:bahan_baku,id_bahan_baku',
            'detail.*.id_supplier' => 'required|exists:suppliers,id_supplier',
            'detail.*.jumlah' => 'required|numeric|min:0',
            'detail.*.harga_satuan' => 'required|numeric|min:0',
            'detail.*.tanggal_diterima' => 'required|date',
            'detail.*.kondisi' => 'required|in:baik,rusak,kadaluarsa',
        ]);

        DB::beginTransaction();
        try {
            // Generate ID Pembelian
            // $lastPembelian = DB::table('pembelian')
            //     ->orderBy('id_pembelian', 'desc')
            //     ->first();
            
            // if ($lastPembelian) {
            //     $lastNumber = intval(substr($lastPembelian->id_pembelian, 2));
            //     $newNumber = $lastNumber + 1;
            // } else {
            //     $newNumber = 1;
            // }
            // $idPembelian = 'PBL' . str_pad($newNumber, 2, '0', STR_PAD_LEFT);
           
            // ambil last berdasarkan angka numerik
            $lastPembelian = DB::table('pembelian')
                ->orderByRaw('CAST(SUBSTRING(id_pembelian, 4) AS UNSIGNED) DESC')
                ->first();

            if ($lastPembelian && preg_match('/(\d+)$/', $lastPembelian->id_pembelian, $m)) {
                $lastNumber = (int) $m[1];
                $newNumber = $lastNumber + 1;
            } else {
                $newNumber = 1;
            }

            // pastikan unik (mencegah race kecil)
            do {
                $idPembelian = 'PBL' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
                $exists = DB::table('pembelian')->where('id_pembelian', $idPembelian)->exists();
                if ($exists) $newNumber++;
            } while ($exists);

            // Hitung total pembelian
            $totalPembelian = 0;
            foreach ($request->detail as $detail) {
                $totalPembelian += $detail['jumlah'] * $detail['harga_satuan'];
            }

            // Simpan Pembelian
            DB::table('pembelian')->insert([
                'id_pembelian' => $idPembelian,
                'id_user' => Auth::user()->id_user,
                'tanggal_pembelian' => $validated['tanggal_pembelian'],
                'total_pembelian' => $totalPembelian,
                'metode_pembayaran' => $validated['metode_pembayaran'],
                'status_pembayaran' => $validated['status_pembayaran'],
                'tanggal_jatuh_tempo' => $validated['tanggal_jatuh_tempo'] ?? null,
                'keterangan' => $validated['keterangan'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            // DB::table('pembelian')->insert([
            //     'id_pembelian' => $idPembelian,
            //     'id_user' => Auth::user()->id_user,
            //     'tanggal_pembelian' => $request->tanggal_pembelian,
            //     'total_pembelian' => $totalPembelian,
            //     'metode_pembayaran' => $request->metode_pembayaran,
            //     'status_pembayaran' => $request->status_pembayaran,
            //     'tanggal_jatuh_tempo' => $request->tanggal_jatuh_tempo,
            //     'keterangan' => $request->keterangan,
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ]);

            // Simpan Detail Pembelian
            // generate id detail yang pendek dan aman: DPB + angka berurut (mis. DPB001, DPB002)
            $lastDetailRow = DB::table('detail_pembelian')
                ->orderByRaw('CAST(SUBSTRING(id_detail_pembelian, 4) AS UNSIGNED) DESC')
                // ->orderBy('id_detail_pembelian', 'desc')
                ->first();
            if ($lastDetailRow && preg_match('/(\d+)$/', $lastDetailRow->id_detail_pembelian, $m)) {
                $lastDetailNumber = (int) $m[1];
            } else {
                $lastDetailNumber = 0;
            }

            foreach ($request->detail as $detail) {
                $lastDetailNumber++;
                // hasil: DPB001, DPB002 ... (ubah padding sesuai panjang kolom di DB)
                $idDetail = 'DPB' . str_pad($lastDetailNumber, 5, '0', STR_PAD_LEFT);

                $subtotal = $detail['jumlah'] * $detail['harga_satuan'];

                DB::table('detail_pembelian')->insert([
                    'id_detail_pembelian' => $idDetail,
                    'id_pembelian' => $idPembelian,
                    'id_bahan_baku' => $detail['id_bahan_baku'],
                    'id_supplier' => $detail['id_supplier'],
                    'jumlah' => $detail['jumlah'],
                    'harga_satuan' => $detail['harga_satuan'],
                    'subtotal' => $subtotal,
                    'tanggal_diterima' => $detail['tanggal_diterima'],
                    'kondisi' => $detail['kondisi'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Update stok bahan baku jika kondisi baik
                // Update stok & harga rata-rata hanya jika kondisi "baik"
                if ($detail['kondisi'] === 'baik') {
                    $bahanBaku = DB::table('bahan_baku')
                        ->where('id_bahan_baku', $detail['id_bahan_baku'])
                        ->first();

                    if ($bahanBaku) {
                        // pastikan cast ke numeric
                        $oldStok = (float) $bahanBaku->stok_saat_ini;
                        $oldHarga = (float) $bahanBaku->harga_rata_rata;

                        $jumlahMasuk = (float) $detail['jumlah'];
                        $hargaSatuan = (float) $detail['harga_satuan'];

                        $stokBaru = $oldStok + $jumlahMasuk;

                        // Hitung harga rata-rata dengan guard
                        $oldTotal = $oldStok * $oldHarga;
                        $newTotal = $jumlahMasuk * $hargaSatuan;

                        if ($stokBaru > 0) {
                            $hargaRataRata = ($oldTotal + $newTotal) / $stokBaru;
                            $hargaRataRata = round($hargaRataRata, 2); // simpan 2 desimal
                        } else {
                            // fallback jika stokBaru 0 (seharusnya tidak terjadi jika jumlahMasuk > 0)
                            $hargaRataRata = $oldHarga;
                        }

                        // Lakukan update stok secara aman
                        DB::table('bahan_baku')
                            ->where('id_bahan_baku', $detail['id_bahan_baku'])
                            ->update([
                                'stok_saat_ini' => $stokBaru,
                                'harga_rata_rata' => $hargaRataRata,
                                'updated_at' => now(),
                            ]);
                    } else {
                        // Opsional: log kasus bahan baku tidak ditemukan
                        Log::warning('Bahan baku tidak ditemukan saat update stok', [
                            'id_bahan_baku' => $detail['id_bahan_baku'],
                            'id_pembelian' => $idPembelian ?? null,
                        ]);
                    }
                }

                // if ($detail['kondisi'] === 'baik') {
                //     $bahanBaku = DB::table('bahan_baku')
                //         ->where('id_bahan_baku', $detail['id_bahan_baku'])
                //         ->first();
                    
                //     $stokBaru = $bahanBaku->stok_saat_ini + $detail['jumlah'];
                    
                //     // Update harga rata-rata
                //     $oldTotal = $bahanBaku->stok_saat_ini * $bahanBaku->harga_rata_rata;
                //     $newTotal = $detail['jumlah'] * $detail['harga_satuan'];
                //     $hargaRataRata = ($oldTotal + $newTotal) / $stokBaru;
                    
                //     DB::table('bahan_baku')
                //         ->where('id_bahan_baku', $detail['id_bahan_baku'])
                //         ->update([
                //             'stok_saat_ini' => $stokBaru,
                //             'harga_rata_rata' => $hargaRataRata,
                //             'updated_at' => now(),
                //         ]);
                // }
            }

            DB::commit();
            return redirect()->route('pembelian.index')
                ->with('success', 'Pembelian berhasil ditambahkan');

        } 
        // catch (\Exception $e) {
        //     DB::rollBack();
        //     return redirect()->back()
        //         ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
        //         ->withInput();
        // }
        catch (\Exception $e) {
            DB::rollBack();
            Log::error('Pembelian store error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan pembelian. Silakan coba lagi.')
                ->withInput();
        }

    }

    public function show($id)
    {
        $pembelian = DB::table('pembelian')
            ->join('users', 'pembelian.id_user', '=', 'users.id_user')
            ->select('pembelian.*', 'users.nama_user')
            ->where('pembelian.id_pembelian', $id)
            ->first();

        if (!$pembelian) {
            return redirect()->route('pembelian.index')
                ->with('error', 'Data pembelian tidak ditemukan');
        }

        $details = DB::table('detail_pembelian')
            ->join('bahan_baku', 'detail_pembelian.id_bahan_baku', '=', 'bahan_baku.id_bahan_baku')
            ->join('suppliers', 'detail_pembelian.id_supplier', '=', 'suppliers.id_supplier')
            ->select(
                'detail_pembelian.*',
                'bahan_baku.nama_bahan',
                'bahan_baku.satuan',
                'suppliers.nama_supplier'
            )
            ->where('detail_pembelian.id_pembelian', $id)
            ->get();

        return view('module.pembelian.show', compact('pembelian', 'details'));
    }

    public function edit($id)
    {
        $pembelian = DB::table('pembelian')
            ->where('id_pembelian', $id)
            ->first();

        if (!$pembelian) {
            return redirect()->route('pembelian.index')
                ->with('error', 'Data pembelian tidak ditemukan');
        }

        $bahanBaku = DB::table('bahan_baku')->get();
        $suppliers = DB::table('suppliers')->where('status', 'aktif')->get();
        
        $detailPembelian = DB::table('detail_pembelian')
            ->join('bahan_baku', 'detail_pembelian.id_bahan_baku', '=', 'bahan_baku.id_bahan_baku')
            ->join('suppliers', 'detail_pembelian.id_supplier', '=', 'suppliers.id_supplier')
            ->select(
                'detail_pembelian.*',
                'bahan_baku.nama_bahan',
                'bahan_baku.satuan',
                'suppliers.nama_supplier'
            )
            ->where('detail_pembelian.id_pembelian', $id)
            ->get();

        return view('module.pembelian.form', compact('pembelian', 'bahanBaku', 'suppliers', 'detailPembelian'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'tanggal_pembelian' => 'required|date',
            'status_pembayaran' => 'required|in:lunas,belum_lunas',
            'tanggal_jatuh_tempo' => 'nullable|date',
            'keterangan' => 'nullable|string',
            'detail' => 'required|array|min:1',
            'detail.*.id_detail_pembelian' => 'required',
            'detail.*.jumlah' => 'required|numeric|min:0.01',
            'detail.*.harga_satuan' => 'required|numeric|min:0',
            'detail.*.tanggal_diterima' => 'required|date',
            'detail.*.kondisi' => 'required|in:baik,rusak,kadaluarsa',
        ]);

        DB::beginTransaction();
        try {
            $pembelian = DB::table('pembelian')
                ->where('id_pembelian', $id)
                ->first();

            if (!$pembelian) {
                return redirect()->route('pembelian.index')
                    ->with('error', 'Data pembelian tidak ditemukan');
            }

            // Update detail pembelian
            $totalPembelian = 0;
            foreach ($request->detail as $detail) {
                // Get old detail untuk kembalikan stok
                $oldDetail = DB::table('detail_pembelian')
                    ->where('id_detail_pembelian', $detail['id_detail_pembelian'])
                    ->first();

                if (!$oldDetail) {
                    continue;
                }

                // Kembalikan stok lama jika kondisi baik
                if ($oldDetail->kondisi === 'baik') {
                    DB::table('bahan_baku')
                        ->where('id_bahan_baku', $oldDetail->id_bahan_baku)
                        ->decrement('stok_saat_ini', $oldDetail->jumlah);
                }

                $subtotal = $detail['jumlah'] * $detail['harga_satuan'];
                $totalPembelian += $subtotal;

                // Update detail
                DB::table('detail_pembelian')
                    ->where('id_detail_pembelian', $detail['id_detail_pembelian'])
                    ->update([
                        'jumlah' => $detail['jumlah'],
                        'harga_satuan' => $detail['harga_satuan'],
                        'subtotal' => $subtotal,
                        'tanggal_diterima' => $detail['tanggal_diterima'],
                        'kondisi' => $detail['kondisi'],
                        'updated_at' => now(),
                    ]);

                // Tambah stok baru jika kondisi baik
                if ($detail['kondisi'] === 'baik') {
                    $bahanBaku = DB::table('bahan_baku')
                        ->where('id_bahan_baku', $oldDetail->id_bahan_baku)
                        ->first();
                    
                    $stokBaru = $bahanBaku->stok_saat_ini + $detail['jumlah'];
                    
                    // Update harga rata-rata
                    if ($stokBaru > 0) {
                        $oldTotal = $bahanBaku->stok_saat_ini * $bahanBaku->harga_rata_rata;
                        $newTotal = $detail['jumlah'] * $detail['harga_satuan'];
                        $hargaRataRata = ($oldTotal + $newTotal) / $stokBaru;
                        
                        DB::table('bahan_baku')
                            ->where('id_bahan_baku', $oldDetail->id_bahan_baku)
                            ->update([
                                'stok_saat_ini' => $stokBaru,
                                'harga_rata_rata' => $hargaRataRata,
                                'updated_at' => now(),
                            ]);
                    }
                }
            }
            
            // Update pembelian
            DB::table('pembelian')
                ->where('id_pembelian', $id)
                ->update([
                    'tanggal_pembelian' => $request->tanggal_pembelian,
                    'total_pembelian' => $totalPembelian,
                    'status_pembayaran' => $request->status_pembayaran,
                    'tanggal_jatuh_tempo' => $request->tanggal_jatuh_tempo,
                    'keterangan' => $request->keterangan,
                    'updated_at' => now(),
                ]);

            DB::commit();
            return redirect()->route('pembelian.index')
                ->with('success', 'Pembelian berhasil diupdate');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function deleteDetail($id)
    {
        DB::beginTransaction();
        try {
            $detail = DB::table('detail_pembelian')
                ->where('id_detail_pembelian', $id)
                ->first();

            if (!$detail) {
                return response()->json([
                    'success' => false,
                    'message' => 'Detail tidak ditemukan'
                ], 404);
            }

            // Kembalikan stok jika kondisi baik
            if ($detail->kondisi === 'baik') {
                DB::table('bahan_baku')
                    ->where('id_bahan_baku', $detail->id_bahan_baku)
                    ->decrement('stok_saat_ini', $detail->jumlah);
            }

            // Hapus detail
            DB::table('detail_pembelian')
                ->where('id_detail_pembelian', $id)
                ->delete();

            // Update total pembelian
            $newTotal = DB::table('detail_pembelian')
                ->where('id_pembelian', $detail->id_pembelian)
                ->sum('subtotal');

            DB::table('pembelian')
                ->where('id_pembelian', $detail->id_pembelian)
                ->update([
                    'total_pembelian' => $newTotal,
                    'updated_at' => now(),
                ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Detail berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $pembelian = DB::table('pembelian')
                ->where('id_pembelian', $id)
                ->first();

            if (!$pembelian) {
                return redirect()->route('pembelian.index')
                    ->with('error', 'Data pembelian tidak ditemukan');
            }
            
            // Kembalikan stok bahan baku
            $details = DB::table('detail_pembelian')
                ->where('id_pembelian', $id)
                ->get();

            foreach ($details as $detail) {
                if ($detail->kondisi === 'baik') {
                    DB::table('bahan_baku')
                        ->where('id_bahan_baku', $detail->id_bahan_baku)
                        ->decrement('stok_saat_ini', $detail->jumlah);
                }
            }

            // Hapus detail pembelian
            DB::table('detail_pembelian')
                ->where('id_pembelian', $id)
                ->delete();

            // Hapus pembelian
            DB::table('pembelian')
                ->where('id_pembelian', $id)
                ->delete();

            DB::commit();
            return redirect()->route('pembelian.index')
                ->with('success', 'Pembelian berhasil dihapus dan stok dikembalikan');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}