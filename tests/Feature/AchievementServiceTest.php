<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AchievementServiceTest extends TestCase
{
    public function testUnlockFirstCommentWritten()
    {
        // Arrange: Create a user and achievements, and set their progress
        $user = User::factory()->create();
        factory(Achievement::class)->create(['name' => 'First Comment Written', 'threshold' => 1]);
        $user->achievements()->attach(Achievement::where('name', 'First Comment Written')->first(), ['progress' => 0]);

        // Act: Unlock the first comment written achievement
        $unlockedAchievement = app(AchievementService::class)->unlockAchievement($user);

        // Assert: Confirm that the achievement is unlocked
        $this->assertEquals('First Comment Written', $unlockedAchievement);
    }
}
