<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan_akhir', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mahasiswa_id')->unique(); 
            $table->string('file_laporan'); 
            $table->enum('status', ['pending', 'verified'])->default('pending');
            $table->timestamps();

            $table->foreign('mahasiswa_id')
                ->references('id')
                ->on('mahasiswa_data')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_akhir');
    }
};
