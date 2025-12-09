<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('divisi', function (Blueprint $table) {
            $table->id();
            $table->string('nama_divisi');
            $table->string('deskripsi')->nullable();
            $table->integer('kuota')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('divisi');
    }
};
