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
        Schema::create('tbl_ip_host', function (Blueprint $table) {
            $table->id('id_ip_host');
            $table->string('ip_host')->unique(); // Contoh: 10.10.0.0 atau 10.10.0.1
            $table->foreignId('id_lokasi')->constrained('tbl_lokasi', 'id_lokasi')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_ip_host');
    }
};
