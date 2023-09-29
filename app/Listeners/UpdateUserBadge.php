<?php

namespace App\Listeners;

use App\Events\AchievementUnlocked;

class UpdateUserBadge
{

    public function handle(AchievementUnlocked $event): void
    {
        //$event->user
        //$event->achievement_name
    }
}
