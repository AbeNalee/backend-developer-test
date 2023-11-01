<?php

namespace App\Listeners;

use App\Services\AchievementService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UserAchievementListener
{
    /**
     * Create the event listener.
     */
    public function __construct(protected AchievementService $achievementService)
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $user = $event->user;

        $this->achievementService->unlockAchievement($user, get_class($event));
    }
}
