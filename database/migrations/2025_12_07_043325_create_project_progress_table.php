<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_progress', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('mahasiswa_id');
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->string('file_path'); // zip/pdf/etc
            $table->enum('status', ['pending', 'revisi', 'verified'])->default('pending');

            $table->timestamps();

            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('mahasiswa_id')->references('id')->on('mahasiswa_data')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_progress');
    }
};
