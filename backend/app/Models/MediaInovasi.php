<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaInovasi extends Model
{
    protected $fillable = ['id_produk', 'jenis_media', 'isi_konten', 'is_primary', 'urutan'];

    public function produkInovasi() {
        return $this->belongsTo(ProdukInovasi::class, 'id_produk');
    }
}
