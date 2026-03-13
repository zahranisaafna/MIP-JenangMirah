<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembelian extends Model
{
    use HasFactory;

    protected $table = 'pembelian'; 
    protected $primaryKey = 'id_pembelian';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_pembelian',
        'id_user',
        'tanggal_pembelian',
        'total_pembelian',
        'status_pembayaran',
        'metode_pembayaran',
        'tanggal_jatuh_tempo',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_pembelian' => 'date',
        'tanggal_jatuh_tempo' => 'date',
        // 'total_pembelian' => 'decimal:2',
        'total_pembelian' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function detailPembelians()
    {
        return $this->hasMany(DetailPembelian::class, 'id_pembelian', 'id_pembelian');
    }
}