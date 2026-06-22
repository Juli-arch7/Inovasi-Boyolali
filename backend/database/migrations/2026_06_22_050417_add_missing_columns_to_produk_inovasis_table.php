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
        Schema::table('produk_inovasis', function (Blueprint $table) {
            // Ubah id_opd agar bisa nullable (supat Masyarakat & Pemerintah bisa submit inovasi)
            $table->unsignedBigInteger('id_opd')->nullable()->change();

            if (!Schema::hasColumn('produk_inovasis', 'id_pemerintah')) {
                $table->foreignId('id_pemerintah')->nullable()->after('id_opd')->constrained('pemerintahs', 'id_pemerintah')->nullOnDelete();
            }
            if (!Schema::hasColumn('produk_inovasis', 'id_masyarakat')) {
                $table->foreignId('id_masyarakat')->nullable()->after('id_pemerintah')->constrained('masyarakats', 'id_masyarakat')->nullOnDelete();
            }
            if (!Schema::hasColumn('produk_inovasis', 'id_kecamatan')) {
                $table->foreignId('id_kecamatan')->nullable()->after('id_masyarakat')->constrained('kecamatans')->nullOnDelete();
            }
            if (!Schema::hasColumn('produk_inovasis', 'id_kelurahan')) {
                $table->foreignId('id_kelurahan')->nullable()->after('id_kecamatan')->constrained('kelurahans')->nullOnDelete();
            }
            if (!Schema::hasColumn('produk_inovasis', 'kontak')) {
                $table->string('kontak', 100)->nullable()->after('id_kelurahan');
            }
            if (!Schema::hasColumn('produk_inovasis', 'link_marketplace')) {
                $table->string('link_marketplace')->nullable()->after('tahun_inovasi');
            }
            if (!Schema::hasColumn('produk_inovasis', 'media_inovasi')) {
                $table->string('media_inovasi')->nullable()->after('link_marketplace');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produk_inovasis', function (Blueprint $table) {
            // Kembalikan id_opd menjadi NOT NULL
            $table->unsignedBigInteger('id_opd')->nullable(false)->change();

            if (Schema::hasColumn('produk_inovasis', 'id_pemerintah')) {
                $table->dropForeign(['id_pemerintah']);
                $table->dropColumn('id_pemerintah');
            }
            if (Schema::hasColumn('produk_inovasis', 'id_masyarakat')) {
                $table->dropForeign(['id_masyarakat']);
                $table->dropColumn('id_masyarakat');
            }
            if (Schema::hasColumn('produk_inovasis', 'id_kecamatan')) {
                $table->dropForeign(['id_kecamatan']);
                $table->dropColumn('id_kecamatan');
            }
            if (Schema::hasColumn('produk_inovasis', 'id_kelurahan')) {
                $table->dropForeign(['id_kelurahan']);
                $table->dropColumn('id_kelurahan');
            }
            if (Schema::hasColumn('produk_inovasis', 'kontak')) {
                $table->dropColumn('kontak');
            }
            if (Schema::hasColumn('produk_inovasis', 'link_marketplace')) {
                $table->dropColumn('link_marketplace');
            }
            if (Schema::hasColumn('produk_inovasis', 'media_inovasi')) {
                $table->dropColumn('media_inovasi');
            }
        });
    }
};
