<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserSettingController;
use App\Http\Controllers\BahanBakuController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\ResepController;
use App\Http\Controllers\ProduksiController;
use App\Http\Controllers\DistribusiController;
use App\Http\Controllers\lokasiController;
use App\Http\Controllers\LaporanProduksiController;
use App\Http\Controllers\LaporanDistribusiController;
// Guest routes (belum login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// Authenticated routes (sudah login)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Dashboard default - redirect based on role
    Route::get('/dashboard', function () {
        $user = Auth::user();
        
        switch ($user->role) {
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'karyawanproduksi':
                return redirect()->route('produksi.dashboard');
            case 'owner':
                return redirect()->route('owner.dashboard');
            default:
                return redirect()->route('admin.dashboard');
        }
    })->name('dashboard');
    
    // Dashboard untuk Admin
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
        
        // User Management Routes
        Route::resource('setting-user', UserSettingController::class)->except(['show']);
        Route::post('/setting-user/check-duplicate', [UserSettingController::class, 'checkDuplicate'])->name('setting-user.check-duplicate');
        
        // Bahan Baku Routes 
        Route::resource('bahan-baku', BahanBakuController::class);
        Route::post('bahan-baku/check-duplicate', [BahanBakuController::class, 'checkDuplicate'])->name('bahan-baku.check-duplicate');

        // Supplier Routes
        Route::resource('supplier', SupplierController::class);
        Route::post('supplier/check-duplicate', [SupplierController::class, 'checkDuplicate'])->name('supplier.check-duplicate');

        // Pembelian Routes
        Route::delete('pembelian/detail/{id}', [PembelianController::class, 'deleteDetail'])->name('pembelian.detail.delete');
        Route::resource('pembelian', PembelianController::class);

        // Resep Routes
        Route::post('resep/{idResep}/komposisi', [ResepController::class, 'storeKomposisi'])->name('resep.komposisi.store');
        Route::delete('resep/komposisi/{idKomposisi}', [ResepController::class, 'deleteKomposisi'])->name('resep.komposisi.delete');
        Route::resource('resep', ResepController::class);
        Route::post('resep/check-duplicate', [ResepController::class, 'checkDuplicate'])->name('resep.check-duplicate');
    });
    
    // Dashboard untuk Karyawan Produksi
    Route::middleware('role:karyawanproduksi')->group(function () {
        Route::get('/produksi/dashboard', [DashboardController::class, 'index'])->name('produksi.dashboard');
        // Tambahkan route produksi lainnya di sini
        // // Resep Routes
        // Route::post('resep/{idResep}/komposisi', [ResepController::class, 'storeKomposisi'])->name('resep.komposisi.store');
        // Route::delete('resep/komposisi/{idKomposisi}', [ResepController::class, 'deleteKomposisi'])->name('resep.komposisi.delete');
        // Route::resource('resep', ResepController::class);
        // Route untuk AJAX - mendapatkan kebutuhan bahan baku
        Route::get('produksi/kebutuhan-bahan', [ProduksiController::class, 'getKebutuhanBahan'])
            ->name('produksi.kebutuhan-bahan');
        // Route resource untuk Produksi
        Route::resource('produksi', ProduksiController::class);
        // Route resource untuk Produk
        Route::post('produksi/store-produk', [ProduksiController::class, 'storeProduk'])
            ->name('produksi.store.produk');
        Route::put('produksi/produk/{id}', [ProduksiController::class, 'updateProduk'])
            ->name('produksi.update.produk');
        Route::delete('produksi/produk/{id}', [ProduksiController::class, 'deleteProduk'])
            ->name('produksi.delete.produk');
        Route::get('produksi/produk-by-resep/{id_resep}', [ProduksiController::class, 'byResep'])
            ->name('produksi.produk.by-resep');
        // Distribusi Routes
        Route::delete('distribusi/detail/{id}', [DistribusiController::class, 'deleteDetail'])->name('distribusi.detail.delete');
        Route::resource('distribusi', DistribusiController::class);

        // Route AJAX untuk tambah lokasi dari modal
        Route::post('/lokasi/store-ajax', [LokasiController::class, 'storeAjax'])->name('lokasi.store.ajax');
        Route::put('/lokasi/{id}/update-ajax', [LokasiController::class, 'updateAjax'])->name('lokasi.update.ajax');
        Route::delete('/lokasi/{id}/delete-ajax', [LokasiController::class, 'deleteAjax'])->name('lokasi.delete.ajax');

        // Route resource untuk lokasi (opsional jika ingin CRUD penuh)
        Route::resource('lokasi', LokasiController::class);
    });
    
    // Dashboard untuk Owner
    Route::middleware('role:owner')->group(function () {
        Route::get('/owner/dashboard', [DashboardController::class, 'index'])->name('owner.dashboard');
        // Tambahkan route owner lainnya di sini
        // Laporan Produksi
        Route::get('/laporan-produksi', [LaporanProduksiController::class, 'index'])->name('laporan.produksi.index');
        Route::get('/laporan-produksi/pdf', [LaporanProduksiController::class, 'downloadPdf'])->name('laporan.produksi.pdf');
                // Laporan Distribusi (READ ONLY untuk Owner)
        Route::get('/laporan-distribusi', [LaporanDistribusiController::class, 'index'])->name('laporan.distribusi.index');
        Route::get('/laporan-distribusi/pdf', [LaporanDistribusiController::class, 'downloadPdf'])->name('laporan.distribusi.pdf');
        
        // Show distribusi (read only)
        Route::get('/distribusi/{id}', [DistribusiController::class, 'show'])->name('distribusi.show');
    });
    
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
});

// Redirect root ke login atau dashboard
Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});