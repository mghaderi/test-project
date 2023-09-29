<?php

namespace Tests\Unit;

use App\Events\AchievementUnlocked;
use App\Models\Comment;
use App\Models\Lesson;
use App\Models\User;
use App\Models\UserAchievement;
use App\Services\AchievementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class AchievementServiceTest extends TestCase
{

    use RefreshDatabase;

    public function test_add_to_user_achievements_with_lesson_method_with_user_that_has_no_achievement(): void
    {
        $achievementService = new AchievementService();
        $user = User::factory()->create();
        $achievementService->addToUserAchievementsWithLesson($user);
        $userAchievementCount = UserAchievement::where('user_id', $user->id)->count();
        $this->assertTrue($userAchievementCount == 0);
    }

    public function test_add_to_user_achievements_with_lesson_method_with_user_that_get_one_achievement(): void
    {
        Event::fake();
        $achievementService = new AchievementService();
        $user = User::factory()->create();
        $lesson = Lesson::factory()->create();
        DB::table('lesson_user')->insert([
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
            'watched' => true,
        ]);
        $achievementService->addToUserAchievementsWithLesson($user);
        $userAchievementCount = UserAchievement::where('user_id', $user->id)->count();
        $this->assertTrue($userAchievementCount == 1);
        Event::assertDispatched(AchievementUnlocked::class);
    }

    public function test_add_to_user_achievements_with_lesson_method_with_user_that_get_more_than_one_achievement(): void
    {
        Event::fake();
        $achievementService = new AchievementService();
        $user = User::factory()->create();
        $lessons = Lesson::factory()->count(12)->create();
        foreach ($lessons as $lesson) {
            DB::table('lesson_user')->insert([
                'user_id' => $user->id,
                'lesson_id' => $lesson->id,
                'watched' => true,
            ]);
        }
        $achievementService->addToUserAchievementsWithLesson($user);
        $userAchievementCount = UserAchievement::where('user_id', $user->id)->count();
        $this->assertTrue($userAchievementCount == 3);
        Event::assertDispatched(AchievementUnlocked::class, 3);
    }

    public function test_add_to_user_achievements_with_comment_method_with_user_that_has_no_achievement(): void
    {
        $achievementService = new AchievementService();
        $user = User::factory()->create();
        $achievementService->addToUserAchievementsWithComment($user);
        $userAchievementCount = UserAchievement::where('user_id', $user->id)->count();
        $this->assertTrue($userAchievementCount == 0);
    }

    public function test_add_to_user_achievements_with_comment_method_with_user_that_get_one_achievement(): void
    {
        Event::fake();
        $achievementService = new AchievementService();
        $user = User::factory()->create();
        Comment::factory()->create(['user_id' => $user->id]);
        $achievementService->addToUserAchievementsWithComment($user);
        $userAchievementCount = UserAchievement::where('user_id', $user->id)->count();
        $this->assertTrue($userAchievementCount == 1);
        Event::assertDispatched(AchievementUnlocked::class);
    }

    public function test_add_to_user_achievements_with_comment_method_with_user_that_get_more_than_one_achievement(): void
    {
        Event::fake();
        $achievementService = new AchievementService();
        $user = User::factory()->create();
        $comments = Comment::factory()->count(7)->create(['user_id' => $user->id]);
        $achievementService->addToUserAchievementsWithComment($user);
        $userAchievementCount = UserAchievement::where('user_id', $user->id)->count();
        $this->assertTrue($userAchievementCount == 3);
        Event::assertDispatched(AchievementUnlocked::class, 3);
    }
}
