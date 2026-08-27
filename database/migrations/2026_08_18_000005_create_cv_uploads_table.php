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
        Schema::create('cv_uploads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->string('original_filename');
            $table->string('stored_filename');
            $table->string('file_path');
            $table->unsignedBigInteger('file_size');
            $table->string('mime_type')->default('application/pdf');
            $table->string('status')->default('uploaded')->index();
            $table->longText('extracted_text')->nullable();
            $table->timestamp('uploaded_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cv_uploads');
    }
};
