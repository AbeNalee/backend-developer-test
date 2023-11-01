<?php

namespace Tests\Feature;

use App\Events\CommentWritten;
use App\Events\LessonWatched;
use App\Listeners\UserAchievementListener;
use App\Models\Achievement;
use App\Models\Badge;
use App\Models\Comment;
use App\Models\Lesson;
use App\Models\User;
use App\Services\AchievementService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use function PHPUnit\Framework\assertTrue;

class AchievementServiceTest extends TestCase
{
    use RefreshDatabase;
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function testIsAttachedToEvents()
    {
        Event::fake();

        Event::assertListening(CommentWritten::class, UserAchievementListener::class);
        Event::assertListening(LessonWatched::class, UserAchievementListener::class);
    }

    public function testEventsTriggerAchievement()
    {
        // create a user
        $user = User::factory()->create();
        $comment = Comment::factory()->create([
            'user_id' => $user
        ]);
        \event(new CommentWritten($comment));

        //assert that the user has unlocked achievement
        $this->assertTrue($user->hasAchievement('First Comment Written'));
        $this->assertDatabaseHas('user_achievements', [
            'user_id' => $user->id,
            'achievement_id' => Achievement::where('name', 'First Comment Written')->first()->id,
        ]);
    }

    public function testUnlockFirstCommentWritten()
    {
        // create a user
        $user = User::factory()->create();

        // Act: Unlock the first comment written achievement
        app(AchievementService::class)->unlockAchievement($user, 'App\Events\CommentWritten', 1);

        // Assert: Confirm that the achievement is unlocked
        $this->assertTrue($user->hasAchievement('First Comment Written'));
    }

    public function testUnlockFirstLessonWatched()
    {
        // create a user
        $user = User::factory()->create();

        // Act: Unlock the first comment written achievement
        app(AchievementService::class)->unlockAchievement($user, 'App\Events\LessonWatched', 1);

        // Assert: Confirm that the achievement is unlocked
        $this->assertTrue($user->hasAchievement('First Lesson Watched'));
    }

    public function testUserCanProgressTowardsNextAchievement()
    {
        // create a user
        $user = User::factory()->create();

        $this->assertTrue($user->achievements()->count() == 0);

        //unlock first achievement
        app(AchievementService::class)->unlockAchievement($user, 'App\Events\CommentWritten', 2);

        //assert that the user is on the second achievement track
        $this->assertTrue($user->achievements()->count() == 2);

        //assert that the user has not unlocked the second achievement
        $this->assertTrue($user->hasAchievement('3 Comments Written'));
        // Assert: Confirm that the 3 comments achievement is not unlocked
        $this->assertFalse($user->hasUnlockedAchievement('3 Comments Written'));
    }

    public function testUserUnlocksMultipleAchievements()
    {
        // create a user
        $user = User::factory()->create();

        $this->assertTrue($user->achievements()->count() == 0);

        //unlock first achievement
        app(AchievementService::class)->unlockAchievement($user, 'App\Events\CommentWritten', 5);

        //assert that the user has achievements
        $this->assertTrue($user->hasUnlockedAchievement('First Comment Written'));
        $this->assertTrue($user->hasUnlockedAchievement('3 Comments Written'));
        $this->assertTrue($user->hasUnlockedAchievement('5 Comments Written'));
        // assert that they do not have the next achievement
        $this->assertFalse($user->hasAchievement('10 Comments Written'));
    }

    public function testNewUserHasBeginnerBadge()
    {
        // create a user
        $user = User::factory()->create();

        $badge = $user->getCurrentBadge();
        // Assert: Confirm that the user has beginner badge
        $this->assertTrue($badge !== null);
        $this->assertTrue($badge->name == 'Beginner');
        $this->assertDatabaseHas('user_badges', [
            'user_id' => $user->id,
            'badge_id' => Badge::where('name', 'Beginner')->first()->id,
        ]);
    }

    public function testUserCannotEarnMoreAchievementsOnCommenting()
    {
        // create a user
        $user = User::factory()->create();

        // Act: Unlock multiple achievements  and exceed max threshold
        app(AchievementService::class)->unlockAchievement($user, 'App\Events\CommentWritten', 50);

        $count = Achievement::where('type', 'App\Events\CommentWritten')->count();

        // Assert: Confirm that the user doesn't have more than required achievements for commenting
        $this->assertFalse($user->achievements()->count() > $count);
    }

    public function testAchievementsIncreaseCanUnlockBadge()
    {
        // create a user
        $user = User::factory()->create();

        // Act: Unlock multiple achievements
        app(AchievementService::class)->unlockAchievement($user, 'App\Events\CommentWritten', 10);

        // Assert: Confirm that the intermediate badge is unlocked
        $this->assertTrue($user->hasBadge('Intermediate'));
    }

    public function testUserCanEarnMaxBadge()
    {
        // create a user
        $user = User::factory()->create();

        // Act: Unlock all achievements
        app(AchievementService::class)->unlockAchievement($user, 'App\Events\CommentWritten', 20);
        app(AchievementService::class)->unlockAchievement($user, 'App\Events\LessonWatched', 50);

        // Assert: Confirm that the master badge is unlocked
        $this->assertTrue($user->getNextBadge() == null);
        $this->assertTrue($user->hasBadge('Master'));
    }
}
