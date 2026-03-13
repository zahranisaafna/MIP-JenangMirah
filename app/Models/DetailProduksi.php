<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailProduksi extends Model
{
    use HasFactory;
    protected $table = 'detail_produksi';
    protected $primaryKey = 'id_detail_produksi';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_detail_produksi',
        'id_produksi',
        'id_resep',
        'id_produk',
        'jumlah_target',
        'jumlah_berhasil',
        'jumlah_gagal',
        'persentase_keberhasilan',
        'keterangan_gagal',
    ];

    protected $casts = [
        // 'jumlah_target' => 'decimal:2',
        // 'jumlah_berhasil' => 'decimal:2',
        // 'jumlah_gagal' => 'decimal:2',
        'jumlah_target' => 'integer',
        'jumlah_berhasil' => 'integer',
        'jumlah_gagal' => 'integer',
        'persentase_keberhasilan' => 'decimal:2',
    ];

    public function produksi()
    {
        return $this->belongsTo(Produksi::class, 'id_produksi', 'id_produksi');
    }

    public function resep()
    {
        return $this->belongsTo(Resep::class, 'id_resep', 'id_resep');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }
}