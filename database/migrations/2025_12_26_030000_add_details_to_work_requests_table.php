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
        Schema::table('work_requests', function (Blueprint $table) {
            $table->string('duration')->nullable()->after('expected_date'); 
            $table->decimal('budget_min', 10, 2)->nullable()->after('duration');
            $table->decimal('budget_max', 10, 2)->nullable()->after('budget_min');
            $table->json('category_ids')->nullable()->after('user_id');
            $table->json('skill_ids')->nullable()->after('category_ids');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_requests', function (Blueprint $table) {
            $table->dropColumn(['duration', 'budget_min', 'budget_max', 'category_ids', 'skill_ids']);
        });
    }
};
