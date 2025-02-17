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
        Schema::table('tbl_riwayat', function (Blueprint $table) {
            $table->renameColumn('kelayakan', 'kelayakan_awal');
            $table->integer('kelayakan_akhir')->nullable()->after('kelayakan_awal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_riwayat', function (Blueprint $table) {
            $table->renameColumn('kelayakan_awal', 'kelayakan');
            $table->dropColumn('kelayakan_akhir');
        });
    }
};
