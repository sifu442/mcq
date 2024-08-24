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
        Schema::create('exam_responses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('exam_id');
            $table->json('response_data'); // JSON column to store user responses
            $table->decimal('total_score');
            $table->boolean('can_retake')->default(false);
            $table->decimal('correct_count')->default(0);
            $table->decimal('wrong_count')->default(0);
            $table->decimal('unanswered_count')->default(0);
            $table->decimal('lost_points')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_responses');
    }
};
