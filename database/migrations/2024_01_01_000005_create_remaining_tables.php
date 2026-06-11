<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tbl_opportunities', function (Blueprint $table) {
            $table->id();
            $table->string('opportunity_number', 20)->unique();
            $table->unsignedBigInteger('lead_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('customer_name');
            $table->string('project_name')->nullable();
            $table->decimal('expected_revenue', 14, 2)->nullable();
            $table->date('expected_closing_date')->nullable();
            $table->unsignedTinyInteger('probability')->default(0);
            $table->enum('stage', ['requirement_gathering','proposal','negotiation','won','lost'])->default('requirement_gathering');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('tbl_site_visits', function (Blueprint $table) {
            $table->id();
            $table->string('visit_id', 20)->unique();
            $table->unsignedBigInteger('lead_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('customer_name');
            $table->date('visit_date');
            $table->unsignedBigInteger('visited_by')->nullable();
            $table->string('location')->nullable();
            $table->text('requirement_summary')->nullable();
            $table->json('products_required')->nullable();
            $table->decimal('estimated_project_value', 14, 2)->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('tbl_quotations', function (Blueprint $table) {
            $table->id();
            $table->string('quotation_number', 20)->unique();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('customer_name');
            $table->unsignedBigInteger('opportunity_id')->nullable();
            $table->date('quotation_date');
            $table->decimal('quotation_value', 14, 2)->nullable();
            $table->unsignedSmallInteger('version_number')->default(1);
            $table->unsignedBigInteger('prepared_by')->nullable();
            $table->enum('status', ['draft','sent','approved','rejected'])->default('draft');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('tbl_documents', function (Blueprint $table) {
            $table->id();
            $table->morphs('documentable');
            $table->string('category');
            $table->string('file_name');
            $table->string('original_name');
            $table->string('file_path');
            $table->unsignedBigInteger('file_size')->default(0);
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Future-ready tables (structure only)
        Schema::create('tbl_builders', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('company_name')->nullable();
            $table->string('mobile_number', 15)->nullable();
            $table->string('email')->nullable();
            $table->string('city')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('tbl_architects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('firm_name')->nullable();
            $table->string('mobile_number', 15)->nullable();
            $table->string('email')->nullable();
            $table->string('city')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_documents');
        Schema::dropIfExists('tbl_quotations');
        Schema::dropIfExists('tbl_site_visits');
        Schema::dropIfExists('tbl_opportunities');
        Schema::dropIfExists('tbl_architects');
        Schema::dropIfExists('tbl_builders');
    }
};
