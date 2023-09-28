<?php

namespace Database\Factories;

use App\Models\Achievement;
use App\Models\Enums\AchievementTypeEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

class AchievementFactory extends Factory
{
    protected $model = Achievement::class;

    public function definition()
    {
        return [
            'name' => fake()->name(),
            'type' => AchievementTypeEnum::cases()[array_rand(AchievementTypeEnum::cases())],
            'minimum_amount' => rand(min: 1, max: 10),
        ];
    }
}
