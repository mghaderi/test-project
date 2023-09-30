<?php

namespace App\Services;

use App\Events\AchievementUnlocked;
use App\Models\Achievement;
use App\Models\Comment;
use App\Models\Enums\AchievementTypeEnum;
use App\Models\User;
use App\Models\UserAchievement;
use Illuminate\Support\Facades\DB;

class AchievementService
{
    public function userAchievementReport(User $user): array
    {
        $unlockedAchievements = Achievement::select('achievements.*')
            ->join('user_achievements', 'achievements.id', '=', 'user_achievements.achievement_id')
            ->where('user_achievements.user_id', $user->id)
            ->get();
        $unlockedAchievements = $unlockedAchievements
            ->merge(
                Achievement::where('minimum_amount', 0)->get()
            );
        $nextAchievements = Achievement
            ::whereNotIn('id', $unlockedAchievements->pluck('achievements.id')->all())
            ->get()
            ->groupBy('type');
        $nextAvailableAchievements = collect([]);
        foreach ($nextAchievements as $type => $achievements) {
            $nextAvailableAchievements = $nextAvailableAchievements->concat(
                $achievements->where('minimum_amount', $achievements->min('minimum_amount'))
            );
        }
        return [
            'unlocked_achievement_names' =>
                $unlockedAchievements->pluck('achievements.name')->unique()->all(),
            'next_available_achievement_names' =>
                $nextAvailableAchievements->pluck('name')->all()
        ];
    }

    public function addToUserAchievementsWithLesson(User $user): void
    {
        $achievementIdsOfUser = UserAchievement::where('user_id', $user->id)
            ->pluck('achievement_id')
            ->all();
        $numberOfUserLessons = DB::table('lesson_user')
            ->where('user_id', $user->id)
            ->where('watched', true)
            ->count();
        $newAchievements = Achievement::whereNotIn('id', $achievementIdsOfUser)
            ->where('type', AchievementTypeEnum::Lesson)
            ->where('minimum_amount', '<=', $numberOfUserLessons)
            ->get();
        foreach ($newAchievements as $achievement) {
            UserAchievement::create([
                'user_id' => $user->id,
                'achievement_id' => $achievement->id,
            ]);
            AchievementUnlocked::dispatch($achievement->name, $user);
        }
    }

    public function addToUserAchievementsWithComment(User $user): void
    {
        $achievementIdsOfUser = UserAchievement::where('user_id', $user->id)
            ->pluck('achievement_id')
            ->all();
        $numberOfUserComments = Comment::where('user_id', $user->id)->count();
        $newAchievements = Achievement::whereNotIn('id', $achievementIdsOfUser)
            ->where('type', AchievementTypeEnum::Comment)
            ->where('minimum_amount', '<=', $numberOfUserComments)
            ->get();
        foreach ($newAchievements as $achievement) {
            UserAchievement::create([
                'user_id' => $user->id,
                'achievement_id' => $achievement->id,
            ]);
            AchievementUnlocked::dispatch($achievement->name, $user);
        }
    }

    public function checkUserAchievementsWithLesson(User $user): void
    {
        $achievementIdsOfUser = UserAchievement::where('user_id', $user->id)
            ->pluck('achievement_id')
            ->all();
        $numberOfUserLessons = DB::table('lesson_user')
            ->where('user_id', $user->id)
            ->where('watched', true)
            ->count();
        $wrongAchievements = Achievement::whereIn('id', $achievementIdsOfUser)
            ->where('type', AchievementTypeEnum::Lesson)
            ->where('minimum_amount', '>', $numberOfUserLessons)
            ->get();
        foreach ($wrongAchievements as $wrongAchievement) {
            UserAchievement::where('user_id', $user->id)
                ->where('achievement_id', $wrongAchievement->id)
                ->delete();
        }
    }

    public function checkUserAchievementsWithComment(User $user): void
    {
        $achievementIdsOfUser = UserAchievement::where('user_id', $user->id)
            ->pluck('achievement_id')
            ->all();
        $numberOfUserComments = Comment::where('user_id', $user->id)->count();
        $wrongAchievements = Achievement::whereIn('id', $achievementIdsOfUser)
            ->where('type', AchievementTypeEnum::Comment)
            ->where('minimum_amount', '>', $numberOfUserComments)
            ->get();
        foreach ($wrongAchievements as $wrongAchievement) {
            UserAchievement::where('user_id', $user->id)
                ->where('achievement_id', $wrongAchievement->id)
                ->delete();
        }
    }
}
