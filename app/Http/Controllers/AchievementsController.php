<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AchievementService;
use App\Services\BadgeService;

class AchievementsController extends Controller
{
    public function index(User $user)
    {
        $achievementReport = (new AchievementService())->userAchievementReport($user);
        $badgeReport = (new BadgeService())->userBadgeReport($user);
        return response()->json([
            'unlocked_achievements' => $achievementReport['unlocked_achievement_names'],
            'next_available_achievements' => $achievementReport['next_available_achievement_names'],
            'current_badge' => $badgeReport['current_badge_name'],
            'next_badge' => $badgeReport['next_badge_name'],
            'remaing_to_unlock_next_badge' => $badgeReport['remaining_for_next_badge'],
        ]);
    }
}
