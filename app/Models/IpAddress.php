<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IpAddress extends Model
{
    use HasFactory;
    protected $table = 'tbl_ip_address';
    protected $primaryKey = 'id_ip';
    protected $fillable = [
        'ip_address', // Alamat IP unik
        'status',     // Status IP (Available/In Use/Blocked)
        'id_barang'   // ID barang yang menggunakan IP ini
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public static function getAvailableStatuses()
    {
        return ['Available', 'In Use', 'Blocked'];
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang');
    }
    
}
