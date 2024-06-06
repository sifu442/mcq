<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Exam;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
         \App\Models\User::factory(1)->create();

         \App\Models\User::factory()->create([
             'name' => 'Sifat',
             'email' => 'test@admin.com',
             'password' => Hash::make('password')
         ]);


    }
}
