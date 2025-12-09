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
    Schema::table('project_submissions', function (Blueprint $table) {
        $table->enum('status', ['pending', 'verified', 'rejected'])
              ->default('pending')
              ->change();
    });
}

public function down()
{
    Schema::table('project_submissions', function (Blueprint $table) {
        $table->string('status')->change();
    });
}

};
