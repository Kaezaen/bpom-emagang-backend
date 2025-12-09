<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penilaian_kampus', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mahasiswa_id');

            $table->integer('nilai_akhir'); // 0–100
            $table->boolean('locked')->default(false);

            $table->timestamps();

            $table->foreign('mahasiswa_id')->references('id')->on('mahasiswa_data')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penilaian_kampus');
    }
};
