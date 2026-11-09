<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('workshops', function (Blueprint $table) {
            $table->string('qr_token', 64)->nullable()->unique()->after('location');
        });

        DB::table('workshops')->whereNull('qr_token')->pluck('id')->each(function ($id) {
            DB::table('workshops')->where('id', $id)->update(['qr_token' => Str::random(40)]);
        });
    }

    public function down(): void
    {
        Schema::table('workshops', function (Blueprint $table) {
            $table->dropUnique(['qr_token']);
            $table->dropColumn('qr_token');
        });
    }
};
