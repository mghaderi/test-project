<?php

namespace Tests\Unit;

use App\Events\BadgeUnlocked;
use App\Models\Badge;
use App\Models\User;
use App\Models\UserAchievement;
use App\Models\UserBadge;
use App\Services\BadgeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class BadgeServiceTest extends TestCase
{

    use RefreshDatabase;

    public function test_add_to_user_badges_method_with_user_that_get_one_badge(): void
    {
        Event::fake();
        $badgeService = new BadgeService();
        $user = User::factory()->create();
        $badgeService->addToUserBadges($user);
        $userBadgeCount = UserBadge::where('user_id', $user->id)->count();
        $this->assertTrue($userBadgeCount == 1);
        Event::assertDispatched(BadgeUnlocked::class, 1);
    }

    public function test_add_to_user_badges_method_with_user_that_get_more_than_one_badge(): void
    {
        Event::fake();
        $badgeService = new BadgeService();
        $user = User::factory()->create();
        UserAchievement::factory()->count(9)->create(['user_id' => $user->id]);
        $badgeService->addToUserBadges($user);
        $userBadgeCount = UserBadge::where('user_id', $user->id)->count();
        $this->assertTrue($userBadgeCount == 3);
        Event::assertDispatched(BadgeUnlocked::class, 3);
    }

    public function test_check_user_badges_method_with_no_badge_changed()
    {
        $badgeService = new BadgeService();
        $user = User::factory()->create();
        UserAchievement::factory()->count(9)->create(['user_id' => $user->id]);
        $badgeService->addToUserBadges($user);
        $userBadgeCount = UserBadge::where('user_id', $user->id)->count();
        $this->assertTrue($userBadgeCount == 3);
        $badgeService->checkUserBadges($user);
        $userbadgeCount = UserBadge::where('user_id', $user->id)->count();
        $this->assertTrue($userBadgeCount == 3);
    }

    public function test_check_user_badges_method_with_badge_changed()
    {
        $badgeService = new BadgeService();
        $user = User::factory()->create();
        UserAchievement::factory()->count(8)->create(['user_id' => $user->id]);
        $badgeService->addToUserBadges($user);
        $userBadgeCount = UserBadge::where('user_id', $user->id)->count();
        $this->assertTrue($userBadgeCount == 3);
        Badge::where('name', 'Advanced')->update(['minimum_achievement_amount' => 9]);
        $badgeService->checkUserBadges($user);
        $userBadgeCount = UserBadge::where('user_id', $user->id)->count();
        $this->assertTrue($userBadgeCount == 2);
    }

    public function test_user_badge_report_method()
    {
        $badgeService = new BadgeService();
        $user = User::factory()->create();
        $report = $badgeService->userBadgeReport($user);
        $this->assertTrue($report == [
            "current_badge_name" => "Beginner",
            "next_badge_name" => "Intermediate",
            "remaining_for_next_badge" => 4
        ]);
        UserAchievement::factory()->count(5)->create(['user_id' => $user->id]);
        $badgeService->addToUserBadges($user);
        $report = $badgeService->userBadgeReport($user);
        $this->assertTrue($report == [
            "current_badge_name" => "Intermediate",
            "next_badge_name" => "Advanced",
            "remaining_for_next_badge" => 3,

        ]);
    }
}
