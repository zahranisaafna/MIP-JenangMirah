<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produksi extends Model
{
    use HasFactory;

    protected $table = 'produksi';
    protected $primaryKey = 'id_produksi';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_produksi',
        'id_user',
        // 'kode_batch',
        'tanggal_produksi',
        'waktu_mulai',
        'waktu_selesai',
        'status',
        'total_produk_dihasilkan',
        'catatan',
    ];

    protected $casts = [
        'tanggal_produksi' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function detailProduksis()
    {
        return $this->hasMany(DetailProduksi::class, 'id_produksi', 'id_produksi');
    }
}