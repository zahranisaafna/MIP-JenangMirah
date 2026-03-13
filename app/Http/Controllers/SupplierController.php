<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\BahanBaku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
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

        // Query builder untuk supplier
        $query = DB::table('suppliers')
            ->join('bahan_baku', 'suppliers.id_bahan_baku', '=', 'bahan_baku.id_bahan_baku')
            ->select('suppliers.*', 'bahan_baku.nama_bahan');

        // Filter berdasarkan nama supplier
        if (request('search_nama')) {
            $query->where('suppliers.nama_supplier', 'LIKE', '%' . request('search_nama') . '%');
        }

        // Filter berdasarkan kontak person
        if (request('search_kontak')) {
            $query->where('suppliers.kontak_person', 'LIKE', '%' . request('search_kontak') . '%');
        }

        // Filter berdasarkan bahan baku
        if (request('search_bahan')) {
            $query->where('suppliers.id_bahan_baku', request('search_bahan'));
        }

        // Ambil data dengan pagination
        $suppliers = $query->orderByRaw("CAST(SUBSTRING(suppliers.id_supplier, 4) AS UNSIGNED) ASC")
            ->paginate($perPage)
            ->appends(request()->except('page'));

        // Ambil daftar bahan baku unik untuk dropdown filter
        $bahanBakuList = DB::table('bahan_baku')
            ->orderBy('nama_bahan')
            ->get(['id_bahan_baku', 'nama_bahan']);
            
        return view('module.supplier.index', compact('suppliers', 'allowed', 'perPage', 'bahanBakuList'));
    }
    // public function index()
    // {
    //     $allowed = [20, 40, 60, 80, 99]; 

    //     $perPage = (int) request('per_page', 20);
    //     if (!in_array($perPage, $allowed)) {
    //         $perPage = 20;
    //     }
    //     $suppliers = DB::table('suppliers')
    //         ->join('bahan_baku', 'suppliers.id_bahan_baku', '=', 'bahan_baku.id_bahan_baku')
    //         ->select('suppliers.*', 'bahan_baku.nama_bahan')
    //         ->orderByRaw("CAST(SUBSTRING(id_supplier, 4) AS UNSIGNED) ASC")
    //         // ->orderBy('suppliers.created_at', 'desc')
    //         ->paginate($perPage)
    //         ->appends(request()->except('page'));
            
    //     return view('module.supplier.index', compact('suppliers'));
    // }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $bahanBakus = DB::table('bahan_baku')
            ->orderBy('nama_bahan', 'asc')
            ->get();
            
        return view('module.supplier.form', [
            'supplier' => new Supplier(),
            'bahanBakus' => $bahanBakus,
            'isEdit' => false
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_bahan_baku' => 'required|exists:bahan_baku,id_bahan_baku',
            'nama_supplier' => 'required|string|max:20',
            'alamat' => 'required|string',
            'no_telepon' => 'required|string|max:15|regex:/^[0-9]+$/',
            'kontak_person' => 'required|string|max:20',
            'status' => 'required|in:aktif,non_aktif',
        ], [
            'id_bahan_baku.required' => 'Bahan baku harus dipilih',
            'id_bahan_baku.exists' => 'Bahan baku tidak valid',
            'nama_supplier.required' => 'Nama supplier harus diisi',
            'nama_supplier.max' => 'Nama supplier maksimal 20 karakter',
            'alamat.required' => 'Alamat harus diisi',
            'no_telepon.required' => 'Nomor telepon harus diisi',
            'no_telepon.max' => 'Nomor telepon maksimal 15 digit',
            'no_telepon.regex' => 'Nomor telepon hanya boleh berisi angka',
            'kontak_person.required' => 'Kontak person harus diisi',
            'kontak_person.max' => 'Kontak person maksimal 20 karakter',
            'status.required' => 'Status harus dipilih',
            'status.in' => 'Status tidak valid',
        ]);

        // Generate ID otomatis
        $validated['id_supplier'] = $this->generateId();
        
        // Tambahkan timestamps
        $validated['created_at'] = now();
        $validated['updated_at'] = now();

        DB::table('suppliers')->insert($validated);

        return redirect()->route('supplier.index')
            ->with('success', 'Supplier berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $supplier = DB::table('suppliers')
            ->where('id_supplier', $id)
            ->first();
            
        if (!$supplier) {
            abort(404, 'Supplier tidak ditemukan');
        }
        
        $bahanBakus = DB::table('bahan_baku')
            ->orderBy('nama_bahan', 'asc')
            ->get();

        return view('module.supplier.form', [
            'supplier' => $supplier,
            'bahanBakus' => $bahanBakus,
            'isEdit' => true
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'id_bahan_baku' => 'required|exists:bahan_baku,id_bahan_baku',
            'nama_supplier' => 'required|string|max:20',
            'alamat' => 'required|string',
            'no_telepon' => 'required|string|max:15|regex:/^[0-9]+$/',
            'kontak_person' => 'required|string|max:20',
            'status' => 'required|in:aktif,non_aktif',
        ], [
            'id_bahan_baku.required' => 'Bahan baku harus dipilih',
            'id_bahan_baku.exists' => 'Bahan baku tidak valid',
            'nama_supplier.required' => 'Nama supplier harus diisi',
            'nama_supplier.max' => 'Nama supplier maksimal 20 karakter',
            'alamat.required' => 'Alamat harus diisi',
            'no_telepon.required' => 'Nomor telepon harus diisi',
            'no_telepon.max' => 'Nomor telepon maksimal 12 digit',
            'no_telepon.regex' => 'Nomor telepon hanya boleh berisi angka',
            'kontak_person.required' => 'Kontak person harus diisi',
            'kontak_person.max' => 'Kontak person maksimal 20 karakter',
            'status.required' => 'Status harus dipilih',
            'status.in' => 'Status tidak valid',
        ]);
        
        // Update timestamps
        $validated['updated_at'] = now();

        $updated = DB::table('suppliers')
            ->where('id_supplier', $id)
            ->update($validated);
            
        if (!$updated) {
            return redirect()->route('supplier.index')
                ->with('error', 'Supplier tidak ditemukan');
        }

        return redirect()->route('supplier.index')
            ->with('success', 'Supplier berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $deleted = DB::table('suppliers')
                ->where('id_supplier', $id)
                ->delete();
                
            if (!$deleted) {
                return redirect()->route('supplier.index')
                    ->with('error', 'Supplier tidak ditemukan');
            }

            return redirect()->route('supplier.index')
                ->with('success', 'Supplier berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('supplier.index')
                ->with('error', 'Supplier tidak dapat dihapus karena masih digunakan');
        }
    }

    /**
     * Generate ID otomatis untuk supplier
     */
    private function generateId()
    {
        $lastSupplier = DB::table('suppliers')
            ->orderBy('id_supplier', 'desc')
            ->first();

        if (!$lastSupplier) {
            return 'SUP01';
        }

        $lastNumber = intval(substr($lastSupplier->id_supplier, 3)); // ambil angka setelah 'SUP'
        $newNumber = $lastNumber + 1;

        return 'SUP' . str_pad($newNumber, 2, '0', STR_PAD_LEFT);
    }
}