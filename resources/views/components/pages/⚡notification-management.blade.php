<?php

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

new class extends Component
{
    public Collection $notifications;

    public Collection $users;

    public string $typeFilter = '';

    public string $recipientFilter = '';

    public string $userFilter = '';

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
                        class="cursor-pointer border-b hover:bg-black hover:text-white"
                    >
                        <td class="px-4 py-4 font-medium">
                            {{ $notification->type }}
                        </td>

                        <td class="px-4 py-4">
                            {{ $notification->message }}
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
</div>