<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Course;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Subject;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //  \App\Models\User::factory(1)->create();

         \App\Models\User::factory()->create([
             'name' => 'Sifat',
             'email' => 'test@admin.com',
             'password' => Hash::make('password')
         ]);

         Course::factory(5)->create();
         Exam::factory(5)->create();
         Question::factory(10)->create();
         Subject::factory(5)->create();

    }
}
