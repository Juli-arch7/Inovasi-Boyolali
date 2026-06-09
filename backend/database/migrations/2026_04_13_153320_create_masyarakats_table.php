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
        Schema::create('masyarakats', function (Blueprint $table) {
        // Buat ID mandiri, lepas keterikatan langsung dari tabel users
        $table->bigIncrements('id_masyarakat'); 
        $table->string('nama_masyarakat', 100); // Tempat menyimpan "Pelajar/Mahasiswa"
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('masyarakats');
    }
};
