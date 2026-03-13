<?php

namespace App\Http\Controllers;

use App\Models\Lokasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LokasiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $allowed = [20, 40, 60, 80, 100, 250, 500];
        $perPage = (int) request('per_page', 20);
        
        if (!in_array($perPage, $allowed)) {
            $perPage = 20;
        }

        $lokasi = Lokasi::orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->appends(request()->except('page'));

        return view('module.lokasi.index', compact('lokasi', 'allowed', 'perPage'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('module.lokasi.form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_lokasi' => 'required|string|max:50',
            'jenis_lokasi' => 'required|in:gudang,toko',
            'alamat' => 'nullable|string',
            'kapasitas' => 'required|integer|min:1',
            'satuan_kapasitas' => 'required|string|max:10',
            'penanggung_jawab' => 'required|string|max:100',
            'no_telepon' => 'nullable|string|max:15',
            'status' => 'required|in:aktif,non_aktif',
        ]);

        DB::beginTransaction();
        try {
            // Generate ID Lokasi
            $lastLokasi = Lokasi::orderBy('id_lokasi', 'desc')->first();
            $nextNumber = $lastLokasi 
                ? intval(substr($lastLokasi->id_lokasi, 3)) + 1 
                : 1;
            $idLokasi = 'LOK' . str_pad($nextNumber, 2, '0', STR_PAD_LEFT);

            Lokasi::create([
                'id_lokasi' => $idLokasi,
                'nama_lokasi' => $request->nama_lokasi,
                'jenis_lokasi' => $request->jenis_lokasi,
                'alamat' => $request->alamat,
                'kapasitas' => $request->kapasitas,
                'satuan_kapasitas' => $request->satuan_kapasitas,
                'penanggung_jawab' => $request->penanggung_jawab,
                'no_telepon' => $request->no_telepon,
                'status' => $request->status,
            ]);

            DB::commit();
            return redirect()->route('lokasi.index')
                ->with('success', 'Lokasi berhasil ditambahkan.');

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
        $lokasi = Lokasi::with('distribusiDetails.distribusi')->findOrFail($id);
        return view('module.lokasi.show', compact('lokasi'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $lokasi = Lokasi::findOrFail($id);
        return view('module.lokasi.form', compact('lokasi'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $lokasi = Lokasi::findOrFail($id);

        $request->validate([
            'nama_lokasi' => 'required|string|max:50',
            'jenis_lokasi' => 'required|in:gudang,toko',
            'alamat' => 'nullable|string',
            'kapasitas' => 'required|integer|min:1',
            'satuan_kapasitas' => 'required|string|max:10',
            'penanggung_jawab' => 'required|string|max:100',
            'no_telepon' => 'nullable|string|max:15',
            'status' => 'required|in:aktif,non_aktif',
        ]);

        DB::beginTransaction();
        try {
            $lokasi->update([
                'nama_lokasi' => $request->nama_lokasi,
                'jenis_lokasi' => $request->jenis_lokasi,
                'alamat' => $request->alamat,
                'kapasitas' => $request->kapasitas,
                'satuan_kapasitas' => $request->satuan_kapasitas,
                'penanggung_jawab' => $request->penanggung_jawab,
                'no_telepon' => $request->no_telepon,
                'status' => $request->status,
            ]);

            DB::commit();
            return redirect()->route('lokasi.index')
                ->with('success', 'Lokasi berhasil diperbarui.');

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
            $lokasi = Lokasi::findOrFail($id);

            // Cek apakah lokasi memiliki distribusi
            $hasDistribusi = $lokasi->distribusiDetails()->exists();
            
            if ($hasDistribusi) {
                return redirect()->back()
                    ->with('error', 'Lokasi tidak dapat dihapus karena memiliki data distribusi.');
            }

            $lokasi->delete();

            DB::commit();
            return redirect()->route('lokasi.index')
                ->with('success', 'Lokasi berhasil dihapus.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    
/**
 * Store lokasi via AJAX
 */
    public function storeAjax(Request $request)
    {
        $request->validate([
            'nama_lokasi' => 'required|string|max:50',
            'jenis_lokasi' => 'required|in:gudang,toko',
            'alamat' => 'nullable|string',
            'kapasitas' => 'required|integer|min:1',
            'satuan_kapasitas' => 'required|string|max:10',
            'penanggung_jawab' => 'required|string|max:100',
            'no_telepon' => 'nullable|string|max:15',
            'status' => 'required|in:aktif,non_aktif',
        ]);

        DB::beginTransaction();
        try {
            // Cek duplikat nama lokasi (case insensitive)
            $existingLokasi = Lokasi::whereRaw('LOWER(nama_lokasi) = ?', [strtolower($request->nama_lokasi)])->first();
            
            if ($existingLokasi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nama lokasi sudah ada, tidak boleh duplikat!'
                ], 422);
            }

            // Generate ID Lokasi
            $lastLokasi = Lokasi::orderBy('id_lokasi', 'desc')->first();
            $nextNumber = $lastLokasi 
                ? intval(substr($lastLokasi->id_lokasi, 3)) + 1 
                : 1;
            $idLokasi = 'LOK' . str_pad($nextNumber, 2, '0', STR_PAD_LEFT);

            $lokasi = Lokasi::create([
                'id_lokasi' => $idLokasi,
                'nama_lokasi' => $request->nama_lokasi,
                'jenis_lokasi' => $request->jenis_lokasi,
                'alamat' => $request->alamat,
                'kapasitas' => $request->kapasitas,
                'satuan_kapasitas' => $request->satuan_kapasitas,
                'penanggung_jawab' => $request->penanggung_jawab,
                'no_telepon' => $request->no_telepon,
                'status' => $request->status,
            ]);

            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Lokasi berhasil ditambahkan',
                'data' => $lokasi
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