<?php

namespace Tests\Feature;

use App\Events\AchievementUnlocked;
use App\Events\BadgeUnlocked;
use App\Events\CommentWritten;
use App\Events\LessonWatched;
use App\Listeners\UpdateUserAchievementWithComment;
use App\Listeners\UpdateUserAchievementWithLesson;
use App\Listeners\UpdateUserBadge;
use App\Models\Badge;
use App\Models\Comment;
use App\Models\Lesson;
use App\Models\User;
use App\Models\UserAchievement;
use App\Models\UserBadge;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class UserAchievementReportRequestTest extends TestCase
{

    use RefreshDatabase;

    public function test_user_achievement_report_requet_for_user_with_no_achievement()
    {
        $user = User::factory()->create();
        $response = $this->get('/users/100/achievements');
        $response->assertStatus(404);
        $response = $this->get('/users/' . $user->id . '/achievements');
        $response->assertStatus(200);
        $response->assertJson(
            fn (AssertableJson $json) =>
                $json->where('unlocked_achievements', [])
                    ->where(
                        'next_available_achievements',
                        ['First Lesson Watched', 'First Comment Written']
                    )
                    ->where('current_badge', 'Beginner')
                    ->where('next_badge', 'Intermediate')
                    ->where('remaing_to_unlock_next_badge', 4)
        );
    }

    public function test_user_achievement_report_requet_for_user_with_some_achievements()
    {
        $user = User::factory()->create();
        $comments = Comment::factory()->count(5)->create(['user_id' => $user->id]);
        $listener = new UpdateUserAchievementWithComment();
        foreach ($comments as $comment) {
            $listener->handle(new CommentWritten($comment));
        }
        $lessons = Lesson::factory()->count(12)->create();
        foreach ($lessons as $lesson) {
            DB::table('lesson_user')->insert([
                'user_id' => $user->id,
                'lesson_id' => $lesson->id,
                'watched' => true,
            ]);
        }
        $listener = new UpdateUserAchievementWithLesson();
        foreach ($lessons as $lesson) {
            $listener->handle(new LessonWatched($lesson, $user));
        }
        $response = $this->get('/users/' . $user->id . '/achievements');
        $response->assertStatus(200);
        $response->assertJson(
            fn (AssertableJson $json) =>
            $json->where('unlocked_achievements', [
                    'First Comment Written',
                    '3 Comment Written',
                    '5 Comment Written',
                    'First Lesson Watched',
                    '5 Lesson Watched',
                    '10 Lesson Watched'
                ])
                ->where('next_available_achievements', [
                    '25 Lesson Watched', '10 Comment Written'
                ])
                ->where('current_badge', 'Intermediate')
                ->where('next_badge', 'Advanced')
                ->where('remaing_to_unlock_next_badge', 2)
        );
    }

    public function test_user_achievement_report_requet_for_user_with_all_achievements()
    {
        $user = User::factory()->create();
        $comments = Comment::factory()->count(100)->create(['user_id' => $user->id]);
        $listener = new UpdateUserAchievementWithComment();
        foreach ($comments as $comment) {
            $listener->handle(new CommentWritten($comment));
        }
        $lessons = Lesson::factory()->count(100)->create();
        foreach ($lessons as $lesson) {
            DB::table('lesson_user')->insert([
                'user_id' => $user->id,
                'lesson_id' => $lesson->id,
                'watched' => true,
            ]);
        }
        $listener = new UpdateUserAchievementWithLesson();
        foreach ($lessons as $lesson) {
            $listener->handle(new LessonWatched($lesson, $user));
        }
        $response = $this->get('/users/' . $user->id . '/achievements');
        $response->assertStatus(200);
        $response->assertJson(
            fn (AssertableJson $json) =>
            $json->where('unlocked_achievements', [
                    'First Comment Written',
                    '3 Comment Written',
                    '5 Comment Written',
                    '10 Comment Written',
                    '20 Comment Written',
                    'First Lesson Watched',
                    '5 Lesson Watched',
                    '10 Lesson Watched',
                    '25 Lesson Watched',
                    '50 Lesson Watched',
                ])
                ->where('next_available_achievements', [])->where('current_badge', 'Master')
                ->where('next_badge', '')
                ->where('remaing_to_unlock_next_badge', 0)
        );
    }
}
