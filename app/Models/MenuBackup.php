<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MenuBackup extends Model
{
    use HasFactory;
    protected $table = 'tbl_menu_backup';
    protected $primaryKey = 'id_backup';
    protected $fillable = [
        'id_barang',   // ID barang yang dibackup
        'keterangan'   // Keterangan backup
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang');
    }
}
