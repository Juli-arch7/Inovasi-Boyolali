<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\TahapanInovasi;
use App\Models\BentukInovasi;
use App\Models\JenisInisiator;
use App\Models\OPD;
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
        $bentukNames = ['Teknologi', 'Pertanian', 'Medis'];
        foreach ($bentukNames as $name) {
            BentukInovasi::create(['nama_bentuk' => $name]);
        }

        // ─── Jenis Inisiator ───
        $jenisNames = ['Kepala Daerah', 'Anggota DPRD', 'OPD', 'ASN', 'Masyarakat'];
        foreach ($jenisNames as $name) {
            JenisInisiator::create(['nama_jenis_inisiator' => $name]);
        }

        // ─── OPD (Dummy) ───
        $opdData = [
            ['nama_opd' => 'Dinas Komunikasi dan Informatika', 'alamat_opd' => 'Jl. Merdeka No. 1, Boyolali'],
            ['nama_opd' => 'Dinas Kesehatan', 'alamat_opd' => 'Jl. Pandanaran No. 10, Boyolali'],
            ['nama_opd' => 'Dinas Pertanian dan Pangan', 'alamat_opd' => 'Jl. Kartini No. 5, Boyolali'],
            ['nama_opd' => 'Dinas Pendidikan dan Kebudayaan', 'alamat_opd' => 'Jl. Boyolali - Solo Km 2, Boyolali'],
            ['nama_opd' => 'Dinas Perindustrian dan Perdagangan', 'alamat_opd' => 'Jl. Raya Semarang No. 8, Boyolali'],
            ['nama_opd' => 'Dinas Lingkungan Hidup', 'alamat_opd' => 'Jl. Merapi No. 3, Boyolali'],
            ['nama_opd' => 'Badan Perencanaan Pembangunan Daerah', 'alamat_opd' => 'Jl. Perintis Kemerdekaan No. 12, Boyolali'],
            ['nama_opd' => 'Dinas Sosial', 'alamat_opd' => 'Jl. Diponegoro No. 7, Boyolali'],
            ['nama_opd' => 'Dinas Kependudukan dan Pencatatan Sipil', 'alamat_opd' => 'Jl. Sudirman No. 15, Boyolali'],
            ['nama_opd' => 'Dinas Pariwisata dan Pemuda Olahraga', 'alamat_opd' => 'Jl. Merbabu No. 20, Boyolali'],
        ];
        foreach ($opdData as $opd) {
            OPD::create($opd);
        }

        // ─── Super Admin ───
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'username' => 'superadmin',
            'email' => 'superadmin@inv.com',
            'password' => Hash::make('password123'),
            'role' => 'superadmin'
        ]);
        $superAdmin->adminProfile()->create(['nama_admin' => 'Super Admin Utama', 'level' => 'super_admin']);

        // ─── Inisiator ───
        $jenisOPD = JenisInisiator::where('nama_jenis_inisiator', 'OPD')->first();
        $inisiator = User::create([
            'name' => 'Budi',
            'username' => 'budi_ini',
            'email' => 'budi@inv.com',
            'password' => Hash::make('password123'),
            'role' => 'inisiator'
        ]);
        $inisiator->inisiatorProfile()->create([
            'nama_inisiator' => 'Budi',
            'id_jenis_inisiator' => $jenisOPD->id
        ]);
    }
}
