<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('volunteer_applications', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('motivation');
            $table->string('availability')->nullable()->after('phone');
            $table->string('transportation')->nullable()->after('availability');
            $table->text('experience')->nullable()->after('transportation');
            $table->text('target_beneficiaries')->nullable()->after('experience');
            $table->boolean('has_food_safety_commitment')->default(true)->after('target_beneficiaries');
        });
    }

    public function down(): void
    {
        Schema::table('volunteer_applications', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'availability',
                'transportation',
                'experience',
                'target_beneficiaries',
                'has_food_safety_commitment',
            ]);
        });
    }
};
