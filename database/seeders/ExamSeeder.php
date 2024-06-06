<?php

namespace Database\Seeders;

use App\Models\Exam;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ExamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Exam::factory(3)->create();

        Exam::factory()->create([
            'title' => '45th BCS',
            'description' => '45th BCS',
            'slug' => '45th-bcs',
            'price' => '100',
            'total_exams' => '50',
         ]);
    }
}
