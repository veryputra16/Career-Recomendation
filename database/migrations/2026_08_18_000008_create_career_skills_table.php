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
        Schema::create('career_skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('career_id')->constrained('careers')->cascadeOnDelete();
            $table->foreignId('skill_id')->constrained('skills')->cascadeOnDelete();
            $table->string('importance')->default('required');
            $table->string('minimum_level')->nullable();
            $table->timestamps();

            $table->unique(['career_id', 'skill_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('career_skills');
    }
};
