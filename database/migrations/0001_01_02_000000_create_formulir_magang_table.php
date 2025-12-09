<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('formulir_magang', function (Blueprint $table) {
            $table->id();

            // Identitas
            $table->string('nama');
            $table->string('nik', 30);
            $table->string('nomor_hp', 20); // <- FIX nama kolom

            // Pendidikan
            $table->string('universitas');
            $table->string('alamat_universitas')->nullable();
            $table->string('jurusan');
            $table->string('semester');

            // Divisi tujuan
            $table->unsignedBigInteger('divisi_id');
            $table->foreign('divisi_id')->references('id')->on('divisi')->cascadeOnDelete();

            $table->date('waktu_mulai');
            $table->date('waktu_selesai');

            // Dokumen upload
            $table->string('proposal'); // <- FIX kolom sesuai input
            $table->string('surat_permohonan'); // <- FIX kolom sesuai input

            // Status
            $table->enum('status', ['pending','diterima','ditolak'])->default('pending');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formulir_magang');
    }
};
