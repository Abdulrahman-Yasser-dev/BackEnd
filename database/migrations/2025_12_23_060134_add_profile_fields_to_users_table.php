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
        Schema::table('users', function (Blueprint $table) {
            $table->text('bio')->nullable();
            $table->string('title')->nullable();
            $table->string('avatar_url')->nullable();
            $table->string('city')->nullable();
            $table->string('category')->nullable();
            $table->json('portfolio')->nullable();
            $table->enum('user_role', ['client', 'provider'])->nullable();
            $table->enum('provider_type', ['freelance', 'local'])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'bio',
                'title',
                'avatar_url',
                'city',
                'category',
                'portfolio',
                'user_role',
                'provider_type',
            ]);
        });
    }
};
