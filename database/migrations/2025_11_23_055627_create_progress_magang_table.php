<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('progress_magang', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mahasiswa_id'); // FK
            $table->string('judul');            
            $table->text('deskripsi')->nullable();
            $table->enum('status', ['pending','proses','selesai'])->default('pending');
            $table->timestamps();

            $table->foreign('mahasiswa_id')
                  ->references('id')
                  ->on('mahasiswa_data')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('progress_magang');
    }
};
