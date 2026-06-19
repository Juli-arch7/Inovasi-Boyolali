<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('produk_inovasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_inisiator')->constrained('inisiator_profiles')->cascadeOnDelete();

            // PENTING: Diubah jadi nullable() karena yang mengajukan bisa jadi Masyarakat/Pemerintah/OPD
            $table->foreignId('id_opd')->nullable()->constrained('opds', 'id_opd')->nullOnDelete();
            $table->foreignId('id_pemerintah')->nullable()->constrained('pemerintahs', 'id_pemerintah')->nullOnDelete();
            $table->foreignId('id_masyarakat')->nullable()->constrained('masyarakats', 'id_masyarakat')->nullOnDelete();

            // TAMBAHKAN WILAYAH & KONTAK
            $table->foreignId('id_kecamatan')->constrained('kecamatans')->cascadeOnDelete();
            $table->foreignId('id_kelurahan')->constrained('kelurahans')->cascadeOnDelete();
            $table->string('kontak', 100);

            $table->foreignId('id_bentuk')->constrained('bentuk_inovasis')->cascadeOnDelete();
            $table->foreignId('id_tahapan')->constrained('tahapan_inovasis')->cascadeOnDelete();
            $table->foreignId('id_admin')->nullable()->constrained('admin_profiles')->nullOnDelete();
            $table->string('nama_inovasi');
            $table->text('deskripsi')->nullable();
            $table->year('tahun_inovasi');

             // TAMBAHKAN LINK MARKETPLACE DAN MEDIA INOVASI:
            $table->string('link_marketplace')->nullable();
            $table->string('media_inovasi')->nullable(); // Menyimpan path file, misal: 'uploads/media/nama_file.pdf'

            $table->string('status_kurasi')->default('pending'); // pending, approved, rejected
            $table->boolean('is_digital')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produk_inovasis');
    }
};
