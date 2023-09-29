<?php

namespace Database\Seeders;

use App\Models\Achievement;
use App\Models\Badge;
use App\Models\Enums\AchievementTypeEnum;
use Illuminate\Database\Seeder;

class AssumptionSeeder extends Seeder
{
    private $lesson_achievements_data = [
        ['name' => 'First Lesson Watched', 'minimum_amount' => 1],
        ['name' => '5 Lesson Watched', 'minimum_amount' => 5],
        ['name' => '10 Lesson Watched', 'minimum_amount' => 10],
        ['name' => '25 Lesson Watched', 'minimum_amount' => 25],
        ['name' => '50 Lesson Watched', 'minimum_amount' => 50],
    ];

    private $comment_achievements_data = [
        ['name' => 'First Comment Written', 'minimum_amount' => 1],
        ['name' => '3 Comment Written', 'minimum_amount' => 3],
        ['name' => '5 Comment Written', 'minimum_amount' => 5],
        ['name' => '10 Comment Written', 'minimum_amount' => 10],
        ['name' => '20 Comment Written', 'minimum_amount' => 20],
    ];

    private $badges_data = [
        ['name' => 'Beginner', 'minimum_achievement_amount' => 0],
        ['name' => 'Intermediate', 'minimum_achievement_amount' => 4],
        ['name' => 'Advanced', 'minimum_achievement_amount' => 8],
        ['name' => 'Master', 'minimum_achievement_amount' => 10],
    ];

    public function run(): void
    {
        $this->setAssumptionLessonAchievements();
        $this->setAssumptionCommentAchievements();
        $this->setAssumptionBadges();
    }

    public function setAssumptionLessonAchievements()
    {
        foreach ($this->lesson_achievements_data as $achievementData) {
            if (empty(Achievement::where('name', $achievementData['name'])->first())) {
                $achievementData['type'] = AchievementTypeEnum::Lesson;
                Achievement::factory()->create($achievementData);
            }
        }
    }

    public function setAssumptionCommentAchievements()
    {
        foreach ($this->comment_achievements_data as $achievementData) {
            if (empty(Achievement::where('name', $achievementData['name'])->first())) {
                $achievementData['type'] = AchievementTypeEnum::Comment;
                Achievement::factory()->create($achievementData);
            }
        }
    }

    public function setAssumptionBadges()
    {
        foreach ($this->badges_data as $badgeData) {
            if (empty(Badge::where('name', $badgeData['name'])->first())) {
                Badge::factory()->create($badgeData);
            }
        }
    }
}
