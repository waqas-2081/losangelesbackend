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
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // STARTER, REVAMP LOGO, STANDARD, etc.
            $table->string('slug')->unique();
            $table->enum('service_type', [
                'logo', 
                'website', 
                'animation', 
                'mobile_apps', 
                'digital_marketing', 
                'seo'
            ]);
            $table->decimal('price', 10, 2);
            $table->enum('price_type', ['one_time', 'project'])->default('project');
            $table->string('badge')->nullable(); // MOST POPULAR, etc.
            $table->json('features'); // Store all features as JSON
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
