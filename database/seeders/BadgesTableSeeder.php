<?php

namespace Database\Seeders;

use App\Models\Badge;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BadgesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Badge::create(['name' => 'Beginner', 'required_achievements' => 0]);

        Badge::create(['name' => 'Intermediate', 'required_achievements' => 4]);

        Badge::create(['name' => 'Advanced', 'required_achievements' => 8]);

        Badge::create(['name' => 'Master', 'required_achievements' => 10]);
    }
}
