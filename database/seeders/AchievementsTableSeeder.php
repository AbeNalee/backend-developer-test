<?php

namespace Database\Seeders;

use App\Models\Achievement;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AchievementsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Achievement::create(['name' => 'First Comment Written', 'threshold' => 1, 'type' => 'App\Events\CommentWritten']);
        Achievement::create(['name' => '3 Comments Written', 'threshold' => 3, 'type' => 'App\Events\CommentWritten']);
        Achievement::create(['name' => '5 Comments Written', 'threshold' => 5, 'type' => 'App\Events\CommentWritten']);
        Achievement::create(['name' => '10 Comments Written', 'threshold' => 10, 'type' => 'App\Events\CommentWritten']);
        Achievement::create(['name' => '20 Comments Written', 'threshold' => 20, 'type' => 'App\Events\CommentWritten']);

        Achievement::create(['name' => 'First Lesson Watched', 'threshold' => 1, 'type' => 'App\Events\LessonWatched']);
        Achievement::create(['name' => '5 Lessons Watched', 'threshold' => 5, 'type' => 'App\Events\LessonWatched']);
        Achievement::create(['name' => '10 Lessons Watched', 'threshold' => 10, 'type' => 'App\Events\LessonWatched']);
        Achievement::create(['name' => '25 Lessons Watched', 'threshold' => 25, 'type' => 'App\Events\LessonWatched']);
        Achievement::create(['name' => '50 Lessons Watched', 'threshold' => 50, 'type' => 'App\Events\LessonWatched']);
    }
}
