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

        $nextAvailable = Achievement::whereNotIn('name', $unlockedAchievements)
            ->groupBy('type')
            ->map(function ($achievements) {
                return $achievements->sortBy('threshold')->values();
            })->map(function ($achievements) {
                return $achievements->first();
            })->pluck('name')->toArray();

        $currentBadge = $user->getCurrentBadge()->name;
        $nextBadge = $user->getNextBadge()->name;

        return response()->json([
            'unlocked_achievements' => $unlockedAchievements,
            'next_available_achievements' => $nextAvailable,
            'current_badge' => $currentBadge,
            'next_badge' => $nextBadge,
            'remaing_to_unlock_next_badge' => $nextBadge->required_achievements - $user->achievements->count()
        ]);
    }
}
