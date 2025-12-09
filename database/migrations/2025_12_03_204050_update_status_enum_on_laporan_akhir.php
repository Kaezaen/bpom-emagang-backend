<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('laporan_akhir', function (Blueprint $table) {
            $table->enum('status', ['pending', 'verified', 'rejected'])
                  ->default('pending')
                  ->change();
        });
    }

    public function down(): void
    {
        Schema::table('laporan_akhir', function (Blueprint $table) {
            $table->enum('status', ['pending', 'verified'])
                  ->default('pending')
                  ->change();
        });
    }
};

