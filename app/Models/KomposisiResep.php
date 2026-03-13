<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KomposisiResep extends Model
{
    use HasFactory;
    protected $table = 'komposisi_resep';

    protected $primaryKey = 'id_komposisi';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_komposisi',
        'id_resep',
        'id_bahan_baku',
        'jumlah_diperlukan',
        'satuan',
        'keterangan',
    ];

    protected $casts = [
        // 'jumlah_diperlukan' => 'decimal:2',
        'jumlah_diperlukan' => 'integer',
    ];

    public function resep()
    {
        return $this->belongsTo(Resep::class, 'id_resep', 'id_resep');
    }

    public function bahanBaku()
    {
        return $this->belongsTo(BahanBaku::class, 'id_bahan_baku', 'id_bahan_baku');
    }
}
