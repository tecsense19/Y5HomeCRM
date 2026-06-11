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
        Schema::table('tbl_leads', function (Blueprint $table) {
            $table->timestamp('locked_from')->nullable();
            $table->timestamp('locked_until')->nullable();
            $table->unsignedBigInteger('locked_by')->nullable();
            $table->foreign('locked_by')->references('id')->on('tbl_users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_leads', function (Blueprint $table) {
            $table->dropForeign(['locked_by']);
            $table->dropColumn(['locked_from', 'locked_until', 'locked_by']);
        });
    }
};
