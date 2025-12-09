<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            // pembimbing yang assign project
            $table->unsignedBigInteger('pembimbing_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('deadline'); // wajib
            $table->enum('status', ['active','completed','cancelled','paused'])->default('active');
            $table->timestamps();

            $table->foreign('pembimbing_id')->references('id')->on('pembimbing_data')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
