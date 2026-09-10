<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'test@example.com')->firstOrFail();

        Notification::create([
            'type' => 'system',
            'message' => 'This is a notification for Test User.',
            'expires_at' => now()->addDays(7),
            'user_id' => $user->id,
        ]);

        Notification::create([
            'type' => 'marketing',
            'message' => 'This is a global notification for all users.',
            'expires_at' => now()->addDays(7),
            'user_id' => null,
        ]);
    }
}