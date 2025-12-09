<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('project_members', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('mahasiswa_id'); // references mahasiswa_data.id
            $table->boolean('is_leader')->default(false);
            $table->enum('status', ['active','left'])->default('active');
            $table->timestamps();

            $table->foreign('project_id')->references('id')->on('projects')->cascadeOnDelete();
            $table->foreign('mahasiswa_id')->references('id')->on('mahasiswa_data')->cascadeOnDelete();
            $table->unique(['project_id','mahasiswa_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_members');
    }
};
