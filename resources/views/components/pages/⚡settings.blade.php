<?php

use App\Services\PhoneNumberService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

new class extends Component
{
    public bool $notificationsEnabled;

    public string $email = '';

    public ?string $phone = null;

    public function mount(): void
    {
        $user = Auth::user();

        $this->notificationsEnabled = $user->notifications_enabled;
        $this->email = $user->email;
        $this->phone = $user->phone;
    }

    public function save(PhoneNumberService $phoneNumberService): void
    {
        $this->validate([
            'email' => ['required', 'email'],
            'phone' => ['nullable', 'string'],
        ]);

        if ($this->phone !== null && $this->phone !== '') {
            if (! $phoneNumberService->isMobile($this->phone)) {
                $this->addError(
                    'phone',
                    'Please enter a valid mobile phone number.'
                );

                return;
            }
        }

        Auth::user()->update([
            'notifications_enabled' => $this->notificationsEnabled,
            'email' => $this->email,
            'phone' => $this->phone,
        ]);
        
        $this->redirect('/settings');
    }
};
?>

<div>
    <p class="mb-6 text-3xl font-semibold">
        Settings
    </p>

    <form wire:submit="save" class="max-w-xl space-y-6">
        <div>
            <label for="notificationsEnabled" class="block text-sm font-medium">
                On-screen notifications
            </label>

            <input
                id="notificationsEnabled"
                type="checkbox"
                wire:model="notificationsEnabled"
                class="mt-2"
            >

            <span class="ml-2 text-sm text-gray-600">
                Receive on-screen notifications
            </span>
        </div>

        <div>
            <label for="email" class="block text-sm font-medium">
                Email
            </label>

            <input
                id="email"
                type="email"
                wire:model="email"
                class="mt-2 w-full rounded border px-3 py-2"
            >

            @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="phone" class="block text-sm font-medium">
                Phone
            </label>

            <input
                id="phone"
                type="tel"
                wire:model="phone"
                class="mt-2 w-full rounded border px-3 py-2"
            >

            @error('phone')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <button
            type="submit"
            class="cursor-pointer rounded-full bg-blue-600 px-4 py-2 text-sm font-semibold text-white"
        >
            Save
        </button>
    </form>
</div>