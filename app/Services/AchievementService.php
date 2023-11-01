<?php

namespace App\Services;

use App\Events\AchievementUnlocked;
use App\Models\Achievement;
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
            }
        }
    }
}
