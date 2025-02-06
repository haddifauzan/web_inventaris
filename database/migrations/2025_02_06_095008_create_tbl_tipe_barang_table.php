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
        Schema::create('tbl_tipe_barang', function (Blueprint $table) {
            $table->id('id_tipe_barang');
            $table->enum('jenis_barang', ['Komputer', 'Tablet', 'Switch']);
            $table->string('tipe_merk');
            $table->json('spesifikasi');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_tipe_barang');
    }
};
