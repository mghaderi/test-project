<?php

namespace Tests\Feature;

use App\Events\AchievementUnlocked;
use App\Events\CommentWritten;
use App\Events\LessonWatched;
use App\Listeners\UpdateUserAchievementWithComment;
use App\Listeners\UpdateUserAchievementWithLesson;
use App\Models\Achievement;
use App\Models\Comment;
use App\Models\Enums\AchievementTypeEnum;
use App\Models\Lesson;
use App\Models\User;
use App\Models\UserAchievement;
use App\Services\AchievementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class UpdateUserAchievementListenerTest extends TestCase
{

    use RefreshDatabase;

    public function test_update_achievement_with_lesson_listener()
    {
        Event::fake();
        $listener = new UpdateUserAchievementWithLesson();
        $user = User::factory()->create();
        $lessons = Lesson::factory()->count(12)->create();
        foreach ($lessons as $lesson) {
            DB::table('lesson_user')->insert([
                'user_id' => $user->id,
                'lesson_id' => $lesson->id,
                'watched' => true,
            ]);
        }
        $wrongAchievement = Achievement::where('name', '25 Lesson Watched')->first();
        UserAchievement::create([
            'user_id' => $user->id,
            'achievement_id' => $wrongAchievement->id,
        ]);
        $userAchievementCount = UserAchievement::where('user_id', $user->id)->count();
        $this->assertTrue($userAchievementCount == 1);
        $this->assertTrue(
            UserAchievement::where('user_id', $user->id)->first()->achievement_id == $wrongAchievement->id
        );
        $listener->handle(new LessonWatched(Lesson::factory()->create(), $user));
        Event::assertDispatched(AchievementUnlocked::class, 3);
        $userAchievementCount = UserAchievement::where('user_id', $user->id)->count();
        $this->assertTrue($userAchievementCount == 3);
    }

    public function test_update_achievement_with_comment_listener()
    {
        Event::fake();
        $listener = new UpdateUserAchievementWithComment();
        $user = User::factory()->create();
        $comments = Comment::factory()->count(5)->create(['user_id' => $user->id]);
        $wrongAchievement = Achievement::where('name', '10 Comment Written')->first();
        UserAchievement::create([
            'user_id' => $user->id,
            'achievement_id' => $wrongAchievement->id,
        ]);
        $userAchievementCount = UserAchievement::where('user_id', $user->id)->count();
        $this->assertTrue($userAchievementCount == 1);
        $this->assertTrue(
            UserAchievement::where('user_id', $user->id)->first()->achievement_id == $wrongAchievement->id
        );
        $listener->handle(new CommentWritten($comments[0]));
        Event::assertDispatched(AchievementUnlocked::class, 3);
        $userAchievementCount = UserAchievement::where('user_id', $user->id)->count();
        $this->assertTrue($userAchievementCount == 3);
    }
}
