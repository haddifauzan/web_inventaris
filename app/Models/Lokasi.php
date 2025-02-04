<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Lokasi extends Model
{
    use HasFactory;
    protected $table = 'tbl_lokasi';
    protected $primaryKey = 'id_lokasi';
    protected $fillable = [
        'nama_lokasi', // Nama lokasi
        'deskripsi'    // Deskripsi lokasi
    ];

    public function menuAktif()
    {
        return $this->hasMany(MenuAktif::class, 'id_lokasi');
    }

    
    public function riwayat()
    {
        return $this->hasMany(Riwayat::class, 'id_lokasi');
    }
}
