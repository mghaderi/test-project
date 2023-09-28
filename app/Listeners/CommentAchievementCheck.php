<?php

namespace App\Listeners;

use App\Events\CommentWritten;

class CommentAchievementCheck
{
    public function __construct()
    {

    }

    public function handle(CommentWritten $event): void
    {
        
    }
}
