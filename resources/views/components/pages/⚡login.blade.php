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
    <h1>Login</h1>

    <form wire:submit="login">
        <div>
            <label for="email">Email</label>
            <input
                id="email"
                type="email"
                wire:model="email"
                autocomplete="email"
            >

            @error('email')
                <span>{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label for="password">Password</label>
            <input
                id="password"
                type="password"
                wire:model="password"
                autocomplete="current-password"
            >

            @error('password')
                <span>{{ $message }}</span>
            @enderror
        </div>

        <button type="submit">
            Login
        </button>
    </form>
</div>