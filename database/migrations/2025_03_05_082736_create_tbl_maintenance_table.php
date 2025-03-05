<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('tbl_maintenance', function (Blueprint $table) {
            $table->id('id_maintenance');
            $table->unsignedBigInteger('id_barang');
            $table->enum('status_maintenance', ['Sudah', 'Belum']);
            $table->date('tgl_maintenance')->nullable();
            $table->integer('node_terpakai');
            $table->integer('node_bagus');
            $table->integer('node_rusak');
            $table->enum('status_net', ['OK', 'Rusak']);
            $table->string('petugas')->nullable();
            $table->string('lokasi_switch');
            $table->string('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('id_barang')->references('id_barang')->on('tbl_barang')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tbl_maintenance');
    }
};

