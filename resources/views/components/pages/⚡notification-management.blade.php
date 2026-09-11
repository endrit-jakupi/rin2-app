<?php

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use Livewire\Component;

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
        {{-- Type filter --}}
        <div
            x-data="{ open: false }"
            @mouseenter="open = true"
            @mouseleave="open = false"
            class="relative"
        >
            <label class="block text-sm font-medium">
                Type
            </label>

            <button
                type="button"
                class="mt-2 flex cursor-pointer items-center gap-2 rounded-full border border-gray-300 bg-white px-5 py-2.5 text-sm"
            >
                <span>
                    @if ($typeFilter === '')
                        All types
                    @elseif ($typeFilter === 'marketing')
                        Marketing
                    @elseif ($typeFilter === 'invoices')
                        Invoices
                    @elseif ($typeFilter === 'system')
                        System
                    @endif
                </span>

                <span class="text-xs">
                    ▾
                </span>
            </button>

            <div
                x-show="open"
                x-transition
                class="absolute left-0 top-full z-30 pt-1"
            >
                <div class="w-48 rounded-xl border border-gray-200 bg-white p-1 shadow-lg">
                    <button
                        type="button"
                        wire:click="$set('typeFilter', '')"
                        class="block w-full cursor-pointer rounded-lg px-4 py-2.5 text-left text-sm hover:bg-gray-100"
                    >
                        All types
                    </button>

                    <button
                        type="button"
                        wire:click="$set('typeFilter', 'marketing')"
                        class="block w-full cursor-pointer rounded-lg px-4 py-2.5 text-left text-sm hover:bg-gray-100"
                    >
                        Marketing
                    </button>

                    <button
                        type="button"
                        wire:click="$set('typeFilter', 'invoices')"
                        class="block w-full cursor-pointer rounded-lg px-4 py-2.5 text-left text-sm hover:bg-gray-100"
                    >
                        Invoices
                    </button>

                    <button
                        type="button"
                        wire:click="$set('typeFilter', 'system')"
                        class="block w-full cursor-pointer rounded-lg px-4 py-2.5 text-left text-sm hover:bg-gray-100"
                    >
                        System
                    </button>
                </div>
            </div>
        </div>

        <div
            x-data="{ open: false, specificOpen: false }"
            @mouseenter="open = true"
            @mouseleave="open = false; specificOpen = false"
            class="relative"
        >
            <label class="block text-sm font-medium">
                Recipient
            </label>

            <button
                type="button"
                class="mt-2 flex cursor-pointer items-center gap-2 rounded-full border border-gray-300 bg-white px-5 py-2.5 text-sm"
            >
                <span>
                    @if ($recipientFilter === 'specific' && $userFilter !== '')
                        {{ $users->firstWhere('id', $userFilter)?->name }}
                    @elseif ($recipientFilter === 'global')
                        Global notifications
                    @else
                        All recipients
                    @endif
                </span>

                <span class="text-xs">
                    ▾
                </span>
            </button>

            <div
                x-show="open"
                x-transition
                class="absolute left-0 top-full z-30 pt-1"
            >
                <div class="w-52 rounded-xl border border-gray-200 bg-white p-1 shadow-lg">
                    <button
                        type="button"
                        wire:click="$set('recipientFilter', '')"
                        class="block w-full cursor-pointer rounded-lg px-4 py-2.5 text-left text-sm hover:bg-gray-100"
                    >
                        All recipients
                    </button>

                    <button
                        type="button"
                        wire:click="$set('recipientFilter', 'global')"
                        class="block w-full cursor-pointer rounded-lg px-4 py-2.5 text-left text-sm hover:bg-gray-100"
                    >
                        Global notifications
                    </button>

                    <div
                        class="relative"
                        @mouseenter="specificOpen = true"
                    >
                        <button
                            type="button"
                            class="flex w-full cursor-pointer items-center justify-between rounded-lg px-4 py-2.5 text-left text-sm hover:bg-gray-100"
                        >
                            <span>Specific user</span>

                            <span class="text-xs">
                                ▸
                            </span>
                        </button>

                        <div
                            x-show="specificOpen"
                            x-transition
                            class="absolute left-full top-0 z-40 w-56 pl-2"
                        >
                            <div class="rounded-xl border border-gray-200 bg-white p-1 shadow-lg">
                                <div class="max-h-52 overflow-y-auto">
                                    @foreach ($users as $user)
                                        <button
                                            type="button"
                                            wire:click="$set('recipientFilter', 'specific'); $set('userFilter', '{{ $user->id }}')"
                                            class="block w-full cursor-pointer rounded-lg px-4 py-2.5 text-left text-sm hover:bg-gray-100"
                                        >
                                            {{ $user->name }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="w-full">
        <table class="w-full border-separate border-spacing-y-2 text-left">
            <thead>
                <tr>
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
                        class="cursor-pointer transition-transform duration-200 ease-out hover:relative hover:z-10 hover:scale-[1.03]"
                    >
                        <td class="border-b border-gray-200 px-4 py-4 font-medium">
                            @if ($notification->type === 'system')
                                ⚙️
                            @elseif ($notification->type === 'marketing')
                                📣
                            @elseif ($notification->type === 'invoices')
                                🧾
                            @endif

                            {{ $notification->type }}
                        </td>

                        <td class="border-b border-gray-200 px-4 py-4">
                            {{ Str::limit($notification->message, 80) }}
                        </td>

                        <td class="border-b border-gray-200 px-4 py-4">
                            {{ $notification->user?->name ?? 'All users' }}
                        </td>

                        <td class="border-b border-gray-200 px-4 py-4">
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