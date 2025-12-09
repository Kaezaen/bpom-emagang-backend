<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('project_progress', function (Blueprint $table) {
        if (Schema::hasColumn('project_progress', 'status')) {
            $table->dropColumn('status');
        }
    });
}

public function down()
{
    Schema::table('project_progress', function (Blueprint $table) {
        $table->enum('status', ['pending', 'verified', 'rejected'])
              ->default('pending');
    });
}
};
