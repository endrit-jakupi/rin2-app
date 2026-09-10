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
            'phone' => [
                'nullable',
                'string',
                'regex:/^\+[1-9][0-9\s-]{7,18}$/',
            ],
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

        session()->flash('success', 'Changes saved successfully.');

        $this->redirect('/settings');
    }
};
?>

<div>
    <p class="mb-8 text-3xl font-semibold">
        Settings
    </p>

    <form wire:submit="save" class="max-w-xl">

        <div class="mb-6">
            <label
                for="notificationsEnabled"
                class="block text-sm font-medium"
            >
                On-screen notifications
            </label>

            <label class="mt-3 flex items-center gap-2">
                <input
                    id="notificationsEnabled"
                    type="checkbox"
                    wire:model="notificationsEnabled"
                >

                <span class="text-sm text-gray-600">
                    Receive on-screen notifications
                </span>
            </label>
        </div>

        <div>
            <label
                for="email"
                class="block text-sm font-medium"
            >
                Email
            </label>

            <input
                id="email"
                type="email"
                wire:model="email"
                placeholder="name@example.com"
                autocomplete="email"
                class="mt-3 w-full rounded-full border border-gray-300 px-4 py-2 text-gray-600 placeholder:text-gray-400 focus:border-gray-400 focus:outline-none"
            >

            <div>
                @error('email')
                    <p class="text-sm font-medium text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>
        </div>

        <div class="mt-3">
            <label
                for="phone"
                class="block text-sm font-medium"
            >
                Phone
            </label>

            <input
                id="phone"
                type="tel"
                wire:model="phone"
                placeholder="+41781234567"
                class="mt-3 w-full rounded-full border border-gray-300 px-4 py-2 text-gray-600 placeholder:text-gray-400 focus:border-gray-400 focus:outline-none"
            >

            <div class="mt-4">
                @error('phone')
                    <p class="text-sm font-medium text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>
        </div>

        @if (session('success'))
            <div class="mt-4">
                <p class="text-sm font-medium text-green-600">
                    {{ session('success') }}
                </p>
            </div>
        @endif

        <button
            type="submit"
            class="cursor-pointer rounded-full bg-blue-600 mt-4 px-4 py-2 text-sm font-semibold text-white"
        >
            Save
        </button>
    </form>
</div>