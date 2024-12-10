<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('logstoks', function (Blueprint $table) {
            $table->id(); // ID primary key
            $table->unsignedBigInteger('produk_id'); // ID produk
            $table->integer('jumlah_perubahan'); // Jumlah perubahan stok
            $table->integer('stok_sebelum'); // Stok sebelum perubahan
            $table->integer('stok_setelah'); // Stok setelah perubahan
            $table->enum('jenis_perubahan', ['masuk', 'keluar', 'penyesuaian']); // Jenis perubahan
            $table->timestamp('tanggal_perubahan')->useCurrent(); // Waktu perubahan stok
            $table->unsignedBigInteger('user_id'); // ID pengguna yang melakukan perubahan
            $table->text('keterangan')->nullable(); // Keterangan tambahan (misalnya alasan penyesuaian)

            // Foreign Key Constraints
            $table->foreign('produk_id')->references('id')->on('produks')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->timestamps(); // Created at and Updated at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logstoks');
    }
};
