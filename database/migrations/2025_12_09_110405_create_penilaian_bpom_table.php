<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penilaian_bpom', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mahasiswa_id');

            $table->integer('kehadiran');
            $table->integer('taat_jadwal');
            $table->integer('pemahaman_materi');
            $table->integer('praktek_kerja');
            $table->integer('komunikasi');
            $table->integer('laporan');
            $table->integer('presentasi');

            $table->integer('nilai_akhir'); // average dari semua komponen
            $table->boolean('locked')->default(false);

            $table->timestamps();

            $table->foreign('mahasiswa_id')->references('id')->on('mahasiswa_data')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penilaian_bpom');
    }
};
