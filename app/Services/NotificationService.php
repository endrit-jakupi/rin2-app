<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\NotificationRead;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class NotificationService
{
    // Get notifications available to the user that have not expired.
    public function getForUser(User $user): Collection
    {
        return $this->availableNotificationsFor($user)
            ->latest()
            ->get();
    }

    // Get available notifications that the user has not read yet.
    public function getUnreadForUser(User $user): Collection
    {
        return $this->availableNotificationsFor($user)
            ->whereDoesntHave('reads', function (Builder $query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->latest()
            ->get();
    }

    // Get the number of available notifications that the user has not read yet.
    public function getUnreadCount(User $user): int
    {
        return $this->availableNotificationsFor($user)
            ->whereDoesntHave('reads', function (Builder $query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->count();
    }

    // Mark a notification as read for the given user.
    public function markAsRead(Notification $notification, User $user): void
    {
        $notification = $this->availableNotificationsFor($user)
            ->whereKey($notification->id)
            ->first();

        if ($notification === null) {
            throw new ModelNotFoundException();
        }

        NotificationRead::firstOrCreate(
            [
                'notification_id' => $notification->id,
                'user_id' => $user->id,
            ],
            [
                'read_at' => now(),
            ]
        );
    }

    // Build the query for notifications available to the user.
    private function availableNotificationsFor(User $user): Builder
    {
        return Notification::query()
            ->where(function (Builder $query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhereNull('user_id');
            })
            ->where('expires_at', '>', now());
    }
}