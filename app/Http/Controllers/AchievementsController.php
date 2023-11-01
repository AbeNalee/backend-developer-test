<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use App\Models\User;
use Illuminate\Http\Request;

class AchievementsController extends Controller
{
    public function index(User $user)
    {
        $unlockedAchievements = $user->achievements()->wherePivotNotNull('unlocked_at')
            ->pluck('name')->toArray();

        $nextAvailable = [];
        $achievementTypes = Achievement::select('type')->groupBy('type')->pluck('type');

        foreach ($achievementTypes as $type) {
            $nextAvailable[] = Achievement::select('name')
                ->where('type', $type)
                ->whereNotIn('name', $unlockedAchievements)
                ->orderBy('threshold')
                ->first()->name;
        }

        $currentBadge = $user->getCurrentBadge()->name;
        $nextBadge = $user->getNextBadge();

        return response()->json([
            'unlocked_achievements' => $unlockedAchievements,
            'next_available_achievements' => $nextAvailable,
            'current_badge' => $currentBadge,
            'next_badge' => $nextBadge->name,
            'remaining_to_unlock_next_badge' => $nextBadge->required_achievements - $user->achievements->count()
        ]);
    }
}
