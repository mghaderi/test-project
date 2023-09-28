<?php

namespace App\Models;

use App\Models\Enums\AchievementTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'minimum_amount'
    ];

    protected $casts = [
        'type' => AchievementTypeEnum::class
    ];
}
