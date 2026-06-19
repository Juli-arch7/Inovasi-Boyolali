<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_admin');
            $table->string('action'); // verify_product, update_tahapan, toggle_user_active, create_admin, toggle_admin_active
            $table->unsignedBigInteger('target_id')->nullable();
            $table->string('target_type')->nullable(); // user, product
            $table->text('description');
            $table->timestamps();

            $table->foreign('id_admin')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_logs');
    }
};
