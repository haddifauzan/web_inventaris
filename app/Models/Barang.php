<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Barang extends Model
{
    use HasFactory;
    protected $table = 'tbl_barang';
    protected $primaryKey = 'id_barang';
    protected $fillable = [
        'jenis_barang',  // Jenis barang: Komputer, Tablet, Switch.
        'model',         // Model dari barang
        'tipe_merk',     // Tipe merk barang
        'serial',        // Nomor seri unik barang
        'operating_system', // OS barang - khusus untuk Komputer
        'spesifikasi',   // Spesifikasi dalam format JSON
        'kelayakan',     // Kelayakan barang
        'tahun_perolehan', // Tahun barang diperoleh dalam format date
        'kepemilikan', // Kepemilikan barang (Inventaris, NOP)
        'status',        // Status barang (Backup, Aktif, Pemusnahan)
    ];
    protected $casts = ['spesifikasi' => 'array'];

    public function menuAktif()
    {
        return $this->hasMany(MenuAktif::class, 'id_barang');
    }
    
    public function ipAddress()
    {
        return $this->hasOne(IpAddress::class, 'id_barang');
    }
    
    public function riwayat()
    {
        return $this->hasMany(Riwayat::class, 'id_barang');
    }
    
    public function menuBackup()
    {
        return $this->hasOne(MenuBackup::class, 'id_barang');
    }
    
    public function menuPemusnahan()
    {
        return $this->hasOne(MenuPemusnahan::class, 'id_barang');
    }

    public function kelayakanTracker()
    {
        return $this->hasOne(BarangKelayakanTracker::class, 'id_barang');
    }

    public function maintenance()
    {
        return $this->hasMany(Maintenance::class, 'id_barang', 'id_barang');
    }
}
