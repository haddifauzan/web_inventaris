<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Maintenance extends Model
{
    use HasFactory;

    protected $table = 'tbl_maintenance'; //Khusus untuk barang switch
    protected $primaryKey = 'id_maintenance';
    public $timestamps = true;

    protected $fillable = [
        'id_barang',          // Foreign key referensi ke tabel barang
        'tgl_maintenance',   // Tanggal maintenance dilakukan
        'node_terpakai',    // Jumlah node yang terpakai
        'node_bagus',       // Jumlah node yang masih bagus
        'node_rusak',       // Jumlah node yang rusak
        'status_net',       // Status jaringan (OK/Rusak)
        'petugas',          // Nama petugas maintenance
        'lokasi_switch',    // Lokasi switch berada contoh (R.MIS, R.RND., R.VAS)
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang', 'id_barang');
    }
}
