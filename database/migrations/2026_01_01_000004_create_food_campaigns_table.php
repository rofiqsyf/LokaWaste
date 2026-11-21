<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('food_campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('district_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('body');
            $table->string('cover_path')->nullable();
            $table->string('beneficiary_type'); // panti_asuhan, ponpes, panti_jompo, tunawisma, lainnya
            $table->string('beneficiary_name');
            $table->text('address');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->decimal('public_latitude', 10, 7);
            $table->decimal('public_longitude', 10, 7);
            $table->string('location_precision')->default('exact'); // exact, area
            $table->string('daily_need')->nullable();
            $table->unsignedInteger('target_portions');
            $table->unsignedInteger('collected_portions')->default(0);
            $table->date('starts_at');
            $table->date('ends_at');
            $table->string('status')->default('draft'); // draft, pending_review, published, paused, closed, rejected, taken_down
            $table->string('paused_reason')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('review_note')->nullable();
            $table->timestamps();

            $table->index(['status', 'district_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('food_campaigns');
    }
};
