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
        switch (get_class($event)) {
            case 'App\Events\CommentWritten':
                $user = $event->comment->user;
                $progress = $user->comments()->count();
                break;
            case 'App\Events\LessonWatched':
                $user = $event->user;
                $progress = $user->watched()->count();
                break;
            default:
                $user = $event->user;
                $progress = 0;
                break;
        }

        $this->achievementService->unlockAchievement($user, get_class($event), $progress);
    }
}
