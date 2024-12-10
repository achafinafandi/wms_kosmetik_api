<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProdukKeluar extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang digunakan oleh model.
     *
     * @var string
     */
    protected $table = 'produkkeluars';  // Pastikan nama tabel sesuai dengan yang ada di database

    /**
     * Kolom yang dapat diisi (mass assignment).
     *
     * @var array
     */
    protected $fillable = [
        'produk_id',  // ID produk yang keluar
        'user_id',    // ID user yang melakukan transaksi
        'jumlah',     // Jumlah produk yang keluar
        'tanggal_keluar', // Tanggal produk keluar
    ];

    /**
     * Relasi dengan model Produk.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id'); // Relasi ke model Produk
    }

    /**
     * Relasi dengan model User (jika diperlukan).
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id'); // Relasi ke model User
    }
}
