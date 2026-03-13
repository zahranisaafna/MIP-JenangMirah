<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPembelian extends Model
{
    use HasFactory;
    protected $table = 'detail_pembelian'; 
    protected $primaryKey = 'id_detail_pembelian';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_detail_pembelian',
        'id_pembelian',
        'id_bahan_baku',
        'id_supplier',
        'jumlah',
        'harga_satuan',
        'subtotal',
        'tanggal_diterima',
        'kondisi',
    ];

    protected $casts = [
        'tanggal_diterima' => 'date',
        // 'jumlah' => 'decimal:2',
        'jumlah' => 'integer',
        'harga_satuan' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class, 'id_pembelian', 'id_pembelian');
    }

    public function bahanBaku()
    {
        return $this->belongsTo(BahanBaku::class, 'id_bahan_baku', 'id_bahan_baku');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'id_supplier', 'id_supplier');
    }
}