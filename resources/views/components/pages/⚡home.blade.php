<?php

use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

new class extends Component
{
    public int $unreadCount = 0;

    public function mount(NotificationService $notificationService): void
    {
        $this->unreadCount = $notificationService->getUnreadCount(auth()->user());
    }

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
    <h1>Home</h1>

    <a href="/notifications">
        🔔 {{ $unreadCount }}
    </a>

    @if (session()->has('impersonating_from'))
        <button type="button" wire:click="stopImpersonating">
            Stop impersonating
        </button>
    @endif
</div>