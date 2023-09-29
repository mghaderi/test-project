<?php

namespace Database\Factories;

use App\Models\Achievement;
use App\Models\User;
use App\Models\UserAchievement;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserAchievementFactory extends Factory
{
    protected $model = UserAchievement::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'achievement_id' => Achievement::factory(),
        ];
    }
}
