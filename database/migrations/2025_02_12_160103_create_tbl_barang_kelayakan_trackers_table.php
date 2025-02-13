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
        Schema::create('barang_kelayakan_trackers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_barang');
            $table->datetime('last_update');
            $table->integer('accumulated_days')->default(0);
            $table->timestamps();
            
            $table->foreign('id_barang')
                  ->references('id_barang')
                  ->on('tbl_barang')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_barang_kelayakan_trackers');
    }
};
