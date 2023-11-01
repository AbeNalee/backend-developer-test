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
    public function unlockAchievement(User $user, $achievementName)
    {
        $achievement = Achievement::where('name', $achievementName)->first();

        if (!$achievement) {
            // Achievement not found, handle the error as needed
            return;
        }

        $userAchievement = $user->achievements()
            ->where('achievements.name', $achievementName)
            ->first();

        if (!$userAchievement) {
            // Achievement doesn't exist for the user, create a new entry
            $user->achievements()->attach($achievement, ['progress' => 1]);
        } else {
            // Increment progress
            $userAchievement->pivot->progress++;

            // Check if the achievement threshold is met
            if ($userAchievement->pivot->progress >= $achievement->threshold) {
                // Unlock the achievement
                $user->achievements()->updateExistingPivot($achievement,
                    [
                        'progress' => $achievement->threshold,
                        'unlocked_at' => Carbon::now()
                    ]
                );

                // Dispatch the AchievementUnlocked event
                event(new AchievementUnlocked($achievementName, $user));

                $this->unlockBadge($user);
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
