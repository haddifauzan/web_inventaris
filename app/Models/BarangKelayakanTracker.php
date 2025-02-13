<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarangKelayakanTracker extends Model
{
    protected $table = 'barang_kelayakan_trackers';
    
    protected $fillable = [
        'id_barang',
        'last_update',
        'accumulated_days'
    ];
    
    protected $casts = [
        'last_update' => 'datetime',
    ];
    
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang');
    }
}