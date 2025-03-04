<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Maintenance extends Model
{
    use HasFactory;

    protected $table = 'tbl_maintenance';
    protected $primaryKey = 'id_maintenance';
    public $timestamps = true;

    protected $fillable = [
        'id_aktif',
        'status_maintenance',
        'tgl_maintenance',
        'status_net',
        'petugas',
        'lokasi_switch',
        'keterangan'
    ];

    public function aktif()
    {
        return $this->belongsTo(MenuAktif::class, 'id_aktif', 'id_aktif');
    }
}
