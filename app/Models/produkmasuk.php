<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProdukMasuk extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang digunakan oleh model.
     *
     * @var string
     */
    protected $table = 'produkmasuks';

    /**
     * Kolom yang dapat diisi (mass assignment).
     *
     * @var array
     */
    protected $fillable = [
        'produk_id',
        'user_id',
        'supplier_id',
        'jumlah',
        'tanggal_masuk',
    ];

    /**
     * Relasi ke model Produk.
     * Menggunakan produk_id untuk menghubungkan data produk yang masuk.
     */
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }

    /**
     * Relasi ke model User.
     * Menggunakan user_id untuk menghubungkan pengguna yang melakukan input produk masuk.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke model Supplier.
     * Menggunakan supplier_id untuk menghubungkan data supplier yang menyediakan produk.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
}
