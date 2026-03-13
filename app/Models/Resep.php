<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resep extends Model
{
    use HasFactory;

    protected $table = 'resep';
    protected $primaryKey = 'id_resep';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_resep',
        'nama_resep',
        'waktu_produksi',
        'kapasitas_produksi',
        'satuan_output',
        'status',
        'catatan',
    ];

    protected $casts = [
        'waktu_produksi' => 'integer',
        // 'kapasitas_produksi' => 'decimal:2',
        'kapasitas_produksi' => 'integer',
    ];

    public function komposisiReseps()
    {
        return $this->hasMany(KomposisiResep::class, 'id_resep', 'id_resep');
    }

    public function produks()
    {
        return $this->hasMany(Produk::class, 'id_resep', 'id_resep');
    }

    public function detailProduksis()
    {
        return $this->hasMany(DetailProduksi::class, 'id_resep', 'id_resep');
    }
}