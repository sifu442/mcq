<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Exam;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Exam>
 */
final class ExamFactory extends Factory
{
    /**
    * The name of the factory's corresponding model.
    *
    * @var string
    */
    protected $model = Exam::class;

    /**
    * Define the model's default state.
    *
    * @return array
    */
    public function definition(): array
    {
        return [
            'name' => fake()->name,
            'course_id' => \App\Models\Course::factory(),
            'syllabus' => fake()->word,
            'duration' => fake()->randomNumber(),
            'score' => fake()->randomFloat(2, 0, 9),
            'penalty' => fake()->randomFloat(2, 0, 9),
            'marks' => fake()->optional()->randomFloat(2, 0, 99),
        ];
    }
}
