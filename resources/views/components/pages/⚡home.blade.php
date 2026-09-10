<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

new class extends Component
{
    public function stopImpersonating(): void
    {
        $originalUserId = session('impersonating_from');

        Auth::loginUsingId($originalUserId);

        request()->session()->forget('impersonating_from');

        request()->session()->regenerate();

        $this->redirect('/');
    }
};
?>

<div>
    <p class="mb-6 text-3xl font-semibold">
        Home
    </p>

    @if (session()->has('impersonating_from'))
        <button
            type="button"
            wire:click="stopImpersonating"
            class="mt-2 rounded-full bg-blue-600 px-4 py-2 text-sm font-semibold text-white cursor-pointer"
        >
            Stop impersonating
        </button>
    @endif
</div>