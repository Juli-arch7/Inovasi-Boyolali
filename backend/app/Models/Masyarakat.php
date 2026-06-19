<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Masyarakat extends Model
{
    protected $table = 'masyarakats';
    protected $primaryKey = 'id_masyarakat';
    protected $fillable = ['nama_masyarakat'];

    public function user() {
        return $this->belongsTo(User::class);
    }
}