<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IpHost extends Model
{
    use HasFactory;

    protected $table = 'tbl_ip_host';
    protected $primaryKey = 'id_ip_host';
    protected $fillable = ['ip_host', 'id_lokasi'];

    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class, 'id_lokasi');
    }

    public function ipAddresses()
    {
        return $this->hasMany(IpAddress::class, 'id_ip_host');
    }
}
