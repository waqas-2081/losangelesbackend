<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('profile', 100)->nullable();
            $table->string('customer_name');
            $table->string('email')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('package_name')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('payment_method', 20);   // stripe|paypal|cashapp|zelle|venmo
            $table->string('payment_type', 20)->nullable();  // front|upsell
            $table->string('status', 20)->default('pending');
            $table->string('payment_link', 20)->unique();
            $table->string('stripe_payment_intent_id')->nullable();
            $table->text('stripe_client_secret')->nullable();  // text: client secrets are long
            $table->string('paypal_order_id')->nullable();
            $table->string('cashapp_payment_intent_id')->nullable();
            $table->string('zelle_receipt_path')->nullable();
            $table->string('transaction_id')->nullable()->unique();  // NULL rows don't violate UNIQUE
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_requests');
    }
};