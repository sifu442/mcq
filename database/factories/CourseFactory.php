<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Course>
 */
final class CourseFactory extends Factory
{
    /**
    * The name of the factory's corresponding model.
    *
    * @var string
    */
    protected $model = Course::class;

    /**
    * Define the model's default state.
    *
    * @return array
    */
    public function definition(): array
    {
        return [
            'title' => fake()->name,
            'description' => fake()->text,
            'slug' => fake()->slug,
            'time_span' => fake()->randomNumber(),
            'price' => fake()->randomFloat(2, 0, 999),
            'discounted_price' => fake()->optional()->randomFloat(2, 0, 999),
            'featured' => fake()->randomNumber(1),
            'total_exams' => fake()->randomFloat(2, 0, 99),
            'participation_time' => fake()->randomNumber(),
            'resources' => fake()->optional()->word,
        ];
    }
}
