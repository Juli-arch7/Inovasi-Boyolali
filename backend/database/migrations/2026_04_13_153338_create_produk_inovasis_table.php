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
            $table->foreignId('id_opd')->constrained('opds')->cascadeOnDelete();
            $table->foreignId('id_bentuk')->constrained('bentuk_inovasis')->cascadeOnDelete();
            $table->foreignId('id_tahapan')->constrained('tahapan_inovasis')->cascadeOnDelete();
            $table->foreignId('id_admin')->nullable()->constrained('admin_profiles')->nullOnDelete();
            $table->string('nama_inovasi');
            $table->text('deskripsi')->nullable();
            $table->year('tahun_inovasi');
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
