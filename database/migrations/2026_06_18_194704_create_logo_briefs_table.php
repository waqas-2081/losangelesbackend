<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logo_briefs', function (Blueprint $table) {
            $table->id();

            // Contact Info
            $table->string('name');
            $table->string('email');
            $table->string('personal_phone', 50);
            $table->string('company_phone', 50)->nullable();

            // Logo & Company
            $table->string('logo_name');
            $table->string('company_slogan')->nullable();
            $table->string('industry')->nullable();
            $table->text('business_desc');
            $table->text('logo_description');

            // Competitor References
            $table->string('competitors_ref');
            $table->string('competitors_ref_two')->nullable();
            $table->string('competitors_ref_three')->nullable();

            // Design Preferences
            $table->string('logo_type')->nullable();
            $table->string('logo_fonts')->nullable();
            $table->string('logo_color')->nullable();   // singular — matches request
            $table->string('primary_color', 20)->nullable();
            $table->string('secondary_color', 20)->nullable();

            // Admin Fields
            $table->enum('status', ['pending', 'in_progress', 'completed', 'rejected'])->default('pending');
            $table->text('admin_notes')->nullable();

            $table->timestamps();
        });

        Schema::create('logo_brief_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('logo_brief_id')->constrained()->cascadeOnDelete();
            $table->string('file_name');
            $table->string('original_name');
            $table->string('file_path');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logo_brief_files');
        Schema::dropIfExists('logo_briefs');
    }
};