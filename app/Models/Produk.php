<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;
    protected $table = 'produk';
    protected $primaryKey = 'id_produk';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_produk',
        'id_resep',
        'nama_produk',
        'kode_produk',
        'kategori',
        'harga_jual',
        'satuan',
        'stok_tersedia',
        'stok_minimum',
        'deskripsi',
        'status',
    ];

    protected $casts = [
        'harga_jual' => 'decimal:2',
        // 'stok_tersedia' => 'decimal:2',
        'stok_tersedia' => 'integer',
        // 'stok_minimum' => 'decimal:2',
        'stok_minimum' => 'integer',
    ];

    public function resep()
    {
        return $this->belongsTo(Resep::class, 'id_resep', 'id_resep');
    }

    public function detailProduksis()
    {
        return $this->hasMany(DetailProduksi::class, 'id_produk', 'id_produk');
    }

    public function itemDistribusis()
    {
        return $this->hasMany(ItemDistribusi::class, 'id_produk', 'id_produk');
    }
}
