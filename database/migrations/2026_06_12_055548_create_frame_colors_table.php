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
        Schema::create('frame_colors', function (Blueprint $table) {
            $table->id();
            $table->string('series'); // classic, architectural-elite, architectural-pro, architectural-pro-plus
            $table->string('name'); // Black, White, Rose Gold, etc
            $table->string('hex_code')->nullable(); // #000000
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('frame_colors');
    }
};
