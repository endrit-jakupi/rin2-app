<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

new class extends Component
{
    public string $email = '';

    public string $password = '';

    public function login(): void
    {
        $credentials = $this->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials)) {
            $this->addError('email', 'The provided credentials are incorrect.');

            return;
        }

        request()->session()->regenerate();

        $this->redirect('/');
    }
};
?>

<div>
    <p class="mb-8 text-3xl font-semibold">
        Login
    </p>

    <form wire:submit="login" class="max-w-xl">

        <div class="mb-6">
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
                autocomplete="email"
                placeholder="name@example.com"
                class="mt-3 w-full rounded-full border border-gray-300 px-4 py-2 text-gray-600 placeholder:text-gray-400 focus:border-gray-400 focus:outline-none"
            >

            @error('email')
                <p class="mt-2 text-sm font-medium text-red-600">
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div class="mb-6">
            <label
                for="password"
                class="block text-sm font-medium"
            >
                Password
            </label>

            <input
                id="password"
                type="password"
                wire:model="password"
                autocomplete="current-password"
                placeholder="Enter password"
                class="mt-3 w-full rounded-full border border-gray-300 px-4 py-2 text-gray-600 placeholder:text-gray-400 focus:border-gray-400 focus:outline-none"
            >

            @error('password')
                <p class="mt-2 text-sm font-medium text-red-600">
                    {{ $message }}
                </p>
            @enderror
        </div>

        <button
            type="submit"
            class="cursor-pointer rounded-full bg-blue-600 px-4 py-2 text-sm font-semibold text-white"
        >
            Login
        </button>
    </form>
</div>