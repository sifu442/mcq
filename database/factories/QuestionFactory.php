<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Question>
 */
final class QuestionFactory extends Factory
{
    /**
    * The name of the factory's corresponding model.
    *
    * @var string
    */
    protected $model = Question::class;

    /**
    * Define the model's default state.
    *
    * @return array
    */
    public function definition(): array
    {
        return [
            'title' => fake()->title,
            'subject_id' => \App\Models\Subject::factory(),
            'options' => fake()->word,
            'previous_exam' => fake()->optional()->word,
            'post' => fake()->optional()->word,
            'date' => fake()->optional()->datetime(),
            'explanation' => fake()->optional()->word,
        ];
    }
}
