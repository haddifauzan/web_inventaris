<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('tbl_maintenance', function (Blueprint $table) {
            $table->id('id_maintenance');
            $table->unsignedBigInteger('id_aktif');
            $table->enum('status_maintenance', ['sudah', 'belum']);
            $table->date('tgl_maintenance');
            $table->enum('status_net', ['OK', 'Rusak']);
            $table->string('petugas');
            $table->string('lokasi_switch');
            $table->string('keterangan');
            $table->timestamps();

            $table->foreign('id_aktif')->references('id_aktif')->on('tbl_menu_aktif')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_maintenance');
    }
};

