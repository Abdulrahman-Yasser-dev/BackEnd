<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $localCategories = [
            'Plumbing',
            'Electricity',
            'Outdoor Photography',
            'Cleaning',
            'Carpentry & Smithing',
            'Decoration & Gypsum',
            'Light Contracting',
            'Car Services'
        ];

        DB::table('categories')
            ->whereIn('name_en', $localCategories)
            ->update(['type' => 'local']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to default if needed, though strictly speaking we can't easily know previous state
        // assuming default was freelance
        $localCategories = [
            'Plumbing',
            'Electricity',
            'Outdoor Photography',
            'Cleaning',
            'Carpentry & Smithing',
            'Decoration & Gypsum',
            'Light Contracting',
            'Car Services'
        ];

        DB::table('categories')
            ->whereIn('name_en', $localCategories)
            ->update(['type' => 'freelance']);
    }
};
