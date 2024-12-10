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
        Schema::create('produks', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED
            $table->string('nama_produk');
            $table->unsignedBigInteger('kategori_id');
            $table->unsignedBigInteger('supplier_id'); // Pastikan tipe data sesuai
            $table->integer('harga');
            $table->integer ('stok_gudang');
            $table->integer ('stok_toko');
            // $table->string('image')->default(null);
            $table->date('expired');
            $table->timestamps();

            $table->foreign('kategori_id')->references('id')->on('kategoris')->onDelete('cascade');            
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
            $table->engine = 'InnoDB';  // Pastikan menggunakan InnoDB

        });

        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produks');
    }
};
