<?php

use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
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

    #[On('notification-read')]
    public function refreshUnreadCount(
        NotificationService $notificationService
    ): void {
        $this->unreadCount = $notificationService->getUnreadCount(
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
        <div class="absolute right-0 z-10 mt-2 w-80 rounded-lg bg-white p-4 shadow-lg">
            <p class="mb-3 text-sm font-semibold">
                Unread notifications
            </p>

            <div class="max-h-[400px] overflow-y-auto overflow-x-visible px-1">
                @forelse ($notifications as $notification)
                    <div class="border-b border-gray-200 last:border-b-0">
                        <button
                            type="button"
                            wire:click="markAsRead({{ $notification->id }})"
                            class="relative w-full cursor-pointer py-3 text-left text-black transition-transform duration-200 ease-out hover:z-10 hover:scale-[1.03]"
                        >
                            <div class="flex gap-3">
                                <div class="flex shrink-0 items-start pt-1 text-lg">
                                    @if ($notification->type === 'system')
                                        ⚙️
                                    @elseif ($notification->type === 'marketing')
                                        📣
                                    @elseif ($notification->type === 'invoices')
                                        🧾
                                    @endif
                                </div>

                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center justify-between gap-3">
                                        <strong class="text-xs">
                                            {{ $notification->type }}
                                        </strong>

                                        <span class="shrink-0 text-[11px] text-black">
                                            {{ $notification->created_at->format('d.m.Y') }}
                                        </span>
                                    </div>

                                    <p class="mt-1 text-xs leading-5">
                                        {{ $notification->message }}
                                    </p>
                                </div>
                            </div>
                        </button>
                    </div>
                @empty
                    <p class="text-xs text-gray-500">
                        No unread notifications.
                    </p>
                @endforelse
            </div>

            <a
                href="/notifications"
                class="mt-3 block text-xs font-medium hover:underline"
            >
                View all notifications
            </a>
        </div>
    @endif
</div>