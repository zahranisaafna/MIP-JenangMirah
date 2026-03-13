<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lokasi extends Model
{
    use HasFactory;
    protected $table = 'lokasi';
    protected $primaryKey = 'id_lokasi';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_lokasi',
        'nama_lokasi',
        'jenis_lokasi',
        'alamat',
        'kapasitas',
        'satuan_kapasitas',
        'penanggung_jawab',
        'no_telepon',
        'status',
    ];

    protected $casts = [
        // 'kapasitas' => 'decimal:2',
        'kapasitas' => 'integer',
    ];

    public function distribusiDetails()
    {
        return $this->hasMany(DistribusiDetail::class, 'id_lokasi', 'id_lokasi');
    }
}