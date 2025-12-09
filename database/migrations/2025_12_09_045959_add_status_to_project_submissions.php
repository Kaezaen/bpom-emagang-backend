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
        if (!Schema::hasColumn('project_submissions', 'status')) {
    $table->enum('status',['pending','verified','rejected'])
        ->default('pending')
        ->after('catatan');
}

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_submissions', function (Blueprint $table) {
            //
        });
    }
};
