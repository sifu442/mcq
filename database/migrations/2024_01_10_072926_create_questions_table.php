<?php

use App\Models\Exam;
use App\Models\Subject;
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
        Schema::create('questions', function (Blueprint $table) {
            $table->id()->from(5000);
            $table->string('title');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade')->nullable();
            $table->foreignId('exam_id')->constrained()->onDelete('cascade')->nullable();
            $table->json('options');
            $table->string('previous_exam')->nullable();
            $table->string('post')->nullable();
            $table->timestamp('date')->nullable();
            $table->longText('explanation')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
