<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LogStok extends Model
{
    use HasFactory;

    protected $table = 'logstoks'; // Nama tabel
    protected $fillable = [
        'produk_id', 
        'jumlah_perubahan', 
        'stok_sebelum', 
        'stok_setelah', 
        'jenis_perubahan', 
        'tanggal_perubahan', 
        'user_id', 
        'keterangan'
    ];
    
    // Definisikan relasi dengan produk
    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }

    // Definisikan relasi dengan user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
