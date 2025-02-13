<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Departemen extends Model
{
    use HasFactory;
    protected $table = 'tbl_departemen';
    protected $primaryKey = 'id_departemen';
    protected $fillable = [
        'nama_departemen', // Nama departemen
        'deskripsi',        // Deskripsi departemen
        'id_lokasi'        // ID lokasi departemen
    ];

    public function menuAktif()
    {
        return $this->hasMany(MenuAktif::class, 'id_departemen');
    }
    
    public function riwayat()
    {
        return $this->hasMany(Riwayat::class, 'id_departemen');
    }

    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class, 'id_lokasi');
    }
}
