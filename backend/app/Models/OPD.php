<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OPD extends Model
{
    protected $table = 'opds';

    // Primary key tetap 'id' (default Laravel) sesuai dengan struktur database yang sudah ada
    // Kolom 'id_opd' tidak ada di tabel, yang ada adalah 'id'

    protected $fillable = ['nama_opd', 'alamat_opd'];
}
