<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DistribusiDetail extends Model
{
    use HasFactory;
    protected $table = 'distribusi_detail';
    protected $primaryKey = 'id_distribusi_detail';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_distribusi_detail',
        'id_distribusi',
        'id_lokasi',
        'id_user',
        'tanggal_detail',
        'lokasi_tujuan',
        'status_detail',
        'nama_penerima',
        'catatan',
    ];

    protected $casts = [
        'tanggal_detail' => 'datetime',
    ];


    public function distribusi()
    {
        return $this->belongsTo(Distribusi::class, 'id_distribusi', 'id_distribusi');
    }


    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class, 'id_lokasi', 'id_lokasi');
    }


    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }


    public function itemDistribusis()
    {
        return $this->hasMany(ItemDistribusi::class, 'id_distribusi_detail', 'id_distribusi_detail');
    }


    public function scopeByStatus($query, $status)
    {
        return $query->where('status_detail', $status);
    }


    public function scopePending($query)
    {
        return $query->where('status_detail', 'pending');
    }


    public function scopeDiterima($query)
    {
        return $query->where('status_detail', 'diterima');
    }

    public function scopeByLokasi($query, $idLokasi)
    {
        return $query->where('id_lokasi', $idLokasi);
    }


    public function scopeByUser($query, $idUser)
    {
        return $query->where('id_user', $idUser);
    }

 
    public function scopeByTanggal($query, $tanggal)
    {
        return $query->whereDate('tanggal_detail', $tanggal);
    }


    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => '<span class="badge bg-warning text-dark">Pending</span>',
            'diterima' => '<span class="badge bg-success">Diterima</span>',
        ];

        return $badges[$this->status_detail] ?? '<span class="badge bg-secondary">Unknown</span>';
    }


    public function getTanggalDetailFormattedAttribute()
    {
        return $this->tanggal_detail->format('d/m/Y H:i');
    }


    public function getTotalItems()
    {
        return $this->itemDistribusis()->count();
    }


    public function getTotalJumlahItems()
    {
        return $this->itemDistribusis()->sum('jumlah');
    }


    public function isDiterima()
    {
        return $this->status_detail === 'diterima';
    }


    public function isPending()
    {
        return $this->status_detail === 'pending';
    }

    public function markAsDiterima()
    {
        return $this->update(['status_detail' => 'diterima']);
    }
}