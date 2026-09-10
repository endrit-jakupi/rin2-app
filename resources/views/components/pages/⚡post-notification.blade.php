<?php

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

new class extends Component
{
    public Collection $users;

    public string $type = '';

    public string $message = '';

    public string $expiresAt = '';

    public string $recipient = '';

    public string $userId = '';

    public function mount(): void
    {
        $this->users = User::query()
            ->orderBy('name')
            ->get();
    }

    public function save(): void
    {
        $this->validate([
            'type' => ['required', 'in:marketing,invoices,system'],
            'message' => ['required', 'string', 'max:255'],
            'expiresAt' => ['required', 'date', 'after:now'],
            'recipient' => ['required', 'in:all,specific'],
            'userId' => ['required_if:recipient,specific'],
        ]);
    
        Notification::create([
            'type' => $this->type,
            'message' => $this->message,
            'expires_at' => $this->expiresAt,
            'user_id' => $this->recipient === 'specific'
                ? $this->userId
                : null,
        ]);
    
        $this->redirect('/notification-management');
    }
};
?>

<div>
    <p class="mb-8 text-3xl font-semibold">
        Post notification
    </p>

    <form wire:submit="save" class="max-w-xl">

        <div class="mb-6">
            <label
                for="type"
                class="block text-sm font-medium"
            >
                Type
            </label>

            <select
                id="type"
                wire:model="type"
                class="mt-3 w-full rounded-full border border-gray-300 px-4 py-2 text-gray-600"
            >
                <option value="">Select type</option>
                <option value="marketing">Marketing</option>
                <option value="invoices">Invoices</option>
                <option value="system">System</option>
            </select>

            @error('type')
                <p class="mt-2 text-sm font-medium text-red-600">
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div class="mb-6">
            <label
                for="message"
                class="block text-sm font-medium"
            >
                Message
            </label>

            <textarea
                id="message"
                wire:model="message"
                rows="4"
                class="mt-3 w-full rounded-2xl border border-gray-300 px-4 py-2 text-gray-600 placeholder:text-gray-400 focus:border-gray-400 focus:outline-none"
                placeholder="Enter notification message"
            ></textarea>

            @error('message')
                <p class="mt-2 text-sm font-medium text-red-600">
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div class="mb-6">
            <label
                for="expiresAt"
                class="block text-sm font-medium"
            >
                Expires
            </label>

            <input
                id="expiresAt"
                type="datetime-local"
                wire:model="expiresAt"
                class="mt-3 w-full rounded-full border border-gray-300 px-4 py-2 text-gray-600"
            >

            @error('expiresAt')
                <p class="mt-2 text-sm font-medium text-red-600">
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div class="mb-6">
            <label
                for="recipient"
                class="block text-sm font-medium"
            >
                Recipient
            </label>

            <select
                id="recipient"
                wire:model.live="recipient"
                class="mt-3 w-full rounded-full border border-gray-300 px-4 py-2 text-gray-600"
            >
                <option value="">Select recipient</option>
                <option value="all">All users</option>
                <option value="specific">Specific user</option>
            </select>

            @error('recipient')
                <p class="mt-2 text-sm font-medium text-red-600">
                    {{ $message }}
                </p>
            @enderror
        </div>

        @if ($recipient === 'specific')
            <div class="mb-6">
                <label
                    for="userId"
                    class="block text-sm font-medium"
                >
                    User
                </label>

                <select
                    id="userId"
                    wire:model="userId"
                    class="mt-3 w-full rounded-full border border-gray-300 px-4 py-2 text-gray-600"
                >
                    <option value="">Select user</option>

                    @foreach ($users as $user)
                        <option value="{{ $user->id }}">
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>

                @error('userId')
                    <p class="mt-2 text-sm font-medium text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>
        @endif

        <button
            type="submit"
            class="cursor-pointer rounded-full bg-blue-600 px-4 py-2 text-sm font-semibold text-white"
        >
            Post notification
        </button>
    </form>
</div>