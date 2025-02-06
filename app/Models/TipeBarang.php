<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipeBarang extends Model
{
    protected $table = 'tbl_tipe_barang';
    protected $primaryKey = 'id_tipe_barang';
    protected $fillable = [
        'jenis_barang', // 'Komputer', 'Tablet', 'Switch'
        'tipe_merk', // nama tipe/merk barang
        'spesifikasi' // spesifikasi barang
    ];
    protected $casts = ['spesifikasi' => 'array'];
}
