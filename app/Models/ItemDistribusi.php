<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemDistribusi extends Model
{
    use HasFactory;
    protected $table = 'item_distribusi';
    protected $primaryKey = 'id_item_distribusi';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_item_distribusi',
        'id_distribusi_detail',
        // 'id_bahan_baku',
        'id_produk',
        // 'jenis_item',
        'jumlah',
        'satuan',
        'kondisi',
        'keterangan',
    ];

    protected $casts = [
        // 'jumlah' => 'decimal:2',
        'jumlah' => 'integer',
    ];


    public function distribusiDetail()
    {
        return $this->belongsTo(DistribusiDetail::class, 'id_distribusi_detail', 'id_distribusi_detail');
    }

    // public function bahanBaku()
    // {
    //     return $this->belongsTo(BahanBaku::class, 'id_bahan_baku', 'id_bahan_baku');
    // }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }

    // public function scopeBahanBakuOnly($query)
    // {
    //     return $query->where('jenis_item', 'bahan_baku');
    // }


    // public function scopeProdukJadiOnly($query)
    // {
    //     return $query->where('jenis_item', 'produk_jadi');
    // }

    public function scopeByKondisi($query, $kondisi)
    {
        return $query->where('kondisi', $kondisi);
    }

    public function scopeKondisiBaik($query)
    {
        return $query->where('kondisi', 'baik');
    }

    public function scopeKondisiRusak($query)
    {
        return $query->where('kondisi', 'rusak');
    }


    public function scopeKondisiKadaluarsa($query)
    {
        return $query->where('kondisi', 'kadaluarsa');
    }

    // public function getNamaItemAttribute()
    // {
    //     if ($this->jenis_item === 'bahan_baku' && $this->bahanBaku) {
    //         return $this->bahanBaku->nama_bahan;
    //     } elseif ($this->jenis_item === 'produk_jadi' && $this->produk) {
    //         return $this->produk->nama_produk;
    //     }
        
    //     return 'Unknown Item';
    // }
    public function getNamaItemAttribute()
    {
        return $this->produk?->nama_produk ?? 'Unknown Item';
    }

    // public function getJenisItemLabelAttribute()
    // {
    //     $labels = [
    //         'bahan_baku' => 'Bahan Baku',
    //         'produk_jadi' => 'Produk Jadi',
    //     ];

    //     return $labels[$this->jenis_item] ?? 'Unknown';
    // }


    // public function getJenisItemBadgeAttribute()
    // {
    //     $badges = [
    //         'bahan_baku' => '<span class="badge bg-info">Bahan Baku</span>',
    //         'produk_jadi' => '<span class="badge bg-primary">Produk Jadi</span>',
    //     ];

    //     return $badges[$this->jenis_item] ?? '<span class="badge bg-secondary">Unknown</span>';
    // }

    public function getKondisiBadgeAttribute()
    {
        $badges = [
            'baik' => '<span class="badge bg-success">Baik</span>',
            'rusak' => '<span class="badge bg-danger">Rusak</span>',
            'kadaluarsa' => '<span class="badge bg-warning text-dark">Kadaluarsa</span>',
        ];

        return $badges[$this->kondisi] ?? '<span class="badge bg-secondary">Unknown</span>';
    }


    public function getJumlahFormattedAttribute()
    {
        // return number_format($this->jumlah, 2, ',', '.') . ' ' . $this->satuan;
        return number_format($this->jumlah, 0, ',', '.') . ' ' . $this->satuan;
    }


    // public function isBahanBaku()
    // {
    //     return $this->jenis_item === 'bahan_baku';
    // }


    // public function isProdukJadi()
    // {
    //     return $this->jenis_item === 'produk_jadi';
    // }

    public function isKondisiBaik()
    {
        return $this->kondisi === 'baik';
    }


    public function isKondisiRusak()
    {
        return $this->kondisi === 'rusak';
    }


    public function isKondisiKadaluarsa()
    {
        return $this->kondisi === 'kadaluarsa';
    }


    // public function getItemReference()
    // {
    //     if ($this->isBahanBaku()) {
    //         return $this->bahanBaku;
    //     } elseif ($this->isProdukJadi()) {
    //         return $this->produk;
    //     }
        
    //     return null;
    // }


    public function getDetailLengkap()
    {
        $item = $this->getItemReference();
        
        return [
            'nama' => $this->nama_item,
            // 'jenis' => $this->jenis_item_label,
            'jumlah' => $this->jumlah_formatted,
            'kondisi' => $this->kondisi,
            'keterangan' => $this->keterangan,
            'reference' => $item,
        ];
    }
}