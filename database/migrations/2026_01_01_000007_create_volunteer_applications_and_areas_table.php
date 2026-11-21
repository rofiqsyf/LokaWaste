<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('volunteer_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('regency_id')->constrained()->cascadeOnDelete();
            $table->text('motivation');
            $table->timestamps();
        });

        Schema::create('volunteer_areas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('volunteer_applications')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('regency_id')->constrained()->cascadeOnDelete();
            $table->foreignId('district_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('pending'); // pending, approved, rejected, revoked, resigned
            $table->foreignId('decided_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('decided_at')->nullable();
            $table->text('decision_note')->nullable();
            $table->dateTime('cooldown_until')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'district_id']);
            $table->index(['user_id', 'status']);
            $table->index(['district_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('volunteer_areas');
        Schema::dropIfExists('volunteer_applications');
    }
};
