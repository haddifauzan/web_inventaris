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
        Schema::create('tbl_barang', function (Blueprint $table) {
            $table->id('id_barang');
            $table->enum('jenis_barang', ['Komputer', 'Tablet', 'Switch']);
            $table->string('model');
            $table->string('tipe_merk');
            $table->string('serial')->unique();
            $table->string('operating_system');
            $table->json('spesifikasi');
            $table->date('tahun_perolehan');
            $table->enum('status', ['Backup', 'Aktif', 'Pemusnahan', 'Baru']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_barang');
    }
};
