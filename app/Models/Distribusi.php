<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Distribusi extends Model
{
    use HasFactory;
    protected $table = 'distribusi';

    protected $primaryKey = 'id_distribusi';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_distribusi',
        // 'kode_distribusi',
        'tanggal_distribusi',
        'jenis_distribusi',
        'keterangan',
        'status',
    ];

    protected $casts = [
        'tanggal_distribusi' => 'date',
    ];

    public function distribusiDetails()
    {
        return $this->hasMany(DistribusiDetail::class, 'id_distribusi', 'id_distribusi');
    }
}