<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Riwayat extends Model
{
    use HasFactory;
    protected $table = 'tbl_riwayat';
    protected $primaryKey = 'id_riwayat';
    protected $fillable = [
        'id_barang',    // ID barang yang memiliki riwayat
        'id_lokasi',    // ID lokasi barang digunakan
        'id_departemen', // ID departemen barang digunakan
        'user', // User yang menggunakan barang
        'kelayakan_awal', // Kelayakan awal barang
        'kelayakan_akhir', // Kelayakan akhir barang (nullable)
        'waktu_awal',   // Waktu mulai penggunaan barang
        'waktu_akhir',  // Waktu akhir penggunaan barang (nullable)
        'status',       // Status barang dalam riwayat (Aktif/Selesai)
        'keterangan'    // Keterangan tambahan
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang');
    }
    
    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class, 'id_lokasi');
    }
    
    public function departemen()
    {
        return $this->belongsTo(Departemen::class, 'id_departemen');
    }
}
