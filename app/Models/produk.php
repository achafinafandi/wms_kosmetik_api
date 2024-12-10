<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Produk extends Model
{
    use HasFactory;
    
    /**
     * Table name
     */
    protected $table = 'produks'; // Jika nama tabel tidak mengikuti konvensi
    
    /**
     * Fillable fields
     *
     * @var array
     */
    protected $fillable = [
        'nama_produk',
        'kategori_id',
        'supplier_id',
        'harga',
        'stok_gudang',
        'stok_toko',
        'expired',
    ];
    
    /**
     * Casts for specific fields
     *
     * @var array
     */
    protected $casts = [
        'expired' => 'date',
    ];

    /**
     * Define relationship with Kategori
     */
    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }

    /**
     * Define relationship with Supplier
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
