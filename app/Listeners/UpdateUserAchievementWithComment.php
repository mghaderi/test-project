<?php

namespace App\Listeners;

use App\Events\CommentWritten;
use App\Services\AchievementService;

class UpdateUserAchievementWithComment
{
    public function handle(CommentWritten $event): void
    {
        (new AchievementService())->checkUserAchievementsWithComment($event->comment->user);
        (new AchievementService())->addToUserAchievementsWithComment($event->comment->user);
    }
}
