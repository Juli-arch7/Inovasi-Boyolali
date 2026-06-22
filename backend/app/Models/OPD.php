<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OPD extends Model
{
    protected $table = 'opds';

    // 🎯 TAMBAHKAN BARIS INI AGAR LARAVEL TIDAK MENCARI KOLOM 'id'
    protected $primaryKey = 'id_opd';

    protected $fillable = ['nama_opd', 'alamat_opd'];
}
