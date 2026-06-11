<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// ============================================================
// Migration: 2024_01_01_000001_create_tbl_users.php
// ============================================================
return new class extends Migration {
    public function up(): void
    {
        Schema::create('tbl_users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('mobile', 15)->nullable();
            $table->enum('role', ['super-admin', 'sales-manager', 'sales-executive', 'experience-center'])->default('sales-executive');
            $table->unsignedBigInteger('experience_center_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void { Schema::dropIfExists('tbl_users'); }
};
