<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\TahapanInovasi;
use App\Models\BentukInovasi;
use App\Models\JenisInisiator;
use App\Models\Masyarakat;
use App\Models\Pemerintah;
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
            'Wonosamodro'
        ];
        foreach ($kecamatanNames as $name) {
            Kecamatan::create(['nama_kecamatan' => $name]);
        }

         // ─── Kelurahan (Mapping Objek per Kecamatan) ───
        $kelurahanNames = [
            'Ampel'     => ['Banyuanyar', 'Candi', 'Gondang Slamet', 'Ngampon', 'Ngargosari', 'Ngenden', 'Selodoko', 'Sidomulyo', 'Tanduk', 'Urutsewu'],
            'Andong'    => ['Andong', 'Beji', 'Gondang Rawe', 'Kacangan', 'Kadipaten', 'Kedungdowo', 'Kunti', 'Mojo', 'Munggur', 'Pakang', 'Pakel', 'Pelemrejo', 'Pranggong', 'Semawung', 'Sempu', 'Senggrong'],
            'Banyudono' => ['Bangak', 'Banyudono', 'Batan', 'Bendan', 'Cangkringan', 'Denggungan', 'Dukuh', 'Jembungan', 'Jipangan', 'Ketaon', 'Kuwiran', 'Ngaru-aru', 'Sambon', 'Tanjungsari', 'Trayu'],
            'Boyolali'  => ['Banaran', 'Karanggeneng', 'Kebonbimo', 'Kiringan', 'Mudal', 'Penggung', 'Pulisen', 'Siswodipuran', 'Winong'],
            'Cepogo'    => ['Bakulan', 'Cabeankunti', 'Candigatak', 'Cepogo', 'Gedangan', 'Genting', 'Gubug', 'Jelok', 'Jombong', 'Kembangkuning', 'Mliwis', 'Paras', 'Selo', 'Sukabumi', 'Sumbung'],
            'Gladagsari' => ['Candisari', 'Gladagsari', 'Jlarem', 'Kaligentong', 'Kembang', 'Ngadirojo', 'Ngagrong', 'Ngargoloka', 'Sampetan', 'Seboto'],
            'Juwangi'   => ['Sambeng', 'Cerme', 'Jerukan', 'Juwangi', 'Kalimati', 'Kayen', 'Krobokan', 'Ngaren', 'Ngleses', 'Pilangrejo'],
            'Karanggede' => ['Bangkok', 'Bantengan', 'Dologan', 'Grogolan', 'Karangkepoh', 'Kebonan', 'Klari', 'Klumpit', 'Manyaran', 'Mojosari', 'Pengkol', 'Pinggir', 'Sempulur', 'Sendang', 'Sranten', 'Tegalsari'],
            'Kemusu'    => ['Bawu', 'Genengsari', 'Kedungmulyo', 'Kedungrejo', 'Kemusu', 'Kendel', 'Klewor', 'Sarimulyo', 'Watugede', 'Wonoharjo'],
            'Klego'     => ['Bade', 'Banyu Urip', 'Blumbang', 'Gondanglegi', 'Jaten', 'Kalangan', 'Karanggatak', 'Karangmojo', 'Klego', 'Sangge', 'Sendangrejo', 'Sumber Agung', 'Tanjung'],
            'Mojosongo' => ['Kemiri', 'Mojosongo', 'Brajan', 'Butuh', 'Dlingo', 'Jurug', 'Karangnongko', 'Kragilan', 'Madu', 'Manggis', 'Metuk', 'Singosari', 'Tambak'],
            'Musuk'     => ['Cluntang', 'Kebongulo', 'Kembangsari', 'Musuk', 'Pagerjurang', 'Pusporenggo', 'Ringin Larik', 'Sruni', 'Sukorame', 'Sukorejo'],
            'Ngemplak'  => ['Dibal', 'Donohudan', 'Gagaksipat', 'Giriroto', 'Kismoyoso', 'Manggung', 'Ngargorejo', 'Ngesrep', 'Pandeyan', 'Sawahan', 'Sobokerto', 'Trayu'],
            'Nogosari'  => ['Bendo', 'Glonggong', 'Guli', 'Jeron', 'Kenteng', 'Ketitang', 'Keyongan', 'Pojok', 'Potronayan', 'Pulutan', 'Rembun', 'Sembungan', 'Tegalgiri'],
            'Sambi'     => ['Babadan', 'Canden', 'Catur', 'Cermo', 'Demangan', 'Glintang', 'Jagoan', 'Jatisari', 'Kepoh', 'Ngaglik', 'Nglembu', 'Sambi', 'Senting', 'Tawengan', 'Tempursari', 'Trosobo'],
            'Sawit'     => ['Bendosari', 'Cepokosawit', 'Gombang', 'Guwokajen', 'Jatirejo', 'Jenengan', 'Karangduren', 'Kateguhan', 'Kemasan', 'Manjung', 'Tegalrejo', 'Tlawong'],
            'Selo'      => ['Jeruk', 'Jrakah', 'Klakah', 'Lencoh', 'Samiran', 'Selo', 'Senden', 'Suroteleng', 'Tarubatang', 'Tlogolele'],
            'Simo'      => ['Bendungan', 'Blagung', 'Gunung', 'Kedung Lengkong', 'Pelem', 'Pentur', 'Simo', 'Sumber', 'Talakbroto', 'Temon', 'Teter', 'Walen', 'Wates'],
            'Tamansari' => ['Dragan', 'Jemowo', 'Karanganyar', 'Karangkendal', 'Keposong', 'Lanjaran', 'Lampar', 'Mriyan', 'Sangup', 'Sumur'],
            'Teras'     => ['Bangsalan', 'Doplang', 'Gumukrejo', 'Kadireso', 'Kopen', 'Krasak', 'Mojolegi', 'Nepen', 'Randusari', 'Salakan', 'Sudimoro', 'Tawangsari', 'Teras'],
            'Wonosegoro' => ['Bandung', 'Banyusri', 'Bojong', 'Bolo', 'Gosono', 'Guwo', 'Karangjati', 'Kauman', 'Ketoyan', 'Lemahireng', 'Wonosegoro'],
            'Wonosamodro' => ['Bengle', 'Bercak', 'Garangan', 'Gilirejo', 'Gunungsari', 'Jatilawang', 'Kalinanas', 'Kedungpilang', 'Ngablak', 'Repaking'],
            // Tinggal tambah baris baru di sini kalau mau melengkapi kecamatan lain
        ];

        foreach ($kelurahanNames as $kecamatanName => $daftarKelurahan) {
            // Cari data kecamatan berdasarkan nama key array di atas
            $kecamatan = Kecamatan::where('nama_kecamatan', $kecamatanName)->first();
            
            // Jika kecamatannya terdaftar, input semua kelurahannya
            if ($kecamatan) {
                foreach ($daftarKelurahan as $name) {
                    Kelurahan::create([
                        'nama_kelurahan' => $name,
                        'id_kecamatan' => $kecamatan->id,
                    ]);
                }
            }
        }

        // ─── Tahapan Inovasi ───
        $tahapanNames = ['Inisiasi', 'Uji Coba', 'Penerapan'];
        foreach ($tahapanNames as $name) {
            TahapanInovasi::create(['nama_tahapan' => $name]);
        }

        // ─── Bentuk Inovasi ───
        $bentukNames = ['Tata Kelola', 'Pelayanan Publik', 'Lainnya'];
        $bentukModels = [];
        foreach ($bentukNames as $name) {
            $bentukModels[$name] = BentukInovasi::create(['nama_bentuk' => $name]);
        }

        // ─── Jenis Inisiator ───
        $jenisNames = ['Masyarakat', 'OPD', 'Pemerintah'];
        foreach ($jenisNames as $name) {
            JenisInisiator::create(['nama_jenis_inisiator' => $name]);
        }

        // ─── Masyarakat (Dummy) ───
        $masyarakatData = [
            ['nama_masyarakat' => 'Pelajar/Mahasiswa'],
            ['nama_masyarakat' => 'UMKM'],
            ['nama_masyarakat' => 'Organisasi Kemasyarakatan'],
            ['nama_masyarakat' => 'Individu'],
            ['nama_masyarakat' => 'Lainnya'],
        ];
        $masyarakatModels = [];
        foreach ($masyarakatData as $masyarakat) {
            $masyarakatModels[$masyarakat['nama_masyarakat']] = Masyarakat::create($masyarakat);
        }

        // ─── Pemerintah (Dummy) ───
        $pemerintahData = [
            ['nama_pemerintah' => 'Pemerintah Kabupaten Boyolali'],
            ['nama_pemerintah' => 'Dewan Perwakilan Rakyat Daerah (DPRD) Boyolali'],
            ['nama_pemerintah' => 'Pemerintah Kecamatan Boyolali'],
            ['nama_pemerintah' => 'Pemerintah Desa/Kelurahan Boyolali'],
            ['nama_pemerintah' => 'Badan Permusyawaratan Desa (BPD) Boyolali'],
            ['nama_pemerintah' => 'Lembaga Kemasyarakatan Desa (LKD) Boyolali'],
            ['nama_pemerintah' => 'Polres Boyolali & Kodim 0724/Boyolali'],
            ['nama_pemerintah' => 'Kantor Kementerian Agama (Kemenag) Boyolali'],
            ['nama_pemerintah' => 'Kejaksaan Negeri (Kejari) & Pengadilan Negeri (PN) Boyolali'],
            ['nama_pemerintah' => 'Badan Usaha Milik Daerah (BUMD) Boyolali'],
        ];
        $pemerintahModels = [];
        foreach ($pemerintahData as $pemerintah) {
            $pemerintahModels[$pemerintah['nama_pemerintah']] = Pemerintah::create($pemerintah);
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
        $superAdminProfile = $superAdmin->adminProfile()->create([
            'nama_admin' => 'Super Admin Utama',
            'level' => 'super_admin',
            'kontak' => '081234567899'
        ]);

        // ─── Inisiator ───
        $jenisMasyarakat = JenisInisiator::where('nama_jenis_inisiator', 'Masyarakat')->first();
        $inisiator = User::create([
            'name' => 'Budi',
            'username' => 'budi_ini',
            'email' => 'budi@inv.com',
            'password' => Hash::make('password123'),
            'role' => 'inisiator'
        ]);
        $inisiatorProfile = $inisiator->inisiatorProfile()->create([
            'nama_inisiator' => 'Budi',
            'id_jenis_inisiator' => $jenisMasyarakat->id,
            'id_kelurahan' => Kelurahan::first()->id // Assign to first Kelurahan (Siswodipuran/Boyolali)
        ]);

        // ─── Seed mock approved products for home page ───
        $tahapanPenerapan = TahapanInovasi::where('nama_tahapan', 'Penerapan')->first();

        // Ambil data kelurahan pertama untuk data dummy produk halaman depan
        $sampleKelurahan = Kelurahan::first();

        // Product 1
        ProdukInovasi::create([
            'id_inisiator' => $inisiatorProfile->id,
            'id_opd' => $opdModels['Dinas Penanaman Modal dan Pelayanan Terpadu Satu Pintu']->id,
            'id_bentuk' => $bentukModels['Pelayanan Publik']->id,
            'id_tahapan' => $tahapanPenerapan->id,
            'id_admin' => $superAdminProfile->id,
            'nama_inovasi' => 'Sistem Informasi Manajemen Pelayanan Terpadu Satu Pintu',
            'deskripsi' => 'Sistem integrasi pelayanan perizinan terpadu satu pintu untuk meningkatkan efisiensi dan transparansi pelayanan publik bagi masyarakat Boyolali.',
            'tahun_inovasi' => 2023,
            'status_kurasi' => 'approved',

            // TAMBAHKAN 3 BARIS INI:
            'id_kecamatan' => $sampleKelurahan->id_kecamatan,
            'id_kelurahan' => $sampleKelurahan->id,
            'kontak'       => '081234567890',
            'status_tahapan' => 'Inovasi telah diterapkan sepenuhnya',

            'is_digital' => true,
        ]);

        // Product 2
        ProdukInovasi::create([
            'id_inisiator' => $inisiatorProfile->id,
            'id_opd' => $opdModels['Dinas Pemberdayaan Masyarakat dan Desa']->id,
            'id_bentuk' => $bentukModels['Lainnya']->id,
            'id_tahapan' => $tahapanPenerapan->id,
            'id_admin' => $superAdminProfile->id,
            'nama_inovasi' => 'Program Pemberdayaan Ekonomi Kreatif Desa Mandiri',
            'deskripsi' => 'Pelatihan dan pendampingan UMKM berbasis keunggulan lokal untuk mendorong kemandirian ekonomi desa di wilayah Kabupaten Boyolali.',
            'tahun_inovasi' => 2023,
            'status_kurasi' => 'approved',

            // TAMBAHKAN 3 BARIS INI:
            'id_kecamatan' => $sampleKelurahan->id_kecamatan,
            'id_kelurahan' => $sampleKelurahan->id,
            'kontak'       => '081234567891',
            'status_tahapan' => 'Dalam proses pendampingan berkala',

            'is_digital' => false,
        ]);

        // Product 3
        ProdukInovasi::create([
            'id_inisiator' => $inisiatorProfile->id,
            'id_opd' => $opdModels['Dinas Komunikasi dan Informatika']->id,
            'id_bentuk' => $bentukModels['Tata Kelola']->id,
            'id_tahapan' => $tahapanPenerapan->id,
            'id_admin' => $superAdminProfile->id,
            'nama_inovasi' => 'Portal Data Terbuka Smart City Integrasi',
            'deskripsi' => 'Platform penyediaan data sektoral yang terbuka, terintegrasi, dan mudah diakses oleh publik guna mendukung transparansi tata kelola daerah.',
            'tahun_inovasi' => 2024,
            'status_kurasi' => 'approved',

            // TAMBAHKAN 3 BARIS INI:
            'id_kecamatan' => $sampleKelurahan->id_kecamatan,
            'id_kelurahan' => $sampleKelurahan->id,
            'kontak'       => '081234567892',
            'status_tahapan' => 'Portal online dapat diakses masyarakat',
            'is_digital' => true,
        ]);
    }
}
