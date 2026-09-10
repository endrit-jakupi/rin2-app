<?php

use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

new class extends Component
{
    public bool $open = false;

    public int $unreadCount = 0;

    public $notifications;

    public function mount(NotificationService $notificationService): void
    {
        $this->unreadCount = $notificationService->getUnreadCount(
            Auth::user()
        );
    }

    public function toggle(NotificationService $notificationService): void
    {
        $this->open = ! $this->open;

        if ($this->open) {
            $this->notifications = $notificationService->getUnreadForUser(
                Auth::user()
            );
        }
    }

    public function markAsRead(
        int $notificationId,
        NotificationService $notificationService
    ): void {
        $notification = Notification::findOrFail($notificationId);

        $notificationService->markAsRead(
            $notification,
            Auth::user()
        );

        $this->unreadCount = $notificationService->getUnreadCount(
            Auth::user()
        );

        $this->notifications = $notificationService->getUnreadForUser(
            Auth::user()
        );
    }
};
?>

<div class="relative">
    <button
        type="button"
        wire:click="toggle"
        class="relative flex cursor-pointer items-center"
        aria-label="Notifications"
    >
        <span class="text-2xl">🔔</span>

        <span class="absolute right-0 top-0 z-10 flex h-5 w-5 translate-x-1/2 -translate-y-1/2 items-center justify-center rounded-full bg-blue-600 text-[10px] font-semibold text-white">
            {{ $unreadCount }}
        </span>
    </button>

    @if ($open)
        <div class="absolute right-0 z-10 mt-2 w-96 rounded-lg bg-white p-5 shadow-lg">
            <h2 class="mb-3 font-semibold">Unread notifications</h2>

            @forelse ($notifications as $notification)
                <div class="border-b py-3 last:border-b-0">
                    <button
                        type="button"
                        wire:click="markAsRead({{ $notification->id }})"
                        class="w-full text-left"
                    >
                        <strong class="text-sm">
                            {{ $notification->type }}
                        </strong>

                        <p class="mt-1 text-sm text-gray-600">
                            {{ $notification->message }}
                        </p>
                    </button>
                </div>
            @empty
                <p class="text-sm text-gray-500">
                    No unread notifications.
                </p>
            @endforelse

            <a
                href="/notifications"
                class="mt-3 block text-sm font-medium hover:underline"
            >
                View all notifications
            </a>
        </div>
    @endif
</div>