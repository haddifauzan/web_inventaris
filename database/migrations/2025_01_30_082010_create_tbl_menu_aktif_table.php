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
        Schema::create('tbl_menu_aktif', function (Blueprint $table) {
            $table->id('id_aktif');
            $table->foreignId('id_barang')->constrained('tbl_barang', 'id_barang')->onDelete('cascade');
            $table->foreignId('id_lokasi')->constrained('tbl_lokasi', 'id_lokasi')->onDelete('cascade');
            $table->foreignId('id_departemen')->constrained('tbl_departemen', 'id_departemen')->onDelete('cascade');
            $table->foreignId('id_ip')->nullable()->constrained('tbl_ip_address', 'id_ip')->onDelete('set null');
            $table->string('komputer_name');
            $table->string('user');
            $table->integer('kelayakan')->nullable();
            $table->integer('node_terpakai')->nullable();
            $table->integer('node_bagus')->nullable();
            $table->integer('node_rusak')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_menu_aktif');
    }
};
