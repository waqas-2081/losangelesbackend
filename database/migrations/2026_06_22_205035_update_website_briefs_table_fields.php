<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('website_briefs', function (Blueprint $table) {
            // Rename existing columns to match React field names
            $table->renameColumn('business_description', 'business_desc');
            $table->renameColumn('business_industry', 'industry');
            $table->renameColumn('overall_feel', 'feel');
            $table->renameColumn('competitors_references', 'competitors');
            $table->renameColumn('pages_count', 'page_count');
            $table->renameColumn('pages_list', 'page_names');
            $table->renameColumn('wants_logo_revamp', 'revamp_logo');
            $table->renameColumn('needs_hosting', 'need_hosting');
            $table->renameColumn('needs_responsive', 'need_responsive');
            $table->renameColumn('products_count', 'product_showcase_count');
            $table->renameColumn('services_count_no_payment', 'service_showcase_count');
            $table->renameColumn('services_count_with_price', 'services_prices');
        });

        Schema::table('website_briefs', function (Blueprint $table) {
            // New columns needed for ecommerce / web-app types
            $table->string('product_categories')->nullable()->after('services_prices');
            $table->string('product_count')->nullable()->after('product_categories');
            $table->text('product_source')->nullable()->after('product_count');
            $table->string('platform_required')->nullable()->after('product_source');
        });
    }

    public function down(): void
    {
        Schema::table('website_briefs', function (Blueprint $table) {
            $table->dropColumn(['product_categories', 'product_count', 'product_source', 'platform_required']);
        });

        Schema::table('website_briefs', function (Blueprint $table) {
            $table->renameColumn('business_desc', 'business_description');
            $table->renameColumn('industry', 'business_industry');
            $table->renameColumn('feel', 'overall_feel');
            $table->renameColumn('competitors', 'competitors_references');
            $table->renameColumn('page_count', 'pages_count');
            $table->renameColumn('page_names', 'pages_list');
            $table->renameColumn('revamp_logo', 'wants_logo_revamp');
            $table->renameColumn('need_hosting', 'needs_hosting');
            $table->renameColumn('need_responsive', 'needs_responsive');
            $table->renameColumn('product_showcase_count', 'products_count');
            $table->renameColumn('service_showcase_count', 'services_count_no_payment');
            $table->renameColumn('services_prices', 'services_count_with_price');
        });
    }
};