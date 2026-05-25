<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\TahapanInovasi;
use App\Models\BentukInovasi;
use App\Models\JenisInisiator;
use App\Models\OPD;
use App\Models\ProdukInovasi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Kecamatan ───
        $kecamatanNames = [
            'Ampel',
            'Andong',
            'Banyudono',
            'Boyolali',
            'Cepogo',
            'Gladagsari',
            'Juwangi',
            'Karanggede',
            'Kemusu',
            'Klego',
            'Mojosongo',
            'Musuk',
            'Ngemplak',
            'Nogosari',
            'Sambi',
            'Sawit',
            'Selo',
            'Simo',
            'Tamansari',
            'Teras',
            'Wonosegoro',
            'Wonosamudro'
        ];
        foreach ($kecamatanNames as $name) {
            Kecamatan::create(['nama_kecamatan' => $name]);
        }

        // ─── Kelurahan (assign to Kecamatan "Boyolali") ───
        $boyolali = Kecamatan::where('nama_kecamatan', 'Boyolali')->first();
        $kelurahanNames = ['Boyolali', 'Siswodipuran', 'Banaran', 'Bayem', 'Pulisen', 'Kalicacing'];
        foreach ($kelurahanNames as $name) {
            Kelurahan::create([
                'nama_kelurahan' => $name,
                'id_kecamatan' => $boyolali->id,
            ]);
        }

        // ─── Tahapan Inovasi ───
        $tahapanNames = ['Inisiasi', 'Uji Coba', 'Penerapan'];
        foreach ($tahapanNames as $name) {
            TahapanInovasi::create(['nama_tahapan' => $name]);
        }

        // ─── Bentuk Inovasi ───
        $bentukNames = [
            'Inovasi Pelayanan Publik',
            'Inovasi Tata Kelola Pemerintahan',
            'Inovasi Daerah Lainnya'
        ];
        $bentukModels = [];
        foreach ($bentukNames as $name) {
            $bentukModels[$name] = BentukInovasi::create(['nama_bentuk' => $name]);
        }

        // ─── Jenis Inisiator ───
        $jenisNames = ['Kepala Daerah', 'Anggota DPRD', 'OPD', 'ASN', 'Masyarakat'];
        foreach ($jenisNames as $name) {
            JenisInisiator::create(['nama_jenis_inisiator' => $name]);
        }

        // ─── OPD (Dummy & Real ones for mockup) ───
        $opdData = [
            ['nama_opd' => 'Dinas Penanaman Modal dan Pelayanan Terpadu Satu Pintu', 'alamat_opd' => 'Jl. Merdeka No. 1, Boyolali'],
            ['nama_opd' => 'Dinas Pemberdayaan Masyarakat dan Desa', 'alamat_opd' => 'Jl. Pandanaran No. 10, Boyolali'],
            ['nama_opd' => 'Dinas Komunikasi dan Informatika', 'alamat_opd' => 'Jl. Kartini No. 5, Boyolali'],
            ['nama_opd' => 'Dinas Kesehatan', 'alamat_opd' => 'Jl. Pandanaran No. 12, Boyolali'],
            ['nama_opd' => 'Dinas Pendidikan dan Kebudayaan', 'alamat_opd' => 'Jl. Boyolali - Solo Km 2, Boyolali'],
            ['nama_opd' => 'Badan Perencanaan Pembangunan Daerah', 'alamat_opd' => 'Jl. Perintis Kemerdekaan No. 12, Boyolali'],
        ];
        $opdModels = [];
        foreach ($opdData as $opd) {
            $opdModels[$opd['nama_opd']] = OPD::create($opd);
        }

        // ─── Super Admin ───
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'username' => 'superadmin',
            'email' => 'superadmin@inv.com',
            'password' => Hash::make('password123'),
            'role' => 'superadmin'
        ]);
        $superAdminProfile = $superAdmin->adminProfile()->create(['nama_admin' => 'Super Admin Utama', 'level' => 'super_admin']);

        // ─── Inisiator ───
        $jenisOPD = JenisInisiator::where('nama_jenis_inisiator', 'OPD')->first();
        $inisiator = User::create([
            'name' => 'Budi',
            'username' => 'budi_ini',
            'email' => 'budi@inv.com',
            'password' => Hash::make('password123'),
            'role' => 'inisiator'
        ]);
        $inisiatorProfile = $inisiator->inisiatorProfile()->create([
            'nama_inisiator' => 'Budi',
            'id_jenis_inisiator' => $jenisOPD->id,
            'id_kelurahan' => Kelurahan::first()->id // Assign to first Kelurahan (Siswodipuran/Boyolali)
        ]);

        // ─── Seed mock approved products for home page ───
        $tahapanPenerapan = TahapanInovasi::where('nama_tahapan', 'Penerapan')->first();

        // Product 1
        ProdukInovasi::create([
            'id_inisiator' => $inisiatorProfile->id,
            'id_opd' => $opdModels['Dinas Penanaman Modal dan Pelayanan Terpadu Satu Pintu']->id,
            'id_bentuk' => $bentukModels['Inovasi Pelayanan Publik']->id,
            'id_tahapan' => $tahapanPenerapan->id,
            'id_admin' => $superAdminProfile->id,
            'nama_inovasi' => 'Sistem Informasi Manajemen Pelayanan Terpadu Satu Pintu',
            'deskripsi' => 'Sistem integrasi pelayanan perizinan terpadu satu pintu untuk meningkatkan efisiensi dan transparansi pelayanan publik bagi masyarakat Boyolali.',
            'tahun_inovasi' => 2023,
            'status_kurasi' => 'approved',
            'is_digital' => true
        ]);

        // Product 2
        ProdukInovasi::create([
            'id_inisiator' => $inisiatorProfile->id,
            'id_opd' => $opdModels['Dinas Pemberdayaan Masyarakat dan Desa']->id,
            'id_bentuk' => $bentukModels['Inovasi Daerah Lainnya']->id,
            'id_tahapan' => $tahapanPenerapan->id,
            'id_admin' => $superAdminProfile->id,
            'nama_inovasi' => 'Program Pemberdayaan Ekonomi Kreatif Desa Mandiri',
            'deskripsi' => 'Pelatihan dan pendampingan UMKM berbasis keunggulan lokal untuk mendorong kemandirian ekonomi desa di wilayah Kabupaten Boyolali.',
            'tahun_inovasi' => 2023,
            'status_kurasi' => 'approved',
            'is_digital' => false
        ]);

        // Product 3
        ProdukInovasi::create([
            'id_inisiator' => $inisiatorProfile->id,
            'id_opd' => $opdModels['Dinas Komunikasi dan Informatika']->id,
            'id_bentuk' => $bentukModels['Inovasi Tata Kelola Pemerintahan']->id,
            'id_tahapan' => $tahapanPenerapan->id,
            'id_admin' => $superAdminProfile->id,
            'nama_inovasi' => 'Portal Data Terbuka Smart City Integrasi',
            'deskripsi' => 'Platform penyediaan data sektoral yang terbuka, terintegrasi, dan mudah diakses oleh publik guna mendukung transparansi tata kelola daerah.',
            'tahun_inovasi' => 2024,
            'status_kurasi' => 'approved',
            'is_digital' => true
        ]);
    }
}
