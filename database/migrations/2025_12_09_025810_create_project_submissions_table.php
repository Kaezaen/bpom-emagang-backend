<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_submissions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
    $table->foreignId('mahasiswa_id')->constrained('mahasiswa_data')->onDelete('cascade');
    $table->string('file_path');
    $table->text('catatan')->nullable();
    $table->enum('status', ['pending', 'verified', 'rejected'])->default('pending');
    $table->timestamps();
});
    }

    public function down(): void
    {
        Schema::dropIfExists('project_submissions');
    }
};
