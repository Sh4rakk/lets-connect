<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // capaciteit per ronde, leeg = de capaciteit van de workshop zelf gebruiken
        Schema::table('workshop_moments', function (Blueprint $table) {
            $table->integer('capacity')->nullable()->after('workshop_id');
        });

        // handmatig toegevoegde workshops, die komen niet van de scraper
        Schema::table('workshops', function (Blueprint $table) {
            $table->boolean('is_manual')->default(false)->after('location');
        });
    }

    public function down(): void
    {
        Schema::table('workshop_moments', function (Blueprint $table) {
            $table->dropColumn('capacity');
        });

        Schema::table('workshops', function (Blueprint $table) {
            $table->dropColumn('is_manual');
        });
    }
};
