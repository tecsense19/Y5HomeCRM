<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tbl_leads', function (Blueprint $table) {
            $table->id();
            $table->string('lead_number', 20)->unique();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('customer_name');
            $table->string('mobile_number', 15);
            $table->string('email')->nullable();
            $table->text('project_address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->default('India');
            $table->enum('lead_source', [
                'website','google_ads','facebook','instagram','justdial',
                'builder','architect','referral','walk_in','channel_partner'
            ])->nullable();
            $table->enum('project_type', ['apartment','villa','bungalow','commercial','office'])->nullable();
            $table->enum('construction_stage', ['planning','construction','finishing','ready_possession'])->nullable();
            $table->decimal('estimated_budget', 14, 2)->nullable();
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->unsignedBigInteger('experience_center_id')->nullable();
            $table->enum('status', [
                'new','contacted','qualified','site_visit_scheduled','site_visit_completed',
                'quotation_sent','negotiation','won','lost'
            ])->default('new');
            $table->date('lead_creation_date')->nullable();
            $table->date('next_followup_date')->nullable();
            $table->enum('lost_reason', [
                'price_high','competition','no_response','project_cancelled','project_delayed','other'
            ])->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('lead_source');
            $table->index('assigned_to');
            $table->index('experience_center_id');
        });
    }

    public function down(): void { Schema::dropIfExists('tbl_leads'); }
};
