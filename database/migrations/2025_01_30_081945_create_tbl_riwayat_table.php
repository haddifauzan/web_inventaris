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
        Schema::create('tbl_riwayat', function (Blueprint $table) {
            $table->id('id_riwayat');
            $table->foreignId('id_barang')->constrained('tbl_barang', 'id_barang')->onDelete('cascade');
            $table->foreignId('id_lokasi')->constrained('tbl_lokasi', 'id_lokasi')->onDelete('cascade');
            $table->foreignId('id_departemen')->constrained('tbl_departemen', 'id_departemen')->onDelete('cascade');
            $table->string('user')->nullable();
            $table->integer('kelayakan')->nullable();
            $table->dateTime('waktu_awal');
            $table->dateTime('waktu_akhir')->nullable();
            $table->enum('status', ['Aktif', 'Selesai']);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_riwayat');
    }
};
