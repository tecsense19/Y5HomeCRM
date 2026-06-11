<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tbl_experience_centers', function (Blueprint $table) {
            $table->id();
            $table->string('center_code', 20)->unique();
            $table->string('center_name');
            $table->string('owner_name');
            $table->string('company_name')->nullable();
            $table->string('gst_number', 20)->nullable();
            $table->string('mobile_number', 15);
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('city');
            $table->string('state');
            $table->string('country')->default('India');
            $table->date('agreement_start_date')->nullable();
            $table->date('agreement_end_date')->nullable();
            $table->decimal('security_deposit_amount', 12, 2)->default(0);
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void { Schema::dropIfExists('tbl_experience_centers'); }
};
