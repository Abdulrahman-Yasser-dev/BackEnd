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
        // Add status to work_requests
        Schema::table('work_requests', function (Blueprint $table) {
            $table->enum('status', ['new', 'in_progress', 'pending_payment', 'delayed', 'completed'])
                ->default('new')
                ->after('file_attachments');
        });

        // Create messages table
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_request_id')->constrained()->onDelete('cascade');
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('receiver_id')->constrained('users')->onDelete('cascade');
            $table->text('content');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });

        // Create work_request_logs table
        Schema::create('work_request_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_request_id')->constrained()->onDelete('cascade');
            $table->string('old_status')->nullable();
            $table->string('new_status');
            $table->foreignId('changed_by_id')->constrained('users')->onDelete('cascade');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_request_logs');
        Schema::dropIfExists('messages');
        Schema::table('work_requests', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
