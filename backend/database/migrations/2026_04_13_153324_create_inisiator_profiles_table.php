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
        Schema::create('inisiator_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('id_jenis_inisiator')->constrained('jenis_inisiators')->cascadeOnDelete();
            $table->foreignId('id_kelurahan')->nullable()->constrained('kelurahans')->nullOnDelete();

            // TAMBAHKAN INI: Hubungkan ke tabel kategori jenis
            // Dibuat nullable() karena kolom ini hanya terisi jika id_jenis_inisiator-nya salah satu dari 3 jenis ini
            $table->foreignId('id_masyarakat')->nullable()->constrained('masyarakats', 'id_masyarakat')->nullOnDelete();
            $table->foreignId('id_opd')->nullable()->constrained('opds', 'id_opd')->nullOnDelete();
            $table->foreignId('id_pemerintah')->nullable()->constrained('pemerintahs', 'id_pemerintah')->nullOnDelete();

            $table->string('nama_inisiator');
            $table->string('kontak')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inisiator_profiles');
    }
};
