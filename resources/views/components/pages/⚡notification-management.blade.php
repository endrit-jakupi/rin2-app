<?php

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;
use Illuminate\Support\Str;

new class extends Component
{
    public Collection $notifications;

    public Collection $users;

    public string $typeFilter = '';

    public string $recipientFilter = '';

    public string $userFilter = '';

    public ?Notification $selectedNotification = null;

    public function mount(): void
    {
        $this->users = User::query()
            ->orderBy('name')
            ->get();

        $this->loadNotifications();
    }

    public function updatedTypeFilter(): void
    {
        $this->loadNotifications();
    }

    public function updatedRecipientFilter(): void
    {
        if ($this->recipientFilter !== 'specific') {
            $this->userFilter = '';
        }

        $this->loadNotifications();
    }

    public function updatedUserFilter(): void
    {
        $this->loadNotifications();
    }

    public function showNotification(int $notificationId): void
    {
        $this->selectedNotification = Notification::query()
            ->with('user')
            ->findOrFail($notificationId);
    }

    public function closeNotification(): void
    {
        $this->selectedNotification = null;
    }

    private function loadNotifications(): void
    {
        $query = Notification::query()
            ->with('user')
            ->latest();

        if ($this->typeFilter !== '') {
            $query->where('type', $this->typeFilter);
        }

        if ($this->recipientFilter === 'global') {
            $query->whereNull('user_id');
        }

        if ($this->recipientFilter === 'specific' && $this->userFilter !== '') {
            $query->where('user_id', $this->userFilter);
        }

        $this->notifications = $query->get();
    }
};
?>

<div>
    <p class="mb-6 text-3xl font-semibold">
        Notification Management
    </p>

    <a
        href="/notification-management/create"
        class="mb-6 inline-block cursor-pointer rounded-full bg-blue-600 px-4 py-2 text-sm font-semibold text-white no-underline"
    >
        Post notification
    </a>

    <div class="mb-6 flex gap-4">
        <div>
            <label
                for="typeFilter"
                class="block text-sm font-medium"
            >
                Type
            </label>

            <select
                id="typeFilter"
                wire:model.live="typeFilter"
                class="mt-2 rounded-full border border-gray-300 px-5 py-2.5 text-sm"
            >
                <option value="">All types</option>
                <option value="marketing">Marketing</option>
                <option value="invoices">Invoices</option>
                <option value="system">System</option>
            </select>
        </div>

        <div>
            <label
                for="recipientFilter"
                class="block text-sm font-medium"
            >
                Recipient
            </label>

            <select
                id="recipientFilter"
                wire:model.live="recipientFilter"
                class="mt-2 rounded-full border border-gray-300 px-5 py-2.5 text-sm"
            >
                <option value="">All recipients</option>
                <option value="global">Global notifications</option>
                <option value="specific">Specific user</option>
            </select>
        </div>

        @if ($recipientFilter === 'specific')
            <div>
                <label
                    for="userFilter"
                    class="block text-sm font-medium"
                >
                    User
                </label>

                <select
                    id="userFilter"
                    wire:model.live="userFilter"
                    class="mt-2 rounded-full border border-gray-300 px-5 py-2.5 text-sm"
                >
                    <option value="">All specific users</option>

                    @foreach ($users as $user)
                        <option value="{{ $user->id }}">
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        @endif
    </div>

    <div class="w-full">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b">
                    <th class="px-4 py-3">Type</th>
                    <th class="px-4 py-3">Message</th>
                    <th class="px-4 py-3">Destination</th>
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
                            {{ $notification->user?->name ?? 'All users' }}
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
                            No notifications have been posted.
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
                            Destination
                        </p>

                        <p class="mt-1">
                            {{ $selectedNotification->user?->name ?? 'All users' }}
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