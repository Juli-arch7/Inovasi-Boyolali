<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('produk_inovasis', function (Blueprint $table) {
            $table->string('status_tahapan')->nullable()->after('id_tahapan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produk_inovasis', function (Blueprint $table) {
            $table->dropColumn('status_tahapan');
        });
    }
};
