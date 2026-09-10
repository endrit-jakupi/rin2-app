<?php

use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

new class extends Component
{
    public $notifications;

    public function mount(NotificationService $notificationService): void
    {
        $user = Auth::user();

        $this->notifications = $notificationService->getForUser($user);

        $readNotificationIds = $user->notificationReads()
            ->pluck('notification_id')
            ->toArray();

        $this->notifications->each(function (Notification $notification) use ($readNotificationIds) {
            $notification->is_read = in_array(
                $notification->id,
                $readNotificationIds
            );
        });
    }

    public function markAsRead(int $notificationId, NotificationService $notificationService): void
    {
        $notification = Notification::findOrFail($notificationId);

        $notificationService->markAsRead(
            $notification,
            Auth::user()
        );

        $this->mount($notificationService);
    }
};
?>

<div>
    <p class="mb-6 text-3xl font-semibold">
        Notifications
    </p>

    @forelse ($notifications as $notification)
        <div>
            <button
                type="button"
                wire:click="markAsRead({{ $notification->id }})"
            >
                <strong>{{ $notification->type }}</strong>
            </button>

            @if ($notification->is_read)
                <span>Read</span>
            @else
                <span>Unread</span>
            @endif

            <p>{{ $notification->message }}</p>

            <small>
                Expires: {{ $notification->expires_at->format('Y-m-d H:i') }}
            </small>
        </div>
    @empty
        <p>No notifications.</p>
    @endforelse
</div>