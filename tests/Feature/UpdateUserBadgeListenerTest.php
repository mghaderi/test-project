<?php

namespace Tests\Feature;

use App\Events\AchievementUnlocked;
use App\Events\BadgeUnlocked;
use App\Listeners\UpdateUserBadge;
use App\Models\Badge;
use App\Models\User;
use App\Models\UserAchievement;
use App\Models\UserBadge;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class UpdateUserBadgeListenerTest extends TestCase
{

    use RefreshDatabase;

    public function test_update_user_badge_listener()
    {
        Event::fake();
        $listener = new UpdateUserBadge();
        $user = User::factory()->create();
        UserAchievement::factory()->count(8)->create(['user_id' => $user->id]);
        $wrongBadge = Badge::where('name', 'Master')->first();
        UserBadge::create([
            'user_id' => $user->id,
            'badge_id' => $wrongBadge->id,
        ]);
        $userBadgeCount = UserBadge::where('user_id', $user->id)->count();
        $this->assertTrue($userBadgeCount == 1);
        $this->assertTrue(
            UserBadge::where('user_id', $user->id)->first()->badge_id == $wrongBadge->id
        );
        $listener->handle(new AchievementUnlocked('First Lesson Watched', $user));
        Event::assertDispatched(BadgeUnlocked::class, 3);
        $userBadgeCount = UserBadge::where('user_id', $user->id)->count();
        $this->assertTrue($userBadgeCount == 3);
    }
}
