<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BahanBaku extends Model
{
    use HasFactory;

    protected $table = 'bahan_baku';


    protected $primaryKey = 'id_bahan_baku';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_bahan_baku',
        'nama_bahan',
        'kategori',
        'satuan',
        'stok_minimum',
        'stok_saat_ini',
        'harga_rata_rata',
        'tanggal_kadaluarsa',
        'deskripsi',
    ];

    protected $casts = [
        'tanggal_kadaluarsa' => 'date',
        // 'stok_minimum' => 'decimal:2',
        // 'stok_saat_ini' => 'decimal:2',
        'stok_minimum' => 'integer',
        'stok_saat_ini' => 'integer',
        'harga_rata_rata' => 'decimal:2',
    ];

    public function suppliers()
    {
        return $this->hasMany(Supplier::class, 'id_bahan_baku', 'id_bahan_baku');
    }

    public function detailPembelians()
    {
        return $this->hasMany(DetailPembelian::class, 'id_bahan_baku', 'id_bahan_baku');
    }

    public function komposisiReseps()
    {
        return $this->hasMany(KomposisiResep::class, 'id_bahan_baku', 'id_bahan_baku');
    }

    // public function itemDistribusis()
    // {
    //     return $this->hasMany(ItemDistribusi::class, 'id_bahan_baku', 'id_bahan_baku');
    // }
}