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
            $table->text('alasan_penolakan')->nullable()->after('status_kurasi');
            $table->timestamp('tanggal_review')->nullable()->after('alasan_penolakan');
            $table->boolean('is_active')->default(true)->after('is_digital');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produk_inovasis', function (Blueprint $table) {
            $table->dropColumn(['alasan_penolakan', 'tanggal_review', 'is_active']);
        });
    }
};
