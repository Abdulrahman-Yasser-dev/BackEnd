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
            $table->string('pending_status')->nullable()->after('status');
            $table->foreignId('pending_status_changed_by')->nullable()->after('pending_status')->constrained('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_requests', function (Blueprint $table) {
            $table->dropForeign(['pending_status_changed_by']);
            $table->dropColumn(['pending_status', 'pending_status_changed_by']);
        });
    }
};
