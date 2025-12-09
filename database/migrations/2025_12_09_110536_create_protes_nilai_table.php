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
    Schema::create('protes_nilai', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('mahasiswa_id');
        $table->enum('jenis', ['bpom','kampus']);
        $table->unsignedBigInteger('referensi_id'); // id di penilaian_bpom atau penilaian_kampus
        $table->text('alasan');
        $table->enum('status', ['pending','resolved','rejected'])->default('pending');
        $table->text('tanggapan')->nullable();
        $table->unsignedBigInteger('resolved_by')->nullable(); // user id yang resolve
        $table->boolean('nilai_diubah')->default(false);
        $table->timestamps();

        $table->foreign('mahasiswa_id')->references('id')->on('mahasiswa_data')->cascadeOnDelete();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('protes_nilai');
    }
};
