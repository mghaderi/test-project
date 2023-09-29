<?php

namespace App\Listeners;

use App\Events\AchievementUnlocked;
use App\Services\BadgeService;

class UpdateUserBadge
{

    public function handle(AchievementUnlocked $event): void
    {
        $badgeService = new BadgeService();
        $badgeService->checkUserBadges($event->user);
        $badgeService->addToUserBadges($event->user);
    }
}
