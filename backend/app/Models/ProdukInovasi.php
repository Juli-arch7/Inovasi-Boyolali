<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProdukInovasi extends Model
{
    use HasFactory;
    protected $fillable = [
        'id_inisiator', 'id_opd', 'id_pemerintah', 'id_masyarakat', 'id_bentuk', 'id_tahapan','id_kecamatan', 'id_kelurahan', 'kontak', 'link_marketplace', 'media_inovasi', 'id_admin',
        'nama_inovasi', 'deskripsi', 'tahun_inovasi', 'status_kurasi', 'is_digital',
        'alasan_penolakan', 'tanggal_review', 'is_active'
    ];

    public function inisiatorProfile() {
        return $this->belongsTo(InisiatorProfile::class, 'id_inisiator');
    }

    public function opd() {
        return $this->belongsTo(OPD::class, 'id_opd');
    }

    public function pemerintah() {
        return $this->belongsTo(Pemerintah::class, 'id_pemerintah', 'id_pemerintah');
    }

    public function masyarakat() {
        return $this->belongsTo(Masyarakat::class, 'id_masyarakat', 'id_masyarakat');
    }

    public function kecamatan() {
        return $this->belongsTo(Kecamatan::class, 'id_kecamatan');
    }

    public function kelurahan() {
        return $this->belongsTo(Kelurahan::class, 'id_kelurahan');
    }

    public function bentukInovasi() {
        return $this->belongsTo(BentukInovasi::class, 'id_bentuk');
    }

    public function tahapanInovasi() {
        return $this->belongsTo(TahapanInovasi::class, 'id_tahapan');
    }

    public function adminProfile() {
        return $this->belongsTo(AdminProfile::class, 'id_admin');
    }

    public function mediaInovasi() {
        return $this->hasMany(MediaInovasi::class, 'id_produk');
    }
}
