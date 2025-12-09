<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');

            $table->string('phone')->nullable();
            $table->boolean('is_active')->default(true);

            // role: admin / pembimbing / user
            $table->unsignedBigInteger('role_id');
            $table->foreign('role_id')->references('id')->on('roles')->cascadeOnDelete();

            $table->unsignedBigInteger('divisi_id')->nullable();
            $table->foreign('divisi_id')->references('id')->on('divisi')->nullOnDelete();

            // pembimbing (self reference)
            $table->unsignedBigInteger('pembimbing_id')->nullable();
            $table->foreign('pembimbing_id')->references('id')->on('users')->nullOnDelete();

            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
