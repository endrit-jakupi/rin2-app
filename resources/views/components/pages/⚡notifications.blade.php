<?php

use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

new class extends Component
{
    public Collection $notifications;

    public array $readNotificationIds = [];

    public ?Notification $selectedNotification = null;

    public function mount(NotificationService $notificationService): void
    {
        $this->loadNotifications($notificationService);
    }

    public function showNotification(
        int $notificationId,
        NotificationService $notificationService
    ): void {
        $notification = Notification::findOrFail($notificationId);

        $notificationService->markAsRead(
            $notification,
            Auth::user()
        );

        $this->loadNotifications($notificationService);

        $this->selectedNotification = $this->notifications
            ->firstWhere('id', $notificationId);
    }

    public function closeNotification(): void
    {
        $this->selectedNotification = null;
    }

    private function loadNotifications(
        NotificationService $notificationService
    ): void {
        $user = Auth::user();

        $this->notifications = $notificationService->getForUser($user);

        $this->readNotificationIds = $user->notificationReads()
            ->pluck('notification_id')
            ->toArray();
    }
};
?>

<div>
    <p class="mb-2 text-3xl font-semibold">
        Notifications
    </p>

    <p class="mb-6 text-sm text-gray-600">
        Click a notification for more details.
    </p>

    <div class="w-full">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b">
                    <th class="px-4 py-3">Type</th>
                    <th class="px-4 py-3">Message</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Expires</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($notifications as $notification)
                    <tr
                        wire:click="showNotification({{ $notification->id }})"
                        class="cursor-pointer border-b hover:bg-black hover:text-white"
                    >
                        <td class="px-4 py-4 font-medium">
                            {{ $notification->type }}
                        </td>

                        <td class="px-4 py-4">
                            {{ Str::limit($notification->message, 80) }}
                        </td>

                        <td class="px-4 py-4">
                            @if (in_array($notification->id, $readNotificationIds))
                                Read
                            @else
                                Unread
                            @endif
                        </td>

                        <td class="px-4 py-4">
                            {{ $notification->expires_at->format('Y-m-d H:i') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td
                            colspan="4"
                            class="px-4 py-4 text-gray-600"
                        >
                            No notifications.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($selectedNotification)
        <div
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-6"
            wire:click.self="closeNotification"
        >
            <div class="w-full max-w-xl rounded-2xl bg-white p-6 shadow-xl">
                <div class="mb-6 flex items-center justify-between">
                    <p class="text-2xl font-semibold">
                        Notification Details
                    </p>

                    <button
                        type="button"
                        wire:click="closeNotification"
                        class="cursor-pointer text-2xl text-gray-500"
                        aria-label="Close"
                    >
                        ×
                    </button>
                </div>

                <div class="space-y-5">
                    <div>
                        <p class="text-sm font-medium">
                            Type
                        </p>

                        <p class="mt-1">
                            {{ $selectedNotification->type }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm font-medium">
                            Message
                        </p>

                        <p class="mt-1 break-words">
                            {{ $selectedNotification->message }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm font-medium">
                            Status
                        </p>

                        <p class="mt-1">
                            Read
                        </p>
                    </div>

                    <div>
                        <p class="text-sm font-medium">
                            Expires
                        </p>

                        <p class="mt-1">
                            {{ $selectedNotification->expires_at->format('Y-m-d H:i') }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm font-medium">
                            Posted
                        </p>

                        <p class="mt-1">
                            {{ $selectedNotification->created_at->format('Y-m-d H:i') }}
                        </p>
                    </div>
                </div>

                <div class="mt-8">
                    <button
                        type="button"
                        wire:click="closeNotification"
                        class="cursor-pointer rounded-full bg-blue-600 px-4 py-2 text-sm font-semibold text-white"
                    >
                        Close
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>