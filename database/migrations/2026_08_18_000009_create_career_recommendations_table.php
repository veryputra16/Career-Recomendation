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
        Schema::create('career_recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('career_id')->constrained('careers')->cascadeOnDelete();
            $table->foreignId('cv_analysis_history_id')->nullable()->constrained('cv_analysis_histories')->nullOnDelete();
            $table->decimal('matching_score', 5, 2)->index();
            $table->text('reason')->nullable();
            $table->json('existing_skills')->nullable();
            $table->json('missing_skills')->nullable();
            $table->json('suggestions')->nullable();
            $table->string('recommendation_source')->default('hybrid');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('career_recommendations');
    }
};
