<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MenuPemusnahan extends Model
{
    use HasFactory;
    protected $table = 'tbl_menu_pemusnahan';
    protected $primaryKey = 'id_pemusnahan';
    protected $fillable = [
        'id_barang',   // ID barang yang dimusnahkan
        'keterangan'   // Keterangan pemusnahan
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang');
    }
}
