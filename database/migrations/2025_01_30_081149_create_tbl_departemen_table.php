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
        Schema::create('tbl_departemen', function (Blueprint $table) {
            $table->id('id_departemen');
            $table->string('nama_departemen');
            $table->text('deskripsi')->nullable();
            $table->foreignId('id_lokasi')->constrained('tbl_lokasi', 'id_lokasi')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_departemen');
    }
};
