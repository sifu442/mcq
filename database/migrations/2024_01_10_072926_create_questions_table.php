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
            $table->string('option_a');
            $table->string('option_b');
            $table->string('option_c');
            $table->string('option_d');
            $table->string('right_answer');
            $table->string('previous_exam')->nullable();
            $table->string('topic')->nullable();
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
