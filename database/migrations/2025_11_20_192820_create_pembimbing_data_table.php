<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pembimbing_data', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique(); // 1-1 ke users
            $table->unsignedBigInteger('divisi_id')->nullable();
            $table->string('jabatan')->nullable();
            $table->string('phone')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('divisi_id')->references('id')->on('divisi')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembimbing_data');
    }
};
