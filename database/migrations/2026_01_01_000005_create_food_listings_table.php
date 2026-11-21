<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('food_listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('campaign_id')->nullable()->constrained('food_campaigns')->nullOnDelete();
            $table->foreignId('district_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description');
            $table->string('photo_path')->nullable();
            $table->string('food_type'); // cooked, packaged, raw
            $table->dateTime('prepared_at')->nullable();
            $table->json('allergens')->nullable();
            $table->string('storage_note')->nullable();
            $table->boolean('halal_claim')->nullable();
            $table->boolean('is_vegetarian')->default(false);
            $table->unsignedInteger('portions_total');
            $table->unsignedInteger('portions_left');
            $table->unsignedTinyInteger('max_portions_per_claim')->default(3);
            $table->text('pickup_address'); // private
            $table->decimal('latitude', 10, 7); // private exact
            $table->decimal('longitude', 10, 7); // private exact
            $table->decimal('public_latitude', 10, 7); // public jittered
            $table->decimal('public_longitude', 10, 7); // public jittered
            $table->boolean('show_exact_location')->default(false);
            $table->dateTime('available_from');
            $table->dateTime('expires_at');
            $table->dateTime('campaign_priority_until')->nullable();
            $table->string('status')->default('available'); // draft, available, reserved, completed, expired, cancelled
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'public_latitude', 'public_longitude']);
            $table->index(['status', 'expires_at']);
            $table->index(['district_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('food_listings');
    }
};
