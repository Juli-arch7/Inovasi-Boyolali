<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InisiatorProfile extends Model
{
    protected $fillable = ['user_id', 'id_jenis_inisiator', 'id_kelurahan', 'nama_inisiator', 'kontak'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function jenisInisiator() {
        return $this->belongsTo(JenisInisiator::class, 'id_jenis_inisiator');
    }

    public function kelurahan() {
        return $this->belongsTo(Kelurahan::class, 'id_kelurahan');
    }

    public function produkInovasi() {
        return $this->hasMany(ProdukInovasi::class, 'id_inisiator');
    }
}
