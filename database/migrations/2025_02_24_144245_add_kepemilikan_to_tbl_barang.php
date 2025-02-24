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
        Schema::table('tbl_barang', function (Blueprint $table) {
            $table->enum('kepemilikan', ['Inventaris', 'NOP'])->nullable()->after('tahun_perolehan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_barang', function (Blueprint $table) {
            $table->dropColumn('kepemilikan');
        });
    }
};
