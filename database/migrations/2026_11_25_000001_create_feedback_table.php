<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workshop_moment_id')->constrained('workshop_moments');
            $table->unsignedTinyInteger('rating');
            $table->string('good', 1000)->nullable();
            $table->string('better', 1000)->nullable();
            $table->string('learned', 20)->nullable();
            $table->string('fun', 20)->nullable();
            $table->boolean('recommend')->nullable();
            $table->string('step_token', 64)->nullable()->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback');
    }
};
