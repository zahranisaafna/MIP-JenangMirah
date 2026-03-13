<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_supplier';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_supplier',
        'id_bahan_baku',
        'nama_supplier',
        'alamat',
        'no_telepon',
        'kontak_person',
        'status',
    ];

    public function bahanBaku()
    {
        return $this->belongsTo(BahanBaku::class, 'id_bahan_baku', 'id_bahan_baku');
    }

    public function detailPembelians()
    {
        return $this->hasMany(DetailPembelian::class, 'id_supplier', 'id_supplier');
    }
}