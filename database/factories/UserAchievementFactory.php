<?php

namespace Database\Factories;

use App\Models\Achievement;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    protected $model = Comment::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'achievement_id' => Achievement::factory(),
        ];
    }
}
