<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('work_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('full_name');
            $table->string('category');
            $table->string('work_title');
            $table->text('work_description');
            $table->string('service_type'); 
            $table->string('city')->nullable();
            $table->string('phone')->nullable();
            $table->date('expected_date')->nullable();
            $table->json('file_attachments')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_requests');
    }
};
