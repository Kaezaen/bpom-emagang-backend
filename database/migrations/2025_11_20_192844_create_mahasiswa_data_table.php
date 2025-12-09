<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mahasiswa_data', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id')->unique(); 
            $table->unsignedBigInteger('pembimbing_id')->nullable(); // -> users
            $table->unsignedBigInteger('formulir_id')->nullable();

            $table->string('status')->default('nonaktif');
            $table->string('phone')->nullable();
            $table->text('catatan')->nullable();

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();

            // FIX: pembimbing_id ke TABEL USERS
            $table->foreign('pembimbing_id')->references('id')->on('users')->nullOnDelete();

            $table->foreign('formulir_id')->references('id')->on('formulir_magang')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mahasiswa_data');
    }
};
