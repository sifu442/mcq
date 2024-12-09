<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

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
             'mobile_phone' => '01989957085',
             'password' => Hash::make('password')
         ]);

    }
}
