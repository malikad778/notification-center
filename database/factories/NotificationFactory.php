<?php

namespace Database\Factories;

use malikad778\NotificationCenter\Enums\NotificationPriority;
use malikad778\NotificationCenter\Enums\NotificationStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\malikad778\NotificationCenter\Models\Notification>
 */
class NotificationFactory extends Factory
{
    protected $model = \malikad778\NotificationCenter\Models\Notification::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => 'App\Notifications\GenericNotification',
            'notifiable_type' => User::class,
            'notifiable_id' => User::factory(),
            'data' => [
                'title' => $this->faker->sentence,
                'body' => $this->faker->paragraph,
            ],
            'status' => NotificationStatus::Sent,
            'priority' => NotificationPriority::Normal,
            'sent_at' => now(),
        ];
    }
}
