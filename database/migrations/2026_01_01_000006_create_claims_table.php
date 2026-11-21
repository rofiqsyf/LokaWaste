<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained('food_listings')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('campaign_id')->nullable()->constrained('food_campaigns')->nullOnDelete();
            $table->unsignedTinyInteger('portions_requested');
            $table->unsignedTinyInteger('portions_approved')->nullable();
            $table->string('status')->default('pending'); // pending, approved, rejected, cancelled, picked_up, expired, no_show
            $table->string('active_key')->nullable()->unique(); // CONCAT(listing_id,'-',user_id) when pending/approved
            $table->string('pickup_code', 6);
            $table->unsignedTinyInteger('pickup_attempts')->default(0);
            $table->dateTime('pickup_locked_until')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('cancel_reason')->nullable();
            $table->text('note')->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->dateTime('picked_up_at')->nullable();
            $table->timestamps();

            $table->index(['listing_id', 'pickup_code']);
            $table->index(['user_id', 'status']);
            $table->index(['listing_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('claims');
    }
};
