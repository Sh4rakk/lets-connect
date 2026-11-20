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
            // houdt bij voor welke keuzes de bevestigingsmail al is verstuurd,
            // zodat dezelfde keuzes nog een keer opslaan geen tweede mail geeft
            $table->timestamp('confirmation_sent_at')->nullable();
            $table->string('confirmation_key')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['confirmation_sent_at', 'confirmation_key']);
        });
    }
};
