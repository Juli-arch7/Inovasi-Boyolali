<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pemerintah extends Model
{
    protected $table = 'pemerintahs';
    protected $primaryKey = 'id_pemerintah';
    protected $fillable = ['nama_pemerintah'];

    // Relasi ke Jenis Inisiator Inovasi (One to Many)
    public function jenisInisiator(): HasMany
    {
        return $this->hasMany(JenisInisiator::class, 'id_pemerintah', 'id_pemerintah');
    }
}