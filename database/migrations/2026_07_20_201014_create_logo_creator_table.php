<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logo_creator', function (Blueprint $table) {
            $table->id();
            $table->string('session_token', 64)->unique();
            $table->string('business_name')->nullable();
            $table->string('slogan')->nullable();
            $table->string('industry')->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 30)->nullable();
            $table->unsignedTinyInteger('current_step')->default(1);
            $table->boolean('is_complete')->default(false);
            $table->string('status')->default('pending'); // pending, in_progress, completed, rejected
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logo_creator');
    }
};