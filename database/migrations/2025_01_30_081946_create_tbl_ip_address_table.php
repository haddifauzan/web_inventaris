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
        Schema::create('tbl_ip_address', function (Blueprint $table) {
            $table->id('id_ip');
            $table->string('ip_address')->unique();
            $table->enum('status', ['Available', 'In Use', 'Blocked']);
            $table->foreignId('id_ip_host')->constrained('tbl_ip_host', 'id_ip_host')->onDelete('cascade');
            $table->foreignId('id_barang')->constrained('tbl_barang', 'id_barang')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_ip_address');
    }
};
