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
        Schema::create('produkmasuks', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED
            $table->unsignedBigInteger('produk_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('supplier_id');
            $table->integer('jumlah');
            $table->date('tanggal_masuk');
            $table->timestamps();
            
            $table->foreign('produk_id')->references('id')->on('produks')->onDelete('cascade');            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');            
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produkmasuks');
    }
};
