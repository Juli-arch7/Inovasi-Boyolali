<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StatusTahapan extends Model
{
    protected $table = 'status_tahapan';
    protected $primaryKey = 'id_status';

    protected $fillable = ['deskripsi'];

    // Relasi ke Tahapan Inovasi (One to Many)
    public function tahapanInovasi(): HasMany
    {
        return $this->hasMany(TahapanInovasi::class, 'id_status', 'id_status');
    }
}