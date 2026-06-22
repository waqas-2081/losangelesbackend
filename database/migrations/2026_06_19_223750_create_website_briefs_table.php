<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('website_briefs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('business_name');
            $table->string('website_type');
            // Conditional fields
            $table->string('products_count')->nullable();
            $table->string('services_count_no_payment')->nullable();
            $table->text('future_images_products')->nullable();
            $table->text('services_count_with_price')->nullable();
            $table->boolean('accept_online_payments')->nullable();
            $table->string('payment_medium')->nullable();
            $table->text('future_images_services')->nullable();
            // Brand
            $table->text('business_description');
            $table->string('business_industry')->nullable();
            $table->string('target_audience')->nullable();
            $table->json('overall_feel')->nullable();
            $table->text('competitors_references')->nullable();
            // Structure
            $table->boolean('has_domain')->nullable();
            $table->integer('pages_count')->nullable();
            $table->text('pages_list')->nullable();
            $table->boolean('has_logo')->nullable();
            $table->boolean('wants_logo_revamp')->nullable();
            $table->boolean('needs_hosting')->nullable();
            $table->boolean('needs_responsive')->nullable();
            // Addons
            $table->json('addon_features')->nullable();
            // Admin
            $table->string('status')->default('pending');
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });



        Schema::create('website_brief_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('website_brief_id')->constrained()->cascadeOnDelete();
            $table->string('original_name');
            $table->string('file_path');
            $table->string('mime_type');
            $table->unsignedBigInteger('file_size');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('website_briefs');
        Schema::dropIfExists('website_brief_files');

    }
};
