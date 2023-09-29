<?php

namespace App\Listeners;

use App\Events\LessonWatched;
use App\Services\AchievementService;

class UpdateUserAchievementWithLesson
{

    public function handle(LessonWatched $event): void
    {
        $achievementService = new AchievementService();
        $achievementService->checkUserAchievementsWithLesson($event->user);
        $achievementService->addToUserAchievementsWithLesson($event->user);
    }
}
