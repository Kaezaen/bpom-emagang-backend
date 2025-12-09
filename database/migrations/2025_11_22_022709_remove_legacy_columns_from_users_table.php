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
    Schema::table('users', function (Blueprint $table) {
        if (Schema::hasColumn('users', 'divisi_id')) {
            $table->dropForeign(['divisi_id']);
            $table->dropColumn('divisi_id');
        }

        if (Schema::hasColumn('users', 'pembimbing_id')) {
            $table->dropForeign(['pembimbing_id']);
            $table->dropColumn('pembimbing_id');
        }
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
