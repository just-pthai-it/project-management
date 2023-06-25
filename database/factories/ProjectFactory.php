<?php

namespace Database\Factories;

use App\Models\ProjectStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition () : array
    {
        return [
            'name'          => 'project ' . Str::random(5),
            'user_id'       => 1,
            'customer_name' => fake()->name(),
            'code'          => Str::random(5),
            'summary'       => 'summary',
            'starts_at'     => now()->subMonth(),
            'ends_at'       => now()->addMonth(),
            'duration'      => now()->subMonth()->diffInDays(now()->addMonth()),
            'status_id'     => ProjectStatus::STATUS_NOT_START,
        ];
    }
}
