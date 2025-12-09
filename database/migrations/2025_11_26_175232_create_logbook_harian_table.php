<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('logbook_harian', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mahasiswa_id'); // FK -> mahasiswa_data.id
            $table->date('tanggal'); // tanggal logbook
            $table->string('judul')->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('file_path'); // path file pdf/doc
            $table->enum('status', ['pending','verified'])->default('pending'); // verifikasi admin
            $table->timestamps();

            $table->foreign('mahasiswa_id')->references('id')->on('mahasiswa_data')->cascadeOnDelete();
            $table->unique(['mahasiswa_id','tanggal']); // satu hari satu entry per mahasiswa
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logbook_harian');
    }
};
