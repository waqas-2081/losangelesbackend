<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promo_leads', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone');
            $table->text('project_details')->nullable();
            $table->string('source')->default('home_promo_popup');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promo_leads');
    }
};