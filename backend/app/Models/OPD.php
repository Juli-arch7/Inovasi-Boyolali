<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OPD extends Model
{
    protected $table = 'opds';

    protected $fillable = ['nama_opd', 'alamat_opd'];
}
