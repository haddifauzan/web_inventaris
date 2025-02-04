<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MenuAktif extends Model
{
    use HasFactory;
    protected $table = 'tbl_menu_aktif';
    protected $primaryKey = 'id_aktif';
    protected $fillable = [
        'id_barang',    // ID barang yang sedang aktif
        'id_lokasi',    // ID lokasi tempat barang digunakan
        'id_departemen', // ID departemen terkait barang
        'id_ip',   // ID IP address yang digunakan (nullable)
        'komputer_name',// Nama komputer atau device - khusus jenis barang komputer
        'user',         // Nama Pengguna barang
        'kelayakan',    // Status kelayakan barang dalam persentase
        'node_terpakai',// Jumlah node yang terpakai (nullable) - khusus jenis barang switch
        'node_bagus',   // Jumlah node dalam kondisi bagus (nullable) - khusus jenis barang switch
        'node_rusak',   // Jumlah node rusak (nullable) - khusus jenis barang switch
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

    public function ipAddress()
    {
        return $this->belongsTo(IpAddress::class, 'id_ip');
    }
}
