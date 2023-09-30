<?php

namespace Tests\Unit;

use App\Events\AchievementUnlocked;
use App\Models\Achievement;
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
        Comment::factory()->count(7)->create(['user_id' => $user->id]);
        $achievementService->addToUserAchievementsWithComment($user);
        $userAchievementCount = UserAchievement::where('user_id', $user->id)->count();
        $this->assertTrue($userAchievementCount == 3);
        Event::assertDispatched(AchievementUnlocked::class, 3);
    }

    public function test_check_user_achievements_with_lesson_method_with_no_achievement_changed()
    {
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
        $achievementService->checkUserAchievementsWithLesson($user);
        $userAchievementCount = UserAchievement::where('user_id', $user->id)->count();
        $this->assertTrue($userAchievementCount == 3);
    }

    public function test_check_user_achievements_with_lesson_method_with_achievement_changed()
    {
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
        Achievement::where('name', '10 Lesson Watched')->update(['minimum_amount' => 15]);
        $achievementService->checkUserAchievementsWithLesson($user);
        $userAchievementCount = UserAchievement::where('user_id', $user->id)->count();
        $this->assertTrue($userAchievementCount == 2);
    }

    public function test_check_user_achievements_with_comment_method_with_no_achievement_changed()
    {
        $achievementService = new AchievementService();
        $user = User::factory()->create();
        Comment::factory()->count(7)->create(['user_id' => $user->id]);
        $achievementService->addToUserAchievementsWithComment($user);
        $userAchievementCount = UserAchievement::where('user_id', $user->id)->count();
        $this->assertTrue($userAchievementCount == 3);
        $achievementService->checkUserAchievementsWithComment($user);
        $userAchievementCount = UserAchievement::where('user_id', $user->id)->count();
        $this->assertTrue($userAchievementCount == 3);
    }

    public function test_check_user_achievements_with_comment_method_with_achievement_changed()
    {
        $achievementService = new AchievementService();
        $user = User::factory()->create();
        Comment::factory()->count(7)->create(['user_id' => $user->id]);
        $achievementService->addToUserAchievementsWithComment($user);
        $userAchievementCount = UserAchievement::where('user_id', $user->id)->count();
        $this->assertTrue($userAchievementCount == 3);
        Achievement::where('name', '5 Comment Written')->update(['minimum_amount' => 8]);
        $achievementService->checkUserAchievementsWithComment($user);
        $userAchievementCount = UserAchievement::where('user_id', $user->id)->count();
        $this->assertTrue($userAchievementCount == 2);
    }

    public function test_user_achievement_report_method()
    {
        $achievementService = new AchievementService();
        $user = User::factory()->create();
        $report = $achievementService->userAchievementReport($user);
        $this->assertTrue($report['unlocked_achievement_names'] == []);
        $this->assertTrue($report['next_available_achievement_names'] == [
            'First Lesson Watched',
            'First Comment Written'
        ]);
        Comment::factory()->count(7)->create(['user_id' => $user->id]);
        $achievementService->addToUserAchievementsWithComment($user);
        $report = $achievementService->userAchievementReport($user);
        $this->assertTrue($report['unlocked_achievement_names'] == [
            'First Comment Written',
            '3 Comment Written',
            '5 Comment Written',
        ]);
        $this->assertTrue($report['next_available_achievement_names'] == [
            'First Lesson Watched',
            '10 Comment Written'
        ]);
    }
}
