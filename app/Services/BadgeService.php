<?php

namespace App\Services;

use App\Events\AchievementUnlocked;
use App\Events\BadgeUnlocked;
use App\Models\Achievement;
use App\Models\Badge;
use App\Models\Enums\AchievementTypeEnum;
use App\Models\User;
use App\Models\UserAchievement;
use App\Models\UserBadge;
use Illuminate\Support\Facades\DB;

class BadgeService
{
    public function addToUserBadges(User $user): void
    {
        $badgeIdsOfUser = UserBadge::where('user_id', $user->id)
            ->pluck('badge_id')
            ->all();
        $numberOfUserAchievements = UserAchievement::where('user_id', $user->id)->count();
        $newBadges = Badge::whereNotIn('id', $badgeIdsOfUser)
            ->where('minimum_achievement_amount', '<=', $numberOfUserAchievements)
            ->get();
        foreach ($newBadges as $badge) {
            UserBadge::create([
                'user_id' => $user->id,
                'badge_id' => $badge->id,
            ]);
            BadgeUnlocked::dispatch($badge->name, $user);
        }
    }

    public function checkUserBadges(User $user): void
    {
        $badgeIdsOfUser = UserBadge::where('user_id', $user->id)
            ->pluck('badge_id')
            ->all();
        $numberOfUserAchievements = UserAchievement::where('user_id', $user->id)->count();
        $wrongBadges = Badge::whereIn('id', $badgeIdsOfUser)
            ->where('minimum_achievement_amount', '>', $numberOfUserAchievements)
            ->get();
        foreach ($wrongBadges as $wrongBadge) {
            UserBadge::where('user_id', $user->id)
                ->where('badge_id', $wrongBadge->id)
                ->delete();
        }
    }
}
