<?php

namespace App\Services;

use App\Events\AchievementUnlocked;
use App\Events\BadgeUnlocked;
use App\Models\Achievement;
use App\Models\Badge;
use App\Models\User;
use Illuminate\Support\Carbon;

class AchievementService
{
    public function unlockAchievement(User $user, $achievementType)
    {
        $achievements = Achievement::where('type', $achievementType)->get();

        foreach ($achievements as $achievement) {
            // Check if the user has already unlocked this achievement
            if ($user->hasAchievement($achievement->name)) {
                continue; // User has this achievement, move to the next
            }

            $requiredThreshold = $achievement->threshold;

            $userAchievement = $user->achievements()
                ->where('achievements.name', $achievement->name)
                ->first();

            if (!$userAchievement) {
                // Achievement doesn't exist for the user, create a new entry
                $user->achievements()->attach($achievement, ['progress' => 1]);
            } else {
                // Increment progress
                $progress = $userAchievement->pivot->progress;
                $progress++;

                // Check if the achievement threshold is met
                if ($progress == $achievement->threshold) {
                    // Unlock the achievement
                    $user->achievements()->updateExistingPivot($achievement,
                        [
                            'progress' => $progress,
                            'unlocked_at' => Carbon::now()
                        ]
                    );

                    // Dispatch the AchievementUnlocked event
                    event(new AchievementUnlocked($achievement->name, $user));

                    $this->unlockBadge($user);
                } else {
                    $user->achievements()->updateExistingPivot($achievement,
                        [
                            'progress' => $progress
                        ]
                    );
                }
            }
        }
    }

    protected function unlockBadge(User $user)
    {
        $achievementsCount = $user->achievements()->count();

        $badges = Badge::all();

        foreach ($badges as $rule) {
            // Check if the user has earned a new badge based on the number of achievements
            if ($achievementsCount >= $rule->required_achievements && !$user->hasBadge($rule->name)) {
                $user->badges()->attach($rule);

                //fire badge unlocked event
                event(new BadgeUnlocked($rule->name, $user));
            }
        }
    }
}
