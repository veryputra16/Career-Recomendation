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
        Schema::create('cv_analysis_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('cv_upload_id')->nullable()->constrained('cv_uploads')->nullOnDelete();
            $table->string('analysis_version')->default('1');
            $table->string('ai_provider')->nullable();
            $table->string('ai_model')->nullable();
            $table->string('prompt_version')->nullable();
            $table->string('status')->default('completed')->index();
            $table->text('summary')->nullable();
            $table->json('raw_result')->nullable();
            $table->timestamp('analyzed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cv_analysis_histories');
    }
};
